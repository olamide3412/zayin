<div class="footer-container" style="margin-top: 8px;">
    <table class="w-full mb-3" style="font-size: 7.5px;">
        <tr>
            <td class="align-top" style="width: 65%; padding-right: 15px;">
                @if($invoice->notes)
                    <div style="margin-bottom: 4px;">
                        <span style="font-weight: bold; color: #374151;">Note:</span>
                        <span style="color: #4b5563;">{{ $invoice->notes }}</span>
                    </div>
                @endif

                @if($invoice->terms)
                    <div style="margin-top: 3px;">
                        <span style="font-weight: bold; color: #374151; display: block; margin-bottom: 1px;">Terms & Conditions:</span>
                        <div style="color: #6b7280; font-size: 7px; line-height: 1.3; white-space: pre-line;">{{ $invoice->terms }}</div>
                    </div>
                @endif
            </td>

            @if($template->showField('signature_line', true))
                <td class="align-bottom text-center" style="width: 35%; padding-left: 10px;">
                    @if(!empty($business->signature_path) && file_exists(public_path($business->signature_path)))
                        <img src="{{ public_path($business->signature_path) }}" alt="Signature" style="max-height: 35px; margin-bottom: 2px;">
                    @elseif(!empty($business->signature_path) && file_exists(storage_path('app/public/' . $business->signature_path)))
                        <img src="{{ storage_path('app/public/' . $business->signature_path) }}" alt="Signature" style="max-height: 35px; margin-bottom: 2px;">
                    @else
                        <div style="height: 30px;"></div>
                    @endif
                    <div style="border-top: 1px solid #9ca3af; padding-top: 3px; font-size: 7.5px; font-weight: bold; color: #374151;">
                        Authorized Signature
                    </div>
                </td>
            @endif
        </tr>
    </table>

    @if($business->footer_text)
        <div class="footer-notice text-center" style="border-top: 1px solid #e5e7eb; padding-top: 4px; font-size: 6.5px; color: #9ca3af;">
            {{ $business->footer_text }}
        </div>
    @endif
</div>
