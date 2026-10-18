<template>
    <Head title="Create Invoice - Zayin Global Limited" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">Create Invoice</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Quickly generate and issue clean Nigerian invoices.</p>
                </div>
                <div class="flex items-center space-x-2.5">
                    <button
                        type="button"
                        @click="submitInvoice('draft')"
                        :disabled="isSubmitting"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm disabled:opacity-50"
                    >
                        Save Draft
                    </button>
                    <button
                        type="button"
                        @click="submitInvoice('issue')"
                        :disabled="isSubmitting"
                        class="px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center disabled:opacity-50"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Issue Invoice
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Form Section (7 cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Form Errors Banner -->
                    <div v-if="formErrors && Object.keys(formErrors).length > 0" class="p-3.5 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-800 space-y-1">
                        <div class="font-bold flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Please check the following before saving:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 pl-1 text-[11px]">
                            <li v-for="(msg, field) in formErrors" :key="field">{{ msg }}</li>
                        </ul>
                    </div>

                    <!-- 1. Customer Section -->
                    <CustomerPicker
                        :customers="customers"
                        v-model:customerId="form.customer_id"
                        :newCustomer="form.new_customer"
                    />

                    <!-- 2. Compact Invoice Dates & Template Bar -->
                    <div class="bg-white p-3 border border-gray-200 rounded-lg shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <label class="block font-semibold text-gray-600 mb-0.5">Issue Date</label>
                            <input
                                type="date"
                                v-model="form.issue_date"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-medium"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-600 mb-0.5">Due Date</label>
                            <input
                                type="date"
                                v-model="form.due_date"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-medium"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-600 mb-0.5">Invoice Layout</label>
                            <select
                                v-model="form.template_id"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            >
                                <option v-for="t in templates" :key="t.id" :value="t.id">
                                    {{ t.name }} ({{ t.layout }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. Line Items Section -->
                    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                Line Items ({{ form.items.length }})
                            </h3>
                            <button
                                type="button"
                                @click="addItem"
                                class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-md transition-colors"
                            >
                                + Add Line Item
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <ItemRow
                                v-for="(item, index) in form.items"
                                :key="index"
                                :item="item"
                                @remove="removeItem(index)"
                            />
                        </div>

                        <div class="pt-1">
                            <button
                                type="button"
                                @click="addItem"
                                class="w-full py-2 border border-dashed border-gray-300 hover:border-primary text-gray-600 hover:text-primary rounded-lg text-xs font-semibold flex items-center justify-center transition-colors bg-gray-50/50 hover:bg-primary/5"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Another Item
                            </button>
                        </div>
                    </div>

                    <!-- 4. Totals and Adjustments -->
                    <TotalsPanel
                        :totals="totals"
                        :vatRateBps="form.vat_rate_bps"
                        v-model:vatMode="form.vat_mode"
                        v-model:discountType="form.discount_type"
                        v-model:discountValue="form.discount_value"
                        v-model:shippingNaira="form.shipping_naira"
                    />

                    <!-- 5. Dedicated Bank Accounts Section -->
                    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h3 class="text-xs font-bold text-gray-900 flex items-center">
                                <span class="mr-1.5 text-base">💳</span>
                                Payment Bank Accounts <span class="ml-1 text-[11px] text-gray-400 font-normal">(Optional)</span>
                            </h3>
                            <span class="text-[11px] font-medium" :class="form.bank_account_ids.length > 0 ? 'text-primary' : 'text-gray-400'">
                                {{ form.bank_account_ids.length > 0 ? `${form.bank_account_ids.length} account(s) included` : 'None (Omitted from invoice)' }}
                            </span>
                        </div>

                        <BankAccountsSelector
                            v-model="form.bank_account_ids"
                            :bankAccounts="localBankAccounts"
                            :defaultAccountName="business?.name"
                            @update:bankAccounts="localBankAccounts = $event"
                            @change="refreshLivePreview"
                        />
                    </div>

                    <!-- 6. Customer Notes & Warranty Terms -->
                    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm space-y-3 text-xs">
                        <h3 class="text-xs font-bold text-gray-900 flex items-center pb-2 border-b border-gray-100">
                            <span class="mr-1.5 text-base">📝</span>
                            Customer Notes & Terms <span class="ml-1 text-[11px] text-gray-400 font-normal">(Optional)</span>
                        </h3>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Customer Notes</label>
                            <textarea
                                rows="2"
                                v-model="form.notes"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                placeholder="Thank you for your business..."
                            ></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Terms & Warranty Conditions</label>
                            <textarea
                                rows="3"
                                v-model="form.terms"
                                class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                placeholder="Payment terms and warranty conditions..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Right Live Preview Section (5 cols, sticky on lg) -->
                <div class="lg:col-span-5 lg:sticky lg:top-6">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3">
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100">
                            <span class="text-xs font-bold text-gray-800 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                Live A5 Document Preview
                            </span>
                            <button
                                type="button"
                                @click="refreshLivePreview"
                                class="text-xs text-primary hover:underline font-medium"
                            >
                                Force Refresh
                            </button>
                        </div>
                        <InvoicePreviewFrame ref="previewFrameRef" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, reactive, toRefs, watch, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerPicker from '@/Components/Invoice/CustomerPicker.vue';
import ItemRow from '@/Components/Invoice/ItemRow.vue';
import TotalsPanel from '@/Components/Invoice/TotalsPanel.vue';
import BankAccountsSelector from '@/Components/Invoice/BankAccountsSelector.vue';
import InvoicePreviewFrame from '@/Components/Invoice/InvoicePreviewFrame.vue';
import { useInvoiceTotals } from '@/Composables/useInvoiceTotals';

const props = defineProps<{
    business: any;
    customers: any[];
    templates: any[];
    bankAccounts: any[];
    defaultVatRateBps: number;
    defaultVatMode: 'exclusive' | 'inclusive';
    defaultNotes?: string;
    defaultTerms?: string;
}>();

const localBankAccounts = ref<any[]>([...(props.bankAccounts || [])]);

const previewFrameRef = ref<any>(null);
const isSubmitting = ref(false);
const showTermsAndBanks = ref(false);
const formErrors = ref<Record<string, string>>({});

const form = reactive({
    customer_id: null as number | null,
    new_customer: {
        name: '',
        email: '',
        phone: '',
        address: '',
        tin: '',
    },
    template_id: props.templates.find((t) => t.is_default)?.id || props.templates[0]?.id,
    issue_date: new Date().toISOString().slice(0, 10),
    due_date: new Date(Date.now() + 14 * 86400000).toISOString().slice(0, 10),
    currency: 'NGN',
    vat_mode: props.defaultVatMode || 'exclusive',
    vat_rate_bps: props.defaultVatRateBps || 750,
    discount_type: 'none' as 'none' | 'percentage' | 'fixed',
    discount_value: 0,
    shipping_naira: 0,
    notes: props.defaultNotes || '',
    terms: props.defaultTerms || '',
    footer_notes: '',
    bank_account_ids: props.bankAccounts.filter((b) => b.is_default).map((b) => b.id),
    items: [
        {
            catalog_id: null,
            name: '',
            description: '',
            quantity: 1,
            unit: 'pcs',
            unit_price: 0,
            discount_type: 'none' as 'none' | 'percentage' | 'fixed',
            discount_value: 0,
            taxable: false,
        },
    ],
});

// Reactivity for useInvoiceTotals
const {
    items: formItems,
    vat_mode: formVatMode,
    vat_rate_bps: formVatRateBps,
    discount_type: formDiscType,
    discount_value: formDiscVal,
    shipping_naira: formShipping,
} = toRefs(form);

const { totals } = useInvoiceTotals(
    formItems,
    formVatMode,
    formVatRateBps,
    formDiscType,
    formDiscVal,
    formShipping
);

function addItem() {
    form.items.push({
        catalog_id: null,
        name: '',
        description: '',
        quantity: 1,
        unit: 'pcs',
        unit_price: 0,
        discount_type: 'none',
        discount_value: 0,
        taxable: false,
    });
}

function removeItem(index: number) {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
}

let previewDebounceTimer: any = null;

async function refreshLivePreview() {
    try {
        const response = await fetch('/invoices/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as any)?.content || '',
            },
            body: JSON.stringify(form),
        });

        if (response.ok) {
            const html = await response.text();
            previewFrameRef.value?.updateContent(html);
        }
    } catch (err) {
        console.error('Failed to update live preview:', err);
    }
}

watch(
    () => form,
    () => {
        clearTimeout(previewDebounceTimer);
        previewDebounceTimer = setTimeout(() => {
            refreshLivePreview();
        }, 350);
    },
    { deep: true }
);

onMounted(() => {
    refreshLivePreview();
});

function submitInvoice(action: 'draft' | 'issue') {
    isSubmitting.value = true;
    formErrors.value = {};
    router.post(
        route('invoices.store'),
        {
            ...form,
            action,
        },
        {
            onError: (errs) => {
                formErrors.value = errs;
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        }
    );
}
</script>
