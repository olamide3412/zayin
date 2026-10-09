<script setup>
import { Link } from '@inertiajs/vue3';

const reasons = [
    {
        title: 'Tier-1 Hardware & Grade-A Lithium',
        desc: 'We never compromise with refurbished cells. All solar inverters, lithium batteries, and Starlink hardware come with authentic manufacturer warranties.',
        icon: 'certificate',
        badge: '100% Genuine',
    },
    {
        title: 'Engineered for Nigerian Tropical Heat',
        desc: 'Our installations incorporate heavy-duty DC surge protectors, lightning arrestors, and oversized aluminum cooling vents built for local humidity and dust.',
        icon: 'shield-halved',
        badge: 'Safety First',
    },
    {
        title: 'Zero-Obstruction Starlink Masts',
        desc: 'We engineer rust-proof steel masts that clear high walls and tree canopies, delivering maximum satellite visibility and steady 200+ Mbps throughput.',
        icon: 'satellite-dish',
        badge: 'Peak Speeds',
    },
    {
        title: 'Meter-Calibrated DSTV Extra View',
        desc: 'We eliminate rain fade and tricky E48-32 signal loss using high-gain 90cm metal dishes, smart LNBs, and digital spectrum analyzers.',
        icon: 'tv',
        badge: '98%+ Signal',
    },
    {
        title: 'Transparent Pricing in Naira (₦)',
        desc: 'No hidden installation surcharges or surprise cable costs. What you see on our quotation is your fixed, complete turnkey price.',
        icon: 'file-invoice-dollar',
        badge: 'No Hidden Fees',
    },
    {
        title: 'Nationwide Logistics & Local Teams',
        desc: 'Headquartered in Abraka, Delta State, with rapid dispatch teams and insured equipment freight servicing all 36 Nigerian states and FCT.',
        icon: 'truck-fast',
        badge: 'Nationwide',
    },
];
</script>

<template>
    <section class="py-16 sm:py-24 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 border border-amber-500/20 px-3.5 py-1.5 rounded-full inline-block mb-3">
                    The Zayin Standard
                </span>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                    Why Homeowners & Businesses Rely on Us
                </h2>
                <p class="mt-4 text-slate-300 text-base leading-relaxed">
                    Engineering rigor, certified personnel, and transparent communication from day one.
                </p>
            </div>

            <!-- Reasons Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="(item, idx) in reasons" 
                    :key="item.title"
                    class="rounded-3xl bg-slate-800/70 border border-slate-700/80 p-8 hover:border-amber-500/70 hover:bg-slate-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg group"
                    data-aos="fade-up"
                    :data-aos-delay="idx * 80"
                >
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors">
                            <font-awesome-icon :icon="['fas', item.icon]" />
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-900 border border-slate-700 text-amber-300">
                            {{ item.badge }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-amber-400 transition">
                        {{ item.title }}
                    </h3>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ item.desc }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
