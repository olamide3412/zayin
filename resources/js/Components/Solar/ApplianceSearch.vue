<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    presets: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['addPreset', 'addCustom']);

const searchQuery = ref('');
const selectedCategory = ref('');
const showCustomModal = ref(false);

const customForm = ref({
    name: '',
    watts: 100,
    qty: 1,
    hours: 4,
    surge: 1.0,
    is_resistive: false,
    use_amps_volts: false,
    amps: 0.5,
    volts: 220,
});

const filteredPresets = computed(() => {
    let list = props.presets;
    if (selectedCategory.value) {
        list = list.filter((p) => p.category_id === Number(selectedCategory.value));
    }
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase().trim();
        list = list.filter((p) => p.name.toLowerCase().includes(q) || (p.notes && p.notes.toLowerCase().includes(q)));
    }
    return list;
});

const onAmpsVoltsChange = () => {
    if (customForm.value.use_amps_volts) {
        const computedWatts = Math.round(customForm.value.amps * customForm.value.volts);
        customForm.value.watts = Math.max(1, computedWatts);
    }
};

const submitCustom = () => {
    if (!customForm.value.name.trim()) return;
    emit('addCustom', {
        name: customForm.value.name.trim(),
        watts: Number(customForm.value.watts) || 100,
        qty: Number(customForm.value.qty) || 1,
        hours: Number(customForm.value.hours) || 4,
        surge: Number(customForm.value.surge) || 1.0,
        is_resistive: Boolean(customForm.value.is_resistive),
        icon: 'bolt',
    });

    customForm.value = {
        name: '',
        watts: 100,
        qty: 1,
        hours: 4,
        surge: 1.0,
        is_resistive: false,
        use_amps_volts: false,
        amps: 0.5,
        volts: 220,
    };
    showCustomModal.value = false;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Search & Category Filters -->
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <font-awesome-icon icon="magnifying-glass" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search appliances (e.g. fan, freezer, TV, Starlink, AC)..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none bg-white transition"
                />
            </div>

            <select
                v-model="selectedCategory"
                class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
            >
                <option value="">All Categories</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">
                    {{ c.name }}
                </option>
            </select>

            <button
                type="button"
                @click="showCustomModal = true"
                class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-sm transition active:scale-95 shrink-0"
            >
                <font-awesome-icon icon="plus" />
                <span>Custom Appliance</span>
            </button>
        </div>

        <!-- Filtered presets search dropdown or grid if user types query -->
        <div v-if="searchQuery.trim()" class="bg-white border border-slate-200 rounded-2xl p-3 shadow-md max-h-60 overflow-y-auto space-y-1">
            <div
                v-for="p in filteredPresets"
                :key="p.id"
                @click="emit('addPreset', p); searchQuery = ''"
                class="flex items-center justify-between p-2.5 rounded-xl hover:bg-amber-50 cursor-pointer transition text-xs"
            >
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center">
                        <font-awesome-icon :icon="p.icon || 'bolt'" class="text-xs" />
                    </div>
                    <div>
                        <div class="font-bold text-navy-950">{{ p.name }}</div>
                        <div class="text-[11px] text-slate-400">{{ p.notes || `${p.watts}W typical` }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-700">{{ p.watts }} W</span>
                    <span class="text-amber-600 font-bold hover:underline">+ Add</span>
                </div>
            </div>
            <div v-if="filteredPresets.length === 0" class="p-4 text-center text-xs text-slate-400">
                No preset matched "{{ searchQuery }}". Click <strong>+ Custom Appliance</strong> to add it manually.
            </div>
        </div>

        <!-- Custom Appliance Modal -->
        <div
            v-if="showCustomModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/70 backdrop-blur-sm animate-fade-in"
        >
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 relative">
                <button
                    type="button"
                    @click="showCustomModal = false"
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm transition"
                >
                    &times;
                </button>

                <h3 class="text-lg font-black text-navy-950 mb-1">Add Custom Appliance</h3>
                <p class="text-xs text-slate-500 mb-5">
                    Enter the label rating for your specialized equipment, machine, or electronic device.
                </p>

                <form @submit.prevent="submitCustom" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Appliance Name *</label>
                        <input
                            v-model="customForm.name"
                            type="text"
                            placeholder="e.g. Submersible Water Pump 2HP"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-amber-500 outline-none"
                            required
                        />
                    </div>

                    <!-- Amps x Volts Helper toggle -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-700">
                            <input v-model="customForm.use_amps_volts" type="checkbox" class="rounded text-amber-500" />
                            <span>Don't know Watts? Calculate from Amps × Volts (A × V)</span>
                        </label>
                        <div v-if="customForm.use_amps_volts" class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-slate-200">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500">Amperes (A)</label>
                                <input
                                    v-model.number="customForm.amps"
                                    @input="onAmpsVoltsChange"
                                    type="number"
                                    step="0.1"
                                    min="0.1"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs"
                                />
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500">Volts (V)</label>
                                <input
                                    v-model.number="customForm.volts"
                                    @input="onAmpsVoltsChange"
                                    type="number"
                                    min="100"
                                    max="400"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Rated Watts</label>
                            <input
                                v-model.number="customForm.watts"
                                type="number"
                                min="1"
                                max="10000"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"
                                required
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Quantity</label>
                            <input
                                v-model.number="customForm.qty"
                                type="number"
                                min="1"
                                max="99"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"
                                required
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Hours / Day</label>
                            <input
                                v-model.number="customForm.hours"
                                type="number"
                                step="0.5"
                                min="0.1"
                                max="24"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Surge Multiplier</label>
                            <select v-model.number="customForm.surge" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                                <option :value="1.0">1.0× (Standard electronic / light)</option>
                                <option :value="2.0">2.0× (Small fan / sewing machine)</option>
                                <option :value="3.0">3.0× (Inverter AC / fridge)</option>
                                <option :value="4.0">4.0× (Chest freezer)</option>
                                <option :value="5.0">5.0× (Borehole water pump)</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-700 font-semibold">
                                <input v-model="customForm.is_resistive" type="checkbox" class="rounded text-amber-500" />
                                <span>Resistive heater (e.g. Iron, Kettle, Cooker)</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button
                            type="button"
                            @click="showCustomModal = false"
                            class="w-1/2 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="w-1/2 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-black shadow-md transition uppercase tracking-wider"
                        >
                            Add Appliance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
