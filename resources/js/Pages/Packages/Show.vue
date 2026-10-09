<script setup>
import { computed } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { formatNaira } from '@/Utils/formatCurrency';
import { generateWhatappLink } from '@/Utils/whatsappLinkGen';
import CallToAction from '@/Components/Home/CallToAction.vue';

defineOptions({ layout: Layout });

const props = defineProps({
    product: Object,
    relatedProducts: Array,
    seo: Object,
});

const page = usePage();

const specs = computed(() => {
    if (!props.product.specs) return {};
    return typeof props.product.specs === 'string' ? JSON.parse(props.product.specs) : props.product.specs;
});

const whatsappUrl = computed(() => {
    const raw = page.props.site?.whatsapp || '2348031234567';
    const clean = String(raw).replace(/[^0-9]/g, '');
    return generateWhatappLink(clean, `Hello Zayin Technologies, I would like to enquire about purchasing the ${props.product.name} (Price: ${formatNaira(props.product.price)}).`);
});

const addToCart = () => {
    router.post('/cart/items', {
        product_id: props.product.id,
        qty: 1
    }, {
        preserveScroll: true
    });
};

const buyNow = () => {
    router.post('/cart/items', {
        product_id: props.product.id,
        qty: 1
    }, {
        onSuccess: () => {
            router.visit('/checkout');
        }
    });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen">
        <!-- Breadcrumbs -->
        <div class="bg-white border-b border-slate-200 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center space-x-2 text-xs text-slate-500">
                    <Link href="/" class="hover:text-slate-900">Home</Link>
                    <span>/</span>
                    <Link href="/packages" class="hover:text-slate-900">Packages</Link>
                    <span>/</span>
                    <Link 
                        v-if="product.category" 
                        :href="`/packages/${product.category.slug}`" 
                        class="hover:text-slate-900"
                    >
                        {{ product.category.name }}
                    </Link>
                    <span v-if="product.category">/</span>
                    <span class="text-amber-600 font-bold truncate max-w-xs">{{ product.name }}</span>
                </nav>
            </div>
        </div>

        <!-- Main Product Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                <!-- Left: Media & Gallery (6 cols) -->
                <div class="lg:col-span-6 space-y-4">
                    <div class="aspect-[16/11] rounded-3xl bg-slate-900 overflow-hidden relative border border-slate-200 shadow-lg">
                        <img 
                            :src="product.image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=1200&auto=format&fit=crop'" 
                            :alt="product.name"
                            class="w-full h-full object-cover" 
                        />
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-full bg-slate-950/85 backdrop-blur-md text-amber-400 text-xs font-black uppercase tracking-wider">
                                {{ product.category?.name || 'Turnkey Kit' }}
                            </span>
                            <span v-if="product.installation_included" class="px-3 py-1 rounded-full bg-emerald-600/90 backdrop-blur-md text-white text-xs font-bold">
                                Installation Included
                            </span>
                        </div>
                    </div>

                    <!-- Warranty & Trust Strip -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-center">
                        <div class="p-3 bg-white rounded-2xl border border-slate-200">
                            <font-awesome-icon :icon="['fas', 'shield-halved']" class="text-amber-500 mb-1 text-sm" />
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Warranty</div>
                            <div class="text-xs font-bold text-slate-800">{{ product.warranty_text || 'Manufacturer Warranty' }}</div>
                        </div>
                        <div class="p-3 bg-white rounded-2xl border border-slate-200">
                            <font-awesome-icon :icon="['fas', 'truck']" class="text-sky-500 mb-1 text-sm" />
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Delivery</div>
                            <div class="text-xs font-bold text-slate-800">Nationwide Transit</div>
                        </div>
                        <div class="p-3 bg-white rounded-2xl border border-slate-200 col-span-2 sm:col-span-1">
                            <font-awesome-icon :icon="['fas', 'certificate']" class="text-emerald-500 mb-1 text-sm" />
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Workmanship</div>
                            <div class="text-xs font-bold text-slate-800">Certified Engineers</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Details, Price & Actions (6 cols) -->
                <div class="lg:col-span-6 space-y-6">
                    <div>
                        <div class="text-xs font-bold uppercase text-amber-600 tracking-wider mb-1">
                            {{ product.brand || 'Zayin Pro Hardware' }} · SKU: {{ product.sku || 'N/A' }}
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-snug">
                            {{ product.name }}
                        </h1>
                        <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                            {{ product.summary }}
                        </p>
                    </div>

                    <!-- Pricing Box -->
                    <div class="p-6 rounded-3xl bg-slate-900 text-white border border-slate-800 shadow-xl space-y-4">
                        <div class="flex items-baseline justify-between">
                            <div>
                                <span class="text-xs text-slate-400 block font-semibold uppercase">Total Installed Price</span>
                                <div class="text-3xl sm:text-4xl font-black text-amber-400">
                                    {{ formatNaira(product.price) }}
                                </div>
                            </div>
                            <div v-if="product.compare_at_price" class="text-right">
                                <span class="text-xs text-slate-500 block">Regular</span>
                                <span class="text-sm text-slate-400 line-through">{{ formatNaira(product.compare_at_price) }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <button 
                                @click="buyNow"
                                class="py-3.5 px-6 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-md transition transform active:scale-95 flex items-center justify-center space-x-2"
                            >
                                <font-awesome-icon :icon="['fas', 'bolt']" />
                                <span>Buy & Checkout Now</span>
                            </button>

                            <button 
                                @click="addToCart"
                                class="py-3.5 px-6 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm border border-slate-700 transition flex items-center justify-center space-x-2"
                            >
                                <font-awesome-icon :icon="['fas', 'shopping-cart']" />
                                <span>Add to Cart</span>
                            </button>
                        </div>

                        <!-- WhatsApp Enquiry Bar -->
                        <div class="pt-2 border-t border-slate-800">
                            <a 
                                :href="whatsappUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full py-3 rounded-xl bg-emerald-600/90 hover:bg-emerald-600 text-white font-bold text-xs flex items-center justify-center space-x-2 transition"
                            >
                                <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-base" />
                                <span>Enquire or Customize via WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <!-- Inclusions / Equipment Breakdown -->
                    <div v-if="product.inclusions && product.inclusions.length" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                            <span>Itemized Equipment Inclusions</span>
                            <span class="text-xs font-semibold text-emerald-600">All Genuine Parts</span>
                        </h3>

                        <div class="divide-y divide-slate-100">
                            <div 
                                v-for="inc in product.inclusions" 
                                :key="inc.id"
                                class="py-3 flex items-start justify-between gap-3 text-xs"
                            >
                                <div class="flex items-start space-x-2.5">
                                    <font-awesome-icon :icon="['fas', 'circle-check']" class="text-emerald-500 text-sm mt-0.5 shrink-0" />
                                    <div>
                                        <div class="font-bold text-slate-800">{{ inc.item_name }}</div>
                                        <div v-if="inc.description" class="text-slate-500 text-[11px] mt-0.5">{{ inc.description }}</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md font-bold shrink-0">
                                    {{ inc.quantity }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Specifications Table -->
                    <div v-if="Object.keys(specs).length" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                            Technical Specifications
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div 
                                v-for="(val, key) in specs" 
                                :key="key"
                                class="p-3 bg-slate-50 rounded-xl"
                            >
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">{{ key }}</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ val }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Packages -->
            <div v-if="relatedProducts && relatedProducts.length" class="mt-20 space-y-8" data-aos="fade-up">
                <div class="flex items-end justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900">Similar Recommended Packages</h2>
                        <p class="text-slate-600 text-xs sm:text-sm mt-1">Compare alternative ratings and capacities.</p>
                    </div>
                    <Link href="/packages" class="text-xs font-bold text-amber-600">View All Store &rarr;</Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div 
                        v-for="rel in relatedProducts" 
                        :key="rel.id"
                        class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between"
                    >
                        <div class="space-y-3">
                            <span class="text-[10px] font-black uppercase text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full inline-block">
                                {{ rel.category?.name }}
                            </span>
                            <Link :href="`/packages/${rel.category?.slug || 'all'}/${rel.slug}`" class="block font-bold text-slate-900 hover:text-amber-600 leading-snug">
                                {{ rel.name }}
                            </Link>
                            <div class="text-xl font-black text-slate-900">{{ formatNaira(rel.price) }}</div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 mt-4">
                            <Link 
                                :href="`/packages/${rel.category?.slug || 'all'}/${rel.slug}`"
                                class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center block transition"
                            >
                                View Package &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CallToAction />
    </div>
</template>
