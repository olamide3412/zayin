<script setup>
import { ref, computed } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { formatNaira } from '@/Utils/formatCurrency';

defineOptions({ layout: Layout });

const props = defineProps({
    cart: Object,
    states: Array,
    seo: Object,
});

const form = useForm({
    guest_name: '',
    guest_phone: '',
    guest_email: '',
    state_id: '',
    city: '',
    address: '',
    landmark: '',
    fulfilment_type: 'installation', // installation, delivery, pickup
    payment_method: 'paystack', // paystack, bank_transfer
    notes: '',
});

const selectedState = computed(() => {
    if (!form.state_id) return null;
    return props.states.find(s => s.id === Number(form.state_id));
});

const subtotal = computed(() => {
    if (!props.cart?.items) return 0;
    return props.cart.items.reduce((sum, item) => sum + (item.unit_price * item.qty), 0);
});

const deliveryFee = computed(() => {
    if (form.fulfilment_type === 'pickup' || !selectedState.value) return 0;
    return selectedState.value.delivery_fee || 0;
});

const installFee = computed(() => {
    if (form.fulfilment_type !== 'installation' || !selectedState.value) return 0;
    return selectedState.value.install_fee || 0;
});

const grandTotal = computed(() => {
    return subtotal.value + deliveryFee.value + installFee.value;
});

