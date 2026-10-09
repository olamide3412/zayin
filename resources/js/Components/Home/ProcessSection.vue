<script setup>
import { Link } from '@inertiajs/vue3';

const steps = [
    {
        num: '01',
        title: 'Load Audit & Consultation',
        desc: 'We review your appliances, surge demands, or satellite obstructions to calculate your exact system requirements.',
        icon: 'calculator',
    },
    {
        num: '02',
        title: 'Itemized Transparent Quote',
        desc: 'You receive a clear proposal in Naira itemizing equipment brands, lithium capacities, and warranty terms without hidden fees.',
        icon: 'file-invoice-dollar',
    },
    {
        num: '03',
        title: 'Engineered Installation',
        desc: 'Our certified technicians carry out clean, concealed trunking, surge protection, and safety earthing in 24–48 hours.',
        icon: 'tools',
    },
    {
        num: '04',
        title: 'Commissioning & Support',
        desc: 'We conduct full load tests, configure your mobile monitoring app, and provide round-the-clock priority technical support.',
        icon: 'handshake',
    },
];
</script>

<template>
    <section class="py-16 sm:py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-100/80 px-3.5 py-1.5 rounded-full inline-block mb-3">
                    How It Works
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Seamless 4-Step Installation Process
                </h2>
                <p class="mt-4 text-slate-600 text-base leading-relaxed">
                    From your initial enquiry to clean handover, here is how we ensure zero disruption and maximum reliability.
                </p>
            </div>

            <!-- Steps Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div 
                    v-for="(step, idx) in steps" 
                    :key="step.num"
                    class="rounded-3xl bg-slate-50 border border-slate-200/80 p-8 relative hover:bg-white hover:border-amber-400 hover:shadow-xl transition-all duration-300 group"
                    data-aos="fade-up"
                    :data-aos-delay="idx * 100"
                >
                    <!-- Step Number Badge -->
                    <div class="text-4xl font-black text-slate-200 group-hover:text-amber-500/30 transition-colors mb-4">
                        {{ step.num }}
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl mb-4 group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors">
                        <font-awesome-icon :icon="['fas', step.icon]" />
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-amber-600 transition">
                        {{ step.title }}
                    </h3>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ step.desc }}
                    </p>
                </div>
            </div>

            <!-- Action Prompt -->
            <div class="mt-12 text-center" data-aos="fade-up">
                <Link 
                    href="/request-quote"
                    class="inline-flex items-center space-x-2 px-8 py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-lg shadow-amber-500/20 transition transform hover:-translate-y-0.5"
                >
                    <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" />
                    <span>Start with a Free Energy Audit</span>
                </Link>
            </div>
        </div>
    </section>
</template>
