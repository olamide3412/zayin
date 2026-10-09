<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    constants: Object,
    inverter_catalog: Array,
    controller_ratings: Array,
    psh_by_zone: Object,
    presets: Array,
    categories: Array,
});

const activeTab = ref('constants'); // 'constants', 'inverters', 'presets'

// Form for constants & inverters
const configForm = useForm({
    constants: { ...props.constants },
    inverter_catalog: [...props.inverter_catalog],
    controller_ratings: [...props.controller_ratings],
    psh_by_zone: { ...props.psh_by_zone },
});

const saveSettings = () => {
    configForm.post(route('admin.solar-settings.update'), {
        preserveScroll: true,
    });
};

// Inverter catalog row management
const addInverterRow = () => {
    configForm.inverter_catalog.push({ kva: 1.0, voltage: 12 });
};
const removeInverterRow = (idx) => {
    configForm.inverter_catalog.splice(idx, 1);
};

// Preset quick edits
const savingPresetId = ref(null);
const savePreset = (preset) => {
    savingPresetId.value = preset.id;
    router.patch(route('admin.solar-settings.update-preset', preset.id), {
        name: preset.name,
        watts: preset.watts,
        surge_multiplier: preset.surge_multiplier,
        default_hours: preset.default_hours,
        is_resistive: preset.is_resistive,
        is_active: preset.is_active,
    }, {
        preserveScroll: true,
        onFinish: () => {
            savingPresetId.value = null;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Solar Calculator Configuration" />

        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Solar Calculator Engine Configuration</h1>
                <p class="text-sm text-slate-500 mt-0.5">Configure engineering coefficients, inverter voltages, solar derating, and starter appliance presets.</p>
            </div>
            <button 
                v-if="activeTab !== 'presets'"
                @click="saveSettings" 
                :disabled="configForm.processing"
                class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm disabled:opacity-50"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ configForm.processing ? 'Saving...' : 'Save Engine Parameters' }}</span>
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200 mb-6">
            <button 
                @click="activeTab = 'constants'" 
                class="px-4 py-2.5 text-sm font-bold border-b-2 transition"
                :class="activeTab === 'constants' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-900'"
            >
                Engineering Constants & Derating
            </button>
            <button 
                @click="activeTab = 'inverters'" 
                class="px-4 py-2.5 text-sm font-bold border-b-2 transition"
                :class="activeTab === 'inverters' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-900'"
            >
                Inverter Catalog & Bus Voltages
            </button>
            <button 
                @click="activeTab = 'presets'" 
                class="px-4 py-2.5 text-sm font-bold border-b-2 transition"
                :class="activeTab === 'presets' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-500 hover:text-slate-900'"
            >
                Appliance Presets ({{ presets.length }})
            </button>
        </div>

        <!-- Tab 1: Engineering Constants -->
        <div v-show="activeTab === 'constants'" class="space-y-6 max-w-4xl">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-5">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Load & Battery Sizing Coefficients</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Safety / Surge Factor</label>
                        <input 
                            v-model.number="configForm.constants.safety_factor" 
                            type="number" 
                            step="0.05"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Default 1.25 (+25% continuous buffer).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Power Factor (cos φ)</label>
                        <input 
                            v-model.number="configForm.constants.power_factor" 
                            type="number" 
                            step="0.05"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Default 0.80 for inductive household appliances.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Inverter Efficiency (η_inv)</label>
                        <input 
                            v-model.number="configForm.constants.inverter_efficiency" 
                            type="number" 
                            step="0.01"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Default 0.90 (90% pure sine conversion efficiency).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Solar PV System Derate (η_derate)</label>
                        <input 
                            v-model.number="configForm.constants.system_derate" 
                            type="number" 
                            step="0.01"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Default 0.75 (dust, temperature coefficient, cable losses).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Lithium LiFePO4 Depth of Discharge (DoD)</label>
                        <input 
                            v-model.number="configForm.constants.dod_lithium" 
                            type="number" 
                            step="0.05"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Default 0.80 (80% usable capacity).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tubular / Lead-Acid Depth of Discharge (DoD)</label>
                        <input 
                            v-model.number="configForm.constants.dod_tubular" 
                            type="number" 
                            step="0.05"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                        <p class="text-[11px] text-slate-400 mt-1">Default 0.50 (50% usable to protect battery life).</p>
                    </div>
                </div>
            </div>

            <!-- Regional Peak Sun Hours -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Geopolitical Peak Sun Hours (PSH)</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div v-for="(val, zone) in configForm.psh_by_zone" :key="zone">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">{{ zone.replace('_', ' ') }}</label>
                        <input 
                            v-model.number="configForm.psh_by_zone[zone]" 
                            type="number" 
                            step="0.1" 
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Inverter Catalog -->
        <div v-show="activeTab === 'inverters'" class="space-y-6 max-w-3xl">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="text-base font-bold text-slate-900">Inverter Sizing Catalog</h2>
                    <button 
                        @click="addInverterRow"
                        class="text-xs font-bold text-amber-600 hover:text-amber-700"
                    >
                        + Add Inverter Tier
                    </button>
                </div>

                <div class="space-y-3">
                    <div 
                        v-for="(inv, idx) in configForm.inverter_catalog" 
                        :key="idx"
                        class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200"
                    >
                        <div class="flex-1">
                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Capacity (kVA)</label>
                            <input 
                                v-model.number="inv.kva" 
                                type="number" 
                                step="0.5" 
                                class="w-full px-3 py-1.5 text-xs font-bold text-slate-900 bg-white border border-slate-200 rounded-lg" 
                            />
                        </div>
                        <div class="flex-1">
                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Battery DC Bus Voltage (V)</label>
                            <select 
                                v-model.number="inv.voltage" 
                                class="w-full px-3 py-1.5 text-xs font-bold text-slate-900 bg-white border border-slate-200 rounded-lg"
                            >
                                <option :value="12">12V DC</option>
                                <option :value="24">24V DC</option>
                                <option :value="48">48V DC</option>
                            </select>
                        </div>
                        <button 
                            @click="removeInverterRow(idx)"
                            class="mt-4 p-2 text-slate-400 hover:text-red-600 rounded-lg"
                            title="Remove Inverter"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Appliance Presets -->
        <div v-show="activeTab === 'presets'" class="space-y-4">
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/75 text-xs text-slate-500 font-bold uppercase tracking-wider">
                                <th class="px-5 py-3.5">Appliance Name</th>
                                <th class="px-4 py-3.5">Category</th>
                                <th class="px-4 py-3.5">Power (Watts)</th>
                                <th class="px-4 py-3.5">Surge Multiplier</th>
                                <th class="px-4 py-3.5">Default Hours / Day</th>
                                <th class="px-4 py-3.5 text-center">Resistive Heating</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="preset in presets" :key="preset.id" class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-3 font-bold text-slate-900">
                                    <input 
                                        v-model="preset.name" 
                                        type="text" 
                                        class="px-2 py-1 text-xs font-bold border border-slate-200 rounded-lg w-full max-w-xs" 
                                    />
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs font-semibold">
                                    {{ preset.category?.name || 'General' }}
                                </td>
                                <td class="px-4 py-3">
                                    <input 
                                        v-model.number="preset.watts" 
                                        type="number" 
                                        class="w-24 px-2 py-1 text-xs font-bold text-slate-900 border border-slate-200 rounded-lg text-center" 
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <input 
                                        v-model.number="preset.surge_multiplier" 
                                        type="number" 
                                        step="0.5" 
                                        class="w-20 px-2 py-1 text-xs font-bold text-slate-900 border border-slate-200 rounded-lg text-center" 
                                    />
                                </td>
                                <td class="px-4 py-3">
                                    <input 
                                        v-model.number="preset.default_hours" 
                                        type="number" 
                                        step="0.5" 
                                        class="w-20 px-2 py-1 text-xs font-bold text-slate-900 border border-slate-200 rounded-lg text-center" 
                                    />
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input 
                                        v-model="preset.is_resistive" 
                                        type="checkbox" 
                                        class="w-4 h-4 rounded text-amber-500" 
                                    />
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button 
                                        @click="savePreset(preset)" 
                                        :disabled="savingPresetId === preset.id"
                                        class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition disabled:opacity-50"
                                    >
                                        <span v-if="savingPresetId === preset.id">Saving...</span>
                                        <span v-else>Save</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
