<script setup>
defineProps({
    currentStep: {
        type: Number,
        default: 1,
    },
    peakW: {
        type: Number,
        default: 0,
    },
    dailyWh: {
        type: Number,
        default: 0,
    },
    applianceCount: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['next', 'prev', 'goToStep']);
</script>

<template>
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-navy-950/95 backdrop-blur-md text-white border-t border-slate-800 px-4 py-3 sm:py-4 shadow-2xl transition-all">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <!-- Left: Running figures -->
            <div class="flex items-center gap-4 sm:gap-6">
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Continuous Peak</div>
                    <div class="text-sm sm:text-base font-black text-amber-400">
                        {{ peakW }} <span class="text-xs text-slate-300 font-normal">W</span>
                    </div>
                </div>

                <div class="h-8 w-px bg-slate-800"></div>

                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Daily Consumption</div>
                    <div class="text-sm sm:text-base font-black text-white">
                        {{ (dailyWh / 1000).toFixed(2) }} <span class="text-xs text-slate-300 font-normal">kWh/day</span>
                    </div>
                </div>

                <div class="hidden md:block h-8 w-px bg-slate-800"></div>

                <div class="hidden md:block text-xs text-slate-400">
                    <span class="font-bold text-white">{{ applianceCount }}</span> appliances in load profile
                </div>
            </div>

            <!-- Right: Action CTA -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <button
                    v-if="currentStep > 1"
                    type="button"
                    @click="emit('prev')"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs sm:text-sm font-bold transition active:scale-95"
                >
                    &larr; Back
                </button>

                <button
                    v-if="currentStep < 3"
                    type="button"
                    @click="emit('next')"
                    :disabled="applianceCount === 0"
                    class="px-6 sm:px-8 py-2.5 sm:py-3 rounded-xl bg-amber-500 hover:bg-amber-400 disabled:opacity-40 disabled:cursor-not-allowed text-navy-950 text-xs sm:text-sm font-black uppercase tracking-wider shadow-lg transition active:scale-95 flex items-center gap-2"
                >
                    <span>{{ currentStep === 1 ? 'Configure System' : 'View Results' }}</span>
                    <span>&rarr;</span>
                </button>

                <a
                    v-else
                    href="#packages-section"
                    class="px-6 sm:px-8 py-2.5 sm:py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 text-xs sm:text-sm font-black uppercase tracking-wider shadow-lg transition active:scale-95 flex items-center gap-2"
                >
                    <span>Shop Matching Packages</span>
                    <font-awesome-icon icon="arrow-down" class="text-xs" />
                </a>
            </div>
        </div>
    </div>
</template>
