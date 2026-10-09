<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerController extends Controller
{
    /**
     * Display a listing of partners for admin.
     */
    public function index(Request $request)
    {
        $query = Partner::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $partners = $query->orderBy('sort_order')->orderByDesc('id')->paginate(15)->withQueryString();

        return Inertia::render('Auth/Partners/Index', [
            'partners' => $partners,
            'filters' => $request->only(['search', 'status']),
            'counts' => [
                'all' => Partner::count(),
                'active' => Partner::where('status', 'active')->count(),
                'inactive' => Partner::where('status', 'inactive')->count(),
            ],
        ]);
    }

    /**
     * Show form for creating a new partner.
     */
    public function create()
    {
        return Inertia::render('Auth/Partners/Create');
    }

    /**
     * Store a newly created partner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'logo_url'    => 'nullable|string',
            'website_url' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer',
        ]);

        if (!empty($validated['website_url']) && !preg_match('/^https?:\/\//i', $validated['website_url'])) {
            $validated['website_url'] = 'https://' . $validated['website_url'];
        }

        Partner::create($validated);

        return redirect()->route('admin.partners.index')->with('success', 'Partner organization added successfully!');
    }

    /**
     * Show form for editing an existing partner.
     */
    public function edit(Partner $partner)
    {
        return Inertia::render('Auth/Partners/Edit', [
            'partner' => $partner,
        ]);
    }

    /**
     * Update the specified partner.
     */
    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'logo_url'    => 'nullable|string',
            'website_url' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer',
        ]);

        if (!empty($validated['website_url']) && !preg_match('/^https?:\/\//i', $validated['website_url'])) {
            $validated['website_url'] = 'https://' . $validated['website_url'];
        }

        $partner->update($validated);

        return redirect()->route('admin.partners.index')->with('success', 'Partner organization updated successfully!');
    }

    /**
     * Remove the specified partner.
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->back()->with('message', 'Partner deleted successfully.');
    }
}
