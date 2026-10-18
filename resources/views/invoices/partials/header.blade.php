<table class="w-full header-table mb-4">
    <tr>
        <td class="align-top company-cell" style="width: 58%;">
            @if(!empty($business->logo_path) && file_exists(public_path($business->logo_path)))
                <img src="{{ public_path($business->logo_path) }}" alt="{{ $business->name }}" class="company-logo mb-2" style="max-height: 48px; max-width: 180px;">
            @elseif(!empty($business->logo_path) && file_exists(storage_path('app/public/' . $business->logo_path)))
                <img src="{{ storage_path('app/public/' . $business->logo_path) }}" alt="{{ $business->name }}" class="company-logo mb-2" style="max-height: 48px; max-width: 180px;">
            @else
                <div class="company-name-fallback" style="font-size: 16px; font-weight: bold; color: {{ $accentColor }}; margin-bottom: 2px;">
                    {{ $business->name }}
                </div>
            @endif

            @if($business->tagline)
                <div class="company-tagline" style="font-size: 8.5px; color: #4b5563; margin-bottom: 4px; font-style: italic;">
                    {{ $business->tagline }}
                </div>
            @endif

            <div class="company-address" style="font-size: 8px; line-height: 1.35; color: #374151;">
                @if($business->address_line1) <div>{{ $business->address_line1 }}</div> @endif
                @if($business->address_line2) <div>{{ $business->address_line2 }}</div> @endif
                <div>{{ collect([$business->city, $business->state])->filter()->join(', ') }}</div>
                @if($business->phone) <div><strong>Tel:</strong> {{ $business->phone }}{{ $business->phone_alt ? ' / ' . $business->phone_alt : '' }}</div> @endif
                @if($business->email) <div><strong>Email:</strong> {{ $business->email }}</div> @endif
                @if($business->website) <div><strong>Web:</strong> {{ $business->website }}</div> @endif
                
                @if($template->showField('rc_number', true) || $template->showField('tin', true))
                    <div style="margin-top: 3px; font-weight: bold; color: #1f2937;">
                        @if($template->showField('rc_number', true) && $business->rc_number)
                            <span>{{ $business->rc_number }}</span>
                        @endif
                        @if($template->showField('rc_number', true) && $business->rc_number && $template->showField('tin', true) && $business->tin)
                            <span> | </span>
                        @endif
                        @if($template->showField('tin', true) && $business->tin)
                            <span>{{ $business->tin }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </td>

        <td class="align-top invoice-meta-cell text-right" style="width: 42%;">
            <div class="invoice-title" style="font-size: 20px; font-weight: bold; color: {{ $accentColor }}; letter-spacing: 0.5px; text-transform: uppercase;">
                {{ $template->document_title ?? 'INVOICE' }}
            </div>

            <div class="meta-box" style="margin-top: 6px; font-size: 8.5px; line-height: 1.45;">
                <table class="w-full text-right">
                    <tr>
                        <td style="color: #6b7280; font-weight: 500; padding-right: 6px;">Invoice No:</td>
                        <td style="font-weight: bold; color: #111827;">{{ $invoice->invoice_number ?? 'DRAFT' }}</td>
                    </tr>
                    @if($invoice->revision_number > 1)
                        <tr>
                            <td style="color: #6b7280; font-weight: 500; padding-right: 6px;">Revision:</td>
                            <td style="font-weight: bold; color: #d97706;">Rev {{ $invoice->revision_number }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="color: #6b7280; font-weight: 500; padding-right: 6px;">Date:</td>
                        <td style="color: #111827;">{{ optional($invoice->issue_date)->format('d M, Y') ?? date('d M, Y') }}</td>
                    </tr>
                    @if($invoice->due_date)
                        <tr>
                            <td style="color: #6b7280; font-weight: 500; padding-right: 6px;">Due Date:</td>
                            <td style="color: #111827;">{{ $invoice->due_date->format('d M, Y') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="color: #6b7280; font-weight: 500; padding-right: 6px;">Status:</td>
                        <td>
                            @php
                                $status = strtoupper($invoice->status);
                                $badgeBg = match($invoice->status) {
                                    'paid' => '#dcfce7',
                                    'partially_paid' => '#fef3c7',
                                    'issued' => '#e0e7ff',
                                    'void' => '#fee2e2',
                                    default => '#f3f4f6'
                                };
                                $badgeColor = match($invoice->status) {
                                    'paid' => '#15803d',
                                    'partially_paid' => '#b45309',
                                    'issued' => '#4338ca',
                                    'void' => '#b91c1c',
                                    default => '#4b5563'
                                };
                            @endphp
                            <span style="display: inline-block; background-color: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 1px 6px; border-radius: 3px; font-weight: bold; font-size: 7.5px;">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
