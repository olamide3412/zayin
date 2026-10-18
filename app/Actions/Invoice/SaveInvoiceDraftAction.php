<?php

namespace App\Actions\Invoice;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\Invoice\InvoiceCalculator;
use App\Services\Invoice\ItemCatalogService;
use App\Services\Invoice\NairaInWords;
use Illuminate\Support\Facades\DB;

class SaveInvoiceDraftAction
{
    public function __construct(
        protected InvoiceCalculator $calculator,
        protected ItemCatalogService $catalogService
    ) {}

    public function execute(Business $business, array $data, ?Invoice $existingInvoice = null, ?int $userId = null): Invoice
    {
        return DB::transaction(function () use ($business, $data, $existingInvoice, $userId) {
            // 1. Resolve or create customer
            $customerId = $data['customer_id'] ?? null;
            if (empty($customerId) && !empty($data['customer_name'])) {
                $customer = Customer::create([
                    'business_id' => $business->id,
                    'name' => trim($data['customer_name']),
                    'address' => $data['customer_address'] ?? null,
                    'phone' => $data['customer_phone'] ?? null,
                    'email' => $data['customer_email'] ?? null,
                    'tin' => $data['customer_tin'] ?? null,
                ]);
                $customerId = $customer->id;
            }

            // 2. Compute authoritative totals using InvoiceCalculator
            $itemsData = $data['items'] ?? [];
            $vatRateBps = (int) ($data['vat_rate_bps'] ?? $business->vat_rate_bps ?? 750);
            $vatMode = $data['vat_mode'] ?? $business->default_vat_mode ?? 'exclusive';
            $invoiceDiscountKobo = (int) ($data['invoice_discount_kobo'] ?? 0);
            $alreadyPaidKobo = (int) ($existingInvoice?->paid_kobo ?? 0);

            $calc = $this->calculator->calculate(
                items: $itemsData,
                vatRateBps: $vatRateBps,
                vatMode: $vatMode,
                invoiceDiscountKobo: $invoiceDiscountKobo,
                paidKobo: $alreadyPaidKobo
            );

            // 3. Amount in words
            $amountInWords = NairaInWords::convert($calc['total_kobo'], isKobo: true);

            // 4. Create or update invoice model
            $invoice = $existingInvoice ?? new Invoice();
            $invoice->fill([
                'business_id' => $business->id,
                'customer_id' => $customerId,
                'template_id' => $data['template_id'] ?? $business->defaultTemplate?->id,
                'status' => $existingInvoice ? $existingInvoice->status : 'draft',
                'issue_date' => $data['issue_date'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? now()->addDays(7)->toDateString(),
                'payment_terms' => $data['payment_terms'] ?? 'Due in 7 days',
                'reference' => $data['reference'] ?? null,
                'subject' => $data['subject'] ?? null,
                'vat_mode' => $vatMode,
                'vat_rate_bps' => $vatRateBps,
                'invoice_discount_kobo' => $invoiceDiscountKobo,
                'subtotal_kobo' => $calc['subtotal_kobo'],
                'discount_kobo' => $calc['discount_kobo'],
                'vat_kobo' => $calc['vat_kobo'],
                'total_kobo' => $calc['total_kobo'],
                'paid_kobo' => $calc['paid_kobo'],
                'balance_kobo' => $calc['balance_kobo'],
                'amount_in_words' => $amountInWords,
                'notes' => $data['notes'] ?? $business->default_notes,
                'terms' => $data['terms'] ?? $business->default_terms,
                'bank_account_ids' => $data['bank_account_ids'] ?? null,
                'created_by' => $userId ?? $invoice->created_by,
            ]);
            $invoice->save();

            // 5. Sync line items & learn new catalog items
            $invoice->items()->delete();
            $sortOrder = 0;

            foreach ($calc['lines'] as $line) {
                $saveToCatalog = $line['save_to_catalog'] ?? true;
                $catalogItem = null;

                $itemName = !empty($line['name']) ? trim($line['name']) : (!empty($line['item_name']) ? trim($line['item_name']) : (!empty($line['description']) ? trim($line['description']) : 'Item'));
                $itemNotes = (!empty($line['description']) && $line['description'] !== $itemName) ? trim($line['description']) : null;

                if (!empty($line['catalog_item_id'])) {
                    $catalogItem = \App\Models\InvoiceItemCatalog::find($line['catalog_item_id']);
                    if ($catalogItem) {
                        $catalogItem->increment('usage_count');
                        $catalogItem->last_used_at = now();
                        if ($catalogItem->default_price_kobo !== (int)$line['unit_price_kobo']) {
                            $catalogItem->last_price_kobo = (int)$line['unit_price_kobo'];
                        }
                        $catalogItem->save();
                    }
                } elseif (!empty($itemName)) {
                    $catalogItem = $this->catalogService->recordUsage(
                        business: $business,
                        name: $itemName,
                        unitPriceKobo: (int) $line['unit_price_kobo'],
                        unit: $line['unit'] ?? 'pcs',
                        isVatable: (bool) ($line['is_vatable'] ?? false),
                        saveToCatalog: (bool) $saveToCatalog
                    );
                }

                $invoice->items()->create([
                    'catalog_item_id' => $catalogItem?->id,
                    'sort_order' => $sortOrder++,
                    'item_name' => $itemName,
                    'description' => $itemNotes,
                    'quantity' => $line['quantity'],
                    'unit' => $line['unit'] ?? 'pcs',
                    'unit_price_kobo' => $line['unit_price_kobo'],
                    'discount_type' => $line['discount_type'] ?? 'none',
                    'discount_value' => $line['discount_value'] ?? 0,
                    'is_vatable' => (bool) ($line['is_vatable'] ?? false),
                    'line_net_kobo' => $line['line_net_kobo'],
                ]);
            }

            return $invoice->fresh(['items', 'customer', 'template']);
        });
    }
}
