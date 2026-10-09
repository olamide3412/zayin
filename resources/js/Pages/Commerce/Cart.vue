<script setup>
import { computed } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, router } from '@inertiajs/vue3';
import { formatNaira } from '@/Utils/formatCurrency';

defineOptions({ layout: Layout });

const props = defineProps({
    cart: Object,
    states: Array,
    seo: Object,
});

const subtotal = computed(() => {
    if (!props.cart?.items) return 0;
    return props.cart.items.reduce((sum, item) => sum + (item.unit_price * item.qty), 0);
});

const updateQty = (item, newQty) => {
    if (newQty < 1) return;
    router.patch(`/cart/items/${item.id}`, { qty: newQty }, { preserveScroll: true });
};

const removeItem = (item) => {
    router.delete(`/cart/items/${item.id}`, { preserveScroll: true });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-8">
                Your Shopping Cart
            </h1>

            <!-- Empty Cart -->
            <div v-if="!cart?.items || cart.items.length === 0" class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm max-w-lg mx-auto space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                    <font-awesome-icon :icon="['fas', 'shopping-cart']" />
                </div>
                <h2 class="text-xl font-bold text-slate-800">Your cart is currently empty</h2>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Explore our engineered solar packages, genuine Starlink kits, and DSTV decoder bundles.
                </p>
                <Link 
                    href="/packages"
                    class="inline-block px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition"
                >
                    Browse Equipment Packages
                </Link>
            </div>

            <!-- Cart Table & Summary -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Items List (8 cols) -->
                <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                    <div 
                        v-for="item in cart.items" 
                        :key="item.id"
                        class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                    >
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-100">
                                <img 
                                    :src="item.product?.image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=400&auto=format&fit=crop'" 
                                    :alt="item.product?.name"
                                    class="w-full h-full object-cover" 
                                />
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase text-amber-600">
                                    {{ item.product?.category?.name || 'Turnkey Kit' }}
                                </span>
                                <Link 
                                    :href="`/packages/${item.product?.category?.slug || 'all'}/${item.product?.slug}`"
                                    class="block font-bold text-slate-900 hover:text-amber-600 text-sm sm:text-base leading-snug"
                                >
                                    {{ item.product?.name }}
                                </Link>
                                <div class="text-xs font-bold text-slate-500 mt-1">
                                    {{ formatNaira(item.unit_price) }} each
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Stepper & Price -->
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto space-x-6 pt-3 sm:pt-0 border-t sm:border-0 border-slate-100">
                            <!-- Stepper -->
                            <div class="flex items-center space-x-2 bg-slate-100 rounded-xl p-1">
                                <button 
                                    @click="updateQty(item, item.qty - 1)"
                                    class="w-7 h-7 rounded-lg bg-white text-slate-800 font-bold flex items-center justify-center hover:bg-slate-200 shadow-sm text-xs"
                                >
                                    -
                                </button>
                                <span class="w-6 text-center text-xs font-black text-slate-900">{{ item.qty }}</span>
                                <button 
                                    @click="updateQty(item, item.qty + 1)"
                                    class="w-7 h-7 rounded-lg bg-white text-slate-800 font-bold flex items-center justify-center hover:bg-slate-200 shadow-sm text-xs"
                                >
                                    +
                                </button>
                            </div>

                            <div class="text-right">
                                <div class="text-base font-black text-slate-900">
                                    {{ formatNaira(item.unit_price * item.qty) }}
                                </div>
                                <button 
                                    @click="removeItem(item)"
                                    class="text-[11px] text-red-500 hover:text-red-700 font-semibold"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary (4 cols) -->
                <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sm:p-8 space-y-6">
                    <h3 class="text-lg font-black text-slate-900 border-b border-slate-100 pb-3">
                        Order Summary
                    </h3>

                    <div class="space-y-3 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Equipment Subtotal</span>
                            <span class="font-bold text-slate-900">{{ formatNaira(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Transit Logistics</span>
                            <span class="text-slate-500 italic">Calculated at Checkout</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Certified Installation</span>
                            <span class="text-emerald-600 font-semibold">Included on Turnkey Kits</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase text-slate-400">Estimated Total</span>
                            <div class="text-2xl font-black text-slate-900">{{ formatNaira(subtotal) }}</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <Link 
                            href="/checkout"
                            class="w-full py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm text-center block shadow-lg shadow-amber-500/20 transition transform active:scale-95"
                        >
                            Proceed to Checkout &rarr;
                        </Link>

                        <Link 
                            href="/packages"
                            class="w-full py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-xs font-bold text-center block"
                        >
                            Continue Shopping
                        </Link>
                    </div>

                    <!-- Trust Points -->
                    <div class="pt-4 border-t border-slate-100 space-y-2 text-[11px] text-slate-500">
                        <div class="flex items-center space-x-2">
                            <font-awesome-icon :icon="['fas', 'shield-halved']" class="text-emerald-500" />
                            <span>Paystack Secure Online & Bank Transfer</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <font-awesome-icon :icon="['fas', 'truck']" class="text-sky-500" />
                            <span>Insured Delivery Across Nigeria</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
