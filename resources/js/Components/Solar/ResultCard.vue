<script setup>
import { ref } from 'vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    subvalue: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: 'bolt',
    },
    badge: {
        type: String,
        default: '',
    },
    explanation: {
        type: String,
        default: '',
    },
    formula: {
        type: String,
        default: '',
    },
    calculationSteps: {
        type: Array,
        default: () => [],
    },
});

const showFormula = ref(false);
</script>

<template>
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-amber-400/80 transition duration-200">
        <div>
            <!-- Top bar -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-lg">
                        <font-awesome-icon :icon="icon" />
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                            {{ title }}
                        </span>
                        <span v-if="badge" class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ badge }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Big Main Metric -->
            <div class="my-3">
                <div class="text-3xl sm:text-4xl font-black text-navy-950 tracking-tight leading-none">
                    {{ value }}
                </div>
                <div v-if="subvalue" class="text-xs sm:text-sm font-semibold text-slate-500 mt-1.5">
                    {{ subvalue }}
                </div>
            </div>

            <!-- One-line explanation -->
            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                {{ explanation }}
            </p>
        </div>

        <!-- How we calculated this expandable section -->
        <div v-if="formula || calculationSteps.length > 0" class="pt-4 border-t border-slate-100">
            <button
                type="button"
                @click="showFormula = !showFormula"
                class="text-[11px] font-bold text-amber-700 hover:text-amber-800 flex items-center gap-1.5 transition"
            >
                <font-awesome-icon :icon="showFormula ? 'chevron-up' : 'chevron-down'" class="text-[10px]" />
                <span>{{ showFormula ? 'Hide engineering calculation' : 'How we calculated this' }}</span>
            </button>

            <div v-show="showFormula" class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1.5">
                <div v-if="formula" class="font-mono text-[10px] text-slate-800 font-bold bg-white p-2 rounded-lg border border-slate-200">
                    {{ formula }}
                </div>
                <ul v-if="calculationSteps.length > 0" class="space-y-1 pl-3 list-disc marker:text-amber-500">
                    <li v-for="(step, i) in calculationSteps" :key="i">
                        {{ step }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
