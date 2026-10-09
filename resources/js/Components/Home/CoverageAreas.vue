<script setup>
import { Link } from '@inertiajs/vue3';
import { formatNaira } from '@/Utils/formatCurrency';

defineProps({
    keyStates: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <section class="py-16 sm:py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-8 sm:p-12 lg:p-16 text-white border border-slate-800 shadow-2xl relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center relative z-10">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 border border-amber-500/20 px-3.5 py-1.5 rounded-full inline-block">
                            Nationwide Coverage
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                            Active Technicians & Logistics Across Nigeria
                        </h2>
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                            Headquartered in Abraka, Delta State, with engineering fleet deployed across Nigeria. Whether you need a solar installation in Delta or Edo, Starlink elevated in Lagos or Abuja, or multi-room DSTV across the country, our certified engineers deploy directly to your site with fixed logistics fees.
                        </p>
                        
                        <div class="pt-2 flex flex-wrap gap-4 text-xs font-semibold text-slate-300">
                            <div class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'clock']" class="text-amber-400" />
                                <span>24–48 Hours Deployment</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'shield-halved']" class="text-emerald-400" />
                                <span>Transit Insured Equipment</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', 'truck']" class="text-sky-400" />
                                <span>All 36 States + FCT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Key Hubs List -->
                    <div class="lg:col-span-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div 
                            v-for="st in keyStates" 
                            :key="st.id"
                            class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center hover:border-amber-400 transition"
                        >
                            <div class="font-bold text-white text-sm mb-1">{{ st.name }}</div>
                            <div class="text-[10px] text-amber-400 uppercase font-semibold">Delivery from {{ formatNaira(st.delivery_fee) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
