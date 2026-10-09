<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { generateWhatappLink } from '@/Utils/whatsappLinkGen';

const page = usePage();

const site = computed(() => page.props.site || {
    whatsapp: '2348031234567',
    phone: '+234 803 123 4567',
});

const whatsappUrl = computed(() => {
    const raw = site.value.whatsapp || '2348031234567';
    const clean = String(raw).replace(/[^0-9]/g, '');
    return generateWhatappLink(clean, 'Hello Zayin Technologies, I want to discuss a solar installation / Starlink setup for my property.');
});
</script>

<template>
    <section class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white py-16 sm:py-20 border-t border-slate-800 relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 via-transparent to-emerald-500/10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                <div class="space-y-3 max-w-2xl">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full inline-block">
                        Ready for 24/7 Power & Superfast Internet?
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                        Get Your Free Energy Audit & System Quotation Today
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        Speak directly with our electrical solar engineers on WhatsApp or fill out our interactive load estimator. No obligation, 100% transparent pricing.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0">
                    <Link 
                        href="/request-quote"
                        class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition transform hover:-translate-y-0.5"
                    >
                        Request Instant Quote
                    </Link>

                    <a 
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-950/60 transition flex items-center justify-center space-x-2"
                    >
                        <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-lg" />
                        <span>Chat With Engineer</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
