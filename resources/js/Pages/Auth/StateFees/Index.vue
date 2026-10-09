<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    states: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const stateList = ref(JSON.parse(JSON.stringify(props.states)));
const savingId = ref(null);
const isBulkSaving = ref(false);

const filteredStates = () => {
    if (!search.value) return stateList.value;
    const q = search.value.toLowerCase();
    return stateList.value.filter(s => s.name.toLowerCase().includes(q) || s.code.toLowerCase().includes(q));
};

const saveRow = (state) => {
    savingId.value = state.id;
    router.patch(route('admin.state-fees.update', state.id), {
        delivery_fee_naira: state.delivery_fee_naira,
        install_fee_naira: state.install_fee_naira,
        is_active: state.is_active,
    }, {
        preserveScroll: true,
        onFinish: () => {
            savingId.value = null;
        },
    });
};

const saveAll = () => {
    isBulkSaving.value = true;
    router.post(route('admin.state-fees.bulk'), {
        states: stateList.value,
    }, {
        preserveScroll: true,
        onFinish: () => {
            isBulkSaving.value = false;
        },
    });
};

const formatNaira = (val) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Regional Delivery & Installation Fees" />

        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Regional Delivery & Installation Fees</h1>
                <p class="text-sm text-slate-500 mt-0.5">Configure state-by-state logistics delivery and engineering installation charges applied at checkout.</p>
            </div>
            <button 
                @click="saveAll" 
                :disabled="isBulkSaving"
                class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm disabled:opacity-50"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ isBulkSaving ? 'Saving All...' : 'Save All Changes' }}</span>
            </button>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-2xl p-4 mb-5 flex items-center gap-4 shadow-xs border border-slate-200">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input 
                    v-model="search" 
                    type="text" 
                    placeholder="Search by state name or code (e.g. Delta, Lagos, DE, LA)..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden transition" 
                />
            </div>
            <div class="text-xs text-slate-500 font-semibold whitespace-nowrap">
                {{ filteredStates().length }} States Configured
            </div>
        </div>

        <!-- States Table -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/75 text-xs text-slate-500 font-bold uppercase tracking-wider">
                            <th class="px-5 py-3.5">State & Code</th>
                            <th class="px-4 py-3.5">Delivery Fee (₦ Naira)</th>
                            <th class="px-4 py-3.5">Installation Fee (₦ Naira)</th>
                            <th class="px-4 py-3.5 text-center">Service Active</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="state in filteredStates()" :key="state.id" class="hover:bg-slate-50/60 transition group">
                            <!-- State Name & Code -->
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <span>{{ state.name }}</span>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold">
                                        {{ state.code }}
                                    </span>
                                </div>
                            </td>

                            <!-- Delivery Fee Input -->
                            <td class="px-4 py-3.5">
                                <div class="relative w-44">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">₦</span>
                                    <input 
                                        v-model.number="state.delivery_fee_naira" 
                                        type="number" 
                                        step="100"
                                        min="0"
                                        class="w-full pl-7 pr-3 py-1.5 text-xs font-bold text-slate-900 border border-slate-200 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-hidden bg-white"
                                    />
                                </div>
                            </td>

                            <!-- Installation Fee Input -->
                            <td class="px-4 py-3.5">
                                <div class="relative w-44">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">₦</span>
                                    <input 
                                        v-model.number="state.install_fee_naira" 
                                        type="number" 
                                        step="100"
                                        min="0"
                                        class="w-full pl-7 pr-3 py-1.5 text-xs font-bold text-slate-900 border border-slate-200 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-hidden bg-white"
                                    />
                                </div>
                            </td>

                            <!-- Active Switch -->
                            <td class="px-4 py-3.5 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input 
                                        v-model="state.is_active" 
                                        type="checkbox" 
                                        class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400" 
                                    />
                                </label>
                            </td>

                            <!-- Save Row Button -->
                            <td class="px-5 py-3.5 text-right">
                                <button 
                                    @click="saveRow(state)" 
                                    :disabled="savingId === state.id"
                                    class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition disabled:opacity-50"
                                >
                                    <span v-if="savingId === state.id">Saving...</span>
                                    <span v-else>Save</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
