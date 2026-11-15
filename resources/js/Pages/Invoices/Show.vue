<template>
    <Head :title="`${invoice.invoice_number || 'Draft'} - Zayin Ltd`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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
                            <h2 class="text-2xl font-black text-gray-900 leading-tight">
                                {{ invoice.invoice_number || 'Draft Invoice' }}
                            </h2>
                            <StatusBadge :status="invoice.status" />
                            <span v-if="invoice.revision_number > 1" class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                                Rev {{ invoice.revision_number }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Customer: <strong class="text-gray-700">{{ invoice.customer?.name }}</strong> &bull; Issued: {{ invoice.issue_date }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons Header -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Issue if Draft -->
                    <button
                        v-if="invoice.status === 'draft'"
                        type="button"
                        @click="issueInvoice"
                        class="px-3.5 py-2 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg shadow-sm flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Issue Invoice
                    </button>

                    <!-- Record Payment (if issued & not fully paid) -->
                    <button
                        v-if="['issued', 'partially_paid'].includes(invoice.status)"
                        type="button"
                        @click="showPaymentModal = true"
                        class="px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Record Payment
                    </button>

                    <!-- Print A5 -->
                    <button
                        type="button"
                        @click="previewFrameRef?.printIframe()"
                        class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg shadow-sm flex items-center"
                        title="Print A5 Invoice"
                    >
                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print (A5)
                    </button>

                    <!-- PDF Download -->
                    <a
                        :href="route('invoices.pdf.download', invoice.id)"
                        class="px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg shadow-sm flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download PDF
                    </a>

                    <!-- Copy Share Link -->
                    <button
                        type="button"
                        @click="copyShareLink"
                        class="px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg shadow-sm flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        {{ copied ? 'Copied!' : 'Share Link' }}
                    </button>

                    <!-- Edit (if not void) -->
                    <Link
                        v-if="invoice.status !== 'void'"
                        :href="route('invoices.edit', invoice.id)"
                        class="px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg shadow-sm"
                    >
                        Edit
                    </Link>

                    <!-- Duplicate -->
                    <button
                        type="button"
                        @click="duplicateInvoice"
                        class="px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg shadow-sm"
                    >
                        Duplicate
                    </button>

                    <!-- Void -->
                    <button
                        v-if="!['draft', 'void'].includes(invoice.status)"
                        type="button"
                        @click="showVoidModal = true"
                        class="px-3.5 py-2 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg"
                    >
                        Void
                    </button>
                </div>
            </div>
        </template>

        <div class="py-3 sm:py-6 max-w-7xl mx-auto px-0 sm:px-6 lg:px-8 space-y-4 sm:space-y-6 pb-28 lg:pb-8">
            <!-- Mobile Tab Segmented Switcher (Visible only below lg) -->
            <div class="lg:hidden mb-3 bg-gray-200/90 p-1 rounded-xl flex items-center shadow-inner">
                <button
                    type="button"
                    @click="mobileTab = 'preview'"
                    class="flex-1 py-2 text-xs font-bold rounded-lg flex items-center justify-center space-x-1.5 transition-all"
                    :class="mobileTab === 'preview' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                >
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>A5 Document</span>
                </button>
                <button
                    type="button"
                    @click="mobileTab = 'summary'"
                    class="flex-1 py-2 text-xs font-bold rounded-lg flex items-center justify-center space-x-1.5 transition-all"
                    :class="mobileTab === 'summary' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Financials & Payments</span>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Column: Details & Audit (5 cols) -->
                <div :class="['lg:col-span-5 space-y-6', mobileTab === 'summary' ? 'block' : 'hidden lg:block']">
                    <!-- Key Financials Card -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Financial Summary</div>
                        <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                            <span class="text-sm font-semibold text-gray-600">Total Payable:</span>
                            <span class="text-xl font-black text-gray-900">₦{{ formatKobo(invoice.total_kobo, false) }}</span>
                        </div>
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm font-semibold text-emerald-600">Amount Paid:</span>
                            <span class="text-lg font-bold text-emerald-600">₦{{ formatKobo(invoice.amount_paid_kobo, false) }}</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                            <span class="text-sm font-bold text-gray-800">Balance Due:</span>
                            <span class="text-xl font-black text-amber-600">
                                ₦{{ formatKobo(Math.max(0, invoice.total_kobo - invoice.amount_paid_kobo), false) }}
                            </span>
                        </div>
                    </div>

                    <!-- Payment Records Card -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Payments Received ({{ (invoice.payments || []).length }})
                            </div>
                            <button
                                v-if="['issued', 'partially_paid'].includes(invoice.status)"
                                type="button"
                                @click="showPaymentModal = true"
                                class="text-xs font-bold text-primary hover:underline"
                            >
                                + Record
                            </button>
                        </div>

                        <div v-if="(invoice.payments || []).length === 0" class="text-xs text-gray-400 py-3 text-center">
                            No payment transactions recorded yet.
                        </div>

                        <div v-else class="divide-y divide-gray-100">
                            <div
                                v-for="pay in invoice.payments"
                                :key="pay.id"
                                class="py-2.5 flex items-center justify-between text-xs"
                            >
                                <div>
                                    <div class="font-bold text-emerald-700">₦{{ formatKobo(pay.amount_kobo, false) }}</div>
                                    <div class="text-gray-500 text-[11px]">
                                        {{ pay.paid_at ? new Date(pay.paid_at).toLocaleDateString() : '' }} &bull; {{ pay.method }}
                                        <span v-if="pay.reference">({{ pay.reference }})</span>
                                    </div>
                                    <div v-if="pay.notes" class="text-gray-400 text-[10px]">{{ pay.notes }}</div>
                                </div>
                                <span class="text-[10px] text-gray-400">By {{ pay.recorded_by?.name || 'Admin' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revision History Audit Trail -->
                    <div v-if="(invoice.revisions || []).length > 0" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                        <div class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Revision History ({{ invoice.revisions.length }})
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="rev in invoice.revisions"
                                :key="rev.id"
                                class="p-3 bg-gray-50 rounded-lg text-xs space-y-1 border border-gray-200"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-800">Revision {{ rev.revision_number }}</span>
                                    <span class="text-[10px] text-gray-400">{{ new Date(rev.created_at).toLocaleString() }}</span>
                                </div>
                                <div class="text-gray-700 font-medium">"{{ rev.reason }}"</div>
                                <div class="text-[11px] text-gray-500">
                                    Author: {{ rev.author?.name || 'Staff' }} &bull; Total at rev: ₦{{ formatKobo(rev.after?.total_kobo || rev.snapshot_data?.total_kobo, false) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Designated Bank Accounts -->
                    <div v-if="designatedBanks && designatedBanks.length > 0" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                        <div class="text-xs font-bold text-gray-700 uppercase tracking-wider">Payment Instructions</div>
                        <div class="space-y-2 text-xs">
                            <div
                                v-for="bank in designatedBanks"
                                :key="bank.id"
                                class="p-2.5 bg-gray-50 rounded border border-gray-200"
                            >
                                <div class="font-bold text-gray-800">{{ bank.bank_name }}</div>
                                <div class="font-mono text-gray-900 font-semibold">{{ bank.account_number }}</div>
                                <div class="text-[11px] text-gray-600 mt-0.5">
                                    <span class="text-gray-400">Account Name:</span> <strong class="text-gray-800">{{ bank.account_name }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: A5 Live Document View (7 cols) -->
                <div :class="['lg:col-span-7', mobileTab === 'preview' ? 'block' : 'hidden lg:block']">
                    <InvoicePreviewFrame ref="previewFrameRef" :src="route('invoices.preview.show', invoice.id)" />
                </div>
            </div>
        </div>

        <!-- Sticky Mobile Bottom Bar -->
        <div class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-gray-200 px-3.5 py-2.5 z-30 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] flex items-center justify-between gap-2">
            <div class="min-w-0 pr-1">
                <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">
                    {{ invoice.status === 'paid' ? 'Paid In Full' : 'Balance Due' }}
                </div>
                <div class="text-sm font-black text-amber-600 truncate tabular-nums">
                    ₦{{ formatKobo(Math.max(0, invoice.total_kobo - invoice.amount_paid_kobo), false) }}
                </div>
            </div>
            <div class="flex items-center space-x-1.5 shrink-0">
                <button
                    type="button"
                    @click="previewFrameRef?.printIframe()"
                    class="px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center"
                    title="Print A5"
                >
                    <svg class="w-3.5 h-3.5 mr-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </button>
                <a
                    :href="route('invoices.pdf.download', invoice.id)"
                    class="px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center"
                >
                    <svg class="w-3.5 h-3.5 mr-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    PDF
                </a>
                <button
                    type="button"
                    @click="copyShareLink"
                    class="px-2.5 py-1.5 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg"
                >
                    {{ copied ? 'Copied!' : 'Share' }}
                </button>
                <button
                    v-if="['issued', 'partially_paid'].includes(invoice.status)"
                    type="button"
                    @click="showPaymentModal = true"
                    class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm"
                >
                    + Pay
                </button>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Record Payment</h3>
                    <button type="button" @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="text-xs text-gray-600">
                    Outstanding balance: <strong class="text-amber-600">₦{{ formatKobo(Math.max(0, invoice.total_kobo - invoice.amount_paid_kobo), false) }}</strong>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Payment Amount (₦) *</label>
                        <input
                            type="number"
                            step="any"
                            min="1"
                            v-model.number="paymentForm.amount_naira"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-bold"
                            placeholder="0.00"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Date Paid *</label>
                            <input
                                type="date"
                                v-model="paymentForm.paid_at"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Payment Method *</label>
                            <select
                                v-model="paymentForm.method"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            >
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="pos">POS</option>
                                <option value="cheque">Cheque</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Transaction Reference / Teller #</label>
                        <input
                            type="text"
                            v-model="paymentForm.reference"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="e.g. ZTH-192849102"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea
                            rows="2"
                            v-model="paymentForm.notes"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="Any payment notes..."
                        ></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showPaymentModal = false"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="submitPayment"
                        class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm"
                    >
                        Save Payment
                    </button>
                </div>
            </div>
        </div>

        <!-- Void Modal -->
        <div v-if="showVoidModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-base font-bold text-rose-600">Void Invoice</h3>
                    <button type="button" @click="showVoidModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <p class="text-xs text-gray-600">
                    Voiding an invoice marks it permanently invalid with an immutable VOID watermark. A required reason must be entered.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Void Reason *</label>
                    <textarea
                        rows="3"
                        v-model="voidReason"
                        class="w-full px-3 py-2 text-xs border border-gray-300 rounded-md focus:ring-rose-500 focus:border-rose-500"
                        placeholder="State why this invoice is being voided..."
                    ></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showVoidModal = false"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="submitVoid"
                        class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm"
                    >
                        Confirm Void
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/Invoice/StatusBadge.vue';
import InvoicePreviewFrame from '@/Components/Invoice/InvoicePreviewFrame.vue';
import { useNaira } from '@/Composables/useNaira';

const props = defineProps<{
    invoice: any;
    designatedBanks: any[];
    publicUrl: string;
}>();

const { formatKobo } = useNaira();

const previewFrameRef = ref<any>(null);
const mobileTab = ref<'preview' | 'summary'>('preview');
const copied = ref(false);
const showPaymentModal = ref(false);
const showVoidModal = ref(false);
const voidReason = ref('');

const paymentForm = ref({
    amount_naira: Math.max(0, (props.invoice.total_kobo - props.invoice.amount_paid_kobo) / 100),
    paid_at: new Date().toISOString().slice(0, 10),
    method: 'bank_transfer',
    reference: '',
    notes: '',
});

function copyShareLink() {
    navigator.clipboard.writeText(props.publicUrl);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2500);
}

function issueInvoice() {
    if (confirm('Are you sure you want to officially issue this invoice and assign a sequence number?')) {
        router.post(route('invoices.issue', props.invoice.id));
    }
}

function duplicateInvoice() {
    router.post(route('invoices.duplicate', props.invoice.id));
}

function submitPayment() {
    router.post(route('invoices.payments.store', props.invoice.id), paymentForm.value, {
        onSuccess: () => {
            showPaymentModal.value = false;
        },
    });
}

function submitVoid() {
    if (!voidReason.value.trim()) {
        alert('Please enter a void reason.');
        return;
    }
    router.post(
        route('invoices.void', props.invoice.id),
        { reason: voidReason.value },
        {
            onSuccess: () => {
                showVoidModal.value = false;
            },
        }
    );
}
</script>
