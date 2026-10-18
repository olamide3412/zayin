<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use InvalidArgumentException;

class VoidInvoiceAction
{
    public function execute(Invoice $invoice, string $reason): Invoice
    {
        if (empty(trim($reason))) {
            throw new InvalidArgumentException('A reason is required to void an invoice.');
        }

        if ($invoice->isVoid()) {
            return $invoice;
        }

        $invoice->status = 'void';
        $invoice->voided_at = now();
        $invoice->void_reason = trim($reason);
        $invoice->save();

        return $invoice;
    }
}
