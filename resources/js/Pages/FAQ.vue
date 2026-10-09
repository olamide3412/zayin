<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    faqs: {
        type: Array,
        default: () => [],
    },
    seo: {
        type: Object,
        default: () => ({}),
    },
});

const searchQuery = ref('');
const activeCategory = ref('all');
const openItem = ref(null);

const categories = [
    { key: 'all', label: 'All Questions' },
    { key: 'solar', label: 'Solar & Inverters' },
    { key: 'starlink', label: 'Starlink & Internet' },
    { key: 'dstv', label: 'DSTV & Satellite' },
    { key: 'payments_warranty', label: 'Warranty & Payments' },
];

const filteredFaqs = computed(() => {
    let result = props.faqs;

    if (activeCategory.value !== 'all') {
        result = result.filter(faq => faq.category === activeCategory.value);
    }

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(faq =>
            faq.question.toLowerCase().includes(query) ||
            faq.answer.toLowerCase().includes(query)
        );
    }

    return result;
});

const toggleFaq = (id) => {
    openItem.value = openItem.value === id ? null : id;
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 lg:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-10">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-3">
                    Answers &amp; Technical Insights
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-navy-950 tracking-tight">
                    Frequently Asked Questions
                </h1>
                <p class="mt-4 text-slate-600 text-sm sm:text-base max-w-2xl mx-auto">
                    Everything you need to know about inverter sizing, solar battery runtime, Starlink positioning, and DSTV signal health in Nigeria.
                </p>

                <!-- Search Input -->
                <div class="mt-8 max-w-xl mx-auto relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search answers (e.g. 'lithium warranty', 'rain fade', 'air conditioner')..."
                        class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-sm shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition outline-none"
                    />
                    <font-awesome-icon icon="magnifying-glass" class="absolute left-4 top-4 text-slate-400" />
                </div>

                <!-- Category Tabs -->
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <button
                        v-for="cat in categories"
                        :key="cat.key"
                        type="button"
                        @click="activeCategory = cat.key"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition shadow-2xs',
                            activeCategory === cat.key
                                ? 'bg-navy-950 text-white'
                                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
                        ]"
                    >
                        {{ cat.label }}
                    </button>
                </div>
            </div>

            <!-- FAQ List -->
            <div class="space-y-4">
                <div
                    v-for="faq in filteredFaqs"
                    :key="faq.id"
                    class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden transition"
                >
                    <button
                        type="button"
                        @click="toggleFaq(faq.id)"
                        class="w-full px-6 py-5 text-left flex items-center justify-between gap-4 font-bold text-navy-950 text-sm sm:text-base hover:text-amber-600 transition"
                    >
                        <span>{{ faq.question }}</span>
                        <font-awesome-icon
                            :icon="openItem === faq.id ? 'chevron-up' : 'chevron-down'"
                            class="text-xs text-slate-400 flex-shrink-0 transition-transform"
                        />
                    </button>

                    <div
                        v-show="openItem === faq.id"
                        class="px-6 pb-6 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-4"
                    >
                        <p class="whitespace-pre-line">{{ faq.answer }}</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="filteredFaqs.length === 0"
                    class="bg-white rounded-2xl p-12 text-center border border-slate-200 text-slate-500 text-sm"
                >
                    <font-awesome-icon icon="circle-question" class="text-4xl text-slate-300 mb-3" />
                    <p class="font-bold text-navy-900">No matching questions found</p>
                    <p class="text-xs text-slate-400 mt-1">Try another search keyword or contact our support team directly.</p>
                </div>
            </div>

            <!-- Still Have Questions CTA -->
            <div class="mt-12 p-8 rounded-3xl bg-gradient-to-r from-navy-950 via-navy-900 to-navy-950 text-white text-center shadow-sm">
                <h3 class="text-xl font-bold">Have a specific question about your home or office?</h3>
                <p class="text-xs sm:text-sm text-slate-300 mt-2 max-w-lg mx-auto">
                    Our electrical engineers will calculate your peak load and provide custom recommendations for free.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <Link
                        :href="route('quote.create')"
                        class="px-6 py-3 bg-amber-500 hover:bg-amber-400 text-navy-950 font-bold rounded-xl text-xs uppercase tracking-wider transition"
                    >
                        Get Free Load Calculation
                    </Link>
                    <Link
                        :href="route('contact')"
                        class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-xs transition border border-white/20"
                    >
                        Speak with a Specialist
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
