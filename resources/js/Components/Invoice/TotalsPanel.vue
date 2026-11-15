<template>
    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Summary & Total
            </h3>

            <!-- Expandable toggle for discounts and delivery -->
            <button
                type="button"
                @click="showAdjustments = !showAdjustments"
                class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors"
            >
                {{ showAdjustments ? 'Done adjusting' : '+ Discount or Delivery Fee' }}
            </button>
        </div>

        <!-- Optional Collapsible Adjustments Drawer -->
        <div v-if="showAdjustments" class="p-3 bg-gray-50/70 border border-gray-200 rounded-lg space-y-3 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Invoice Discount -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Invoice Discount</label>
                    <div class="flex space-x-1.5">
                        <select
                            v-model="discountType"
                            class="w-1/2 px-2 py-1.5 text-xs bg-white border border-gray-300 rounded focus:ring-primary focus:border-primary"
                        >
                            <option value="none">None</option>
                            <option value="percentage">% Percent</option>
                            <option value="fixed">₦ Fixed</option>
                        </select>
                        <input
                            v-if="discountType !== 'none'"
                            type="number"
                            step="any"
                            min="0"
                            v-model.number="discountValue"
                            @wheel="$event.target.blur()"
                            class="w-1/2 px-2 py-1.5 text-xs bg-white border border-gray-300 rounded focus:ring-primary focus:border-primary font-medium no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            :placeholder="discountType === 'percentage' ? '%' : '₦'"
                        />
                    </div>
                </div>

                <!-- Logistics / Shipping -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Logistics / Mobilization Fee (₦)</label>
                    <input
                        type="number"
                        step="any"
                        min="0"
                        v-model.number="shippingNaira"
                        @wheel="$event.target.blur()"
                        class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded focus:ring-primary focus:border-primary font-medium no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        placeholder="0.00"
                    />
                </div>
            </div>

            <!-- VAT Treatment (only visible in adjustments) -->
            <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between">
                <span class="font-medium text-gray-600">VAT Calculation Mode:</span>
                <div class="flex items-center space-x-1 bg-white border border-gray-200 rounded p-0.5">
                    <button
                        type="button"
                        @click="vatMode = 'exclusive'"
                        :class="[
                            'px-2 py-1 rounded text-[11px] font-semibold transition-colors',
                            vatMode === 'exclusive' ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900'
                        ]"
                    >
                        Exclusive (+7.5%)
                    </button>
                    <button
                        type="button"
                        @click="vatMode = 'inclusive'"
                        :class="[
                            'px-2 py-1 rounded text-[11px] font-semibold transition-colors',
                            vatMode === 'inclusive' ? 'bg-primary text-white' : 'text-gray-600 hover:text-gray-900'
                        ]"
                    >
                        Inclusive (7.5% incl)
                    </button>
                </div>
            </div>
        </div>

        <!-- Breakdown list -->
        <div class="space-y-2 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal:</span>
                <span class="font-semibold text-gray-900">₦{{ format(totals.subtotalNaira) }}</span>
            </div>

            <div v-if="totals.invoiceDiscountNaira > 0" class="flex justify-between text-rose-600">
                <span>Invoice Discount:</span>
                <span class="font-semibold">-₦{{ format(totals.invoiceDiscountNaira) }}</span>
            </div>

            <div v-if="totals.vatNaira > 0" class="flex justify-between text-gray-600">
                <span>
                    VAT ({{ (vatRateBps / 100).toFixed(1) }}%{{ vatMode === 'inclusive' ? ' incl.' : '' }}):
                </span>
                <span class="font-semibold text-gray-900">₦{{ format(totals.vatNaira) }}</span>
            </div>

            <div v-if="totals.shippingNaira > 0" class="flex justify-between text-gray-600">
                <span>Delivery / Logistics:</span>
                <span class="font-semibold text-gray-900">₦{{ format(totals.shippingNaira) }}</span>
            </div>

            <!-- Highlighted Total Payable -->
            <div class="pt-3 border-t-2 border-primary/20 flex justify-between items-baseline">
                <span class="text-sm font-bold text-gray-900">Total Payable:</span>
                <span class="text-xl font-black text-primary">₦{{ format(totals.totalNaira) }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ComputedInvoiceTotals } from '@/Composables/useInvoiceTotals';

const props = defineProps<{
    totals: ComputedInvoiceTotals;
    vatRateBps: number;
}>();

const vatMode = defineModel<'exclusive' | 'inclusive'>('vatMode', { default: 'exclusive' });
const discountType = defineModel<'none' | 'percentage' | 'fixed'>('discountType', { default: 'none' });
const discountValue = defineModel<number>('discountValue', { default: 0 });
const shippingNaira = defineModel<number>('shippingNaira', { default: 0 });

const showAdjustments = ref(Boolean(
    (props.totals.invoiceDiscountNaira > 0) ||
    (props.totals.shippingNaira > 0)
));

function format(val: number): string {
    return (val || 0).toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>
