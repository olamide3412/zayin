<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePlan;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class MaintenancePlanController extends Controller
{
    public function index(): Response
    {
        $plans = MaintenancePlan::with('service')->orderBy('sort_order')->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'service_name' => $plan->service ? $plan->service->name : 'Solar Care',
                'service_id' => $plan->service_id,
                'description' => $plan->description,
                'price_naira' => $plan->price ? round($plan->price / 100, 2) : 0,
                'formatted_price' => $plan->formatted_price,
                'billing_period' => $plan->billing_period,
                'visits_per_year' => $plan->visits_per_year,
                'inclusions' => $plan->inclusions ?? [],
                'is_featured' => (bool) $plan->is_featured,
                'is_active' => (bool) $plan->is_active,
                'sort_order' => $plan->sort_order,
            ];
        });

        $services = Service::published()->get(['id', 'name']);

        return Inertia::render('Auth/MaintenancePlans/Index', [
            'plans' => $plans,
            'services' => $services,
        ]);
    }

    public function create(): Response
    {
        $services = Service::published()->get(['id', 'name']);

        return Inertia::render('Auth/MaintenancePlans/Form', [
            'plan' => null,
            'services' => $services,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'service_id' => 'nullable|exists:services,id',
            'description' => 'nullable|string',
            'price_naira' => 'required|numeric|min:0',
            'billing_period' => 'required|string|in:monthly,quarterly,bi-annually,annually',
            'visits_per_year' => 'required|integer|min:1',
            'inclusions' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $priceKobo = (int) round(((float) $validated['price_naira']) * 100);

        $slug = Str::slug($validated['name']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (MaintenancePlan::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-" . $counter++;
        }

        MaintenancePlan::create([
            'name' => $validated['name'],
            'slug' => $uniqueSlug,
            'service_id' => $validated['service_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $priceKobo,
            'billing_period' => $validated['billing_period'],
            'visits_per_year' => $validated['visits_per_year'],
            'inclusions' => $validated['inclusions'] ?? [],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.plans.index')->with('message', 'Maintenance Plan created successfully!');
    }

    public function edit(MaintenancePlan $plan): Response
    {
        $services = Service::published()->get(['id', 'name']);

        return Inertia::render('Auth/MaintenancePlans/Form', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'service_id' => $plan->service_id,
                'description' => $plan->description,
                'price_naira' => $plan->price ? round($plan->price / 100, 2) : 0,
                'billing_period' => $plan->billing_period,
                'visits_per_year' => $plan->visits_per_year,
                'inclusions' => $plan->inclusions ?? [],
                'is_featured' => (bool) $plan->is_featured,
                'is_active' => (bool) $plan->is_active,
                'sort_order' => $plan->sort_order,
            ],
            'services' => $services,
        ]);
    }

    public function update(Request $request, MaintenancePlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'service_id' => 'nullable|exists:services,id',
            'description' => 'nullable|string',
            'price_naira' => 'required|numeric|min:0',
            'billing_period' => 'required|string|in:monthly,quarterly,bi-annually,annually',
            'visits_per_year' => 'required|integer|min:1',
            'inclusions' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $priceKobo = (int) round(((float) $validated['price_naira']) * 100);

        $plan->update([
            'name' => $validated['name'],
            'service_id' => $validated['service_id'] ?? $plan->service_id,
            'description' => $validated['description'] ?? null,
            'price' => $priceKobo,
            'billing_period' => $validated['billing_period'],
            'visits_per_year' => $validated['visits_per_year'],
            'inclusions' => $validated['inclusions'] ?? [],
            'is_featured' => $validated['is_featured'] ?? $plan->is_featured,
            'is_active' => $validated['is_active'] ?? $plan->is_active,
            'sort_order' => $validated['sort_order'] ?? $plan->sort_order,
        ]);

        return redirect()->route('admin.plans.index')->with('message', 'Maintenance Plan updated successfully!');
    }

    public function destroy(MaintenancePlan $plan): RedirectResponse
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('message', 'Maintenance Plan deleted successfully!');
    }
}
