<?php

namespace Tests\Unit\Invoice;

use App\Services\Invoice\InvoiceCalculator;
use PHPUnit\Framework\TestCase;

class InvoiceCalculatorTest extends TestCase
{
    private InvoiceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new InvoiceCalculator();
    }

    /**
     * Fixture 1: Simple single item, exclusive VAT 7.5%
     * 1 × Inverter @ ₦850,000.00
     * Subtotal: 85,000,000 kobo
     * VAT: 6,375,000 kobo
     * Total: 91,375,000 kobo (₦913,750.00)
     */
    public function test_fixture_1_single_item_exclusive_vat(): void
    {
        $items = [
            [
                'unit_price_kobo' => 85000000,
                'quantity' => 1,
                'taxable' => true,
                'discount_type' => 'none',
                'discount_value' => 0,
            ],
        ];

        $res = $this->calculator->calculate($items, 750, 'exclusive');

        $this->assertSame(85000000, $res['subtotal_kobo']);
        $this->assertSame(6375000, $res['vat_kobo']);
        $this->assertSame(91375000, $res['total_kobo']);
        $this->assertSame(0, $res['invoice_discount_kobo']);
    }

    /**
     * Fixture 2: Multi-item with line discounts, exclusive VAT 7.5%
     * - 2 × Panels @ ₦115,000 = ₦230,000 (no discount) => line_total = 23,000,000 kobo
     * - 1 × Cable roll @ ₦28,000 with 10% line discount => gross = 2,800,000, disc = 280,000, line_total = 2,520,000 kobo
     * Subtotal: 25,520,000 kobo
     * VAT (7.5%): 1,914,000 kobo
     * Total: 27,434,000 kobo (₦274,340.00)
     */
    public function test_fixture_2_multi_item_with_line_discounts(): void
    {
        $items = [
            [
                'unit_price_kobo' => 11500000,
                'quantity' => 2,
                'taxable' => true,
                'discount_type' => 'none',
                'discount_value' => 0,
            ],
            [
                'unit_price_kobo' => 2800000,
                'quantity' => 1,
                'taxable' => true,
                'discount_type' => 'percentage',
                'discount_value' => 10,
            ],
        ];

        $res = $this->calculator->calculate($items, 750, 'exclusive');

        $this->assertSame(23000000, $res['items'][0]['line_total_kobo']);
        $this->assertSame(2520000, $res['items'][1]['line_total_kobo']);
        $this->assertSame(280000, $res['items'][1]['discount_kobo']);
        $this->assertSame(25520000, $res['subtotal_kobo']);
        $this->assertSame(1914000, $res['vat_kobo']);
        $this->assertSame(27434000, $res['total_kobo']);
    }

    /**
     * Fixture 3: Inclusive VAT 7.5% with invoice-level discount
     * Subtotal: ₦1,000,000 (100,000,000 kobo)
     * 5% invoice discount => 5,000,000 kobo
     * Net base: 95,000,000 kobo
     * VAT: round(95,000,000 * 750 / 10750) = 6,627,907 kobo (₦66,279.07 embedded)
     * Total: 95,000,000 kobo (₦950,000.00)
     */
    public function test_fixture_3_inclusive_vat_with_invoice_discount(): void
    {
        $items = [
            [
                'unit_price_kobo' => 100000000,
                'quantity' => 1,
                'taxable' => true,
                'discount_type' => 'none',
                'discount_value' => 0,
            ],
        ];

        // 5% discount on 100,000,000 kobo = 5,000,000 kobo
        $res = $this->calculator->calculate(
            $items,
            750,
            'inclusive',
            5000000,
            0,
            0
        );

        $this->assertSame(100000000, $res['subtotal_kobo']);
        $this->assertSame(5000000, $res['invoice_discount_kobo']);
        $this->assertSame(6627907, $res['vat_kobo']);
        $this->assertSame(95000000, $res['total_kobo']);
    }

    /**
     * Fixture 4: Mixed taxable & zero-rated items with shipping
     * - 1 × Starlink Kit @ ₦590,000 (taxable) => 59,000,000 kobo
     * - 1 × Installation Service @ ₦35,000 (zero-rated / non-taxable) => 3,500,000 kobo
     * Shipping: ₦15,000 (1,500,000 kobo)
     * Subtotal: 62,500,000 kobo
     * Vatable base: 59,000,000 kobo
     * VAT: 4,425,000 kobo (59,000,000 * 750 / 10000)
     * Total: 59,000,000 + 3,500,000 + 4,425,000 + 1,500,000 = 68,425,000 kobo (₦684,250.00)
     */
    public function test_fixture_4_mixed_taxable_and_zero_rated_with_shipping(): void
    {
        $items = [
            [
                'unit_price_kobo' => 59000000,
                'quantity' => 1,
                'taxable' => true,
                'discount_type' => 'none',
                'discount_value' => 0,
            ],
            [
                'unit_price_kobo' => 3500000,
                'quantity' => 1,
                'taxable' => false,
                'discount_type' => 'none',
                'discount_value' => 0,
            ],
        ];

        $res = $this->calculator->calculate(
            $items,
            750,
            'exclusive',
            0,
            0,
            1500000
        );

        $this->assertSame(62500000, $res['subtotal_kobo']);
        $this->assertSame(59000000, $res['vatable_subtotal_kobo']);
        $this->assertSame(4425000, $res['vat_kobo']);
        $this->assertSame(1500000, $res['shipping_kobo']);
        $this->assertSame(68425000, $res['total_kobo']);
    }
}
