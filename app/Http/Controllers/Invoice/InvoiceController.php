<?php

namespace App\Http\Controllers\Invoice;

use App\Actions\Invoice\DuplicateInvoiceAction;
use App\Actions\Invoice\IssueInvoiceAction;
use App\Actions\Invoice\RecordPaymentAction;
use App\Actions\Invoice\SaveInvoiceDraftAction;
use App\Actions\Invoice\UpdateIssuedInvoiceAction;
use App\Actions\Invoice\VoidInvoiceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Business;
use App\Models\BusinessBankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    /**
     * Display a paginated listing of invoices with search & filters.
     */
    public function index(Request $request): Response
    {
        $business = Business::current();

        $query = Invoice::with(['customer', 'template'])
            ->where('business_id', $business->id)
            ->latest('issue_date')
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        $invoices = $query->paginate(15)->withQueryString();

        $stats = [
            'total_invoices' => Invoice::where('business_id', $business->id)->count(),
            'total_revenue_kobo' => (int) Invoice::where('business_id', $business->id)->whereIn('status', [Invoice::STATUS_ISSUED, Invoice::STATUS_PARTIALLY_PAID, Invoice::STATUS_PAID])->sum('paid_kobo'),
            'total_outstanding_kobo' => (int) Invoice::where('business_id', $business->id)->whereIn('status', [Invoice::STATUS_ISSUED, Invoice::STATUS_PARTIALLY_PAID])->sum('balance_kobo'),
            'drafts_count' => Invoice::where('business_id', $business->id)->where('status', Invoice::STATUS_DRAFT)->count(),
            'paid_count' => Invoice::where('business_id', $business->id)->where('status', Invoice::STATUS_PAID)->count(),
        ];

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['status', 'search', 'customer_id', 'date_from', 'date_to']),
            'stats' => $stats,
            'customers' => Customer::where('business_id', $business->id)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the invoice creation form.
     */
    public function create(): Response
    {
        $business = Business::current();

        return Inertia::render('Invoices/Create', [
            'business' => $business,
            'customers' => Customer::where('business_id', $business->id)->latest('id')->limit(25)->get(),
            'templates' => InvoiceTemplate::where('business_id', $business->id)->get(),
            'bankAccounts' => BusinessBankAccount::where('business_id', $business->id)->orderBy('sort_order')->get(),
            'defaultVatRateBps' => $business->vat_rate_bps ?? 750,
            'defaultVatMode' => $business->default_vat_mode ?? 'exclusive',
            'defaultNotes' => $business->default_notes,
            'defaultTerms' => $business->default_terms,
        ]);
    }

    /**
     * Store a newly created invoice (draft or issued).
     */
    public function store(
        StoreInvoiceRequest $request,
        SaveInvoiceDraftAction $saveDraft,
        IssueInvoiceAction $issueInvoice
    ) {
        $business = Business::current();
        $data = $this->prepareInvoiceData($request, $business);

        $invoice = $saveDraft->execute($business, $data, null, $request->user()?->id);

        if ($request->input('action') === 'issue') {
            $invoice = $issueInvoice->execute($invoice);
            return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice #' . $invoice->invoice_number . ' has been issued successfully.');
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice draft saved successfully.');
    }

    /**
     * Display a specific invoice details page.
     */
    public function show(Invoice $invoice): Response
    {
        $invoice->load([
            'customer',
            'items',
            'payments.recordedBy',
            'revisions.author',
            'template',
            'business.bankAccounts',
        ]);

        $bankAccountIds = $invoice->bank_account_ids ?? [];
        $designatedBanks = !empty($bankAccountIds)
            ? BusinessBankAccount::whereIn('id', $bankAccountIds)->get()
            : collect([]);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
            'designatedBanks' => $designatedBanks,
            'publicUrl' => route('invoices.public', ['token' => $invoice->public_token]),
        ]);
    }

    /**
     * Show invoice editing form.
     */
    public function edit(Invoice $invoice): Response
    {
        $invoice->load(['items', 'customer', 'template']);
        $business = Business::current();

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'business' => $business,
            'customers' => Customer::where('business_id', $business->id)->latest('id')->limit(25)->get(),
            'templates' => InvoiceTemplate::where('business_id', $business->id)->get(),
            'bankAccounts' => BusinessBankAccount::where('business_id', $business->id)->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Update an invoice (draft or issued revision).
     */
    public function update(
        UpdateInvoiceRequest $request,
        Invoice $invoice,
        SaveInvoiceDraftAction $saveDraft,
        UpdateIssuedInvoiceAction $updateIssued,
        IssueInvoiceAction $issueInvoice
    ) {
        $business = Business::current();
        $data = $this->prepareInvoiceData($request, $business);

        if ($invoice->status === Invoice::STATUS_DRAFT) {
            $invoice = $saveDraft->execute($business, $data, $invoice, $request->user()?->id);

            if ($request->input('action') === 'issue') {
                $invoice = $issueInvoice->execute($invoice);
                return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice #' . $invoice->invoice_number . ' has been issued.');
            }

            return redirect()->route('invoices.show', $invoice)->with('success', 'Draft updated successfully.');
        }

        // Issued invoice: requires revision reason
        $reason = $request->input('revision_reason', 'Invoice content revised');
        $invoice = $updateIssued->execute($invoice, $data, $request->user()?->id, $reason);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice revision created successfully.');
    }

    /**
     * Issue a draft invoice.
     */
    public function issue(Invoice $invoice, IssueInvoiceAction $issueInvoice)
    {
        $issued = $issueInvoice->execute($invoice);
        return redirect()->route('invoices.show', $issued)->with('success', 'Invoice #' . $issued->invoice_number . ' has been issued.');
    }

    /**
     * Void an invoice with required reason.
     */
    public function void(Request $request, Invoice $invoice, VoidInvoiceAction $voidInvoice)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $voidInvoice->execute($invoice, $request->input('reason'));

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice marked as VOID.');
    }

    /**
     * Duplicate an invoice.
     */
    public function duplicate(Invoice $invoice, DuplicateInvoiceAction $duplicateAction)
    {
        $newDraft = $duplicateAction->execute($invoice, request()->user());
        return redirect()->route('invoices.edit', $newDraft)->with('success', 'Invoice duplicated as a draft.');
    }

    /**
     * Record a payment against an invoice.
     */
    public function recordPayment(Request $request, Invoice $invoice, RecordPaymentAction $recordPayment)
    {
        $request->validate([
            'amount_naira' => ['nullable', 'numeric', 'min:1'],
            'amount_kobo' => ['nullable', 'integer', 'min:1'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:bank_transfer,cash,pos,cheque,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $amountKobo = $request->filled('amount_kobo')
            ? (int) $request->amount_kobo
            : (int) round(((float) $request->amount_naira) * 100);

        $recordPayment->execute($invoice, [
            'amount_kobo' => $amountKobo,
            'paid_at' => $request->paid_at,
            'method' => $request->method,
            'reference' => $request->reference,
            'notes' => $request->notes,
        ], $request->user());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded successfully.');
    }

    /**
     * Prepare validated request data into standardized structure for actions.
     */
    private function prepareInvoiceData(Request $request, Business $business): array
    {
        $customerId = $request->customer_id;

        // Auto-create new customer if requested
        if (!$customerId && $request->filled('new_customer.name')) {
            $newCust = Customer::create([
                'business_id' => $business->id,
                'name' => $request->input('new_customer.name'),
                'email' => $request->input('new_customer.email'),
                'phone' => $request->input('new_customer.phone'),
                'address' => $request->input('new_customer.address'),
                'tin' => $request->input('new_customer.tin'),
            ]);
            $customerId = $newCust->id;
        }

        $items = [];
        foreach ($request->input('items', []) as $item) {
            $unitPriceKobo = (isset($item['unit_price_kobo']) && $item['unit_price_kobo'] !== '' && $item['unit_price_kobo'] !== null)
                ? (int) $item['unit_price_kobo']
                : (int) round(((float) ($item['unit_price'] ?? 0)) * 100);

            $discountType = $item['discount_type'] ?? 'none';
            $discountValue = (float) ($item['discount_value'] ?? 0);

            $items[] = [
                'catalog_id' => $item['catalog_id'] ?? null,
                'name' => $item['name'] ?? 'Item',
                'description' => $item['description'] ?? null,
                'quantity' => (float) ($item['quantity'] ?? 1),
                'unit' => $item['unit'] ?? 'pcs',
                'unit_price_kobo' => $unitPriceKobo,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'taxable' => filter_var($item['taxable'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        $shippingKobo = $request->filled('shipping_kobo')
            ? (int) $request->shipping_kobo
            : (int) round(((float) ($request->shipping_naira ?? 0)) * 100);

        return [
            'business_id' => $business->id,
            'customer_id' => $customerId,
            'template_id' => $request->template_id,
            'currency' => $request->currency ?? 'NGN',
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'vat_mode' => $request->vat_mode,
            'vat_rate_bps' => (int) $request->vat_rate_bps,
            'discount_type' => $request->discount_type ?? 'none',
            'discount_value' => (float) ($request->discount_value ?? 0),
            'shipping_kobo' => $shippingKobo,
            'notes' => $request->notes,
            'terms' => $request->terms,
            'footer_notes' => $request->footer_notes,
            'bank_account_ids' => $request->bank_account_ids,
            'items' => $items,
        ];
    }
}
