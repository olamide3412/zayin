<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    seo: {
        type: Object,
        default: () => ({}),
    },
});

const stats = [
    { value: '500+', label: 'Clean Energy Sites Delivered' },
    { value: '350+', label: 'Starlink & Enterprise Networks' },
    { value: '36 + FCT', label: 'Nationwide State Coverage' },
    { value: '99.8%', label: 'Uptime Reliability Index' },
];

const pillars = [
    {
        icon: 'solar-panel',
        title: 'Tier-1 Renewable Energy',
        desc: 'We engineer solar and battery energy storage systems (BESS) designed specifically for the intense heat, surge conditions, and grid unreliability of Nigeria.',
    },
    {
        icon: 'satellite-dish',
        title: 'High-Throughput Connectivity',
        desc: 'Certified installers of Starlink satellite terminals, enterprise UniFi mesh networks, structured fiber cabling, and bonded cellular failover.',
    },
    {
        icon: 'tv',
        title: 'Commercial Satellite & Audio/Visual',
        desc: 'Precision DSTV multi-switch and SMATV systems for estates, hotels, and luxury residences with zero rain-fade engineering.',
    },
    {
        icon: 'shield-halved',
        title: 'Safety & Regulatory Compliance',
        desc: 'All installations are designed with dual AC/DC surge protection devices (SPDs), dedicated copper earthing rods, and fire-retardant conduit channels.',
    },
];

const milestones = [
    {
        year: '2019',
        title: 'Engineering Inception',
        desc: 'Founded by senior electrical and telecom engineers in Delta State to eliminate power and connectivity downtime for Nigerian enterprises and residences.',
    },
    {
        year: '2021',
        title: 'Solar & Storage Expansion',
        desc: 'Partnered with global Tier-1 manufacturers (Victron, Deye, Felicity) to provide 5-year warranty lithium-iron phosphate battery solutions across the Niger Delta and South-West.',
    },
    {
        year: '2023',
        title: 'SpaceX Starlink Specialization',
        desc: 'Pioneered custom mast fabrications, lightning protection, and enterprise multi-WAN integrations for Starlink in rural and urban Nigeria.',
    },
    {
        year: 'Today',
        title: 'Pan-Nigerian Fleet Operations',
        desc: 'Headquartered in Abraka, Ethiope East, Delta State, with full-time field engineers providing rapid installation and maintenance nationwide across all 36 states and FCT.',
    },
];
</script>

<template>
    <SeoHead :seo="seo" />

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-navy-950 via-navy-900 to-navy-950 text-white py-16 lg:py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(#F59E0B_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-4 border border-amber-500/30">
                    Engineered for Reliability
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                    Powering Nigeria’s Homes &amp; Enterprises with Precision Engineering.
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-300 leading-relaxed">
                    Zayin Technologies is a leading Nigerian engineering and installations firm specializing in high-performance solar power plants, Starlink satellite internet, enterprise Wi-Fi networks, and professional DSTV distribution.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <Link
                        :href="route('quote.create')"
                        class="px-6 py-3.5 bg-amber-500 hover:bg-amber-400 text-navy-950 font-extrabold rounded-xl text-sm transition shadow-md uppercase tracking-wider"
                    >
                        Request Site Audit
                    </Link>
                    <Link
                        :href="route('projects.index')"
                        class="px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-sm transition border border-white/20"
                    >
                        View Completed Projects
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Ribbon -->
    <section class="bg-navy-950 border-y border-slate-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div v-for="stat in stats" :key="stat.label" class="p-4">
                    <div class="text-3xl sm:text-4xl font-black text-amber-400">{{ stat.value }}</div>
                    <div class="text-xs sm:text-sm text-slate-300 font-medium mt-1">{{ stat.label }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Story and Mission -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600">Our Foundation</span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-navy-950 mt-2 tracking-tight">
                        We don't cut corners. We design systems that last a decade.
                    </h2>
                    <p class="mt-6 text-slate-600 leading-relaxed text-sm sm:text-base">
                        The Nigerian environment presents unique electrical challenges: volatile grid voltages, frequent surges, extreme solar irradiance, and high humidity. Ordinary, uncertified setups fail quickly, costing homeowners and business owners millions in blown inverters and damaged electronics.
                    </p>
                    <p class="mt-4 text-slate-600 leading-relaxed text-sm sm:text-base">
                        At Zayin Technologies, every installation is engineered from first principles. We calculate true starting surge currents (inrush inductive loads), install class-leading DC/AC lightning protection, balance phases with digital meters, and deliver clean, uninterrupted power and gigabit communication.
                    </p>
                    
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="font-bold text-navy-900 text-sm">Certified Engineers</div>
                            <div class="text-xs text-slate-500 mt-1">NEMSA-compliant protocols &amp; strict European safety standards.</div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="font-bold text-navy-900 text-sm">Genuine Tier-1 Hardware</div>
                            <div class="text-xs text-slate-500 mt-1">100% manufacturer verified cells and OEM components.</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-100 aspect-4/3 bg-slate-900">
                        <img 
                            src="https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=1200&auto=format&fit=crop" 
                            alt="Solar Engineering by Zayin Technologies" 
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="absolute -bottom-6 -right-6 hidden sm:block p-6 rounded-2xl bg-amber-500 text-navy-950 shadow-xl max-w-xs font-bold text-sm">
                        <div class="flex items-center gap-2 mb-1">
                            <font-awesome-icon icon="certificate" class="text-lg" />
                            <span>Zero Compromise</span>
                        </div>
                        <p class="text-xs text-navy-900 font-medium">Guaranteed performance with genuine warranty certificates handed to every client.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Engineering Pillars -->
    <section class="py-16 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600">Core Capabilities</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-navy-950 mt-2">What Sets Our Engineering Apart</h2>
                <p class="text-slate-600 text-sm sm:text-base mt-3">From residential penthouses to offshore bases and industrial warehouses.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div 
                    v-for="pillar in pillars" 
                    :key="pillar.title"
                    class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md hover:border-amber-400 transition"
                >
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-5">
                        <font-awesome-icon :icon="pillar.icon" />
                    </div>
                    <h3 class="text-base font-bold text-navy-950 mb-2">{{ pillar.title }}</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ pillar.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Milestones Timeline -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-extrabold uppercase tracking-widest text-amber-600">Evolution</span>
                <h2 class="text-2xl sm:text-3xl font-black text-navy-950 mt-2">Our Engineering Journey</h2>
            </div>

            <div class="space-y-8">
                <div 
                    v-for="(item, idx) in milestones" 
                    :key="item.year"
                    class="flex flex-col sm:flex-row items-start gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-200"
                >
                    <div class="px-4 py-2 bg-navy-950 text-amber-400 font-black rounded-xl text-lg sm:text-xl flex-shrink-0">
                        {{ item.year }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-navy-950">{{ item.title }}</h3>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ item.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to action -->
    <section class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-950 text-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight">Ready to Work with Nigeria’s Best?</h2>
            <p class="text-slate-300 text-sm sm:text-base mt-3 max-w-xl mx-auto">
                Schedule an on-site engineer visit or receive a comprehensive load analysis tailored to your specific appliances and site.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <Link
                    :href="route('quote.create')"
                    class="px-8 py-3.5 bg-amber-500 hover:bg-amber-400 text-navy-950 font-black rounded-xl text-sm transition shadow-lg uppercase tracking-wider"
                >
                    Request Free Engineering Quote
                </Link>
                <Link
                    :href="route('contact')"
                    class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-sm transition border border-white/20"
                >
                    Contact Our Office
                </Link>
            </div>
        </div>
    </section>
</template>
