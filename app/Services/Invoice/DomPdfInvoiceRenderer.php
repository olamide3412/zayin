<?php

namespace App\Services\Invoice;

use App\Models\BusinessBankAccount;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class DomPdfInvoiceRenderer implements InvoicePdfRenderer
{
    /**
     * Prepare data array for invoice Blade template.
     */
    public function prepareViewData(Invoice $invoice): array
    {
        $business = $invoice->business ?? \App\Models\Business::current();
        $template = $invoice->template ?? $business->defaultTemplate ?? \App\Models\InvoiceTemplate::first();
        $customer = $invoice->customer;
        $items = $invoice->items;

        $bankAccountIds = $invoice->bank_account_ids;
        $bankAccounts = !empty($bankAccountIds)
            ? BusinessBankAccount::whereIn('id', $bankAccountIds)->get()
            : collect([]);

        $layout = $template?->layout ?? 'classic';

        return [
            'invoice' => $invoice,
            'business' => $business,
            'template' => $template,
            'customer' => $customer,
            'items' => $items,
            'bankAccounts' => $bankAccounts,
            'layout' => $layout,
            'accentColor' => $template?->accent_color ?? '#0F5132',
            'showFields' => $template?->show_fields ?? \App\Models\InvoiceTemplate::defaultShowFields(),
            'isPdf' => true,
        ];
    }

    /**
     * Render the Blade HTML string for the invoice.
     */
    public function renderHtml(Invoice $invoice): string
    {
        $data = $this->prepareViewData($invoice);
        $layout = $data['layout'];
        $viewName = view()->exists("invoices.layouts.{$layout}")
            ? "invoices.layouts.{$layout}"
            : "invoices.layouts.classic";

        return view($viewName, $data)->render();
    }

    public function render(Invoice $invoice, array $options = []): string
    {
        $html = $this->renderHtml($invoice);

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a5', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
        ]);

        return $pdf->output();
    }

    public function downloadResponse(Invoice $invoice, ?string $filename = null): Response
    {
        $filename = $filename ?? $this->makeFilename($invoice);
        $pdfContent = $this->render($invoice);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function streamResponse(Invoice $invoice, ?string $filename = null): Response
    {
        $filename = $filename ?? $this->makeFilename($invoice);
        $pdfContent = $this->render($invoice);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    protected function makeFilename(Invoice $invoice): string
    {
        $num = $invoice->invoice_number ?: ('DRAFT-' . $invoice->id);
        return 'invoice-' . strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '-', $num)) . '.pdf';
    }
}
