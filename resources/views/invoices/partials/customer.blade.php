<table class="w-full bill-to-table mb-3" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 6px 8px;">
    <tr>
        <td class="align-top" style="width: 100%;">
            <div style="font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: {{ $accentColor }}; margin-bottom: 2px;">
                BILL TO:
            </div>
            <div style="font-size: 10px; font-weight: bold; color: #111827;">
                {{ $customerSnapshot['name'] ?? $invoice->customer->name ?? 'Valued Customer' }}
            </div>
            @if(!empty($customerSnapshot['address']) || !empty($invoice->customer?->address))
                <div style="font-size: 8px; color: #374151; margin-top: 1px; white-space: pre-line;">
                    {{ $customerSnapshot['address'] ?? $invoice->customer?->address }}
                </div>
            @endif
            <div style="font-size: 8px; color: #4b5563; margin-top: 2px;">
                @if(!empty($customerSnapshot['phone']) || !empty($invoice->customer?->phone))
                    <span><strong>Phone:</strong> {{ $customerSnapshot['phone'] ?? $invoice->customer?->phone }}</span>
                @endif
                @if((!empty($customerSnapshot['phone']) || !empty($invoice->customer?->phone)) && (!empty($customerSnapshot['email']) || !empty($invoice->customer?->email)))
                    <span> &bull; </span>
                @endif
                @if(!empty($customerSnapshot['email']) || !empty($invoice->customer?->email))
                    <span><strong>Email:</strong> {{ $customerSnapshot['email'] ?? $invoice->customer?->email }}</span>
                @endif
                @if(!empty($customerSnapshot['tin']) || !empty($invoice->customer?->tin))
                    <div><strong>TIN:</strong> {{ $customerSnapshot['tin'] ?? $invoice->customer?->tin }}</div>
                @endif
            </div>
        </td>
    </tr>
</table>
