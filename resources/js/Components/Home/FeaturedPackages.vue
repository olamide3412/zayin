<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { formatNaira } from '@/Utils/formatCurrency';

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    }
});

const activeTab = ref('all');

const filteredProducts = computed(() => {
    if (activeTab.value === 'all') {
        return props.products;
    }
    return props.products.filter(p => p.category?.slug === activeTab.value || p.type?.includes(activeTab.value));
});

const addToCart = (product) => {
    router.post('/cart/items', {
        product_id: product.id,
        qty: 1
    }, {
        preserveScroll: true
    });
};
</script>

<template>
    <section class="py-16 sm:py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header & Filter Tabs -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12" data-aos="fade-up">
                <div>
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-100/80 px-3.5 py-1.5 rounded-full inline-block mb-3">
                        Featured Systems
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        Fixed-Price Packages & Turnkey Kits
                    </h2>
                    <p class="mt-2 text-slate-600 text-sm sm:text-base">
                        Transparent pricing in Naira with genuine equipment, warranty, and professional installation.
                    </p>
                </div>

                <!-- Tabs -->
                <div class="mt-6 md:mt-0 flex items-center space-x-2 overflow-x-auto pb-2 md:pb-0 scrollbar-none">
                    <button 
                        @click="activeTab = 'all'"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            activeTab === 'all' 
                                ? 'bg-slate-950 text-white shadow-md' 
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        All Packages
                    </button>
                    <button 
                        @click="activeTab = 'solar'"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            activeTab === 'solar' 
                                ? 'bg-amber-500 text-slate-950 shadow-md' 
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Solar Systems
                    </button>
                    <button 
                        @click="activeTab = 'starlink'"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            activeTab === 'starlink' 
                                ? 'bg-sky-500 text-white shadow-md' 
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Starlink Kits
                    </button>
                    <button 
                        @click="activeTab = 'dstv'"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            activeTab === 'dstv' 
                                ? 'bg-blue-600 text-white shadow-md' 
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        DSTV Satellite
                    </button>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="product in filteredProducts" 
                    :key="product.id"
                    class="rounded-3xl bg-white border border-slate-200 hover:border-amber-400/80 shadow-md hover:shadow-2xl transition-all duration-300 flex flex-col justify-between overflow-hidden group"
                    data-aos="fade-up"
                >
                    <div>
                        <!-- Product Image -->
                        <div class="aspect-[16/10] bg-slate-100 relative overflow-hidden">
                            <img 
                                :src="product.image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop'" 
                                :alt="product.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                            />
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 rounded-full bg-slate-900/85 backdrop-blur-md text-amber-400 text-[10px] font-black uppercase tracking-wider">
                                    {{ product.category?.name || 'Package' }}
                                </span>
                                <span v-if="product.installation_included" class="px-2.5 py-1 rounded-full bg-emerald-600/90 backdrop-blur-md text-white text-[10px] font-bold">
                                    Install Included
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-3">
                            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                                <span>{{ product.brand || 'Zayin Pro' }}</span>
                                <span v-if="product.sku" class="font-mono text-[10px]">{{ product.sku }}</span>
                            </div>

                            <Link 
                                :href="`/packages/${product.category?.slug || 'all'}/${product.slug}`"
                                class="block font-bold text-lg text-slate-900 hover:text-amber-600 transition leading-snug"
                            >
                                {{ product.name }}
                            </Link>

                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ product.summary }}
                            </p>

                            <!-- Inclusions Preview -->
                            <div v-if="product.inclusions && product.inclusions.length" class="pt-2 border-t border-slate-100">
                                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1.5">
                                    What's Included:
                                </div>
                                <ul class="space-y-1 text-xs text-slate-700">
                                    <li 
                                        v-for="inc in product.inclusions.slice(0, 3)" 
                                        :key="inc.id"
                                        class="flex items-center space-x-1.5 truncate"
                                    >
                                        <font-awesome-icon :icon="['fas', 'check']" class="text-emerald-500 text-[10px] shrink-0" />
                                        <span class="truncate">{{ inc.item_name }} ({{ inc.quantity }})</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Price & Actions -->
                    <div class="p-6 pt-0 border-t border-slate-100 mt-4">
                        <div class="flex items-baseline justify-between mb-4">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold uppercase">Total Price</span>
                                <span class="text-2xl font-black text-slate-900">{{ formatNaira(product.price) }}</span>
                            </div>
                            <span v-if="product.compare_at_price" class="text-xs text-slate-400 line-through">
                                {{ formatNaira(product.compare_at_price) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                @click="addToCart(product)"
                                class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center space-x-1.5 transition active:scale-95 shadow-sm"
                            >
                                <font-awesome-icon :icon="['fas', 'shopping-cart']" />
                                <span>Buy Now</span>
                            </button>

                            <Link 
                                :href="`/packages/${product.category?.slug || 'all'}/${product.slug}`"
                                class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs flex items-center justify-center space-x-1 transition shadow-sm"
                            >
                                <span>Details</span>
                                <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-[10px]" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All CTA -->
            <div class="text-center mt-12" data-aos="fade-up">
                <Link 
                    href="/packages"
                    class="inline-flex items-center space-x-2 px-8 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm transition shadow-sm"
                >
                    <span>View All Packages & Accessories</span>
                    <font-awesome-icon :icon="['fas', 'arrow-right']" />
                </Link>
            </div>
        </div>
    </section>
</template>
