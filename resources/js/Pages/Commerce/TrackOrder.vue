<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { formatNaira } from '@/Utils/formatCurrency';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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
const companyPhone = computed(() => page.props.support?.phone || '+234 800 000 0000');
const companyWhatsApp = computed(() => page.props.support?.whatsapp || '+2348000000000');

const steps = [
    { key: 'awaiting_payment', label: 'Order Placed', desc: 'Order details logged in queue' },
    { key: 'awaiting_payment_confirmation', label: 'Payment Verifying', desc: 'Confirming wire transfer or proof' },
    { key: 'processing', label: 'Processing & Prep', desc: 'Inventory allocated & bench testing' },
    { key: 'in_transit', label: 'Dispatched / In Transit', desc: 'Technician/Courier dispatched' },
    { key: 'completed', label: 'Delivered & Installed', desc: 'Commissioned & signed off' },
];

const statusIndexMap = {
    'awaiting_payment': 0,
    'awaiting_payment_confirmation': 1,
    'processing': 2,
    'in_transit': 3,
    'completed': 4,
    'cancelled': -1,
};

const currentStepIndex = computed(() => {
    return statusIndexMap[props.order.status] ?? 0;
});

const statusBadge = computed(() => {
    switch (props.order.status) {
        case 'completed':
            return { label: 'Completed & Installed', class: 'bg-emerald-100 text-emerald-800 border-emerald-300' };
        case 'in_transit':
            return { label: 'In Transit / On Site', class: 'bg-blue-100 text-blue-800 border-blue-300' };
        case 'processing':
            return { label: 'Processing & Staging', class: 'bg-indigo-100 text-indigo-800 border-indigo-300' };
        case 'awaiting_payment_confirmation':
            return { label: 'Verifying Payment', class: 'bg-amber-100 text-amber-800 border-amber-300' };
        case 'cancelled':
            return { label: 'Cancelled', class: 'bg-rose-100 text-rose-800 border-rose-300' };
        default:
            return { label: 'Awaiting Payment', class: 'bg-yellow-100 text-yellow-800 border-yellow-300' };
    }
});

