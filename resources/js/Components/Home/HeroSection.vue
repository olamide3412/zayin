<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { generateWhatappLink } from '@/Utils/whatsappLinkGen';

const page = usePage();

const site = computed(() => page.props.site || {
    phone: '+234 803 123 4567',
    whatsapp: '2348031234567',
});

const whatsappUrl = computed(() => {
    const raw = site.value.whatsapp || '2348031234567';
    const clean = String(raw).replace(/[^0-9]/g, '');
    return generateWhatappLink(clean, 'Hello Zayin Technologies, I would like to enquire about your Solar, Starlink or DSTV installation packages.');
});
</script>

<template>
    <section class="relative bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white overflow-hidden py-16 sm:py-24 lg:py-28">
        <!-- Ambient decorative effects -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] sm:w-[900px] h-[400px] bg-gradient-to-r from-amber-500/15 via-sky-500/10 to-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Left Column: Copy & CTAs (7 cols) -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left" data-aos="fade-right">
                    <!-- Badge -->
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs sm:text-sm font-bold tracking-wide">
                        <font-awesome-icon :icon="['fas', 'bolt']" class="text-amber-400" />
                        <span>Nigeria's Trusted Engineering & Tech Partner</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15] text-white">
                        Power Your Home 24/7.
                        <span class="block bg-gradient-to-r from-amber-400 via-amber-300 to-yellow-500 bg-clip-text text-transparent">
                            Connect Faster. Never Cut Out.
                        </span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Say goodbye to soaring generator fuel costs, buffering internet, and frustrating TV signal drops. We install engineered <strong class="text-white font-semibold">Hybrid Solar Systems</strong>, zero-obstruction <strong class="text-white font-semibold">Starlink Satellite Wi-Fi</strong>, and certified <strong class="text-white font-semibold">DSTV Extra View</strong> setups across Nigeria.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <Link 
                            href="/request-quote"
                            class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-base shadow-xl shadow-amber-500/20 hover:shadow-amber-500/30 transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center space-x-2.5"
                        >
                            <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" />
                            <span>Get Free Instant Quote</span>
                        </Link>

                        <a 
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-emerald-600/90 hover:bg-emerald-600 text-white font-bold text-base border border-emerald-500/30 shadow-lg shadow-emerald-950/50 transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2.5"
                        >
                            <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-xl" />
                            <span>Chat on WhatsApp</span>
                        </a>

                        <Link 
                            href="/packages"
                            class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 font-bold text-base border border-slate-700 transition flex items-center justify-center space-x-2"
                        >
                            <span>Browse Packages</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-xs" />
                        </Link>
                    </div>

                    <!-- Trust Stats Bar -->
                    <div class="pt-6 border-t border-slate-800/80 grid grid-cols-3 gap-4 max-w-xl mx-auto lg:mx-0 text-center lg:text-left">
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-amber-400">0 Seconds</div>
                            <div class="text-xs text-slate-400 mt-0.5">Switchover Transfer</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-sky-400">200+ Mbps</div>
                            <div class="text-xs text-slate-400 mt-0.5">Starlink Speeds</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-emerald-400">5 Years</div>
                            <div class="text-xs text-slate-400 mt-0.5">Battery Warranty</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Product Cards / Featured Showcase (5 cols) -->
                <div class="lg:col-span-5 relative" data-aos="fade-left">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Main Preview Card -->
                        <div class="rounded-3xl bg-slate-800/60 backdrop-blur-xl border border-slate-700/60 p-6 shadow-2xl relative overflow-hidden group">
                            <!-- Background Image with Overlay -->
                            <div class="aspect-[16/10] rounded-2xl overflow-hidden mb-5 relative bg-slate-900">
                                <img 
                                    src="https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop" 
                                    alt="5kVA Solar Installation in Lekki Phase 1" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-amber-500 text-slate-950 text-xs font-black uppercase tracking-wider">
                                    Turnkey Package
                                </span>
                                <span class="absolute bottom-3 left-3 text-xs font-semibold text-white bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-full">
                                    <font-awesome-icon :icon="['fas', 'location-dot']" class="text-amber-400 mr-1" />
                                    Lekki Phase 1, Lagos
                                </span>
                            </div>

                            <!-- Card Content -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">5kVA / 48V Premium Hybrid</span>
                                    <span class="text-xs text-slate-400">1HP Inverter AC Ready</span>
                                </div>
                                <h3 class="text-lg font-bold text-white leading-snug">
                                    Complete Household Solar System
                                </h3>
                                <p class="text-xs text-slate-300 leading-relaxed">
                                    Includes 5kVA Inverter, 5.12kWh LiFePO4 Lithium Battery, 6x 550W Tier-1 Panels, DC Protection Box & Turnkey Installation.
                                </p>

                                <div class="pt-3 border-t border-slate-700/60 flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Installed Price</div>
                                        <div class="text-xl font-black text-amber-400">₦3,450,000</div>
                                    </div>
                                    <Link 
                                        href="/packages/solar/5kva-premium-hybrid-solar-system"
                                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-amber-500 hover:text-slate-950 text-white font-bold text-xs transition"
                                    >
                                        View Inclusions &rarr;
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Floating Badge: Starlink Verified -->
                        <div class="absolute -bottom-5 -left-4 sm:-left-6 bg-slate-900/90 backdrop-blur-md border border-slate-700 rounded-2xl p-3 shadow-2xl flex items-center space-x-3 text-xs">
                            <div class="w-9 h-9 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-base">
                                <font-awesome-icon :icon="['fas', 'satellite-dish']" />
                            </div>
                            <div>
                                <div class="font-bold text-white">Starlink Pro Mounts</div>
                                <div class="text-slate-400 text-[10px]">Zero obstruction elevation</div>
                            </div>
                        </div>

                        <!-- Mini Floating Badge: Warranty -->
                        <div class="absolute -top-4 -right-4 sm:-right-6 bg-slate-900/90 backdrop-blur-md border border-slate-700 rounded-2xl p-3 shadow-2xl flex items-center space-x-3 text-xs">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base">
                                <font-awesome-icon :icon="['fas', 'shield-halved']" />
                            </div>
                            <div>
                                <div class="font-bold text-white">Verified Warranty</div>
                                <div class="text-slate-400 text-[10px]">Certified electrical engineers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
