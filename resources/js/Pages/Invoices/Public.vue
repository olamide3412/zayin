<template>
    <Head :title="`Invoice ${invoice.invoice_number || ''} - Zayin Ltd`" />

    <div class="min-h-screen bg-gray-50 py-4 sm:py-10 px-2 sm:px-6 lg:px-8 flex flex-col items-center pb-24 sm:pb-10">
        <!-- Top bar with company branding & PDF button -->
        <div class="max-w-4xl w-full flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 sm:pb-6 mb-4 sm:mb-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-primary text-white font-black text-xl flex items-center justify-center shrink-0">
                    Z
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">Zayin Ltd</h1>
                    <p class="text-xs text-gray-500">Invoice: {{ invoice.invoice_number || 'DRAFT' }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a
                    :href="pdfDownloadUrl"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-primary hover:bg-primary/90 rounded-lg shadow-sm transition-colors"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Official PDF
                </a>
            </div>
        </div>

        <!-- Embedded A5 Frame & Payment Info -->
        <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
            <div class="lg:col-span-7 flex justify-center w-full overflow-x-auto">
                <InvoicePreviewFrame :src="route('invoices.preview.show', invoice.id)" />
            </div>

            <div class="lg:col-span-5 space-y-4 sm:space-y-5 w-full">
                <!-- Payment Status Box -->
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Summary</div>
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm text-gray-600">Total Invoice:</span>
                        <span class="text-lg font-bold text-gray-900">₦{{ formatKobo(invoice.total_kobo, false) }}</span>
                    </div>
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm text-emerald-600 font-semibold">Amount Paid:</span>
                        <span class="text-base font-bold text-emerald-600">₦{{ formatKobo(invoice.amount_paid_kobo, false) }}</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                        <span class="text-sm font-bold text-gray-900">Outstanding Due:</span>
                        <span class="text-xl font-black text-amber-600">
                            ₦{{ formatKobo(Math.max(0, invoice.total_kobo - invoice.amount_paid_kobo), false) }}
                        </span>
                    </div>
                </div>

                <!-- Bank Accounts for Transfer -->
                <div v-if="designatedBanks && designatedBanks.length > 0" class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                    <div class="text-xs font-bold text-gray-800 uppercase tracking-wide">
                        Payment Bank Details:
                    </div>
                    <div class="space-y-2.5">
                        <div
                            v-for="bank in designatedBanks"
                            :key="bank.id"
                            class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs space-y-1"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900">{{ bank.bank_name }}</span>
                                <button
                                    type="button"
                                    @click="copyAccount(bank.account_number, bank.id)"
                                    class="text-[11px] font-semibold text-primary hover:underline px-2 py-0.5 rounded bg-primary/10"
                                >
                                    {{ copiedBankId === bank.id ? 'Copied!' : 'Copy Number' }}
                                </button>
                            </div>
                            <div class="font-mono text-base font-black text-primary">{{ bank.account_number }}</div>
                            <div class="text-[11px] text-gray-500">Account Name: <strong class="text-gray-700">{{ bank.account_name }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Mobile Bottom Bar for Customers -->
        <div class="sm:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-gray-200 px-4 py-2.5 z-30 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] flex items-center justify-between gap-3">
            <div class="min-w-0 pr-1">
                <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">
                    {{ invoice.status === 'paid' ? 'Paid' : 'Balance Due' }}
                </div>
                <div class="text-sm font-black text-amber-600 truncate tabular-nums">
                    ₦{{ formatKobo(Math.max(0, invoice.total_kobo - invoice.amount_paid_kobo), false) }}
                </div>
            </div>
            <a
                :href="pdfDownloadUrl"
                class="px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg shadow-sm flex items-center shrink-0"
            >
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF
            </a>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import InvoicePreviewFrame from '@/Components/Invoice/InvoicePreviewFrame.vue';
import { useNaira } from '@/Composables/useNaira';

const props = defineProps<{
    invoice: any;
    designatedBanks: any[];
    pdfDownloadUrl: string;
}>();

const { formatKobo } = useNaira();
const copiedBankId = ref<number | null>(null);

function copyAccount(accNum: string, bankId: number) {
    if (!accNum) return;
    navigator.clipboard.writeText(accNum);
    copiedBankId.value = bankId;
    setTimeout(() => {
        copiedBankId.value = null;
    }, 2500);
}
</script>
