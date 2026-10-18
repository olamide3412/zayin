<?php

namespace Tests\Unit\Invoice;

use App\Services\Invoice\NairaInWords;
use PHPUnit\Framework\TestCase;

class NairaInWordsTest extends TestCase
{
    public function test_zero_amount(): void
    {
        $this->assertSame('Zero Naira Only', NairaInWords::convert(0));
    }

    public function test_single_naira(): void
    {
        $this->assertSame('One Naira Only', NairaInWords::convert(100));
    }

    public function test_kobo_only(): void
    {
        $this->assertSame('Fifty Kobo Only', NairaInWords::convert(50));
        $this->assertSame('One Kobo Only', NairaInWords::convert(1));
    }

    public function test_naira_and_kobo(): void
    {
        $this->assertSame('One Thousand, Two Hundred and Fifty Naira, Fifty Kobo Only', NairaInWords::convert(125050));
    }

    public function test_large_amounts(): void
    {
        // ₦850,000.00
        $this->assertSame('Eight Hundred and Fifty Thousand Naira Only', NairaInWords::convert(85000000));

        // ₦2,500,000.00
        $this->assertSame('Two Million, Five Hundred Thousand Naira Only', NairaInWords::convert(250000000));
    }
}
