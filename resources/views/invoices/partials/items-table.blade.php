@php
    $hasLineDiscounts = $invoice->items->contains(fn($i) => ($i->discount_kobo ?? 0) > 0);
@endphp

<table class="w-full items-table mb-3">
    <thead>
        <tr style="background-color: {{ $accentColor }}; color: #ffffff;">
            <th style="width: 5%; text-align: center; padding: 4px 3px; font-size: 8px;">#</th>
            <th style="width: {{ $hasLineDiscounts ? '47%' : '57%' }}; text-align: left; padding: 4px 6px; font-size: 8px;">Item & Description</th>
            <th style="width: 10%; text-align: center; padding: 4px 4px; font-size: 8px;">Qty</th>
            <th style="width: 16%; text-align: right; padding: 4px 6px; font-size: 8px;">Rate (₦)</th>
            @if($hasLineDiscounts)
                <th style="width: 10%; text-align: right; padding: 4px 4px; font-size: 8px;">Disc.</th>
            @endif
            <th style="width: {{ $hasLineDiscounts ? '12%' : '14%' }}; text-align: right; padding: 4px 6px; font-size: 8px;">Amount (₦)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $index => $item)
            <tr style="border-bottom: 1px solid #e5e7eb; background-color: {{ $index % 2 === 0 ? '#ffffff' : '#f9fafb' }};">
                <td style="text-align: center; padding: 4px 3px; font-size: 8px; color: #6b7280; vertical-align: top;">
                    {{ $index + 1 }}
                </td>
                <td style="text-align: left; padding: 4px 6px; vertical-align: top;">
                    <div style="font-size: 8.5px; font-weight: bold; color: #111827;">
                        {{ $item->name }}
                        @if(!$item->taxable)
                            <span style="font-size: 7px; color: #6b7280; font-weight: normal;">(Zero-rated)</span>
                        @endif
                    </div>
                    @if(!empty($item->description) && $item->description !== $item->name)
                        <div style="font-size: 7.5px; color: #4b5563; margin-top: 1px; line-height: 1.25;">
                            {{ $item->description }}
                        </div>
                    @endif
                </td>
                <td style="text-align: center; padding: 4px 4px; font-size: 8px; color: #374151; vertical-align: top;">
                    {{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}
                    <span style="font-size: 7px; color: #6b7280;">{{ $item->unit }}</span>
                </td>
                <td style="text-align: right; padding: 4px 6px; font-size: 8px; color: #374151; vertical-align: top;">
                    {{ number_format($item->unit_price_kobo / 100, 2) }}
                </td>
                @if($hasLineDiscounts)
                    <td style="text-align: right; padding: 4px 4px; font-size: 8px; color: #dc2626; vertical-align: top;">
                        @if(($item->discount_kobo ?? 0) > 0)
                            -{{ number_format($item->discount_kobo / 100, 2) }}
                        @else
                            -
                        @endif
                    </td>
                @endif
                <td style="text-align: right; padding: 4px 6px; font-size: 8px; font-weight: bold; color: #111827; vertical-align: top;">
                    {{ number_format($item->total_kobo / 100, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
