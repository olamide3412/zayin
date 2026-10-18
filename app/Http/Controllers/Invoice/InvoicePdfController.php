<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Invoice\DomPdfInvoiceRenderer;
use Illuminate\Http\Response;

class InvoicePdfController extends Controller
{
    /**
     * Download the invoice PDF as an attachment.
     */
    public function download(Invoice $invoice, DomPdfInvoiceRenderer $renderer): Response
    {
        $invoice->loadMissing(['customer', 'items', 'template', 'business.bankAccounts']);
        $pdfOutput = $renderer->render($invoice);

        $filename = ($invoice->invoice_number ?: 'Invoice-' . $invoice->id) . '.pdf';

        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Stream the invoice PDF inline in the browser.
     */
    public function stream(Invoice $invoice, DomPdfInvoiceRenderer $renderer): Response
    {
        $invoice->loadMissing(['customer', 'items', 'template', 'business.bankAccounts']);
        $pdfOutput = $renderer->render($invoice);

        $filename = ($invoice->invoice_number ?: 'Invoice-' . $invoice->id) . '.pdf';

        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