const submitOrder = () => {
    form.post('/checkout');
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-8">
                Secure Checkout
            </h1>

            <form @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Form: Customer Details, Address, Fulfilment (8 cols) -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- 1. Contact Info -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-6">
                        <h2 class="text-lg font-black text-slate-900 flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 text-xs flex items-center justify-center font-black">1</span>
                            <span>Customer Information</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-700 block mb-1">Full Name *</label>
                                <input 
                                    v-model="form.guest_name"
                                    type="text"
                                    required
                                    placeholder="e.g. Babatunde Adeleke"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-700 block mb-1">Phone Number (WhatsApp) *</label>
                                <input 
                                    v-model="form.guest_phone"
                                    type="text"
                                    required
                                    placeholder="e.g. 0803 123 4567"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-xs font-bold text-slate-700 block mb-1">Email Address (For Invoice PDF) *</label>
                                <input 
                                    v-model="form.guest_email"
                                    type="email"
                                    required
                                    placeholder="babatunde@example.com"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Destination & Landmark -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-6">
                        <h2 class="text-lg font-black text-slate-900 flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 text-xs flex items-center justify-center font-black">2</span>
                            <span>Delivery & Installation Address</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-700 block mb-1">State * (Determines Logistics Fee)</label>
                                <select 
                                    v-model="form.state_id"
                                    required
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500"
                                >
                                    <option value="">Select Destination State</option>
                                    <option v-for="st in states" :key="st.id" :value="st.id">
                                        {{ st.name }} (Delivery: {{ formatNaira(st.delivery_fee) }})
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-700 block mb-1">City / Town *</label>
                                <input 
                                    v-model="form.city"
                                    type="text"
                                    required
                                    placeholder="e.g. Lekki Phase 1, Ikeja, Maitama"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-xs font-bold text-slate-700 block mb-1">Street Address *</label>
                                <input 
                                    v-model="form.address"
                                    type="text"
                                    required
                                    placeholder="e.g. Plot 15, Admiralty Way"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="text-xs font-bold text-slate-700 block mb-1">Nearest Landmark / Junction *</label>
                                <input 
                                    v-model="form.landmark"
                                    type="text"
                                    required
                                    placeholder="e.g. Beside Zenith Bank / Opposite Shoprite"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- 3. Fulfilment & Payment Method -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-6">
                        <h2 class="text-lg font-black text-slate-900 flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 text-xs flex items-center justify-center font-black">3</span>
                            <span>Fulfilment & Payment Method</span>
                        </h2>

                        <!-- Fulfilment Radio -->
                        <div class="space-y-3">
                            <label class="text-xs font-bold text-slate-700 block">Fulfilment Preference</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label 
                                    class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between text-xs"
                                    :class="form.fulfilment_type === 'installation' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500 font-bold' : 'border-slate-200'"
                                >
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" value="installation" v-model="form.fulfilment_type" class="text-amber-500" />
                                        <span>Delivery & Turnkey Setup</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-2">Full technician deployment to site</span>
                                </label>

                                <label 
                                    class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between text-xs"
                                    :class="form.fulfilment_type === 'delivery' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500 font-bold' : 'border-slate-200'"
                                >
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" value="delivery" v-model="form.fulfilment_type" class="text-amber-500" />
                                        <span>Equipment Delivery Only</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-2">Transit to your doorstep</span>
                                </label>

                                <label 
                                    class="p-4 rounded-2xl border cursor-pointer flex flex-col justify-between text-xs"
                                    :class="form.fulfilment_type === 'pickup' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500 font-bold' : 'border-slate-200'"
                                >
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" value="pickup" v-model="form.fulfilment_type" class="text-amber-500" />
                                        <span>Warehouse Pickup</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-2">Zero logistics cost</span>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method Choice -->
                        <div class="space-y-3 pt-4 border-t border-slate-100">
                            <label class="text-xs font-bold text-slate-700 block">Payment Option</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label 
                                    class="p-4 rounded-2xl border cursor-pointer text-xs flex items-start space-x-3"
                                    :class="form.payment_method === 'paystack' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500' : 'border-slate-200'"
                                >
                                    <input type="radio" value="paystack" v-model="form.payment_method" class="mt-0.5 text-amber-500" />
                                    <div>
                                        <div class="font-bold text-slate-900">Paystack (Instant Card / USSD)</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">Automated instant verification & payment receipt</div>
                                    </div>
                                </label>

                                <label 
                                    class="p-4 rounded-2xl border cursor-pointer text-xs flex items-start space-x-3"
                                    :class="form.payment_method === 'bank_transfer' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500' : 'border-slate-200'"
                                >
                                    <input type="radio" value="bank_transfer" v-model="form.payment_method" class="mt-0.5 text-amber-500" />
                                    <div>
                                        <div class="font-bold text-slate-900">Direct Bank Transfer</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">Pay to official Zenith Bank account and upload proof</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Summary (4 cols) -->
                <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sm:p-8 space-y-6 sticky top-28">
                    <h3 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3">
                        Order Summary
                    </h3>

                    <!-- Line items -->
                    <div class="space-y-3 max-h-60 overflow-y-auto divide-y divide-slate-100 pr-1">
                        <div v-for="item in cart.items" :key="item.id" class="pt-2 text-xs flex justify-between">
                            <span class="text-slate-800 font-semibold truncate pr-2">
                                {{ item.qty }}x {{ item.product?.name }}
                            </span>
                            <span class="font-bold text-slate-900 shrink-0">
                                {{ formatNaira(item.unit_price * item.qty) }}
                            </span>
                        </div>
                    </div>

                    <!-- Fee breakdown -->
                    <div class="space-y-2.5 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900">{{ formatNaira(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Regional Delivery</span>
                            <span class="font-bold text-slate-900">{{ formatNaira(deliveryFee) }}</span>
                        </div>
                        <div v-if="form.fulfilment_type === 'installation'" class="flex justify-between">
                            <span>Turnkey Installation</span>
                            <span class="font-bold text-slate-900">{{ formatNaira(installFee) }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase text-slate-400">Total Payable</span>
                            <div class="text-2xl font-black text-slate-900">{{ formatNaira(grandTotal) }}</div>
                        </div>
                    </div>

                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition transform active:scale-95 disabled:opacity-50 flex items-center justify-center space-x-2"
                    >
                        <font-awesome-icon :icon="['fas', 'lock']" />
                        <span>{{ form.processing ? 'Processing...' : 'Place Order & Pay' }}</span>
                    </button>

                    <div class="text-[11px] text-slate-400 text-center leading-relaxed">
                        By placing this order you agree to our Warranty Guarantee and Terms of Service.
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
