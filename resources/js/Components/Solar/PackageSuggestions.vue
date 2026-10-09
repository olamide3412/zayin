<script setup>
import { Link } from '@inertiajs/vue3';
import { formatNaira } from '@/Utils/formatCurrency';

defineProps({
    packages: {
        type: Array,
        default: () => [],
    },
    calculationUuid: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['requestQuote']);
</script>

<template>
    <div id="packages-section" class="space-y-6 pt-4">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                Turnkey Hardware Options
            </span>
            <h3 class="text-2xl sm:text-3xl font-black text-navy-950 mt-2">
                Engineered Packages Matching Your Load Profile
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">
                All packages include Tier-1 equipment, balance-of-system safety enclosures, nationwide freight, and certified installation.
            </p>
        </div>

        <!-- If packages are matched -->
        <div v-if="packages.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                v-for="pkg in packages"
                :key="pkg.id"
                :class="[
                    'bg-white rounded-3xl p-6 sm:p-7 border flex flex-col justify-between transition-all duration-300 relative shadow-sm hover:shadow-xl',
                    pkg.tier === 'better'
                        ? 'border-2 border-amber-500 ring-2 ring-amber-500/20'
                        : 'border-slate-200 hover:border-slate-300'
                ]"
            >
                <div v-if="pkg.tier === 'better'" class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-amber-500 text-navy-950 font-black text-[10px] uppercase tracking-wider shadow-md">
                    Most Popular Fit
                </div>

                <div>
                    <!-- Tier Badge -->
                    <div class="flex items-center justify-between mb-3">
                        <span
                            :class="[
                                'text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full',
                                pkg.tier === 'best'
                                    ? 'bg-purple-100 text-purple-900 border border-purple-200'
                                    : pkg.tier === 'better'
                                    ? 'bg-amber-100 text-amber-900 border border-amber-300'
                                    : 'bg-slate-100 text-slate-800'
                            ]"
                        >
                            {{ pkg.tier_label }}
                        </span>
                    </div>

                    <!-- Package Image & Title -->
                    <div class="aspect-video w-full rounded-2xl overflow-hidden mb-4 bg-slate-100 relative">
                        <img
                            :src="pkg.image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=600&auto=format&fit=crop'"
                            :alt="pkg.name"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                    </div>

                    <h4 class="text-base sm:text-lg font-black text-navy-950 leading-snug mb-2">
                        {{ pkg.name }}
                    </h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4 line-clamp-2">
                        {{ pkg.summary }}
                    </p>

                    <!-- Technical specs chips -->
                    <div class="flex flex-wrap gap-1.5 mb-5 text-[11px] font-semibold text-slate-700">
                        <span v-if="pkg.specs?.capacity_kva" class="px-2 py-1 rounded-lg bg-slate-100">
                            {{ pkg.specs.capacity_kva }} kVA Inverter
                        </span>
                        <span v-if="pkg.specs?.battery_kwh" class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-bold">
                            {{ pkg.specs.battery_kwh }} kWh Storage
                        </span>
                        <span v-if="pkg.specs?.panels_w && pkg.specs.panels_w > 0" class="px-2 py-1 rounded-lg bg-sky-50 text-sky-800 font-bold">
                            {{ pkg.specs.panels_w }}W Solar Panels
                        </span>
                    </div>
                </div>

                <!-- Price and CTA -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Turnkey Installed Price</div>
                        <div class="text-xl sm:text-2xl font-black text-navy-950">
                            {{ formatNaira(pkg.price) }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Link
                            :href="`/packages/${pkg.slug}`"
                            class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-black text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center gap-1.5 active:scale-95 text-center"
                        >
                            <span>View Full Package & Order</span>
                            <span>&rarr;</span>
                        </Link>

                        <button
                            type="button"
                            @click="emit('requestQuote', pkg)"
                            class="w-full py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition border border-slate-200"
                        >
                            Request Custom Quote For This
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fallback if no exact package fits -->
        <div v-else class="p-8 sm:p-10 rounded-3xl bg-slate-50 border border-slate-200 text-center space-y-4 max-w-2xl mx-auto">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mx-auto">
                <font-awesome-icon icon="compass-drafting" />
            </div>
            <h4 class="text-xl font-bold text-navy-950">
                Your Setup Requires a Bespoke Engineering Sizing
            </h4>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-lg mx-auto">
                Your appliances or heavy surge draws need custom high-voltage stringing or parallel inverters. Our senior solar engineers will design a custom single-line diagram and tailored proposal for your property.
            </p>
            <button
                type="button"
                @click="emit('requestQuote', null)"
                class="px-8 py-3.5 bg-amber-500 hover:bg-amber-400 text-navy-950 font-black rounded-xl text-xs uppercase tracking-wider transition shadow-lg active:scale-95 inline-flex items-center gap-2"
            >
                <font-awesome-icon icon="file-invoice" />
                <span>Request Custom Turnkey Proposal</span>
            </button>
        </div>
    </div>
</template>