const paymentStatusBadge = computed(() => {
    switch (props.order.payment_status) {
        case 'paid':
            return { label: 'Paid in Full', class: 'text-emerald-700 bg-emerald-50' };
        case 'partial':
            return { label: 'Partially Paid', class: 'text-amber-700 bg-amber-50' };
        case 'refunded':
            return { label: 'Refunded', class: 'text-slate-700 bg-slate-100' };
        default:
            return { label: 'Unpaid / Pending', class: 'text-rose-700 bg-rose-50' };
    }
});
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 lg:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center text-xs text-slate-500 mb-6">
                <Link href="/" class="hover:text-amber-500 transition-colors">Home</Link>
                <span class="mx-2">/</span>
                <span class="text-slate-800 font-medium">Track Order #{{ order.number }}</span>
            </nav>

            <!-- Success flash if any -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3"
            >
                <font-awesome-icon icon="circle-check" class="text-emerald-600 text-lg flex-shrink-0" />
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <!-- Top Header Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <div class="text-xs uppercase tracking-wider font-semibold text-slate-400">Order Reference</div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 mt-1">#{{ order.number }}</h1>
                        <p class="text-xs text-slate-500 mt-1">Placed on {{ new Date(order.created_at).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' }) }}</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span :class="['px-3 py-1.5 rounded-full text-xs font-bold border inline-flex items-center gap-1.5', statusBadge.class]">
                            <span class="w-2 h-2 rounded-full bg-current"></span>
                            {{ statusBadge.label }}
                        </span>
                        <span :class="['px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center', paymentStatusBadge.class]">
                            {{ paymentStatusBadge.label }}
                        </span>
                    </div>
                </div>

                <!-- Stepper -->
                <div class="pt-8">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">Fulfillment Progress</h2>
                    
                    <div class="relative">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div 
                                v-for="(step, idx) in steps" 
                                :key="step.key"
                                class="flex md:flex-col items-center md:items-start gap-4 md:gap-2 relative"
                            >
                                <div 
                                    :class="[
                                        'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors flex-shrink-0 shadow-sm',
                                        idx <= currentStepIndex && order.status !== 'cancelled'
                                            ? 'bg-amber-500 text-navy-950 font-extrabold ring-4 ring-amber-100'
                                            : 'bg-slate-100 text-slate-400 border border-slate-200'
                                    ]"
                                >
                                    <font-awesome-icon v-if="idx < currentStepIndex && order.status !== 'cancelled'" icon="check" />
                                    <span v-else>{{ idx + 1 }}</span>
                                </div>
                                <div>
                                    <div :class="['text-xs font-bold', idx <= currentStepIndex ? 'text-navy-900' : 'text-slate-400']">
                                        {{ step.label }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 leading-tight">
                                        {{ step.desc }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unpaid CTA if Bank Transfer pending -->
                <div 
                    v-if="order.payment_status === 'unpaid' && order.status === 'awaiting_payment'"
                    class="mt-8 p-4 rounded-xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                >
                    <div class="flex items-start gap-3">
                        <font-awesome-icon icon="receipt" class="text-amber-600 text-xl mt-0.5" />
                        <div>
                            <div class="text-sm font-bold text-navy-900">Awaiting Bank Transfer Payment</div>
                            <div class="text-xs text-slate-600">Please upload your payment receipt or transfer proof so our billing team can confirm the slot.</div>
                        </div>
                    </div>
                    <Link
                        :href="route('orders.bank_transfer', order.number)"
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-navy-950 font-bold rounded-lg text-xs tracking-wider uppercase transition shadow-sm flex-shrink-0"
                    >
                        View Bank Details & Upload
                    </Link>
                </div>
            </div>

            <!-- Grid: Order Items & Delivery Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Items list -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-sm font-bold text-navy-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        Equipment & Services in this Order
                    </h2>

                    <div class="divide-y divide-slate-100">
                        <div v-for="item in order.items" :key="item.id" class="py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    <img 
                                        v-if="item.product?.image_url" 
                                        :src="item.product.image_url" 
                                        :alt="item.name_snapshot"
                                        class="w-full h-full object-cover" 
                                    />
                                    <font-awesome-icon v-else icon="box" class="text-slate-400" />
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-navy-900">{{ item.name_snapshot }}</div>
                                    <div class="text-xs text-slate-500">Qty: {{ item.qty }} &times; {{ formatNaira(item.unit_price) }}</div>
                                </div>
                            </div>
                            <div class="text-sm font-extrabold text-navy-900">
                                {{ formatNaira(item.total) }}
                            </div>
                        </div>
                    </div>

                    <!-- Financial breakdown -->
                    <div class="mt-6 pt-4 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Equipment Subtotal</span>
                            <span class="font-medium text-slate-900">{{ formatNaira(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery logistics ({{ order.state?.name || 'Standard' }})</span>
                            <span class="font-medium text-slate-900">{{ formatNaira(order.delivery_fee) }}</span>
                        </div>
                        <div v-if="order.install_fee > 0" class="flex justify-between">
                            <span>Certified Installation Fee</span>
                            <span class="font-medium text-slate-900">{{ formatNaira(order.install_fee) }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-slate-200 text-sm font-extrabold text-navy-900">
                            <span>Grand Total</span>
                            <span class="text-base text-amber-600">{{ formatNaira(order.total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Destination & Customer Info -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-sm font-bold text-navy-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                            Fulfillment Details
                        </h2>

                        <div class="space-y-3 text-xs text-slate-600">
                            <div>
                                <div class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Client Name</div>
                                <div class="text-sm font-bold text-navy-900">{{ order.guest_name }}</div>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Contact Phone</div>
                                <div class="font-medium text-slate-800">{{ order.guest_phone }}</div>
                            </div>

                            <div v-if="order.guest_email">
                                <div class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Email</div>
                                <div class="font-medium text-slate-800">{{ order.guest_email }}</div>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Mode</div>
                                <div class="font-semibold text-amber-600 capitalize">{{ order.fulfilment_type }}</div>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-400 uppercase tracking-wider text-[10px]">Destination Address</div>
                                <div class="font-medium text-slate-800">
                                    {{ order.address }}, {{ order.city }}, {{ order.state?.name }}
                                </div>
                                <div v-if="order.landmark" class="text-[11px] text-slate-500 italic mt-0.5">
                                    Landmark: {{ order.landmark }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Support Card -->
                    <div class="bg-gradient-to-br from-navy-900 to-navy-950 text-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold mb-2">Need Live Support?</h3>
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            Our field dispatch engineers are available 24/7 to assist with your order progress and site scheduling.
                        </p>
                        <div class="flex flex-col gap-2">
                            <a 
                                :href="`https://wa.me/${companyWhatsApp.replace(/[^0-9]/g, '')}?text=Hello%20Zayin%20Support,%20checking%20status%20for%20order%20${order.number}`"
                                target="_blank"
                                class="w-full py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition"
                            >
                                <font-awesome-icon :icon="['fab', 'whatsapp']" />
                                Chat with Dispatch via WhatsApp
                            </a>
                            <a 
                                :href="`tel:${companyPhone}`"
                                class="w-full py-2.5 px-4 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-2 transition"
                            >
                                <font-awesome-icon icon="phone" />
                                Call Hotline: {{ companyPhone }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
