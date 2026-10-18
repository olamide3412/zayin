<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

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
const showApplianceModal = ref(false);
const showCustomModal = ref(false);
const recentlyAddedId = ref(null);
const searchInputRef = ref(null);

const openApplianceModal = () => {
    showApplianceModal.value = true;
};

// Handle Escape key to close modals
const onKeydown = (e) => {
    if (e.key === 'Escape') {
        if (showCustomModal.value) {
            showCustomModal.value = false;
        } else if (showApplianceModal.value) {
            showApplianceModal.value = false;
        }
    }
};

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
});

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

// Category counts
const categoryCounts = computed(() => {
    const counts = {};
    props.presets.forEach((p) => {
        counts[p.category_id] = (counts[p.category_id] || 0) + 1;
    });
    return counts;
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

const handleAddPreset = (preset) => {
    emit('addPreset', preset);
    recentlyAddedId.value = preset.id;
    setTimeout(() => {
        if (recentlyAddedId.value === preset.id) {
            recentlyAddedId.value = null;
        }
    }, 1200);
};

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
        <!-- Compact Bar on Main Page (Closed by default to keep screen uncluttered) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Trigger button for Dialogue with Search & Category & Available Appliances -->
            <button
                type="button"
                @click="openApplianceModal"
                class="flex-1 flex items-center justify-between px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:border-amber-400 hover:shadow-md text-slate-600 text-sm transition group cursor-pointer"
            >
                <span class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs group-hover:bg-amber-500 group-hover:text-navy-950 transition">
                        <font-awesome-icon icon="magnifying-glass" />
                    </span>
                    <span class="font-medium text-slate-700 group-hover:text-navy-950 text-xs sm:text-sm">
                        Search &amp; browse 28+ appliances...
                    </span>
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-slate-100 group-hover:bg-amber-500 group-hover:text-navy-950 text-slate-700 px-3 py-1.5 rounded-xl transition">
                    <span>Show More Appliances ({{ presets.length }})</span>
                    <font-awesome-icon icon="chevron-right" class="text-[10px]" />
                </span>
            </button>

            <!-- Custom Appliance Trigger Button -->
            <button
                type="button"
                @click="showCustomModal = true"
                class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm transition active:scale-95 shrink-0 cursor-pointer"
            >
                <font-awesome-icon icon="plus" />
                <span>+ Custom Appliance</span>
            </button>
        </div>

        <!-- Available Appliances Catalogue Dialogue / Modal (Teleported to body to break out of all stacking contexts) -->
        <Teleport to="body">
            <div
                v-if="showApplianceModal"
                class="fixed inset-0 z-[90] flex items-center justify-center p-2 sm:p-4 md:p-6 bg-navy-950/75 backdrop-blur-sm animate-fade-in"
                @click.self="showApplianceModal = false"
            >
                <div class="bg-white rounded-2xl sm:rounded-3xl max-w-4xl w-full max-h-[92vh] sm:max-h-[85vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden relative my-auto">
                    <!-- Modal Header -->
                    <div class="px-4 sm:px-6 py-3 sm:py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                        <div class="min-w-0 pr-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base sm:text-lg font-black text-navy-950">
                                    Available Appliances Directory
                                </h3>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 shrink-0">
                                    {{ presets.length }} Available
                                </span>
                            </div>
                            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 truncate">
                                Click <span class="font-bold text-amber-700">+ Add</span> to append any appliance to your active load schedule.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="showApplianceModal = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-base font-bold transition cursor-pointer shrink-0"
                            title="Close (Esc)"
                        >
                            &times;
                        </button>
                    </div>

                    <!-- Search Bar & Category Filters (Inside Modal) -->
                    <div class="px-4 sm:px-6 py-2.5 sm:py-3 space-y-2.5 bg-slate-50/80 border-b border-slate-100 shrink-0">
                        <!-- Search input -->
                        <div class="relative">
                            <font-awesome-icon icon="magnifying-glass" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs sm:text-sm" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search all appliances (e.g. fan, freezer, TV, Starlink, AC)..."
                                class="w-full pl-9 pr-9 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none bg-white transition"
                            />
                            <button
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs w-5 h-5 flex items-center justify-center rounded-full bg-slate-100 cursor-pointer"
                            >
                                &times;
                            </button>
                        </div>

                        <!-- Category Filter Pills / Tabs with hidden native scrollbar -->
                        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-hide py-0.5">
                            <button
                                type="button"
                                @click="selectedCategory = ''"
                                :class="[
                                    'px-2.5 py-1 rounded-full text-xs font-bold transition whitespace-nowrap shrink-0 flex items-center gap-1.5 cursor-pointer',
                                    selectedCategory === ''
                                        ? 'bg-navy-950 text-white shadow-xs'
                                        : 'bg-white text-slate-600 hover:bg-slate-200 hover:text-navy-950 border border-slate-200'
                                ]"
                            >
                                <span>All Appliances</span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="selectedCategory === '' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ presets.length }}
                                </span>
                            </button>

                            <button
                                v-for="c in categories"
                                :key="c.id"
                                type="button"
                                @click="selectedCategory = selectedCategory === String(c.id) ? '' : String(c.id)"
                                :class="[
                                    'px-2.5 py-1 rounded-full text-xs font-bold transition whitespace-nowrap shrink-0 flex items-center gap-1.5 cursor-pointer',
                                    selectedCategory === String(c.id)
                                        ? 'bg-amber-500 text-navy-950 shadow-xs'
                                        : 'bg-white text-slate-600 hover:bg-slate-200 hover:text-navy-950 border border-slate-200'
                                ]"
                            >
                                <span>{{ c.name }}</span>
                                <span
                                    v-if="categoryCounts[c.id]"
                                    class="text-[10px] px-1.5 py-0.2 rounded-full"
                                    :class="selectedCategory === String(c.id) ? 'bg-navy-950/20 text-navy-950' : 'bg-slate-100 text-slate-600'"
                                >
                                    {{ categoryCounts[c.id] }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Scrollable Grid of Appliances (Inside Modal) -->
                    <div class="px-4 sm:px-6 py-3 sm:py-4 overflow-y-auto flex-1 bg-slate-50/40">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2.5">
                            <span class="font-bold uppercase tracking-wider text-[11px] text-slate-600">
                                Available Appliances
                                <span v-if="filteredPresets.length > 0">({{ filteredPresets.length }})</span>
                            </span>
                            <span class="text-[11px] text-slate-400 hidden sm:inline">Click + Add to append to your load schedule</span>
                        </div>

                        <!-- Grid of Presets - High Density & Responsive -->
                        <div v-if="filteredPresets.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-2.5">
                            <div
                                v-for="p in filteredPresets"
                                :key="p.id"
                                class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200 hover:border-amber-400 hover:shadow-xs transition group"
                            >
                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0 text-xs group-hover:bg-amber-500 group-hover:text-navy-950 transition">
                                        <font-awesome-icon :icon="p.icon || 'bolt'" />
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-navy-950 text-xs truncate group-hover:text-amber-600 transition" :title="p.name">
                                            {{ p.name }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 truncate">
                                            <span class="font-bold text-slate-700">{{ p.watts }}W</span> • {{ p.default_hours || 4 }}h/day
                                            <span v-if="p.surge_multiplier && p.surge_multiplier > 1.5" class="text-amber-600 font-semibold">
                                                • {{ p.surge_multiplier }}×
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="handleAddPreset(p)"
                                    :class="[
                                        'px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 shrink-0 active:scale-95 cursor-pointer',
                                        recentlyAddedId === p.id
                                            ? 'bg-emerald-600 text-white shadow-xs'
                                            : 'bg-amber-100 hover:bg-amber-500 text-navy-950 font-bold'
                                    ]"
                                >
                                    <span v-if="recentlyAddedId === p.id">✓ Added</span>
                                    <span v-else>+ Add</span>
                                </button>
                            </div>
                        </div>

                        <!-- Empty Search State -->
                        <div v-else class="p-8 text-center text-xs text-slate-500 space-y-2 bg-white rounded-2xl border border-dashed border-slate-200 my-4">
                            <div class="font-bold text-slate-700">No appliances match your filter</div>
                            <p class="text-slate-400 max-w-sm mx-auto">
                                Can't find what you're looking for? Add any specialized device or machine using the custom appliance button.
                            </p>
                            <button
                                type="button"
                                @click="showCustomModal = true"
                                class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-bold text-xs cursor-pointer shadow-xs"
                            >
                                <font-awesome-icon icon="plus" />
                                <span>Add Custom Appliance</span>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-4 sm:px-6 py-2.5 sm:py-3 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                        <button
                            type="button"
                            @click="showCustomModal = true"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-600 transition cursor-pointer"
                        >
                            <font-awesome-icon icon="plus" class="text-amber-500" />
                            <span>+ Custom Appliance</span>
                        </button>

                        <button
                            type="button"
                            @click="showApplianceModal = false"
                            class="px-4 py-2 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs shadow-md transition cursor-pointer"
                        >
                            Done Adding Appliances ✓
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Custom Appliance Modal (Teleported to body) -->
        <Teleport to="body">
            <div
                v-if="showCustomModal"
                class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-4 bg-navy-950/75 backdrop-blur-sm animate-fade-in"
                @click.self="showCustomModal = false"
            >
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 max-w-md w-full max-h-[92vh] overflow-y-auto shadow-2xl border border-slate-200 relative my-auto">
                    <button
                        type="button"
                        @click="showCustomModal = false"
                        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-base font-bold transition cursor-pointer"
                    >
                        &times;
                    </button>

                    <h3 class="text-base sm:text-lg font-black text-navy-950 mb-1">Add Custom Appliance</h3>
                    <p class="text-xs text-slate-500 mb-4">
                        Enter the label rating for your specialized equipment, machine, or electronic device.
                    </p>

                    <form @submit.prevent="submitCustom" class="space-y-3.5 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 text-[11px]">Appliance Name *</label>
                            <input
                                v-model="customForm.name"
                                type="text"
                                placeholder="e.g. Submersible Water Pump 2HP"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-amber-500 outline-none"
                                required
                            />
                        </div>

                        <!-- Amps x Volts Helper toggle -->
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-700 text-xs">
                                <input v-model="customForm.use_amps_volts" type="checkbox" class="rounded text-amber-500" />
                                <span>Don't know Watts? Calculate from Amps × Volts (A × V)</span>
                            </label>
                            <div v-if="customForm.use_amps_volts" class="grid grid-cols-2 gap-2.5 mt-2.5 pt-2.5 border-t border-slate-200">
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-slate-500">Amperes (A)</label>
                                    <input
                                        v-model.number="customForm.amps"
                                        @input="onAmpsVoltsChange"
                                        type="number"
                                        step="any"
                                        min="0.01"
                                        class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-slate-500">Volts (V)</label>
                                    <input
                                        v-model.number="customForm.volts"
                                        @input="onAmpsVoltsChange"
                                        type="number"
                                        step="any"
                                        min="1"
                                        max="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 text-[10px]">Rated Watts</label>
                                <input
                                    v-model.number="customForm.watts"
                                    type="number"
                                    step="any"
                                    min="1"
                                    max="50000"
                                    class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs sm:text-sm"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 text-[10px]">Quantity</label>
                                <input
                                    v-model.number="customForm.qty"
                                    type="number"
                                    step="1"
                                    min="1"
                                    max="99"
                                    class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs sm:text-sm"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 text-[10px]">Hours / Day</label>
                                <input
                                    v-model.number="customForm.hours"
                                    type="number"
                                    step="any"
                                    min="0.1"
                                    max="24"
                                    class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs sm:text-sm"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 pt-1">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1 text-[10px]">Surge Multiplier</label>
                                <select v-model.number="customForm.surge" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs bg-white">
                                    <option :value="1.0">1.0× (Standard electronic / light)</option>
                                    <option :value="2.0">2.0× (Small fan / sewing machine)</option>
                                    <option :value="3.0">3.0× (Inverter AC / fridge)</option>
                                    <option :value="4.0">4.0× (Chest freezer)</option>
                                    <option :value="5.0">5.0× (Borehole water pump)</option>
                                </select>
                            </div>
                            <div class="flex items-center pt-4">
                                <label class="flex items-center gap-1.5 cursor-pointer text-slate-700 font-semibold text-xs">
                                    <input v-model="customForm.is_resistive" type="checkbox" class="rounded text-amber-500" />
                                    <span>Resistive heater</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-3 flex gap-2.5">
                            <button
                                type="button"
                                @click="showCustomModal = false"
                                class="w-1/2 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition text-xs cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="w-1/2 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-black shadow-md transition uppercase tracking-wider text-xs cursor-pointer"
                            >
                                Add Appliance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </div>
</template>
