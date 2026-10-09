<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { generateWhatappLink } from '@/Utils/whatsappLinkGen';

const page = usePage();

const site = computed(() => page.props.site || {
    name: 'Zayin Technologies',
    phone: '+234 803 123 4567',
    whatsapp: '2348031234567',
    email: 'support@zayin.ng',
    address: 'Abraka, Ethiope East, Delta State, Nigeria',
});

const whatsappLink = computed(() => {
    const raw = site.value.whatsapp || '2348031234567';
    const clean = String(raw).replace(/[^0-9]/g, '');
    return generateWhatappLink(clean, 'Hello Zayin Technologies, I would like to make an enquiry.');
});

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
    <footer class="bg-slate-950 text-slate-300 border-t-4 border-amber-500 relative overflow-hidden font-sans">
        <!-- Ambient background glows -->
        <div class="absolute -top-40 right-0 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 left-0 w-96 h-96 bg-sky-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 relative z-10">
            <!-- Top Grid: 4 Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-14 border-b border-slate-800">
                <!-- Col 1: Brand & Bio (4 cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <Link href="/" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-900 to-amber-500 flex items-center justify-center text-white shadow-md">
                            <font-awesome-icon :icon="['fas', 'bolt']" class="text-xl text-amber-300" />
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight text-white flex items-center">
                                ZAYIN<span class="text-amber-500 ml-1">TECH</span>
                            </span>
                            <span class="text-xs font-semibold text-amber-400 tracking-wider uppercase block">
                                Solar · Starlink · DSTV
                            </span>
                        </div>
                    </Link>

                    <p class="text-slate-400 text-sm leading-relaxed pr-4">
                        Engineered solar power, high-speed Starlink satellite networking, and certified DSTV satellite solutions for homes, estates, and corporate facilities across Nigeria.
                    </p>

                    <!-- Trust indicators -->
                    <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-300">
                        <span class="px-3 py-1 rounded-full bg-slate-900 border border-slate-800 flex items-center gap-1.5">
                            <font-awesome-icon :icon="['fas', 'shield-halved']" class="text-amber-400" />
                            Up to 5 Yrs Warranty
                        </span>
                        <span class="px-3 py-1 rounded-full bg-slate-900 border border-slate-800 flex items-center gap-1.5">
                            <font-awesome-icon :icon="['fas', 'certificate']" class="text-emerald-400" />
                            Certified Technicians
                        </span>
                    </div>

                    <!-- Social Icons -->
                    <div class="flex items-center space-x-3 pt-2">
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:text-white hover:bg-amber-500 hover:border-amber-500 transition" aria-label="Instagram">
                            <font-awesome-icon :icon="['fab', 'instagram']" />
                        </a>
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:text-white hover:bg-amber-500 hover:border-amber-500 transition" aria-label="Facebook">
                            <font-awesome-icon :icon="['fab', 'facebook']" />
                        </a>
                        <a :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 transition" aria-label="WhatsApp">
                            <font-awesome-icon :icon="['fab', 'whatsapp']" />
                        </a>
                    </div>
                </div>

                <!-- Col 2: Engineering Services (3 cols) -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="text-white text-sm font-bold uppercase tracking-wider border-b border-amber-500/40 pb-2 inline-block">
                        Services
                    </h3>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <Link href="/services/solar-installation" class="text-slate-400 hover:text-amber-400 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Solar & Inverter Setup</span>
                            </Link>
                        </li>
                        <li>
                            <Link href="/services/solar-maintenance" class="text-slate-400 hover:text-amber-400 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Solar Panel Cleaning & Audit</span>
                            </Link>
                        </li>
                        <li>
                            <Link href="/solar-calculator" class="text-amber-400 font-bold hover:text-amber-300 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                <span>Solar Load Calculator</span>
                            </Link>
                        </li>
                        <li>
                            <Link href="/services/starlink-installation" class="text-slate-400 hover:text-amber-400 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                <span>Starlink Satellite Internet</span>
                            </Link>
                        </li>
                        <li>
                            <Link href="/services/dstv-installation" class="text-slate-400 hover:text-amber-400 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                <span>DSTV Installation & Extra View</span>
                            </Link>
                        </li>
                        <li>
                            <Link href="/maintenance-plans" class="text-slate-400 hover:text-amber-400 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>Annual Maintenance Plans</span>
                            </Link>
                        </li>
                        <li>
                            <Link href="/projects" class="text-slate-400 hover:text-amber-400 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                <span>Proof of Work & Videos</span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Packages & Store (2 cols) -->
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="text-white text-sm font-bold uppercase tracking-wider border-b border-amber-500/40 pb-2 inline-block">
                        Store
                    </h3>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <Link href="/packages/solar" class="text-slate-400 hover:text-amber-400 transition">
                                Solar Packages
                            </Link>
                        </li>
                        <li>
                            <Link href="/packages/starlink" class="text-slate-400 hover:text-amber-400 transition">
                                Starlink Kits & Masts
                            </Link>
                        </li>
                        <li>
                            <Link href="/packages/dstv" class="text-slate-400 hover:text-amber-400 transition">
                                DSTV Decoders & Dishes
                            </Link>
                        </li>
                        <li>
                            <Link href="/packages/accessories" class="text-slate-400 hover:text-amber-400 transition">
                                Lithium Batteries
                            </Link>
                        </li>
                        <li>
                            <Link href="/request-quote" class="text-amber-400 font-bold hover:text-amber-300 transition">
                                Request a Quote
                            </Link>
                        </li>
                        <li>
                            <Link href="/book-installation" class="text-slate-400 hover:text-amber-400 transition">
                                Book Site Survey
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Nationwide Coverage (3 cols) -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="text-white text-sm font-bold uppercase tracking-wider border-b border-amber-500/40 pb-2 inline-block">
                        Contact & Offices
                    </h3>
                    <div class="space-y-3 text-sm text-slate-400">
                        <div class="flex items-start space-x-3">
                            <font-awesome-icon :icon="['fas', 'location-dot']" class="text-amber-400 mt-1 shrink-0" />
                            <div>
                                <span class="font-medium text-white block">{{ site.address }}</span>
                                <span class="text-xs text-amber-400 font-semibold inline-flex items-center gap-1.5 mt-1">
                                    <font-awesome-icon :icon="['fas', 'truck-fast']" />
                                    Nationwide Service (36 States + FCT)
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <font-awesome-icon :icon="['fas', 'phone']" class="text-amber-400 shrink-0" />
                            <a :href="`tel:${site.phone}`" class="hover:text-white transition font-medium">{{ site.phone }}</a>
                        </div>
                        <div class="flex items-center space-x-3">
                            <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-emerald-400 shrink-0" />
                            <a :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="hover:text-white transition">{{ site.whatsapp }}</a>
                        </div>
                        <div class="flex items-center space-x-3">
                            <font-awesome-icon :icon="['fas', 'envelope']" class="text-amber-400 shrink-0" />
                            <a :href="`mailto:${site.email}`" class="hover:text-white transition">{{ site.email }}</a>
                        </div>

                        <!-- Payment Trust Badges -->
                        <div class="pt-2 border-t border-slate-800">
                            <span class="text-xs text-slate-500 uppercase tracking-wider block mb-1">Accepted Payments</span>
                            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-300">
                                <span class="bg-slate-900 border border-slate-800 px-2 py-1 rounded">Paystack</span>
                                <span class="bg-slate-900 border border-slate-800 px-2 py-1 rounded">Card & USSD</span>
                                <span class="bg-slate-900 border border-slate-800 px-2 py-1 rounded">Bank Transfer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Copyright, Legal & Back to Top -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <div>
                    &copy; {{ new Date().getFullYear() }} {{ site.name }}. All rights reserved.
                </div>

                <div class="flex flex-wrap items-center gap-6">
                    <Link href="/privacy-policy" class="hover:text-slate-300 transition">Privacy Policy (NDPA)</Link>
                    <Link href="/terms" class="hover:text-slate-300 transition">Terms of Service</Link>
                    <Link href="/warranty" class="hover:text-slate-300 transition">Warranty Guarantee</Link>
                    <Link href="/refund-policy" class="hover:text-slate-300 transition">Refunds</Link>
                </div>

                <button 
                    @click="scrollToTop"
                    class="flex items-center space-x-2 hover:text-amber-400 transition"
                    aria-label="Back to top"
                >
                    <span>Back to Top</span>
                    <font-awesome-icon :icon="['fas', 'arrow-up']" />
                </button>
            </div>
        </div>
    </footer>
</template>
