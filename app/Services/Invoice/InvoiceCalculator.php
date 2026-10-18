<?php

namespace App\Services\Invoice;

class InvoiceCalculator
{
    /**
     * Calculate financial totals for invoice items strictly using integer kobo math.
     *
     * @param array $items Array of item rows:
     *                     - quantity (numeric)
     *                     - unit_price_kobo (int)
     *                     - discount_type ('none' | 'percentage' | 'percent' | 'fixed')
     *                     - discount_value (numeric)
     *                     - is_vatable / taxable (bool)
     * @param int $vatRateBps e.g. 750 for 7.5%
     * @param string $vatMode 'exclusive' | 'inclusive'
     * @param int $invoiceDiscountKobo
     * @param int $paidKobo
     * @param int $shippingKobo
     * @return array
     */
    public function calculate(
        array $items,
        int $vatRateBps = 750,
        string $vatMode = 'exclusive',
        int $invoiceDiscountKobo = 0,
        int $paidKobo = 0,
        int $shippingKobo = 0
    ): array {
        $computedLines = [];
        $subtotalKobo = 0;
        $vatableSubtotalKobo = 0;

        foreach ($items as $item) {
            $qty = (float) ($item['quantity'] ?? 1);
            $unitPriceKobo = (isset($item['unit_price_kobo']) && $item['unit_price_kobo'] !== '' && $item['unit_price_kobo'] !== null)
                ? (int) $item['unit_price_kobo']
                : (int) round(((float) ($item['unit_price'] ?? 0)) * 100);
            $discountType = $item['discount_type'] ?? 'none';
            $discountValue = (float) ($item['discount_value'] ?? 0);
            $isVatable = filter_var($item['taxable'] ?? ($item['is_vatable'] ?? false), FILTER_VALIDATE_BOOLEAN);

            // line_gross = round_half_up(quantity * unit_price)
            $lineGrossKobo = (int) round($qty * $unitPriceKobo, 0, PHP_ROUND_HALF_UP);

            // line_discount = fixed amount OR (line_gross * percent)
            $lineDiscountKobo = 0;
            if (in_array($discountType, ['percent', 'percentage']) && $discountValue > 0) {
                $lineDiscountKobo = (int) round(($lineGrossKobo * $discountValue) / 100, 0, PHP_ROUND_HALF_UP);
            } elseif ($discountType === 'fixed' && $discountValue > 0) {
                $lineDiscountKobo = (int) round($discountValue, 0, PHP_ROUND_HALF_UP);
            }

            // Cap line discount to line gross
            $lineDiscountKobo = min($lineDiscountKobo, $lineGrossKobo);
            $lineNetKobo = $lineGrossKobo - $lineDiscountKobo;

            $subtotalKobo += $lineNetKobo;
            if ($isVatable) {
                $vatableSubtotalKobo += $lineNetKobo;
            }

            $computedLines[] = array_merge($item, [
                'name' => $item['name'] ?? ($item['item_name'] ?? ($item['description'] ?? 'Item')),
                'item_name' => $item['name'] ?? ($item['item_name'] ?? ($item['description'] ?? 'Item')),
                'description' => $item['description'] ?? null,
                'quantity' => $qty,
                'unit_price_kobo' => $unitPriceKobo,
                'line_gross_kobo' => $lineGrossKobo,
                'line_discount_kobo' => $lineDiscountKobo,
                'discount_kobo' => $lineDiscountKobo,
                'line_net_kobo' => $lineNetKobo,
                'line_total_kobo' => $lineNetKobo,
                'is_vatable' => $isVatable,
                'taxable' => $isVatable,
            ]);
        }

        // Invoice-level discount (capped at subtotal)
        $invoiceDiscountKobo = max(0, min($invoiceDiscountKobo, $subtotalKobo));

        // Proportional share of invoice discount on taxable base
        $taxableBaseKobo = $vatableSubtotalKobo;
        if ($subtotalKobo > 0 && $invoiceDiscountKobo > 0 && $vatableSubtotalKobo > 0) {
            $discountShareOnVat = (int) round(($invoiceDiscountKobo * ($vatableSubtotalKobo / $subtotalKobo)), 0, PHP_ROUND_HALF_UP);
            $taxableBaseKobo = max(0, $vatableSubtotalKobo - $discountShareOnVat);
        }

        $vatRateMultiplier = $vatRateBps / 10000; // e.g. 750 / 10000 = 0.075

        $vatKobo = 0;
        $totalKobo = 0;

        if ($vatMode === 'inclusive') {
            // Inclusive VAT: prices already include VAT
            // vat = taxable_total - (taxable_total / (1 + rate))
            if ($vatRateMultiplier > 0 && $taxableBaseKobo > 0) {
                $untaxedBase = $taxableBaseKobo / (1 + $vatRateMultiplier);
                $vatKobo = (int) round($taxableBaseKobo - $untaxedBase, 0, PHP_ROUND_HALF_UP);
            }
            $totalKobo = $subtotalKobo - $invoiceDiscountKobo + $shippingKobo;
        } else {
            // Exclusive VAT (default): VAT added on top
            // vat = round_half_up(taxable_base * vat_rate)
            if ($vatRateMultiplier > 0 && $taxableBaseKobo > 0) {
                $vatKobo = (int) round($taxableBaseKobo * $vatRateMultiplier, 0, PHP_ROUND_HALF_UP);
            }
            $totalKobo = $subtotalKobo - $invoiceDiscountKobo + $vatKobo + $shippingKobo;
        }

        $totalKobo = max(0, $totalKobo);
        $paidKobo = max(0, $paidKobo);
        $balanceKobo = max(0, $totalKobo - $paidKobo);

        return [
            'lines' => $computedLines,
            'items' => $computedLines,
            'subtotal_kobo' => $subtotalKobo,
            'discount_kobo' => $invoiceDiscountKobo,
            'invoice_discount_kobo' => $invoiceDiscountKobo,
            'vatable_subtotal_kobo' => $vatableSubtotalKobo,
            'taxable_base_kobo' => $taxableBaseKobo,
            'vat_rate_bps' => $vatRateBps,
            'vat_mode' => $vatMode,
            'vat_kobo' => $vatKobo,
            'shipping_kobo' => $shippingKobo,
            'total_kobo' => $totalKobo,
            'paid_kobo' => $paidKobo,
            'balance_kobo' => $balanceKobo,
        ];
    }
}
