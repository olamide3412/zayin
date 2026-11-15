<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $invoice->invoice_number ?? 'Invoice' }}</title>
    <style>
        /* Strict A5 Page definition for PDF and print */
        @page {
            size: a5 portrait;
            margin: 9mm 8mm 5mm 8mm;
        }

        * {
            box-sizing: border-box;
            font-family: 'DejaVu Sans', sans-serif;
        }

        body {
            margin: 0;
            padding: {{ !empty($isPdf) ? '0' : '9mm 8mm 5mm 8mm' }};
            background-color: #ffffff;
            font-size: 9px;
            color: #111827;
            line-height: 1.45;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @media print {
            @page {
                size: a5 portrait;
                margin: 9mm 8mm 5mm 8mm;
            }
            body {
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background-color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
        .w-full {
            width: 100%;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .align-top { vertical-align: top; }
        .align-bottom { vertical-align: bottom; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 18px; }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .items-table {
            border-collapse: collapse;
            width: 100%;
        }
        .items-table th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .items-table th, .items-table td {
            padding-top: 7px !important;
            padding-bottom: 7px !important;
        }
        .totals-container td {
            padding-top: 3.5px !important;
            padding-bottom: 3.5px !important;
        }
        .footer-container {
            margin-top: 20px !important;
        }
        .watermark {
            position: fixed;
            top: 35%;
            left: 10%;
            width: 80%;
            text-align: center;
            opacity: 0.08;
            font-size: 64px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000000;
            transform: rotate(-30deg);
            z-index: -1000;
        }
    </style>
</head>
<body>
    @if($invoice->status === 'void')
        <div class="watermark" style="color: #ef4444; opacity: 0.12;">VOID</div>
    @elseif($invoice->status === 'paid')
        <div class="watermark" style="color: #15803d; opacity: 0.09;">PAID</div>
    @endif

    @php
        $accentColor = $template->accent_color ?? '#111827';
    @endphp

    @include('invoices.partials.header', ['accentColor' => $accentColor])
    @include('invoices.partials.customer', ['accentColor' => $accentColor])
    @include('invoices.partials.items-table', ['accentColor' => $accentColor])
    @include('invoices.partials.totals', ['accentColor' => $accentColor])
    @include('invoices.partials.footer', ['accentColor' => $accentColor])

    @if(request()->has('print'))
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.focus();
                    window.print();
                }, 300);
            });
        </script>
    @endif
</body>
</html>
