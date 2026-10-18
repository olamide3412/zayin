<?php

namespace App\Services\Invoice;

class NairaInWords
{
    protected static array $ones = [
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
    ];

    protected static array $tens = [
        2 => 'Twenty',
        3 => 'Thirty',
        4 => 'Forty',
        5 => 'Fifty',
        6 => 'Sixty',
        7 => 'Seventy',
        8 => 'Eighty',
        9 => 'Ninety',
    ];

    /**
     * Convert integer kobo or Naira to English Words with Nigerian currency suffix.
     *
     * @param int|float $amountInKobo Total integer kobo (e.g. 20850000) or float naira
     * @param bool $isKobo True if amount provided is in kobo, false if already in Naira
     * @return string e.g. "Two Hundred and Eight Thousand, Five Hundred Naira Only"
     */
    public static function convert(int|float $amount, bool $isKobo = true): string
    {
        $totalKobo = $isKobo ? (int) round($amount) : (int) round($amount * 100);

        if ($totalKobo <= 0) {
            return 'Zero Naira Only';
        }

        $naira = intdiv($totalKobo, 100);
        $kobo = $totalKobo % 100;

        $parts = [];

        if ($naira > 0) {
            $nairaWords = self::numberToWords($naira);
            $parts[] = $nairaWords . ' ' . ($naira === 1 ? 'Naira' : 'Naira');
        }

        if ($kobo > 0) {
            $koboWords = self::numberToWords($kobo);
            $parts[] = $koboWords . ' ' . ($kobo === 1 ? 'Kobo' : 'Kobo');
        }

        if (empty($parts)) {
            return 'Zero Naira Only';
        }

        return implode(', ', $parts) . ' Only';
    }

    /**
     * Convert an integer number into English words with appropriate separators.
     */
    public static function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        if ($number < 0) {
            return 'Negative ' . self::numberToWords(abs($number));
        }

        $words = [];

        // Billions
        if ($number >= 1000000000) {
            $billions = intdiv($number, 1000000000);
            $words[] = self::numberToWords($billions) . ' Billion';
            $number %= 1000000000;
        }

        // Millions
        if ($number >= 1000000) {
            $millions = intdiv($number, 1000000);
            $words[] = self::numberToWords($millions) . ' Million';
            $number %= 1000000;
        }

        // Thousands
        if ($number >= 1000) {
            $thousands = intdiv($number, 1000);
            $words[] = self::numberToWords($thousands) . ' Thousand';
            $number %= 1000;
        }

        // Hundreds
        if ($number >= 100) {
            $hundreds = intdiv($number, 100);
            $words[] = self::$ones[$hundreds] . ' Hundred';
            $number %= 100;
        }

        if ($number > 0) {
            $prefix = (!empty($words)) ? 'and ' : '';

            if ($number < 20) {
                $words[] = $prefix . self::$ones[$number];
            } else {
                $ten = intdiv($number, 10);
                $unit = $number % 10;
                $tenWord = self::$tens[$ten];
                if ($unit > 0) {
                    $tenWord .= '-' . self::$ones[$unit];
                }
                $words[] = $prefix . $tenWord;
            }
        }

        // Separate groups smoothly: e.g. "Two Hundred and Eight Thousand, Five Hundred"
        $result = '';
        $count = count($words);
        for ($i = 0; $i < $count; $i++) {
            $w = $words[$i];
            if ($i > 0 && !str_starts_with($w, 'and ')) {
                $result .= ', ' . $w;
            } elseif ($i > 0) {
                $result .= ' ' . $w;
            } else {
                $result .= $w;
            }
        }

        return trim($result);
    }
}
