<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, router } from '@inertiajs/vue3';
import { formatNaira } from '@/Utils/formatCurrency';
import CallToAction from '@/Components/Home/CallToAction.vue';

defineOptions({ layout: Layout });

const props = defineProps({
    products: Object,
    categories: Array,
    currentCategory: Object,
    filters: Object,
    seo: Object,
});

const search = ref(props.filters?.search || '');
const sort = ref(props.filters?.sort || '');

const handleSearch = () => {
    router.get(window.location.pathname, {
        search: search.value || undefined,
        sort: sort.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const handleSortChange = () => {
    handleSearch();
};

const addToCart = (product) => {
    router.post('/cart/items', { product_id: product.id, qty: 1 }, { preserveScroll: true });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen">
        <!-- Header Banner -->
        <div class="bg-slate-950 text-white py-14 sm:py-18 border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-4">
                    <Link href="/" class="hover:text-amber-400">Home</Link>
                    <span>/</span>
                    <span class="text-amber-400 font-bold">Packages & Store</span>
                </nav>

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                            {{ currentCategory ? currentCategory.name : 'All Equipment Packages & Turnkey Kits' }}
                        </h1>
                        <p class="mt-2 text-slate-400 text-sm max-w-2xl">
                            Fixed pricing in Naira with genuine hardware, warranty, and certified installation across Nigeria.
                        </p>
                    </div>

                    <!-- Search Input -->
                    <div class="w-full md:w-72">
                        <div class="relative">
                            <input 
                                v-model="search"
                                @keyup.enter="handleSearch"
                                type="text"
                                placeholder="Search packages, kVA..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            />
                            <font-awesome-icon :icon="['fas', 'search']" class="absolute left-3.5 top-3 text-slate-500 text-xs" />
                        </div>
                    </div>
                </div>

                <!-- Category Filter Pills -->
                <div class="mt-8 flex items-center space-x-2 overflow-x-auto pb-2 scrollbar-none">
                    <Link 
                        href="/packages"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            !currentCategory ? 'bg-amber-500 text-slate-950' : 'bg-slate-900 text-slate-300 hover:bg-slate-800'
                        ]"
                    >
                        All ({{ categories.reduce((sum, c) => sum + c.products_count, 0) }})
                    </Link>

                    <Link 
                        v-for="cat in categories" 
                        :key="cat.id"
                        :href="`/packages/${cat.slug}`"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            currentCategory?.id === cat.id ? 'bg-amber-500 text-slate-950' : 'bg-slate-900 text-slate-300 hover:bg-slate-800'
                        ]"
                    >
                        {{ cat.name }} ({{ cat.products_count }})
                    </Link>
                </div>
            </div>
        </div>

        <!-- Products Grid & Sort Bar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200">
                <span class="text-xs font-semibold text-slate-500">
                    Showing {{ products.data?.length || 0 }} of {{ products.total || 0 }} packages
                </span>

                <div class="flex items-center space-x-2 text-xs">
                    <label for="sort-select" class="text-slate-500 font-medium">Sort by:</label>
                    <select 
                        id="sort-select"
                        v-model="sort"
                        @change="handleSortChange"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-amber-500"
                    >
                        <option value="">Featured / Newest</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!products.data || products.data.length === 0" class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <font-awesome-icon :icon="['fas', 'box-open']" />
                </div>
                <h3 class="text-lg font-bold text-slate-800">No packages found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Try clearing your search query or switching categories.</p>
                <Link href="/packages" class="mt-4 inline-block px-5 py-2 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold">
                    View All Packages
                </Link>
            </div>

            <!-- Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="product in products.data" 
                    :key="product.id"
                    class="rounded-3xl bg-white border border-slate-200 hover:border-amber-400 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group"
                >
                    <div>
                        <!-- Image -->
                        <div class="aspect-[16/10] bg-slate-100 relative overflow-hidden">
                            <img 
                                :src="product.image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop'" 
                                :alt="product.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                            />
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 rounded-full bg-slate-900/85 backdrop-blur-md text-amber-400 text-[10px] font-black uppercase">
                                    {{ product.category?.name || 'Package' }}
                                </span>
                                <span v-if="product.installation_included" class="px-2.5 py-1 rounded-full bg-emerald-600/90 backdrop-blur-md text-white text-[10px] font-bold">
                                    Install Included
                                </span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="p-6 space-y-3">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                {{ product.brand || 'Zayin Pro' }}
                            </div>

                            <Link 
                                :href="`/packages/${product.category?.slug || 'all'}/${product.slug}`"
                                class="block font-bold text-lg text-slate-900 group-hover:text-amber-600 transition leading-snug line-clamp-2"
                            >
                                {{ product.name }}
                            </Link>

                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ product.summary }}
                            </p>

                            <!-- Inclusions Preview -->
                            <div v-if="product.inclusions && product.inclusions.length" class="pt-2 border-t border-slate-100 space-y-1">
                                <div class="text-[10px] font-bold uppercase text-slate-400">Included Equipment:</div>
                                <div 
                                    v-for="inc in product.inclusions.slice(0, 3)" 
                                    :key="inc.id"
                                    class="text-xs text-slate-700 flex items-center space-x-1.5 truncate"
                                >
                                    <font-awesome-icon :icon="['fas', 'check']" class="text-emerald-500 text-[10px] shrink-0" />
                                    <span class="truncate">{{ inc.item_name }} ({{ inc.quantity }})</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Price & Actions -->
                    <div class="p-6 pt-0 border-t border-slate-100 mt-4">
                        <div class="flex items-baseline justify-between mb-4">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold uppercase">Turnkey Price</span>
                                <span class="text-2xl font-black text-slate-900">{{ formatNaira(product.price) }}</span>
                            </div>
                            <span v-if="product.compare_at_price" class="text-xs text-slate-400 line-through">
                                {{ formatNaira(product.compare_at_price) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                @click="addToCart(product)"
                                class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center space-x-1.5 transition active:scale-95"
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

            <!-- Pagination -->
            <div v-if="products.links && products.links.length > 3" class="mt-12 flex justify-center space-x-1">
                <Link 
                    v-for="(link, i) in products.links" 
                    :key="i"
                    :href="link.url || '#'"
                    :class="[
                        'px-3.5 py-2 rounded-xl text-xs font-bold transition',
                        link.active ? 'bg-amber-500 text-slate-950' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200',
                        !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>

        <CallToAction />
    </div>
</template>
