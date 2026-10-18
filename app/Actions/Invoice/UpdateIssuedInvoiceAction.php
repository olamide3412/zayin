<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceRevision;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UpdateIssuedInvoiceAction
{
    public function __construct(
        protected SaveInvoiceDraftAction $saveDraftAction
    ) {}

    public function execute(Invoice $invoice, array $data, ?int $userId = null, ?string $reason = null): Invoice
    {
        if ($invoice->isVoid()) {
            throw new InvalidArgumentException('Cannot edit a voided invoice.');
        }

        return DB::transaction(function () use ($invoice, $data, $userId, $reason) {
            // Take snapshot of invoice and items BEFORE edit
            $beforeSnapshot = $invoice->load(['items', 'customer'])->toArray();

            // Perform update using SaveInvoiceDraftAction
            $updatedInvoice = $this->saveDraftAction->execute(
                business: $invoice->business,
                data: $data,
                existingInvoice: $invoice,
                userId: $userId
            );

            // Take snapshot AFTER edit
            $afterSnapshot = $updatedInvoice->load(['items', 'customer'])->toArray();

            // Record revision log
            InvoiceRevision::create([
                'invoice_id' => $updatedInvoice->id,
                'user_id' => $userId,
                'before' => $beforeSnapshot,
                'after' => $afterSnapshot,
                'reason' => $reason ?: 'Invoice updated after issue',
                'created_at' => now(),
            ]);

            return $updatedInvoice;
        });
    }
}
