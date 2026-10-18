<?php

namespace App\Services\Invoice;

use App\Models\Business;
use App\Models\InvoiceNumberSequence;
use App\Models\InvoiceTemplate;
use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    /**
     * Generate the next gap-free sequential invoice number under a database row lock.
     *
     * @param Business $business
     * @param InvoiceTemplate|null $template
     * @param int|null $year
     * @return string e.g. "INV-2026-0001"
     */
    public function generate(Business $business, ?InvoiceTemplate $template = null, ?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = $template?->number_prefix ?? 'INV-';
        $padding = $template?->number_padding ?? 4;

        return DB::transaction(function () use ($business, $year, $prefix, $padding) {
            // Row-lock sequence record using FOR UPDATE
            $sequence = InvoiceNumberSequence::where('business_id', $business->id)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                // Check if business has an un-versioned sequence or create new
                $sequence = InvoiceNumberSequence::create([
                    'business_id' => $business->id,
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $nextNumber = $sequence->last_number + 1;
            $sequence->last_number = $nextNumber;
            $sequence->save();

            $formattedSequence = str_pad((string) $nextNumber, $padding, '0', STR_PAD_LEFT);

            // Format with year if prefix doesn't already contain it: e.g. INV-2026-0001
            if (str_contains($prefix, '{YEAR}')) {
                return str_replace('{YEAR}', (string) $year, $prefix) . $formattedSequence;
            }

            return "{$prefix}{$year}-{$formattedSequence}";
        });
    }
}
