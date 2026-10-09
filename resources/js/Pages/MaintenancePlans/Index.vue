<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { formatNaira } from '@/Utils/formatCurrency';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
    seo: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const companyWhatsApp = computed(() => page.props.support?.whatsapp || '+2348000000000');

const parseInclusions = (inc) => {
    if (Array.isArray(inc)) return inc;
    if (typeof inc === 'string') {
        try {
            const parsed = JSON.parse(inc);
            if (Array.isArray(parsed)) return parsed;
        } catch {
            return inc.split('\n').filter(Boolean);
        }
    }
    return [];
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-3">
                    Preventive Maintenance Contracts
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-navy-950 tracking-tight">
                    Keep Your Energy &amp; Network Running at 100% Efficiency
                </h1>
                <p class="mt-4 text-slate-600 text-sm sm:text-base leading-relaxed">
                    Dust accumulation on solar panels causes up to 35% power drop in the dry season. Loose connections cause premature inverter burnouts. Our Annual Maintenance Contracts (AMC) protect your multi-million naira investments.
                </p>
            </div>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                <div
                    v-for="(plan, idx) in plans"
                    :key="plan.id"
                    :class="[
                        'rounded-3xl p-8 flex flex-col justify-between transition-all duration-300 relative',
                        idx === 1
                            ? 'bg-gradient-to-b from-navy-950 to-navy-900 text-white shadow-xl ring-2 ring-amber-500 scale-100 md:-translate-y-2'
                            : 'bg-white text-slate-900 border border-slate-200 shadow-sm hover:shadow-md'
                    ]"
                >
                    <!-- Popular Badge -->
                    <div 
                        v-if="idx === 1"
                        class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-amber-500 text-navy-950 text-[11px] font-black uppercase tracking-wider shadow-sm"
                    >
                        Most Popular for Homes
                    </div>

                    <div>
                        <!-- Plan Header -->
                        <div class="mb-6">
                            <h2 :class="['text-xl font-black', idx === 1 ? 'text-white' : 'text-navy-950']">
                                {{ plan.name }}
                            </h2>
                            <p :class="['text-xs mt-2 leading-relaxed', idx === 1 ? 'text-slate-300' : 'text-slate-500']">
                                {{ plan.summary }}
                            </p>
                        </div>

                        <!-- Price -->
                        <div class="mb-8 pb-6 border-b" :class="idx === 1 ? 'border-slate-800' : 'border-slate-100'">
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl sm:text-4xl font-black" :class="idx === 1 ? 'text-amber-400' : 'text-navy-950'">
                                    {{ formatNaira(plan.price) }}
                                </span>
                                <span :class="['text-xs font-semibold uppercase tracking-wider', idx === 1 ? 'text-slate-400' : 'text-slate-500']">
                                    / {{ plan.interval }}
                                </span>
                            </div>
                        </div>

                        <!-- Inclusions -->
                        <div class="space-y-3 mb-8">
                            <div class="text-xs font-bold uppercase tracking-wider" :class="idx === 1 ? 'text-slate-300' : 'text-slate-400'">
                                Included in this Plan:
                            </div>
                            <ul class="space-y-2.5 text-xs">
                                <li
                                    v-for="(item, i) in parseInclusions(plan.inclusions)"
                                    :key="i"
                                    class="flex items-start gap-2.5"
                                >
                                    <font-awesome-icon
                                        icon="circle-check"
                                        :class="['mt-0.5 text-sm flex-shrink-0', idx === 1 ? 'text-amber-400' : 'text-emerald-500']"
                                    />
                                    <span :class="idx === 1 ? 'text-slate-200' : 'text-slate-700'">
                                        {{ item }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="pt-4">
                        <a
                            :href="`https://wa.me/${companyWhatsApp.replace(/[^0-9]/g, '')}?text=Hello%20Zayin%20Support,%20I%20am%20interested%20in%20subscribing%20to%20the%20${encodeURIComponent(plan.name)}%20maintenance%20plan.`"
                            target="_blank"
                            :class="[
                                'w-full py-3.5 px-6 rounded-xl font-extrabold text-xs tracking-wider uppercase transition text-center flex items-center justify-center gap-2 shadow-sm',
                                idx === 1
                                    ? 'bg-amber-500 hover:bg-amber-400 text-navy-950'
                                    : 'bg-navy-900 hover:bg-navy-800 text-white'
                            ]"
                        >
                            <font-awesome-icon :icon="['fab', 'whatsapp']" />
                            <span>Subscribe via WhatsApp</span>
                        </a>

                        <div class="text-center mt-2.5">
                            <Link
                                :href="route('booking.create')"
                                :class="['text-[11px] font-semibold underline', idx === 1 ? 'text-slate-400 hover:text-white' : 'text-slate-500 hover:text-navy-900']"
                            >
                                Or schedule a one-off diagnostic visit
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Value Props -->
            <div class="mt-16 bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-950">Why an Annual Maintenance Contract?</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-2">
                        Routine preventative maintenance pays for itself within the first harmattan season.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg mb-3">
                            <font-awesome-icon icon="fire-flame-curved" />
                        </div>
                        <h4 class="font-bold text-navy-950 text-sm">Thermal Hotspot Prevention</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Dust, bird droppings, and loose terminals cause localized hotspots that destroy photovoltaic solar cells and trigger electrical fires.
                        </p>
                    </div>

                    <div class="p-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg mb-3">
                            <font-awesome-icon icon="battery-three-quarters" />
                        </div>
                        <h4 class="font-bold text-navy-950 text-sm">Lithium Health Monitoring</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            We check cell balancing (BMS logs), firmware updates, state of charge (SoC) calibration, and ventilation temperatures.
                        </p>
                    </div>

                    <div class="p-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg mb-3">
                            <font-awesome-icon icon="shield-check" />
                        </div>
                        <h4 class="font-bold text-navy-950 text-sm">Priority Dispatch Guarantee</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Subscribers receive priority emergency response within 4 hours in Lagos and Abuja whenever an unexpected outage occurs.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
