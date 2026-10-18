<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use Symfony\Component\HttpFoundation\Response;

interface InvoicePdfRenderer
{
    /**
     * Render an invoice as a PDF binary string.
     */
    public function render(Invoice $invoice, array $options = []): string;

    /**
     * Return a downloadable HTTP PDF response.
     */
    public function downloadResponse(Invoice $invoice, ?string $filename = null): Response;

    /**
     * Return an inline HTTP PDF response (for browser viewer).
     */
    public function streamResponse(Invoice $invoice, ?string $filename = null): Response;
}
