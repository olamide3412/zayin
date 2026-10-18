<table class="w-full totals-container mb-3">
    <tr>
        <td class="align-top" style="width: 52%; padding-right: 10px;">
            @if($template->showField('amount_in_words', true))
                <div class="words-card" style="background-color: #f8fafc; border-left: 3px solid {{ $accentColor }}; padding: 5px 8px; border-radius: 2px;">
                    <div style="font-size: 7px; font-weight: bold; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">
                        Amount in Words:
                    </div>
                    <div style="font-size: 8px; font-weight: bold; color: #1e293b; margin-top: 2px; line-height: 1.35; font-style: italic;">
                        {{ \App\Services\Invoice\NairaInWords::convert($invoice->total_kobo) }}
                    </div>
                </div>
            @endif

            @if($template->showField('bank_accounts', true) && $bankAccounts->isNotEmpty())
                <div class="bank-card" style="margin-top: 6px; background-color: #f8fafc; border: 1px dashed #cbd5e1; padding: 5px 8px; border-radius: 4px;">
                    <div style="font-size: 7px; font-weight: bold; text-transform: uppercase; color: {{ $accentColor }}; letter-spacing: 0.5px; margin-bottom: 3px;">
                        Payment Bank Accounts:
                    </div>
                    @foreach($bankAccounts as $bank)
                        <div style="font-size: 7.5px; line-height: 1.35; color: #1e293b; margin-bottom: 2px;">
                            <strong>{{ $bank->bank_name }}:</strong>
                            <span style="font-family: monospace; font-weight: bold; color: #0f172a;">{{ $bank->account_number }}</span>
                            <span style="color: #475569;"> &bull; Acc Name: <strong style="color: #0f172a;">{{ $bank->account_name }}</strong></span>
                        </div>
                    @endforeach
                </div>
            @endif
        </td>

        <td class="align-top text-right" style="width: 48%;">
            <table class="w-full text-right" style="border-collapse: collapse; font-size: 8px;">
                <tr>
                    <td style="padding: 2px 4px; color: #6b7280;">Subtotal:</td>
                    <td style="padding: 2px 4px; font-weight: 500; color: #111827;">₦{{ number_format($invoice->subtotal_kobo / 100, 2) }}</td>
                </tr>

                @if(($invoice->discount_kobo ?? 0) > 0)
                    <tr>
                        <td style="padding: 2px 4px; color: #dc2626;">Discount:</td>
                        <td style="padding: 2px 4px; font-weight: 500; color: #dc2626;">-₦{{ number_format($invoice->discount_kobo / 100, 2) }}</td>
                    </tr>
                @endif

                @if(($invoice->vat_rate_bps ?? 0) > 0)
                    <tr>
                        <td style="padding: 2px 4px; color: #6b7280;">
                            VAT ({{ number_format($invoice->vat_rate_bps / 100, 1) }}%{{ $invoice->vat_mode === 'inclusive' ? ' incl.' : '' }}):
                        </td>
                        <td style="padding: 2px 4px; font-weight: 500; color: #111827;">₦{{ number_format($invoice->vat_kobo / 100, 2) }}</td>
                    </tr>
                @endif

                @if(($invoice->shipping_kobo ?? 0) > 0)
                    <tr>
                        <td style="padding: 2px 4px; color: #6b7280;">Delivery / Logistics:</td>
                        <td style="padding: 2px 4px; font-weight: 500; color: #111827;">₦{{ number_format($invoice->shipping_kobo / 100, 2) }}</td>
                    </tr>
                @endif

                <tr style="border-top: 1.5px solid {{ $accentColor }}; border-bottom: 1.5px solid {{ $accentColor }};">
                    <td style="padding: 4px 4px; font-size: 9.5px; font-weight: bold; color: {{ $accentColor }};">Total Payable:</td>
                    <td style="padding: 4px 4px; font-size: 10px; font-weight: bold; color: {{ $accentColor }};">₦{{ number_format($invoice->total_kobo / 100, 2) }}</td>
                </tr>

                @if(($invoice->amount_paid_kobo ?? 0) > 0)
                    <tr>
                        <td style="padding: 2px 4px; color: #15803d; font-weight: 500;">Amount Paid:</td>
                        <td style="padding: 2px 4px; font-weight: bold; color: #15803d;">₦{{ number_format($invoice->amount_paid_kobo / 100, 2) }}</td>
                    </tr>
                    @php
                        $balanceDue = max(0, $invoice->total_kobo - $invoice->amount_paid_kobo);
                    @endphp
                    <tr style="background-color: #fef2f2;">
                        <td style="padding: 3px 4px; font-weight: bold; color: #b91c1c;">Balance Due:</td>
                        <td style="padding: 3px 4px; font-weight: bold; color: #b91c1c;">₦{{ number_format($balanceDue / 100, 2) }}</td>
                    </tr>
                @endif
            </table>
        </td>
    </tr>
</table>
