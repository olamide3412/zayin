<?php

namespace App\Http\Controllers\Public;

use App\DataTransferObjects\Solar\CalculatorInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\Solar\CalculateRequest;
use App\Models\ApplianceCategory;
use App\Models\AppliancePreset;
use App\Models\Setting;
use App\Models\SolarCalculation;
use App\Services\Seo\JsonLdBuilder;
use App\Services\Solar\PackageMatcher;
use App\Services\Solar\SolarSettings;
use App\Services\Solar\SolarSizingCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SolarCalculatorController extends Controller
{
    public function __construct(
        protected SolarSizingCalculator $calculator,
        protected PackageMatcher $packageMatcher,
    ) {}

    /**
     * Display the Solar Load Calculator (Public, SEO-optimised SSR).
     */
    public function index(Request $request): Response
    {
        $categories = ApplianceCategory::orderBy('sort_order')->get(['id', 'name', 'slug', 'icon']);
        $presets = AppliancePreset::active()->orderBy('sort_order')->get();
        $settings = SolarSettings::load();

        // Default initial calculation (worked example or flat)
        $defaultInput = CalculatorInput::fromArray([
            'appliances' => [
                ['name' => 'LED bulb', 'watts' => 10, 'qty' => 6, 'hours' => 5, 'surge' => 1.0, 'is_resistive' => false, 'icon' => 'lightbulb'],
                ['name' => 'Ceiling fan', 'watts' => 75, 'qty' => 2, 'hours' => 8, 'surge' => 2.0, 'is_resistive' => false, 'icon' => 'fan'],
                ['name' => 'TV LED 43"–55"', 'watts' => 80, 'qty' => 1, 'hours' => 6, 'surge' => 1.0, 'is_resistive' => false, 'icon' => 'tv'],
                ['name' => 'Refrigerator (medium)', 'watts' => 150, 'qty' => 1, 'hours' => 10, 'surge' => 3.0, 'is_resistive' => false, 'icon' => 'temperature-low'],
            ],
            'backup_hours' => 12,
            'goal' => 'backup',
            'battery_type' => 'lithium',
            'zone' => 'south_west',
        ]);

        $defaultResult = $this->calculator->calculate($defaultInput, $settings);
        $matchedPackages = $this->packageMatcher->match($defaultResult);

        $companyName = Setting::get('company_name', 'Zayin Technologies');

        $seo = [
            'title' => "Solar Calculator Nigeria – Size Your Inverter, Battery & Panels | {$companyName}",
            'description' => 'Free solar load calculator for Nigerian homes and businesses. Add your appliances and get an exact inverter size, battery bank capacity, and solar panel array.',
            'canonical' => url('/solar-calculator'),
            'jsonLd' => [
                JsonLdBuilder::organization(),
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebApplication',
                    'name' => 'Zayin Technologies Household Energy & Solar Sizing Calculator',
                    'applicationCategory' => 'UtilitiesApplication',
                    'operatingSystem' => 'All Web Browsers',
                    'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'NGN',
                    ],
                ],
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => url('/'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => 'Solar Load Calculator',
                            'item' => url('/solar-calculator'),
                        ],
                    ],
                ],
            ],
        ];

        return Inertia::render('Public/SolarCalculator/Index', [
            'categories' => $categories,
            'presets' => $presets,
            'constants' => $settings->constants,
            'zones' => $settings->pshByZone,
            'packages' => $matchedPackages,
            'seo' => $seo,
        ]);
    }

    /**
     * Compute authoritative verified calculation (POST /solar-calculator/calculate).
     */
    public function calculate(CalculateRequest $request): JsonResponse
    {
        $input = CalculatorInput::fromArray($request->validated());
        $settings = SolarSettings::load();

        $result = $this->calculator->calculate($input, $settings);
        $packages = $this->packageMatcher->match($result);

        $data = $result->toArray();
        $data['packages'] = $packages;
        $data['matchedPackages'] = $packages;

        return response()->json($data);
    }

    /**
     * Persist calculation and return shareable unguessable URL (POST /solar-calculator/save).
     */
    public function save(CalculateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $input = CalculatorInput::fromArray($validated);
        $settings = SolarSettings::load();

        // Server recomputes authoritative result (never trust client numbers)
        $result = $this->calculator->calculate($input, $settings);
        $packages = $this->packageMatcher->match($result);

        $ipHash = hash('sha256', (string) $request->ip());

        $calc = SolarCalculation::create([
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
            'inputs' => $input->toArray(),
            'results' => $result->toArray(),
            'settings_version' => $settings->version,
            'zone' => $input->zone,
            'ip_hash' => $ipHash,
        ]);

        return response()->json([
            'uuid' => $calc->uuid,
            'share_url' => url("/solar-calculator/r/{$calc->uuid}"),
            'pdf_url' => url("/solar-calculator/r/{$calc->uuid}/pdf"),
            'result' => $result->toArray(),
            'results' => $result->toArray(),
            'packages' => $packages,
            'matchedPackages' => $packages,
        ]);
    }

    /**
     * View a shared calculation (GET /solar-calculator/r/{uuid}).
     */
    public function showShared(string $uuid): Response
    {
        $calc = SolarCalculation::where('uuid', $uuid)->firstOrFail();

        $settings = SolarSettings::load();
        $input = CalculatorInput::fromArray($calc->inputs);
        $result = $this->calculator->calculate($input, $settings);
        $packages = $this->packageMatcher->match($result);

        $seo = [
            'title' => "Saved Solar Sizing Specification [Ref: " . substr($uuid, 0, 8) . "] | Zayin Technologies",
            'description' => 'Customized solar power and lithium battery sizing report.',
            'noindex' => true,
        ];

        return Inertia::render('Public/SolarCalculator/Shared', [
            'calculation' => $calc,
            'packages' => $packages,
            'matchedPackages' => $packages,
            'seo' => $seo,
        ]);
    }

    /**
     * Download calculation specification as PDF (GET /solar-calculator/r/{uuid}/pdf).
     */
    public function downloadPdf(string $uuid): HttpResponse
    {
        $calc = SolarCalculation::where('uuid', $uuid)->firstOrFail();

        $settings = SolarSettings::load();
        $input = CalculatorInput::fromArray($calc->inputs);
        $result = $this->calculator->calculate($input, $settings);

        $pdf = Pdf::loadView('pdf.solar_calculation', [
            'calculation' => $calc,
            'inputs' => $calc->inputs,
            'results' => $result->toArray(),
            'companyPhone' => Setting::get('phone', '+234 803 123 4567'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("zayin-solar-sizing-{$uuid}.pdf");
    }
}
