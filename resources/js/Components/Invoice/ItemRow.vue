<template>
    <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-gray-300 transition-all space-y-2.5">
        <!-- Primary row -->
        <div class="grid grid-cols-12 gap-2.5 items-center">
            <!-- Item Name & Auto Suggest (4 cols on md) -->
            <div class="col-span-12 md:col-span-4">
                <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">
                    Item / Service <span class="text-rose-500">*</span>
                </label>
                <ItemSuggestInput
                    v-model="item.name"
                    placeholder="Search or enter item title..."
                    @select="onItemSelected"
                />
            </div>

            <!-- Quantity (reduced width, 1 col on md, no arrows) -->
            <div class="col-span-4 sm:col-span-2 md:col-span-1">
                <label class="block text-[11px] font-semibold text-gray-600 mb-0.5 text-center truncate" title="Quantity">
                    Qty
                </label>
                <input
                    type="number"
                    step="any"
                    min="0.01"
                    v-model.number="item.quantity"
                    @wheel="$event.target.blur()"
                    class="w-full px-1 py-2 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary text-center font-medium no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                    placeholder="1"
                />
            </div>

            <!-- Unit Price (3 cols on md, no arrows) -->
            <div class="col-span-8 sm:col-span-4 md:col-span-3">
                <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Unit Price (₦)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-500 text-xs font-semibold pointer-events-none">₦</span>
                    <input
                        type="number"
                        step="any"
                        min="0"
                        v-model.number="item.unit_price"
                        @wheel="$event.target.blur()"
                        class="w-full pl-6 pr-2.5 py-2 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-medium no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        placeholder="0.00"
                    />
                </div>
            </div>

            <!-- Line Total & Actions (expanded to 4 cols on md for large amounts) -->
            <div class="col-span-12 sm:col-span-6 md:col-span-4">
                <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Total</label>
                <div class="flex items-center space-x-1.5 w-full">
                    <div
                        class="flex-1 min-w-0 h-[38px] px-2.5 bg-gray-50 rounded-md border border-gray-200 flex items-center justify-end font-bold text-gray-900 text-xs tabular-nums whitespace-nowrap overflow-x-auto shadow-2xs"
                        :title="`₦${computedLineTotal}`"
                    >
                        ₦{{ computedLineTotal }}
                    </div>
                    <button
                        type="button"
                        @click="$emit('remove')"
                        class="h-[38px] w-9 flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors shrink-0 border border-transparent hover:border-rose-100"
                        title="Remove item"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Toggle for Optional Details (Notes, Unit, Discount, VAT) -->
        <div class="pt-1 flex items-center justify-between text-xs">
            <button
                type="button"
                @click="showDetails = !showDetails"
                class="inline-flex items-center text-[11px] font-medium text-gray-500 hover:text-primary transition-colors space-x-1"
            >
                <svg
                    class="w-3.5 h-3.5 transition-transform"
                    :class="showDetails ? 'rotate-90 text-primary' : ''"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>{{ showDetails ? 'Hide item options' : '+ Add notes, unit, discount or VAT' }}</span>
                <span
                    v-if="hasActiveOptions && !showDetails"
                    class="ml-1 px-1.5 py-0.2 bg-primary/10 text-primary rounded-full text-[10px] font-semibold"
                >
                    Customized
                </span>
            </button>

            <!-- Compact VAT quick-indicator -->
            <label class="flex items-center space-x-1.5 cursor-pointer text-gray-500 hover:text-gray-700 select-none text-[11px]">
                <input
                    type="checkbox"
                    v-model="item.taxable"
                    class="rounded border-gray-300 text-primary focus:ring-primary h-3 w-3"
                />
                <span>VAT (7.5%)</span>
            </label>
        </div>

        <!-- Collapsible Secondary Details Drawer -->
        <div
            v-if="showDetails"
            class="pt-2.5 pb-1 border-t border-gray-100 grid grid-cols-1 md:grid-cols-12 gap-2.5 text-xs bg-gray-50/70 p-2.5 rounded-md"
        >
            <!-- Notes / Specs (6 cols) -->
            <div class="md:col-span-6">
                <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Notes or Specifications (Optional)</label>
                <input
                    type="text"
                    v-model="item.description"
                    class="w-full px-2.5 py-1.5 text-xs text-gray-700 bg-white border border-gray-200 rounded focus:ring-primary focus:border-primary placeholder-gray-400"
                    placeholder="e.g. 2-year warranty, model no., accessories..."
                />
            </div>

            <!-- Unit (2 cols) -->
            <div class="md:col-span-2">
                <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Unit</label>
                <input
                    type="text"
                    v-model="item.unit"
                    class="w-full px-2 py-1.5 text-xs text-gray-700 bg-white border border-gray-200 rounded focus:ring-primary focus:border-primary text-center"
                    placeholder="pcs"
                />
            </div>

            <!-- Line Discount (4 cols) -->
            <div class="md:col-span-4">
                <label class="block text-[10px] font-semibold text-gray-600 mb-0.5">Line Discount</label>
                <div class="flex items-center space-x-1">
                    <select
                        v-model="item.discount_type"
                        class="text-xs bg-white border border-gray-200 rounded px-1.5 py-1.5 text-gray-600 focus:ring-primary focus:border-primary"
                    >
                        <option value="none">None</option>
                        <option value="percentage">% Disc.</option>
                        <option value="fixed">₦ Disc.</option>
                    </select>
                    <input
                        v-if="item.discount_type && item.discount_type !== 'none'"
                        type="number"
                        step="any"
                        min="0"
                        v-model.number="item.discount_value"
                        @wheel="$event.target.blur()"
                        class="flex-1 px-2 py-1.5 text-xs bg-white border border-gray-200 rounded focus:ring-primary focus:border-primary no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        :placeholder="item.discount_type === 'percentage' ? '%' : '₦'"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import ItemSuggestInput from './ItemSuggestInput.vue';

