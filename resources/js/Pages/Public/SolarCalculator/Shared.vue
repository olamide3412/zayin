<script setup>
import { computed } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import ResultSummary from '@/Components/Solar/ResultSummary.vue';
import { router, usePage, Link } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    calculation: {
        type: Object,
        required: true,
    },
    packages: {
        type: Array,
        default: () => [],
    },
    seo: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const companyWhatsApp = computed(() => page.props.support?.phone_whatsapp || page.props.site?.whatsapp || '2348031234567');
const shareUrl = computed(() => window.location.href);

const onRequestQuote = (matchedPkg) => {
    router.visit('/request-quote', {
        data: {
            service_type: 'solar',
            calculation_uuid: props.calculation.uuid,
            product_id: matchedPkg ? matchedPkg.id : undefined,
            budget_range: matchedPkg ? `₦${(matchedPkg.price / 100).toLocaleString()}` : undefined,
        },
    });
};

const onModify = () => {
    router.visit('/solar-calculator');
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Header Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 rounded-3xl bg-white border border-slate-200 shadow-sm">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider mb-2">
                        Shared System Specification
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-navy-950">
                        Saved Solar Sizing Profile
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Archived on {{ new Date(calculation.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) }} · Reference ID: <span class="font-mono text-slate-700">{{ calculation.uuid }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        href="/solar-calculator"
                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-bold text-xs uppercase tracking-wider transition shadow-sm"
                    >
                        Create New Sizing &rarr;
                    </Link>
                </div>
            </div>

            <!-- Appliances summary breakdown -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-navy-950 uppercase tracking-wider">
                    Included Appliances in this Load Profile
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    <div
                        v-for="(app, idx) in calculation.inputs.appliances"
                        :key="idx"
                        class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs"
                    >
                        <div>
                            <span class="font-bold text-navy-950 block">{{ app.name }}</span>
                            <span class="text-[11px] text-slate-500">{{ app.qty }}x · {{ app.watts }}W · {{ app.hours }}h/day</span>
                        </div>
                        <span class="font-bold text-amber-700 font-mono text-[11px]">
                            {{ Math.round(app.watts * app.qty * app.hours) }} Wh
                        </span>
                    </div>
                </div>
            </div>

            <!-- Results Presentation -->
            <ResultSummary
                :results="calculation.results"
                :setup="calculation.inputs"
                :calculation-uuid="calculation.uuid"
                :share-url="shareUrl"
                :packages="packages"
                :company-whats-app="companyWhatsApp"
                @request-quote="onRequestQuote"
                @edit-appliances="onModify"
            />
        </div>
    </div>
</template>
