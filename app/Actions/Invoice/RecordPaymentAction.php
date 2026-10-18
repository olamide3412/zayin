<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RecordPaymentAction
{
    /**
     * Record a payment against an invoice and update the invoice payment status.
     *
     * @param Invoice $invoice
     * @param array $data [
     *     'amount_kobo' => int,
     *     'paid_at' => string|DateTime,
     *     'method' => string,
     *     'reference' => ?string,
     *     'notes' => ?string,
     *     'recorded_by' => ?int
     * ]
     * @return InvoicePayment
     */
    public function execute(Invoice $invoice, array $data, ?User $user = null): InvoicePayment
    {
        return DB::transaction(function () use ($invoice, $data, $user) {
            $freshInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

            if (in_array($freshInvoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_VOID])) {
                throw new InvalidArgumentException("Cannot record payment on a {$freshInvoice->status} invoice.");
            }

            $amountKobo = (int) ($data['amount_kobo'] ?? 0);
            if ($amountKobo <= 0) {
                throw new InvalidArgumentException('Payment amount must be greater than zero.');
            }

            $remainingKobo = max(0, $freshInvoice->total_kobo - $freshInvoice->amount_paid_kobo);
            if ($amountKobo > $remainingKobo) {
                throw new InvalidArgumentException("Payment amount exceeds remaining balance of ₦" . number_format($remainingKobo / 100, 2));
            }

            $payment = InvoicePayment::create([
                'invoice_id' => $freshInvoice->id,
                'amount_kobo' => $amountKobo,
                'paid_on' => $data['paid_on'] ?? ($data['paid_at'] ?? now()),
                'method' => $data['method'] ?? InvoicePayment::METHOD_TRANSFER,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'recorded_by' => $user?->id ?? ($data['recorded_by'] ?? null),
            ]);

            $newAmountPaid = $freshInvoice->amount_paid_kobo + $amountKobo;
            $newStatus = ($newAmountPaid >= $freshInvoice->total_kobo)
                ? Invoice::STATUS_PAID
                : Invoice::STATUS_PARTIALLY_PAID;

            $freshInvoice->update([
                'paid_kobo' => $newAmountPaid,
                'amount_paid_kobo' => $newAmountPaid,
                'balance_kobo' => max(0, $freshInvoice->total_kobo - $newAmountPaid),
                'status' => $newStatus,
            ]);

            return $payment;
        });
    }
}
