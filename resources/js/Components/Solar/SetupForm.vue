<script setup>
import { ref } from 'vue';
import { ZONE_LABELS, PSH_BY_ZONE } from '@/lib/solar/constants';

const props = defineProps({
    setup: {
        type: Object,
        required: true,
    },
});

const showAdvanced = ref(false);
</script>

<template>
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-8">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-2">
                Step 2 of 3
            </span>
            <h2 class="text-2xl font-black text-navy-950 tracking-tight">Your Power Goal & System Preferences</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Tell us how long you need battery backup and where in Nigeria your site is located.
            </p>
        </div>

        <!-- 1. Backup Hours Slider -->
        <div class="space-y-3 p-5 rounded-2xl bg-slate-50 border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-bold text-navy-950">
                        Desired Battery Backup Duration
                    </label>
                    <span class="text-xs text-slate-500">
                        How many hours must the battery power your appliances when grid (NEPA) or sun is unavailable?
                    </span>
                </div>
                <div class="px-3.5 py-1.5 rounded-xl bg-amber-500 text-navy-950 font-black text-lg sm:text-xl shrink-0 shadow-sm">
                    {{ setup.backup_hours }} hrs
                </div>
            </div>

            <input
                v-model.number="setup.backup_hours"
                type="range"
                min="1"
                max="24"
                step="1"
                class="w-full accent-amber-500 h-2.5 bg-slate-200 rounded-lg cursor-pointer"
            />

            <div class="flex justify-between text-[11px] font-semibold text-slate-400">
                <span>1 hr (Emergency)</span>
                <span>8 hrs (Workday)</span>
                <span>12 hrs (Overnight)</span>
                <span>24 hrs (Full 24/7)</span>
            </div>
        </div>

        <!-- 2. System Goal Radio -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-navy-950">Operating Strategy</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label
                    :class="[
                        'p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5',
                        setup.goal === 'backup'
                            ? 'border-amber-500 bg-amber-500/5 ring-1 ring-amber-500'
                            : 'border-slate-200 hover:border-slate-300 bg-white'
                    ]"
                >
                    <input v-model="setup.goal" type="radio" value="backup" class="mt-1 text-amber-500" />
                    <div>
                        <div class="font-bold text-navy-950 text-sm">Backup for Outages</div>
                        <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            Primary focus is keeping appliances alive during power cuts. Solar panels recharge the battery bank quickly.
                        </div>
                    </div>
                </label>

                <label
                    :class="[
                        'p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5',
                        setup.goal === 'full_solar'
                            ? 'border-amber-500 bg-amber-500/5 ring-1 ring-amber-500'
                            : 'border-slate-200 hover:border-slate-300 bg-white'
                    ]"
                >
                    <input v-model="setup.goal" type="radio" value="full_solar" class="mt-1 text-amber-500" />
                    <div>
                        <div class="font-bold text-navy-950 text-sm">Full Off-Grid / Solar First</div>
                        <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            Generate enough solar power to cover 100% of your daily electricity needs, slashing generator petrol costs to zero.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- 3. Battery Chemistry -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-navy-950">Battery Chemistry</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label
                    :class="[
                        'p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5',
                        setup.battery_type === 'lithium'
                            ? 'border-amber-500 bg-amber-500/5 ring-1 ring-amber-500'
                            : 'border-slate-200 hover:border-slate-300 bg-white'
                    ]"
                >
                    <input v-model="setup.battery_type" type="radio" value="lithium" class="mt-1 text-amber-500" />
                    <div>
                        <div class="font-bold text-navy-950 text-sm flex items-center gap-2">
                            <span>Lithium (LiFePO4)</span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                Recommended
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            80% usable depth of discharge, 10+ years lifespan, rapid 2-hour recharge, wall-mount form factor.
                        </div>
                    </div>
                </label>

                <label
                    :class="[
                        'p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5',
                        setup.battery_type === 'tubular'
                            ? 'border-amber-500 bg-amber-500/5 ring-1 ring-amber-500'
                            : 'border-slate-200 hover:border-slate-300 bg-white'
                    ]"
                >
                    <input v-model="setup.battery_type" type="radio" value="tubular" class="mt-1 text-amber-500" />
                    <div>
                        <div class="font-bold text-navy-950 text-sm">Tubular Deep Cycle (Lead-Acid)</div>
                        <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            50% usable depth of discharge. Lower upfront investment, 2–3 year lifespan, requires larger physical bank.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- 4. Location Zone in Nigeria -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-bold text-navy-950">Installation Region in Nigeria</label>
                <span class="text-xs text-amber-600 font-bold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                    Sun Irradiance: {{ PSH_BY_ZONE[setup.zone] || 4.5 }} Peak Sun Hours/day
                </span>
            </div>
            <select
                v-model="setup.zone"
                class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm bg-white font-medium text-slate-800 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
            >
                <option v-for="(label, key) in ZONE_LABELS" :key="key" :value="key">
                    {{ label }}
                </option>
            </select>
        </div>

        <!-- Advanced Collapsible Settings -->
        <div class="border-t border-slate-200 pt-4">
            <button
                type="button"
                @click="showAdvanced = !showAdvanced"
                class="text-xs font-bold text-slate-600 hover:text-navy-950 flex items-center gap-2 transition"
            >
                <font-awesome-icon :icon="showAdvanced ? 'chevron-up' : 'chevron-down'" />
                <span>{{ showAdvanced ? 'Hide Advanced Sizing Engineering Options' : 'Show Advanced Engineering Options (Autonomy, Panel Size, Custom PSH)' }}</span>
            </button>

            <div v-show="showAdvanced" class="mt-4 p-5 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Days of Autonomy</label>
                    <select v-model.number="setup.days_of_autonomy" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                        <option :value="1">1 Day (Standard Nigeria)</option>
                        <option :value="2">2 Days (Rainy season backup)</option>
                        <option :value="3">3 Days (Critical off-grid infrastructure)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Preferred Panel Wattage</label>
                    <select v-model.number="setup.panel_watts" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                        <option :value="550">550W Tier-1 Mono Half-Cut (Standard)</option>
                        <option :value="450">450W Mono Half-Cut</option>
                        <option :value="400">400W High Efficiency</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Diversity Factor</label>
                    <select v-model.number="setup.diversity_factor" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white">
                        <option :value="1.0">1.0 (All appliances on together)</option>
                        <option :value="0.9">0.9 (90% coincident load)</option>
                        <option :value="0.8">0.8 (80% coincident load)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>
