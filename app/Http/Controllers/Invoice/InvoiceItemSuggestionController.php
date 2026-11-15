<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\Invoice\ItemCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceItemSuggestionController extends Controller
{
    /**
     * Debounced search endpoint for invoice item suggestions.
     */
    public function suggest(Request $request, ItemCatalogService $catalogService): JsonResponse
    {
        $query = (string) $request->input('q', '');
        $business = Business::current();
        $limit = min(20, max(1, (int) $request->input('limit', 8)));

        $items = $catalogService->suggest($business, $query, $limit);

        $results = $items->map(function ($item) {
            $priceKobo = $item->last_price_kobo ?? $item->default_price_kobo;
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'unit' => $item->unit ?? 'pcs',
                'category' => $item->category,
                'unit_price_kobo' => $priceKobo,
                'unit_price' => round($priceKobo / 100, 2),
                'unit_price_naira' => number_format($priceKobo / 100, 2),
                'is_vatable' => (bool) $item->is_vatable,
                'usage_count' => $item->usage_count,
            ];
        });

        return response()->json($results);
    }
}