const props = defineProps<{
    item: {
        catalog_id?: number | null;
        name: string;
        description?: string | null;
        quantity: number;
        unit?: string;
        unit_price?: number;
        discount_type?: 'none' | 'percentage' | 'fixed';
        discount_value?: number;
        taxable?: boolean;
    };
}>();

defineEmits<{
    (e: 'remove'): void;
}>();

const showDetails = ref(Boolean(
    (props.item.description && props.item.description.trim().length > 0) ||
    (props.item.discount_type && props.item.discount_type !== 'none' && (props.item.discount_value || 0) > 0) ||
    (props.item.unit && props.item.unit !== 'pcs')
));

const hasActiveOptions = computed(() => {
    return Boolean(
        (props.item.description && props.item.description.trim().length > 0) ||
        (props.item.discount_type && props.item.discount_type !== 'none' && (props.item.discount_value || 0) > 0) ||
        (props.item.unit && props.item.unit !== 'pcs')
    );
});

function onItemSelected(catalogItem: any) {
    props.item.catalog_id = catalogItem.id;
    props.item.name = catalogItem.name;
    if (catalogItem.description) {
        props.item.description = catalogItem.description;
        showDetails.value = true;
    }
    if (catalogItem.unit) {
        props.item.unit = catalogItem.unit;
    }
    if (catalogItem.unit_price !== undefined) {
        props.item.unit_price = Number(catalogItem.unit_price);
    }
    if (catalogItem.is_vatable !== undefined) {
        props.item.taxable = Boolean(catalogItem.is_vatable);
    }
}

const computedLineTotal = computed(() => {
    const qty = Math.max(0, Number(props.item.quantity) || 0);
    const unitPrice = Math.max(0, Number(props.item.unit_price) || 0);
    const gross = qty * unitPrice;

    let disc = 0;
    if (props.item.discount_type === 'percentage' && (props.item.discount_value || 0) > 0) {
        disc = (gross * Math.min(100, Math.max(0, props.item.discount_value || 0))) / 100;
    } else if (props.item.discount_type === 'fixed' && (props.item.discount_value || 0) > 0) {
        disc = Math.min(gross, props.item.discount_value || 0);
    }

    const net = Math.max(0, gross - disc);
    return net.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});
</script>
