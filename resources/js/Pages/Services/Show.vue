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
    service: Object,
    relatedProducts: Array,
    relatedProjects: Array,
    faqs: Array,
    seo: Object,
});

const page = usePage();

const benefits = computed(() => {
    if (!props.service.benefits) return [];
    return typeof props.service.benefits === 'string' ? JSON.parse(props.service.benefits) : props.service.benefits;
});

const processSteps = computed(() => {
    if (!props.service.process) return [];
    return typeof props.service.process === 'string' ? JSON.parse(props.service.process) : props.service.process;
});

const whatsappUrl = computed(() => {
    const raw = page.props.site?.whatsapp || '2348031234567';
    const clean = String(raw).replace(/[^0-9]/g, '');
    return generateWhatappLink(clean, `Hello Zayin Technologies, I would like to enquire about your ${props.service.name} service.`);
});

const addToCart = (product) => {
    router.post('/cart/items', { product_id: product.id, qty: 1 }, { preserveScroll: true });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen">
        <!-- Hero Section -->
        <div class="bg-slate-950 text-white py-16 sm:py-24 border-b border-slate-800 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 via-transparent to-sky-500/10 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-6">
                    <Link href="/" class="hover:text-amber-400">Home</Link>
                    <span>/</span>
                    <Link href="/services" class="hover:text-amber-400">Services</Link>
                    <span>/</span>
                    <span class="text-amber-400 font-bold">{{ service.name }}</span>
                </nav>

                <div class="max-w-3xl space-y-4">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full inline-block">
                        Engineering Solution
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                        {{ service.name }}
                    </h1>
                    <p class="text-slate-300 text-base sm:text-lg leading-relaxed">
                        {{ service.summary }}
                    </p>

                    <div class="pt-4 flex flex-wrap gap-4">
                        <Link 
                            href="/request-quote"
                            class="px-7 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-lg transition"
                        >
                            Request Quote
                        </Link>
                        <a 
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md transition flex items-center space-x-2"
                        >
                            <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-lg" />
                            <span>WhatsApp Enquiry</span>
                        </a>
                        <Link 
                            href="/book-installation"
                            class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm border border-slate-700 transition"
                        >
                            Book Site Visit
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
            <!-- Overview & Key Benefits -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                <div class="lg:col-span-7 space-y-6">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Overview & Engineering Scope
                    </h2>
                    <div class="text-slate-700 text-base leading-relaxed space-y-4 whitespace-pre-line">
                        {{ service.body }}
                    </div>

                    <!-- Benefits Checklist -->
                    <div v-if="benefits.length" class="pt-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Key Value & Advantages:</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div 
                                v-for="(b, i) in benefits" 
                                :key="i"
                                class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-start space-x-3"
                            >
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 text-base shrink-0 mt-0.5" />
                                <span class="text-xs font-semibold text-slate-800 leading-snug">{{ b }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Quick Card -->
                <div class="lg:col-span-5 bg-white rounded-3xl p-8 border border-slate-200 shadow-xl space-y-6">
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
                        Why Choose Zayin For This Service?
                    </h3>

                    <ul class="space-y-3.5 text-xs text-slate-600">
                        <li class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                <font-awesome-icon :icon="['fas', 'certificate']" />
                            </div>
                            <span>Certified Electrical Solar & Satellite Technicians</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                <font-awesome-icon :icon="['fas', 'truck']" />
                            </div>
                            <span>Active Logistics Across All 36 States + FCT</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <font-awesome-icon :icon="['fas', 'shield-halved']" />
                            </div>
                            <span>Authentic Manufacturer Hardware Warranty</span>
                        </li>
                    </ul>

                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 leading-relaxed">
                        <strong>Need an on-site survey?</strong> Our engineers inspect rooftop angles, calculate electrical loads, and test satellite line of sight before you commit.
                    </div>

                    <Link 
                        href="/request-quote"
                        class="w-full py-3.5 rounded-xl bg-slate-950 hover:bg-slate-900 text-white font-bold text-sm text-center block transition shadow-md"
                    >
                        Request Exact System Quote
                    </Link>
                </div>
            </div>

            <!-- Process Steps -->
            <div v-if="processSteps.length" class="space-y-8" data-aos="fade-up">
                <div class="text-center max-w-2xl mx-auto">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Installation & Execution Workflow
                    </h2>
                    <p class="mt-2 text-slate-600 text-sm">
                        Standardized procedure designed for maximum safety, clean aesthetics, and long-term durability.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div 
                        v-for="step in processSteps" 
                        :key="step.step"
                        class="bg-white rounded-3xl p-6 border border-slate-200 shadow-md space-y-3"
                    >
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 font-black text-sm flex items-center justify-center">
                            Step {{ step.step }}
                        </div>
                        <h4 class="font-bold text-slate-900 text-base">{{ step.title }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ step.desc }}</p>
                    </div>
                </div>
            </div>

            <!-- Related Packages -->
            <div v-if="relatedProducts && relatedProducts.length" class="space-y-8" data-aos="fade-up">
                <div class="flex items-end justify-between border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900">Recommended Packages For This Service</h2>
                        <p class="text-slate-600 text-xs sm:text-sm mt-1">Complete kits available with turnkey installation included.</p>
                    </div>
                    <Link href="/packages" class="text-xs font-bold text-amber-600 hover:text-amber-700">
                        All Packages &rarr;
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div 
                        v-for="prod in relatedProducts" 
                        :key="prod.id"
                        class="bg-white rounded-3xl border border-slate-200 p-6 shadow-md hover:shadow-xl transition-all flex flex-col justify-between"
                    >
                        <div class="space-y-3">
                            <span class="text-[10px] font-black uppercase text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full inline-block">
                                Turnkey Kit
                            </span>
                            <h4 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2">{{ prod.name }}</h4>
                            <div class="text-xl font-black text-slate-900">{{ formatNaira(prod.price) }}</div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 mt-4 flex items-center gap-2">
                            <button 
                                @click="addToCart(prod)"
                                class="flex-1 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition"
                            >
                                Buy
                            </button>
                            <Link 
                                :href="`/packages/${prod.category?.slug || 'all'}/${prod.slug}`"
                                class="flex-1 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold text-center transition"
                            >
                                Details
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service FAQs -->
            <div v-if="faqs && faqs.length" class="space-y-6 max-w-4xl mx-auto" data-aos="fade-up">
                <h2 class="text-2xl font-black text-slate-900 text-center">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <div 
                        v-for="faq in faqs" 
                        :key="faq.id"
                        class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-2"
                    >
                        <h4 class="font-bold text-slate-900 text-base flex items-start gap-2.5">
                            <span class="text-amber-500 font-black">Q:</span>
                            <span>{{ faq.question }}</span>
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pl-5">
                            {{ faq.answer }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <CallToAction />
    </div>
</template>
