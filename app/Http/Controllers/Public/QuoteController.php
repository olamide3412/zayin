<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\State;
use App\Services\Seo\SeoData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class QuoteController extends Controller
{
    public function create(): Response
    {
        $services = Service::published()->get(['id', 'name', 'slug']);
        $states = State::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);

        $seo = new SeoData(
            title: 'Request a Free Quote & Solar Sizing',
            description: 'Get an accurate, transparent quotation for solar installation, Starlink internet, or DSTV setup tailored to your energy needs and budget.',
            breadcrumbs: [
                'Home' => url('/'),
                'Request Quote' => url('/request-quote'),
            ]
        );

        return Inertia::render('Quote/Create', [
            'services' => $services,
            'states' => $states,
            'seo' => $seo->toArray(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:120',
            'service_type' => 'required|string',
            'property_type' => 'required|string',
            'state_id' => 'nullable|exists:states,id',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'budget_range' => 'nullable|string|max:60',
            'appliances' => 'nullable|array',
            'details' => 'nullable|string|max:1500',
            'preferred_contact' => 'nullable|string|in:whatsapp,phone,email',
            'calculation_uuid' => 'nullable|string|max:40',
        ]);

        $reference = 'ZAY-Q-' . strtoupper(Str::random(6));

        $calc = null;
        if (!empty($validated['calculation_uuid'])) {
            $calc = \App\Models\SolarCalculation::where('uuid', $validated['calculation_uuid'])->first();
        }

        $quote = QuoteRequest::create([
            'reference' => $reference,
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'phone' => normalize_nigerian_phone($validated['phone']),
            'email' => $validated['email'] ?? null,
            'service_type' => $validated['service_type'],
            'property_type' => $validated['property_type'],
            'state_id' => $validated['state_id'] ?? null,
            'city' => $validated['city'] ?? null,
            'address' => $validated['address'] ?? null,
            'budget_range' => $validated['budget_range'] ?? null,
            'appliances' => $validated['appliances'] ?? ($calc?->inputs['appliances'] ?? null),
            'calculation_id' => $calc?->id,
            'calculation_uuid' => $calc?->uuid,
            'details' => $validated['details'] ?? null,
            'preferred_contact' => $validated['preferred_contact'] ?? 'whatsapp',
            'status' => 'new',
        ]);

        if ($calc) {
            $calc->quote_request_id = $quote->id;
            $calc->save();
        }

        $whatsAppMsg = "Hello Zayin, I just submitted a quote request for {$quote->service_type} on your website. My Reference Code is {$reference}.";
        $whatsappLink = whatsapp_url(config('services.whatsapp.number', '2348031234567'), $whatsAppMsg);

        return redirect()->back()->with([
            'success' => "Your quote request has been received! Your Reference Number is {$reference}.",
            'quote_reference' => $reference,
            'whatsapp_link' => $whatsappLink,
        ]);
    }
}
