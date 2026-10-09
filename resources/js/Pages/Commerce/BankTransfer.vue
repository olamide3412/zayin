<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { formatNaira } from '@/Utils/formatCurrency';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    seo: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const bankDetails = computed(() => ({
    bankName: page.props.site?.bank_name || 'Zenith Bank PLC',
    accountName: page.props.site?.account_name || 'Zayin Technologies Limited',
    accountNumber: page.props.site?.account_number || '1224908123',
    sortCode: '057150013',
}));

const copied = ref(false);
const copyAccountNumber = async () => {
    try {
        await navigator.clipboard.writeText(bankDetails.value.accountNumber);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 3000);
    } catch (err) {
        console.error('Failed to copy', err);
    }
};

const form = useForm({
    proof_file: null,
});

const previewUrl = ref(null);

const onFileSelected = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.proof_file = file;
        if (file.type.startsWith('image/')) {
            previewUrl.value = URL.createObjectURL(file);
        } else {
            previewUrl.value = null;
        }
    }
};

const submitProof = () => {
    form.post(route('orders.bank_transfer.upload', props.order.number), {
        forceFormData: true,
    });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 lg:py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center text-xs text-slate-500 mb-6">
                <Link href="/" class="hover:text-amber-500 transition-colors">Home</Link>
                <span class="mx-2">/</span>
                <Link :href="route('orders.track', order.number)" class="hover:text-amber-500 transition-colors">Order #{{ order.number }}</Link>
                <span class="mx-2">/</span>
                <span class="text-slate-800 font-medium">Bank Transfer</span>
            </nav>

            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header Banner -->
                <div class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-800 text-white p-6 sm:p-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-semibold mb-3">
                        <font-awesome-icon icon="building-columns" />
                        Official Corporate Account
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Direct Bank Transfer</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-2">
                        Order <span class="font-bold text-amber-400">#{{ order.number }}</span> is reserved for you. Please transfer the exact amount below to activate immediate fulfillment.
                    </p>
                </div>

                <div class="p-6 sm:p-8 space-y-8">
                    <!-- Amount to Transfer -->
                    <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="text-xs uppercase font-bold text-amber-800 tracking-wider">Amount Due to Pay</div>
                            <div class="text-3xl font-black text-navy-950 mt-1">
                                {{ formatNaira(order.total) }}
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                                Includes equipment and logistics to {{ order.state?.name || 'destination' }}
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-block text-xs font-mono font-bold bg-white px-3 py-1.5 rounded-lg border border-amber-200 text-navy-900 shadow-sm">
                                Ref: {{ order.number }}
                            </span>
                            <div class="text-[11px] text-slate-500 mt-1">Use order ref as narration/remark</div>
                        </div>
                    </div>

                    <!-- Bank Account Details Card -->
                    <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50">
                        <h2 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-4">Official Bank Account Information</h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 bg-white rounded-xl border border-slate-200">
                                <div class="text-[11px] text-slate-400 font-medium">Bank Name</div>
                                <div class="text-base font-bold text-navy-950 mt-0.5">{{ bankDetails.bankName }}</div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-slate-200">
                                <div class="text-[11px] text-slate-400 font-medium">Account Name</div>
                                <div class="text-base font-bold text-navy-950 mt-0.5">{{ bankDetails.accountName }}</div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-slate-200 sm:col-span-2 flex items-center justify-between">
                                <div>
                                    <div class="text-[11px] text-slate-400 font-medium">Account Number (NUBAN)</div>
                                    <div class="text-xl font-mono font-extrabold text-navy-950 mt-0.5 tracking-wider">
                                        {{ bankDetails.accountNumber }}
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="copyAccountNumber"
                                    class="px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2"
                                >
                                    <font-awesome-icon :icon="copied ? 'check' : 'copy'" />
                                    <span>{{ copied ? 'Copied!' : 'Copy Account Number' }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-900 flex items-start gap-2">
                            <font-awesome-icon icon="circle-info" class="text-blue-600 mt-0.5" />
                            <span>
                                <strong>Important:</strong> Kindly ensure the transfer description / narration contains your order number <strong>{{ order.number }}</strong> for instant automated reconciliation.
                            </span>
                        </div>
                    </div>

                    <!-- Receipt Upload Form -->
                    <div class="border border-slate-200 rounded-2xl p-6 bg-white">
                        <h2 class="text-sm font-bold text-navy-950 uppercase tracking-wider mb-2">Upload Transfer Proof / Receipt</h2>
                        <p class="text-xs text-slate-500 mb-6">
                            Upload a screenshot, receipt PDF, or payment confirmation slip generated by your banking app.
                        </p>

                        <form @submit.prevent="submitProof" class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Select Receipt File (JPG, PNG or PDF, max 5MB)
                                </label>
                                <div class="relative border-2 border-dashed border-slate-300 hover:border-amber-500 rounded-2xl p-6 text-center cursor-pointer transition bg-slate-50/50">
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/jpg,application/pdf"
                                        @change="onFileSelected"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        required
                                    />
                                    <div class="flex flex-col items-center">
                                        <font-awesome-icon icon="cloud-arrow-up" class="text-3xl text-slate-400 mb-2" />
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ form.proof_file ? form.proof_file.name : 'Click to select or drag and drop your receipt' }}
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">PNG, JPG, PDF up to 5MB</p>
                                    </div>
                                </div>
                                <div v-if="form.errors.proof_file" class="text-xs text-rose-600 font-semibold mt-1.5">
                                    {{ form.errors.proof_file }}
                                </div>
                            </div>

                            <!-- Preview thumbnail if image -->
                            <div v-if="previewUrl" class="p-3 bg-slate-50 rounded-xl border border-slate-200 inline-block">
                                <div class="text-[11px] font-bold text-slate-500 mb-1">Preview:</div>
                                <img :src="previewUrl" alt="Receipt preview" class="max-h-48 rounded border object-contain" />
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-3">
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.proof_file"
                                    class="w-full sm:w-auto px-8 py-3.5 bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-navy-950 font-extrabold rounded-xl text-sm transition shadow-sm uppercase tracking-wider flex items-center justify-center gap-2"
                                >
                                    <font-awesome-icon v-if="form.processing" icon="spinner" class="animate-spin" />
                                    <span>{{ form.processing ? 'Uploading...' : 'Submit Payment Proof' }}</span>
                                </button>

                                <Link
                                    :href="route('orders.track', order.number)"
                                    class="text-xs text-slate-500 hover:text-slate-800 font-semibold px-4 py-2"
                                >
                                    Upload later &amp; return to status tracker
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
