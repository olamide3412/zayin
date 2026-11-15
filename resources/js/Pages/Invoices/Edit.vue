<template>
    <Head :title="`Edit ${invoice.invoice_number || 'Draft'} - Zayin Ltd`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('invoices.index')"
                        class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Back to Invoices"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">
                                Edit {{ invoice.invoice_number || 'Draft Invoice' }}
                            </h2>
                            <span v-if="invoice.revision_number > 1" class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                                Revision {{ invoice.revision_number }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ isIssued ? 'Editing an issued invoice will increment the revision number and create an audit snapshot.' : 'Update invoice details.' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        type="button"
                        @click="submitInvoice('draft')"
                        :disabled="isSubmitting"
                        class="flex-1 sm:flex-none px-3.5 sm:px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm disabled:opacity-50 text-center"
                    >
                        Save Changes
                    </button>
                    <button
                        v-if="!isIssued"
                        type="button"
                        @click="submitInvoice('issue')"
                        :disabled="isSubmitting"
                        class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center disabled:opacity-50"
                    >
                        Issue Invoice
                    </button>
                </div>
            </div>
        </template>

        <div class="py-2 sm:py-6 max-w-[1600px] mx-auto px-0 sm:px-6 lg:px-8 pb-28 lg:pb-8">
            <!-- Mobile Tab Segmented Switcher (Visible only below lg) -->
            <div class="lg:hidden mb-3 bg-gray-200/90 p-1 rounded-xl flex items-center shadow-inner">
                <button
                    type="button"
                    @click="mobileTab = 'form'"
                    class="flex-1 py-2 text-xs font-bold rounded-lg flex items-center justify-center space-x-1.5 transition-all"
                    :class="mobileTab === 'form' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                >
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Invoice Details</span>
                </button>
                <button
                    type="button"
                    @click="switchToPreview"
                    class="flex-1 py-2 text-xs font-bold rounded-lg flex items-center justify-center space-x-1.5 transition-all"
                    :class="mobileTab === 'preview' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                >
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live A5 Preview</span>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Form Section (7 cols) -->
                <div :class="['lg:col-span-7 space-y-4', mobileTab === 'form' ? 'block' : 'hidden lg:block']">
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

                    <!-- Revision Reason Alert if already issued -->
                    <div v-if="isIssued" class="p-3.5 bg-amber-50 border border-amber-300 rounded-lg space-y-2">
                        <div class="text-xs font-bold text-amber-900 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Revision Audit Reason Required
                        </div>
                        <input
                            type="text"
                            v-model="form.revision_reason"
                            class="w-full px-3 py-1.5 text-xs border border-amber-300 rounded-md focus:ring-primary focus:border-primary bg-white"
                            placeholder="Reason for revision (e.g. Added panel cables, Client requested discount)..."
                        />
                    </div>

                    <!-- 1. Customer Section -->
                    <CustomerPicker
                        :customers="customers"
                        :initialCustomer="invoice.customer"
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
                            <label class="block font-semibold text-gray-700 mb-1">Terms & Conditions</label>
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
                <div :class="['lg:col-span-5 lg:sticky lg:top-6', mobileTab === 'preview' ? 'block' : 'hidden lg:block']">
                    <!-- Mobile helper banner to jump back to form -->
                    <div class="lg:hidden mb-2.5 flex items-center justify-between bg-primary/10 border border-primary/20 px-3 py-2 rounded-lg text-xs">
                        <span class="font-semibold text-primary flex items-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                            Live A5 Preview Mode
                        </span>
                        <button
                            type="button"
                            @click="mobileTab = 'form'"
                            class="font-bold text-primary hover:underline flex items-center"
                        >
                            ← Back to Form
                        </button>
                    </div>

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

        <!-- Sticky Mobile Bottom Bar -->
        <div class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-gray-200 px-3.5 py-2.5 z-30 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] flex items-center justify-between gap-2">
            <div class="min-w-0 pr-1">
                <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total Payable</div>
                <div class="text-sm font-black text-primary truncate tabular-nums">
                    ₦{{ totals.totalNaira.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                </div>
            </div>
            <div class="flex items-center space-x-1.5 shrink-0">
                <button
                    type="button"
                    @click="toggleMobileTab"
                    class="px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center"
                >
                    <span v-if="mobileTab === 'form'" class="flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview
                    </span>
                    <span v-else class="flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </span>
                </button>
                <button
                    type="button"
                    @click="submitInvoice('draft')"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50"
                >
                    Save
                </button>
                <button
                    v-if="!isIssued"
                    type="button"
                    @click="submitInvoice('issue')"
                    :disabled="isSubmitting"
                    class="px-3.5 py-1.5 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center disabled:opacity-50"
                >
                    Issue
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, reactive, toRefs, watch, onMounted, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CustomerPicker from '@/Components/Invoice/CustomerPicker.vue';
import ItemRow from '@/Components/Invoice/ItemRow.vue';
import TotalsPanel from '@/Components/Invoice/TotalsPanel.vue';
import BankAccountsSelector from '@/Components/Invoice/BankAccountsSelector.vue';
import InvoicePreviewFrame from '@/Components/Invoice/InvoicePreviewFrame.vue';
import { useInvoiceTotals } from '@/Composables/useInvoiceTotals';

const props = defineProps<{
    invoice: any;
    business: any;
    customers: any[];
    templates: any[];
    bankAccounts: any[];
}>();

const mobileTab = ref<'form' | 'preview'>('form');

function switchToPreview() {
    mobileTab.value = 'preview';
    refreshLivePreview();
}

function toggleMobileTab() {
    if (mobileTab.value === 'form') {
        switchToPreview();
    } else {
        mobileTab.value = 'form';
    }
}

const localBankAccounts = ref<any[]>([...(props.bankAccounts || [])]);

const isIssued = computed(() => ['issued', 'partially_paid', 'paid'].includes(props.invoice.status));
const previewFrameRef = ref<any>(null);
const isSubmitting = ref(false);
const showTermsAndBanks = ref(false);
const formErrors = ref<Record<string, string>>({});

const initialItems = (props.invoice.items || []).map((it: any) => {
    const itemTitle = it.item_name || it.name || it.description || '';
    const itemNotes = (it.description && it.description !== itemTitle) ? it.description : '';
    return {
        id: it.id,
        catalog_id: it.catalog_id,
        name: itemTitle,
        description: itemNotes,
        quantity: Number(it.quantity) || 1,
        unit: it.unit || 'pcs',
        unit_price: (it.unit_price_kobo || 0) / 100,
        discount_type: it.discount_type || 'none',
        discount_value: it.discount_value || 0,
        taxable: Boolean(it.taxable || it.is_vatable),
    };
});

const formatDateInput = (val: any) => {
    if (!val) return '';
    return String(val).slice(0, 10);
};

const form = reactive({
    customer_id: props.invoice.customer_id,
    new_customer: { name: '', email: '', phone: '', address: '', tin: '' },
    template_id: props.invoice.template_id || props.templates[0]?.id,
    issue_date: formatDateInput(props.invoice.issue_date),
    due_date: formatDateInput(props.invoice.due_date),
    currency: props.invoice.currency || 'NGN',
    vat_mode: props.invoice.vat_mode || 'exclusive',
    vat_rate_bps: props.invoice.vat_rate_bps || 750,
    discount_type: props.invoice.discount_type || 'none',
    discount_value: Number(props.invoice.discount_value) || 0,
    shipping_naira: (props.invoice.shipping_kobo || 0) / 100,
    notes: props.invoice.notes || '',
    terms: props.invoice.terms || '',
    footer_notes: props.invoice.footer_notes || '',
    bank_account_ids: Array.isArray(props.invoice.bank_account_ids) ? props.invoice.bank_account_ids : (props.bankAccounts.filter(b => b.is_default).map(b => b.id)),
    revision_reason: '',
    items: initialItems.length > 0 ? initialItems : [
        {
            catalog_id: null,
            name: '',
            description: '',
            quantity: 1,
            unit: 'pcs',
            unit_price: 0,
            discount_type: 'none',
            discount_value: 0,
            taxable: false,
        },
    ],
});

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
    router.put(
        route('invoices.update', props.invoice.id),
        {
            ...form,
            action,
        },
        {
            onError: (errs) => {
                formErrors.value = errs;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        }
    );
}
</script>
