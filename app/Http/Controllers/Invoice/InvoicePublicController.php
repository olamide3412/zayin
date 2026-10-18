<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\BusinessBankAccount;
use App\Models\Invoice;
use App\Services\Invoice\DomPdfInvoiceRenderer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class InvoicePublicController extends Controller
{
    /**
     * Public read-only page for an invoice.
     */
    public function show(string $token): InertiaResponse
    {
        $invoice = Invoice::where('public_token', $token)
            ->with(['customer', 'items', 'payments', 'template', 'business.bankAccounts'])
            ->firstOrFail();

        $bankAccountIds = $invoice->bank_account_ids ?? [];
        $designatedBanks = !empty($bankAccountIds)
            ? BusinessBankAccount::whereIn('id', $bankAccountIds)->get()
            : collect([]);

        return Inertia::render('Invoices/Public', [
            'invoice' => $invoice,
            'designatedBanks' => $designatedBanks,
            'pdfDownloadUrl' => route('invoices.public.pdf', ['token' => $token]),
        ]);
    }

    /**
     * Public PDF download via token.
     */
    public function downloadPdf(string $token, DomPdfInvoiceRenderer $renderer): Response
    {
        $invoice = Invoice::where('public_token', $token)
            ->with(['customer', 'items', 'template', 'business.bankAccounts'])
            ->firstOrFail();

        $pdfOutput = $renderer->render($invoice);
        $filename = ($invoice->invoice_number ?: 'Invoice-' . $invoice->id) . '.pdf';

        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
