@php
    $logoFile = null;
    $candidates = array_filter([
        $business->logo_path ?? null,
        \App\Models\Setting::get('site_logo_url'),
        'images/logo.png',
    ]);

    foreach ($candidates as $candidate) {
        $cleanPath = ltrim(parse_url($candidate, PHP_URL_PATH) ?? $candidate, '/');
        if ($cleanPath && file_exists(public_path($cleanPath)) && is_file(public_path($cleanPath))) {
            $logoFile = public_path($cleanPath);
            break;
        }
        if ($cleanPath && file_exists(storage_path('app/public/' . $cleanPath)) && is_file(storage_path('app/public/' . $cleanPath))) {
            $logoFile = storage_path('app/public/' . $cleanPath);
            break;
        }
        if (str_starts_with($cleanPath, 'storage/')) {
            $storageRelative = substr($cleanPath, strlen('storage/'));
            if (file_exists(storage_path('app/public/' . $storageRelative)) && is_file(storage_path('app/public/' . $storageRelative))) {
                $logoFile = storage_path('app/public/' . $storageRelative);
                break;
            }
        }
    }

    $logoDataUri = null;
    if ($logoFile && is_readable($logoFile)) {
        $mime = @mime_content_type($logoFile) ?: 'image/png';
        $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
    }
@endphp
<table class="w-full header-table mb-4">
    <tr>
        <td class="align-top company-cell" style="width: 58%;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                <tr>
                    @if($logoDataUri)
                        <td style="width: 36px; padding: 0 8px 0 0; vertical-align: middle;">
                            <img src="{{ $logoDataUri }}" alt="{{ $business->name }}" class="company-logo" style="width: 34px; height: 34px; max-width: 34px; max-height: 34px; object-fit: contain; border-radius: 6px; display: block;">
                        </td>
                    @endif
                    <td style="padding: 0; vertical-align: middle;">
                        <div class="company-name" style="font-size: 16px; font-weight: bold; color: {{ $accentColor }}; line-height: 1.15; margin-bottom: 1px;">
                            {{ $business->name }}
                        </div>
                        @if($business->tagline)
                            <div class="company-tagline" style="font-size: 8.5px; color: #4b5563; font-style: italic; line-height: 1.2;">
                                {{ $business->tagline }}
                            </div>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="company-address" style="font-size: 8px; line-height: 1.35; color: #374151;">
                @if($business->address_line1) <div>{{ $business->address_line1 }}</div> @endif
                @if($business->address_line2) <div>{{ $business->address_line2 }}</div> @endif
                <div>{{ collect([$business->city, $business->state])->filter()->join(', ') }}</div>
                @if($business->phone) <div><strong>Tel:</strong> {{ $business->phone }}{{ $business->phone_alt ? ' / ' . $business->phone_alt : '' }}</div> @endif
                @if($business->email) <div><strong>Email:</strong> {{ $business->email }}</div> @endif
                @if($business->website) <div><strong>Web:</strong> {{ $business->website }}</div> @endif
                
                @php
                    $showTin = ($business->show_tin ?? true) && $template->showField('tin', true);
                    $showRc = $template->showField('rc_number', true);
                @endphp
                @if(($showRc && $business->rc_number) || ($showTin && $business->tin))
                    <div style="margin-top: 3px; font-weight: bold; color: #1f2937;">
                        @if($showRc && $business->rc_number)
                            <span>{{ $business->rc_number }}</span>
                        @endif
                        @if($showRc && $business->rc_number && $showTin && $business->tin)
                            <span> | </span>
                        @endif
                        @if($showTin && $business->tin)
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
