<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $invoice->invoice_number ?? 'Invoice' }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 8mm 8mm 8mm 8mm;
        }
        * {
            box-sizing: border-box;
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            font-size: 8px;
            color: #1f2937;
            background-color: #ffffff;
            line-height: 1.35;
        }
        .w-full {
            width: 100%;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .align-top { vertical-align: top; }
        .align-bottom { vertical-align: bottom; }
        .mb-2 { margin-bottom: 6px; }
        .mb-3 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 12px; }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .items-table {
            border-collapse: collapse;
            width: 100%;
        }
        .items-table th {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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
        <div class="watermark" style="color: #22c55e; opacity: 0.09;">PAID</div>
    @endif

    @php
        $accentColor = $template->accent_color ?? '#1e3a8a';
    @endphp

    @include('invoices.partials.header', ['accentColor' => $accentColor])
    @include('invoices.partials.customer', ['accentColor' => $accentColor])
    @include('invoices.partials.items-table', ['accentColor' => $accentColor])
    @include('invoices.partials.totals', ['accentColor' => $accentColor])
    @include('invoices.partials.footer', ['accentColor' => $accentColor])
</body>
</html>
