<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\InvoiceItemCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $business = Business::current();

        $query = InvoiceItemCatalog::where('business_id', $business->id)->where('is_archived', false);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->orderByDesc('usage_count')->paginate(20)->withQueryString();

        $categories = InvoiceItemCatalog::where('business_id', $business->id)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return Inertia::render('Invoices/Catalog/Index', [
            'items' => $items,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = Business::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['required', 'string', 'max:50'],
            'price_naira' => ['nullable', 'numeric', 'min:0'],
            'is_vatable' => ['boolean'],
        ]);

        $priceKobo = (int) round(((float) ($validated['price_naira'] ?? 0)) * 100);

        InvoiceItemCatalog::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'normalized_name' => InvoiceItemCatalog::normalizeName($validated['name']),
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'unit' => $validated['unit'] ?? 'pcs',
            'default_price_kobo' => $priceKobo,
            'last_price_kobo' => $priceKobo,
            'is_vatable' => $validated['is_vatable'] ?? true,
        ]);

        return back()->with('success', 'Catalog item added successfully.');
    }

    public function update(Request $request, InvoiceItemCatalog $item): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['required', 'string', 'max:50'],
            'price_naira' => ['nullable', 'numeric', 'min:0'],
            'is_vatable' => ['boolean'],
        ]);

        $priceKobo = (int) round(((float) ($validated['price_naira'] ?? 0)) * 100);

        $item->update([
            'name' => $validated['name'],
            'normalized_name' => InvoiceItemCatalog::normalizeName($validated['name']),
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'unit' => $validated['unit'] ?? 'pcs',
            'default_price_kobo' => $priceKobo,
            'last_price_kobo' => $priceKobo,
            'is_vatable' => $validated['is_vatable'] ?? true,
        ]);

        return back()->with('success', 'Catalog item updated successfully.');
    }

    public function destroy(InvoiceItemCatalog $item): RedirectResponse
    {
        $item->update(['is_archived' => true]);
        return back()->with('success', 'Catalog item archived.');
    }
}
