<?php

namespace App\Services\Solar;

use App\DataTransferObjects\Solar\CalculatorResult;
use App\Models\Product;

class PackageMatcher
{
    /**
     * Match published solar packages to sizing result.
     *
     * @return array<int, array{
     *   id: int,
     *   name: string,
     *   slug: string,
     *   price: int,
     *   compare_at_price: int|null,
     *   image: string|null,
     *   tier: string,
     *   tier_label: string,
     *   specs: array<string, mixed>,
     *   summary: string|null
     * }>
     */
    public function match(CalculatorResult $result): array
    {
        $minKva = $result->inverter['kva'];
        $minBatteryKwh = $result->battery['bankWh'] / 1000.0;
        $minArrayW = $result->solar['arrayW'];

        $products = Product::query()
            ->where('is_published', true)
            ->where(function ($q) {
                $q->where('type', 'solar_package')
                  ->orWhere('type', 'package');
            })
            ->get();

        $matched = [];
        $closeMatches = [];

        foreach ($products as $product) {
            $specs = is_array($product->specs) ? $product->specs : (json_decode($product->specs ?? '[]', true) ?: []);
            $pkgKva = (float) ($specs['capacity_kva'] ?? 0);
            $pkgBatteryKwh = (float) ($specs['battery_kwh'] ?? 0);
            $pkgPanelsW = (float) ($specs['panels_w'] ?? 0);

            // Exact or covering match
            $coversInverter = ($pkgKva >= $minKva);
            $coversBattery = ($pkgBatteryKwh >= ($minBatteryKwh * 0.85)); // 15% tolerance
            $coversPanels = ($minArrayW == 0 || $pkgPanelsW >= ($minArrayW * 0.80));

            $item = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (int) $product->price,
                'compare_at_price' => $product->compare_at_price ? (int) $product->compare_at_price : null,
                'image' => $product->image,
                'specs' => $specs,
                'summary' => $product->summary,
            ];

            if ($coversInverter && $coversBattery && $coversPanels) {
                $matched[] = $item;
            } elseif ($coversInverter) {
                $closeMatches[] = $item;
            }
        }

        // If no full match, use close matches
        $candidates = !empty($matched) ? $matched : $closeMatches;

        if (empty($candidates)) {
            return [];
        }

        // Sort ascending by price
        usort($candidates, fn($a, $b) => $a['price'] <=> $b['price']);

        $tiers = [
            ['tier' => 'good', 'tier_label' => 'Standard Fit'],
            ['tier' => 'better', 'tier_label' => 'High Performance'],
            ['tier' => 'best', 'tier_label' => 'Premium Autonomy'],
        ];

        $results = [];
        $count = min(3, count($candidates));

        for ($i = 0; $i < $count; $i++) {
            $candidate = $candidates[$i];
            $tierInfo = $tiers[$i] ?? $tiers[0];
            $candidate['tier'] = $tierInfo['tier'];
            $candidate['tier_label'] = $tierInfo['tier_label'];
            $results[] = $candidate;
        }

        return $results;
    }
}
