<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    services: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <section class="py-16 sm:py-24 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-14" data-aos="fade-up">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-100/80 px-3.5 py-1.5 rounded-full inline-block mb-3">
                    What We Do
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Specialized Engineering & Connectivity Solutions
                </h2>
                <p class="mt-4 text-slate-600 text-base leading-relaxed">
                    Designed and deployed by certified engineers using top-tier hardware built for Nigeria's environmental and power conditions.
                </p>
            </div>

            <!-- Pillars Grid (3 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1: Solar -->
                <div 
                    class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group flex flex-col justify-between"
                    data-aos="fade-up"
                    data-aos-delay="100"
                >
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl mb-6 group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors duration-300">
                            <font-awesome-icon :icon="['fas', 'solar-panel']" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-amber-600 transition">
                            Solar Energy & Power Backup
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">
                            Clean, uninterrupted pure sine wave inverter power paired with long-life LiFePO4 lithium batteries and tier-1 solar panels. Cut out fuel expenses completely.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium mb-6">
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>Zero-second cutover (no flicker or reboot)</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>LiFePO4 lithium batteries (6,000+ cycles)</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>Panel washing & preventive maintenance</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <Link href="/services/solar-installation" class="text-sm font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1.5 group-hover:gap-2.5 transition-all">
                            <span>Explore Solar</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" />
                        </Link>
                        <Link href="/packages/solar" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Packages &rarr;
                        </Link>
                    </div>
                </div>

                <!-- Pillar 2: Starlink & Networking -->
                <div 
                    class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group flex flex-col justify-between"
                    data-aos="fade-up"
                    data-aos-delay="200"
                >
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-sky-500/10 text-sky-600 flex items-center justify-center text-2xl mb-6 group-hover:bg-sky-500 group-hover:text-white transition-colors duration-300">
                            <font-awesome-icon :icon="['fas', 'satellite-dish']" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-sky-600 transition">
                            Starlink & Enterprise Wi-Fi
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">
                            Lightning-fast 100Mbps–250Mbps satellite broadband anywhere in Nigeria. Elevated custom steel pipe mounts for 100% zero obstruction and seamless mesh distribution.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium mb-6">
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>Heavy-duty galvanized steel pipe masts</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>Multi-room / duplex Wi-Fi mesh coverage</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>UV weather-proof cabling with drip loops</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <Link href="/services/starlink-installation" class="text-sm font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1.5 group-hover:gap-2.5 transition-all">
                            <span>Explore Starlink</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" />
                        </Link>
                        <Link href="/packages/starlink" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Kits & Masts &rarr;
                        </Link>
                    </div>
                </div>

                <!-- Pillar 3: DSTV & Satellite TV -->
                <div 
                    class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group flex flex-col justify-between"
                    data-aos="fade-up"
                    data-aos-delay="300"
                >
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-2xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <font-awesome-icon :icon="['fas', 'tv']" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-blue-600 transition">
                            DSTV & Satellite TV Solutions
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">
                            Precision digital signal peaking, certified Extra View multi-room setups, and rapid resolution for E48-32 signal loss, rain fade, and faulty LNBs.
                        </p>
                        <ul class="space-y-2.5 text-xs text-slate-700 font-medium mb-6">
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>98%+ signal strength digital meter alignment</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>Extra View dual/triple decoder pairing</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-500 shrink-0" />
                                <span>Smart LNB & Explora Ultra 4K setups</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <Link href="/services/dstv-installation" class="text-sm font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 group-hover:gap-2.5 transition-all">
                            <span>Explore DSTV</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" />
                        </Link>
                        <Link href="/packages/dstv" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Decoders &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
