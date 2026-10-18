<?php

namespace App\Http\Requests\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $isIssued = $invoice && in_array($invoice->status, [Invoice::STATUS_ISSUED, Invoice::STATUS_PARTIALLY_PAID, Invoice::STATUS_PAID]);

        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'new_customer' => ['nullable', 'array'],
            'new_customer.name' => ['required_without:customer_id', 'nullable', 'string', 'max:255'],
            'new_customer.email' => ['nullable', 'email', 'max:255'],
            'new_customer.phone' => ['nullable', 'string', 'max:50'],
            'new_customer.address' => ['nullable', 'string', 'max:500'],
            'new_customer.tin' => ['nullable', 'string', 'max:50'],

            'template_id' => ['nullable', 'exists:invoice_templates,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'max:3'],
            'vat_mode' => ['required', 'in:exclusive,inclusive'],
            'vat_rate_bps' => ['required', 'integer', 'min:0'],

            'discount_type' => ['nullable', 'in:none,percentage,fixed'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'shipping_kobo' => ['nullable', 'integer', 'min:0'],
            'shipping_naira' => ['nullable', 'numeric', 'min:0'],

            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'footer_notes' => ['nullable', 'string'],
            'bank_account_ids' => ['nullable', 'array'],
            'bank_account_ids.*' => ['integer', 'exists:business_bank_accounts,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.catalog_id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.unit_price_kobo' => ['nullable', 'integer', 'min:0'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_type' => ['nullable', 'in:none,percentage,fixed'],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.taxable' => ['nullable', 'boolean'],

            'revision_reason' => [$isIssued ? 'required' : 'nullable', 'string', 'max:500'],
            'action' => ['nullable', 'in:draft,issue'],
        ];
    }
}
