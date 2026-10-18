<?php

namespace App\Services\Invoice;

use App\Models\Business;
use App\Models\InvoiceItemCatalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ItemCatalogService
{
    /**
     * Search items for smart suggestion dropdown (§6).
     * Exact/prefix match first, contains next, ranked by usage_count and last_used_at.
     */
    public function suggest(Business $business, string $query, int $limit = 8): Collection
    {
        $normalized = InvoiceItemCatalog::normalizeName($query);

        if (empty($normalized)) {
            return InvoiceItemCatalog::where('business_id', $business->id)
                ->active()
                ->orderByDesc('usage_count')
                ->orderByDesc('last_used_at')
                ->take($limit)
                ->get();
        }

        return InvoiceItemCatalog::where('business_id', $business->id)
            ->active()
            ->where(function ($q) use ($query, $normalized) {
                $q->where('normalized_name', 'like', "{$normalized}%")
                  ->orWhere('normalized_name', 'like', "%{$normalized}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->orderByRaw("
                CASE 
                    WHEN normalized_name = ? THEN 1
                    WHEN normalized_name LIKE ? THEN 2
                    ELSE 3
                END ASC
            ", [$normalized, "{$normalized}%"])
            ->orderByDesc('usage_count')
            ->orderByDesc('last_used_at')
            ->take($limit)
            ->get();
    }

    /**
     * Upsert or record usage of an item from an invoice line (§6).
     */
    public function recordUsage(
        Business $business,
        string $name,
        int $unitPriceKobo,
        string $unit = 'pcs',
        bool $isVatable = true,
        bool $saveToCatalog = true
    ): ?InvoiceItemCatalog {
        $name = trim($name);
        if (empty($name)) {
            return null;
        }

        $normalized = InvoiceItemCatalog::normalizeName($name);

        $item = InvoiceItemCatalog::where('business_id', $business->id)
            ->where('normalized_name', $normalized)
            ->first();

        if ($item) {
            // Existing item: increment usage, update last_price if different without overwriting default
            $item->increment('usage_count');
            $item->last_used_at = now();

            if ($item->default_price_kobo !== $unitPriceKobo) {
                $item->last_price_kobo = $unitPriceKobo;
            }

            $item->save();
            return $item;
        }

        if (!$saveToCatalog) {
            return null;
        }

        // New item: insert into catalog
        return InvoiceItemCatalog::create([
            'business_id' => $business->id,
            'name' => $name,
            'normalized_name' => $normalized,
            'unit' => $unit ?: 'pcs',
            'default_price_kobo' => $unitPriceKobo,
            'last_price_kobo' => $unitPriceKobo,
            'is_vatable' => $isVatable,
            'usage_count' => 1,
            'last_used_at' => now(),
            'is_archived' => false,
        ]);
    }

    /**
     * Merge duplicate catalog items into a target item (§6).
     */
    public function merge(InvoiceItemCatalog $target, array $sourceIds): void
    {
        DB::transaction(function () use ($target, $sourceIds) {
            $sources = InvoiceItemCatalog::whereIn('id', $sourceIds)
                ->where('id', '!=', $target->id)
                ->where('business_id', $target->business_id)
                ->get();

            $totalUsage = $target->usage_count;

            foreach ($sources as $source) {
                $totalUsage += $source->usage_count;
                // Re-point past invoice items to target
                $source->invoiceItems()->update(['catalog_item_id' => $target->id]);
                $source->delete();
            }

            $target->usage_count = $totalUsage;
            $target->save();
        });
    }
}
