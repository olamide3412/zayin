<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Services\Invoice\InvoiceNumberGenerator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class IssueInvoiceAction
{
    public function __construct(
        protected InvoiceNumberGenerator $numberGenerator
    ) {}

    public function execute(Invoice $invoice): Invoice
    {
        if ($invoice->isVoid()) {
            throw new InvalidArgumentException('Cannot issue a voided invoice.');
        }

        if (!empty($invoice->invoice_number) && $invoice->isIssued()) {
            return $invoice; // Already issued
        }

        return DB::transaction(function () use ($invoice) {
            $business = $invoice->business ?? \App\Models\Business::current();
            $template = $invoice->template;

            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = $this->numberGenerator->generate(
                    business: $business,
                    template: $template,
                    year: (int) ($invoice->issue_date?->format('Y') ?? date('Y'))
                );
            }

            $invoice->issued_at = now();

            if ($invoice->balance_kobo <= 0 && $invoice->total_kobo > 0) {
                $invoice->status = 'paid';
            } elseif ($invoice->paid_kobo > 0) {
                $invoice->status = 'partially_paid';
            } else {
                $invoice->status = 'issued';
            }

            $invoice->save();

            return $invoice->fresh(['items', 'customer', 'template', 'business']);
        });
    }
}
