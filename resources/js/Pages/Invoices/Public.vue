<template>
    <Head :title="`Invoice ${invoice.invoice_number || ''} - Zayin Global Limited`" />

    <div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8 flex flex-col items-center">
        <!-- Top bar with company branding & PDF button -->
        <div class="max-w-4xl w-full flex items-center justify-between pb-6 mb-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-primary text-white font-black text-xl flex items-center justify-center">
                    Z
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">Zayin Global Limited</h1>
                    <p class="text-xs text-gray-500">Invoice: {{ invoice.invoice_number || 'DRAFT' }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a
                    :href="pdfDownloadUrl"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-primary/90 rounded-lg shadow-sm transition-colors"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>

        <!-- Embedded A5 Frame -->
        <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            <div class="md:col-span-7 flex justify-center">
                <div class="shadow-2xl rounded border border-gray-300 bg-white overflow-hidden w-[420px] h-[595px]">
                    <iframe
                        :src="route('invoices.preview.show', invoice.id)"
                        class="w-full h-full border-0"
                    ></iframe>
                </div>
            </div>

            <div class="md:col-span-5 space-y-5">
                <!-- Payment Status Box -->
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                    <div class="text-xs font-semibold text-gray-500 uppercase">Payment Summary</div>
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm text-gray-600">Total Invoice:</span>
                        <span class="text-lg font-bold text-gray-900">₦{{ formatKobo(invoice.total_kobo, false) }}</span>
                    </div>
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm text-emerald-600 font-semibold">Amount Paid:</span>
                        <span class="text-base font-bold text-emerald-600">₦{{ formatKobo(invoice.amount_paid_kobo, false) }}</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                        <span class="text-sm font-bold text-gray-900">Outstanding:</span>
                        <span class="text-xl font-black text-amber-600">
                            ₦{{ formatKobo(Math.max(0, invoice.total_kobo - invoice.amount_paid_kobo), false) }}
                        </span>
                    </div>
                </div>

                <!-- Bank Accounts for Transfer -->
                <div v-if="designatedBanks && designatedBanks.length > 0" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-3">
                    <div class="text-xs font-bold text-gray-800 uppercase tracking-wide">
                        Transfer to any of our accounts:
                    </div>
                    <div class="space-y-2.5">
                        <div
                            v-for="bank in designatedBanks"
                            :key="bank.id"
                            class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs"
                        >
                            <div class="font-bold text-gray-900">{{ bank.bank_name }}</div>
                            <div class="font-mono text-sm font-black text-primary my-0.5">{{ bank.account_number }}</div>
                            <div class="text-[11px] text-gray-500">{{ bank.account_name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useNaira } from '@/Composables/useNaira';

const props = defineProps<{
    invoice: any;
    designatedBanks: any[];
    pdfDownloadUrl: string;
}>();

const { formatKobo } = useNaira();
</script>
