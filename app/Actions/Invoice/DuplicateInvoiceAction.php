<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DuplicateInvoiceAction
{
    /**
     * Duplicate an existing invoice into a new draft invoice.
     */
    public function execute(Invoice $sourceInvoice, ?User $user = null): Invoice
    {
        return DB::transaction(function () use ($sourceInvoice, $user) {
            $sourceInvoice->loadMissing('items');

            $newInvoice = Invoice::create([
                'business_id' => $sourceInvoice->business_id,
                'customer_id' => $sourceInvoice->customer_id,
                'template_id' => $sourceInvoice->template_id,
                'status' => Invoice::STATUS_DRAFT,
                'invoice_number' => null,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'issued_at' => null,
                'voided_at' => null,
                'void_reason' => null,
                'paid_kobo' => 0,
                'balance_kobo' => $sourceInvoice->total_kobo,
                'subtotal_kobo' => $sourceInvoice->subtotal_kobo,
                'discount_kobo' => $sourceInvoice->discount_kobo,
                'invoice_discount_kobo' => $sourceInvoice->invoice_discount_kobo,
                'vat_mode' => $sourceInvoice->vat_mode,
                'vat_rate_bps' => $sourceInvoice->vat_rate_bps,
                'vat_kobo' => $sourceInvoice->vat_kobo,
                'total_kobo' => $sourceInvoice->total_kobo,
                'notes' => $sourceInvoice->notes,
                'terms' => $sourceInvoice->terms,
                'bank_account_ids' => $sourceInvoice->bank_account_ids,
                'created_by' => $user?->id ?? $sourceInvoice->created_by,
            ]);

            foreach ($sourceInvoice->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $newInvoice->id,
                    'catalog_item_id' => $item->catalog_item_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price_kobo' => $item->unit_price_kobo,
                    'discount_type' => $item->discount_type,
                    'discount_value' => $item->discount_value,
                    'is_vatable' => $item->is_vatable,
                    'line_net_kobo' => $item->line_net_kobo,
                    'sort_order' => $item->sort_order,
                ]);
            }

            return $newInvoice->fresh(['items', 'customer']);
        });
    }
}
