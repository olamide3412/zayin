<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessBankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceTemplate;
use App\Services\Invoice\InvoiceCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoicePreviewController extends Controller
{
    /**
     * Preview an existing saved invoice in HTML format for iframe.
     */
    public function show(Request $request, Invoice $invoice): Response
    {
        $invoice->loadMissing(['customer', 'items', 'template', 'business.bankAccounts']);

        $business = $invoice->business ?? Business::current();
        $template = $invoice->template ?? InvoiceTemplate::getDefault($business->id);

        $bankAccountIds = $invoice->bank_account_ids ?? [];
        $bankAccounts = !empty($bankAccountIds)
            ? BusinessBankAccount::whereIn('id', $bankAccountIds)->get()
            : collect([]);

        $layout = $template->layout ?? 'classic';
        $viewName = "invoices.layouts.{$layout}";
        if (!view()->exists($viewName)) {
            $viewName = 'invoices.layouts.classic';
        }

        $html = view($viewName, [
            'invoice' => $invoice,
            'business' => $business,
            'template' => $template,
            'bankAccounts' => $bankAccounts,
            'customerSnapshot' => $invoice->customer_snapshot ?? ($invoice->customer ? $invoice->customer->toArray() : []),
        ])->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * Live preview from form payload (unsaved draft).
     */
    public function live(Request $request, InvoiceCalculator $calculator): Response
    {
        $business = Business::current();
        $templateId = $request->input('template_id');
        $template = $templateId ? InvoiceTemplate::find($templateId) : InvoiceTemplate::getDefault($business->id);

        // Build temporary Customer object or array
        $customer = null;
        if ($request->filled('customer_id')) {
            $customer = Customer::find($request->customer_id);
        }

        $customerSnapshot = [
            'name' => $customer?->name ?? $request->input('new_customer.name', 'Customer Name'),
            'address' => $customer?->address ?? $request->input('new_customer.address', ''),
            'phone' => $customer?->phone ?? $request->input('new_customer.phone', ''),
            'email' => $customer?->email ?? $request->input('new_customer.email', ''),
            'tin' => $customer?->tin ?? $request->input('new_customer.tin', ''),
        ];

        // Format items
        $rawItems = $request->input('items', []);
        $itemsCollection = collect();
        $calcItems = [];

        foreach ($rawItems as $index => $item) {
            $unitPriceKobo = (isset($item['unit_price_kobo']) && $item['unit_price_kobo'] !== '' && $item['unit_price_kobo'] !== null)
                ? (int) $item['unit_price_kobo']
                : (int) round(((float) ($item['unit_price'] ?? 0)) * 100);

            $quantity = (float) ($item['quantity'] ?? 1);
            $taxable = filter_var($item['taxable'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $discType = $item['discount_type'] ?? 'none';
            $discVal = (float) ($item['discount_value'] ?? 0);

            $calcItems[] = [
                'unit_price_kobo' => $unitPriceKobo,
                'quantity' => $quantity,
                'taxable' => $taxable,
                'discount_type' => $discType,
                'discount_value' => $discVal,
            ];

            $itemTitle = !empty($item['name']) ? $item['name'] : (!empty($item['item_name']) ? $item['item_name'] : (!empty($item['description']) ? $item['description'] : 'Sample Item'));
            $itemNotes = (!empty($item['description']) && $item['description'] !== $itemTitle) ? $item['description'] : null;

            $tempItem = new InvoiceItem();
            $tempItem->item_name = $itemTitle;
            $tempItem->description = $itemNotes;
            $tempItem->quantity = $quantity;
            $tempItem->unit = $item['unit'] ?? 'pcs';
            $tempItem->unit_price_kobo = $unitPriceKobo;
            $tempItem->discount_type = $discType;
            $tempItem->discount_value = $discVal;
            $tempItem->is_vatable = $taxable;
            $itemsCollection->push($tempItem);
        }

        $vatMode = $request->input('vat_mode', 'exclusive');
        $vatRateBps = (int) $request->input('vat_rate_bps', $business->vat_rate_bps ?? 750);
        $invoiceDiscType = $request->input('discount_type', 'none');
        $invoiceDiscVal = (float) $request->input('discount_value', 0);
        $invoiceDiscountKobo = (int) ($request->input('invoice_discount_kobo', 0));
        if ($invoiceDiscountKobo <= 0 && $invoiceDiscVal > 0) {
            if ($invoiceDiscType === 'percentage') {
                $rawSubtotal = 0;
                foreach ($calcItems as $ci) {
                    $rawSubtotal += ($ci['unit_price_kobo'] * $ci['quantity']);
                }
                $invoiceDiscountKobo = (int) round(($rawSubtotal * $invoiceDiscVal) / 100);
            } elseif ($invoiceDiscType === 'fixed') {
                $invoiceDiscountKobo = (int) round($invoiceDiscVal * 100);
            }
        }

        $shippingKobo = $request->filled('shipping_kobo')
            ? (int) $request->shipping_kobo
            : (int) round(((float) ($request->shipping_naira ?? 0)) * 100);

        $computed = $calculator->calculate(
            items: $calcItems,
            vatRateBps: $vatRateBps,
            vatMode: $vatMode,
            invoiceDiscountKobo: $invoiceDiscountKobo,
            paidKobo: 0,
            shippingKobo: $shippingKobo
        );

        // Update each item total_kobo & discount_kobo from calculation
        foreach ($itemsCollection as $idx => $tempItem) {
            if (isset($computed['items'][$idx])) {
                $tempItem->total_kobo = $computed['items'][$idx]['line_total_kobo'];
                $tempItem->line_net_kobo = $computed['items'][$idx]['line_total_kobo'];
                $tempItem->discount_kobo = $computed['items'][$idx]['discount_kobo'];
            }
        }

        $tempInvoice = new Invoice([
            'business_id' => $business->id,
            'status' => 'draft',
            'invoice_number' => 'PREVIEW',
            'issue_date' => $request->input('issue_date', now()->toDateString()),
            'due_date' => $request->input('due_date'),
            'subtotal_kobo' => $computed['subtotal_kobo'],
            'discount_type' => $invoiceDiscType,
            'discount_value' => $invoiceDiscVal,
            'discount_kobo' => $computed['invoice_discount_kobo'],
            'vat_mode' => $vatMode,
            'vat_rate_bps' => $vatRateBps,
            'vat_kobo' => $computed['vat_kobo'],
            'shipping_kobo' => $shippingKobo,
            'total_kobo' => $computed['total_kobo'],
            'amount_paid_kobo' => 0,
            'notes' => $request->input('notes', $business->default_notes),
            'terms' => $request->input('terms', $business->default_terms),
            'footer_notes' => $request->input('footer_notes'),
            'revision_number' => 1,
        ]);
        $tempInvoice->setRelation('items', $itemsCollection);

        $bankAccountIds = $request->input('bank_account_ids', []);
        $bankAccounts = !empty($bankAccountIds)
            ? BusinessBankAccount::whereIn('id', $bankAccountIds)->get()
            : collect([]);

        $layout = $template->layout ?? 'classic';
        $viewName = "invoices.layouts.{$layout}";
        if (!view()->exists($viewName)) {
            $viewName = 'invoices.layouts.classic';
        }

        $html = view($viewName, [
            'invoice' => $tempInvoice,
            'business' => $business,
            'template' => $template,
            'bankAccounts' => $bankAccounts,
            'customerSnapshot' => $customerSnapshot,
        ])->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
