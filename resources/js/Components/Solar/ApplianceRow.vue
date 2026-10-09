<script setup>
import { computed } from 'vue';

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['remove', 'update']);

const dailyWh = computed(() => {
    const w = Number(props.item.watts) || 0;
    const q = Number(props.item.qty) || 1;
    const h = Number(props.item.hours) || 0;
    return Math.round(w * q * h);
});
</script>

<template>
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 hover:border-slate-300 shadow-sm transition space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-sm shrink-0">
                    <font-awesome-icon :icon="item.icon || 'bolt'" />
                </div>
                <div>
                    <h4 class="font-bold text-navy-950 text-sm sm:text-base leading-tight">
                        {{ item.name }}
                    </h4>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span v-if="item.is_resistive" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            Resistive Heating
                        </span>
                        <span v-if="item.surge && item.surge > 1.5" class="text-[10px] font-semibold text-slate-500">
                            Surge: {{ item.surge }}×
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-xs font-black text-navy-950 sm:text-sm">
                        {{ dailyWh >= 1000 ? (dailyWh / 1000).toFixed(2) + ' kWh' : dailyWh + ' Wh' }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium">Daily Energy</div>
                </div>
                <button
                    type="button"
                    @click="emit('remove', index)"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-400 flex items-center justify-center text-xs transition active:scale-95"
                    title="Remove appliance"
                >
                    <font-awesome-icon icon="trash-can" />
                </button>
            </div>
        </div>

        <!-- Inputs: Watts, Qty, Hours -->
        <div class="grid grid-cols-3 gap-3 pt-2 border-t border-slate-100 text-xs">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Watts (W)</label>
                <div class="relative">
                    <input
                        v-model.number="item.watts"
                        type="number"
                        min="1"
                        max="10000"
                        class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold focus:border-amber-500 outline-none"
                    />
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Quantity</label>
                <input
                    v-model.number="item.qty"
                    type="number"
                    min="1"
                    max="99"
                    class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold focus:border-amber-500 outline-none"
                />
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Hrs / Day</label>
                <input
                    v-model.number="item.hours"
                    type="number"
                    step="0.5"
                    min="0.1"
                    max="24"
                    class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold focus:border-amber-500 outline-none"
                />
            </div>
        </div>
    </div>
</template>
