<script setup>
import { computed } from 'vue';
import { WARNING_MESSAGES } from '@/lib/solar/constants';

const props = defineProps({
    warnings: {
        type: Array,
        default: () => [],
    },
});

const warningList = computed(() => {
    return props.warnings
        .map((key) => WARNING_MESSAGES[key])
        .filter(Boolean);
});
</script>

<template>
    <div v-if="warningList.length > 0" class="space-y-3">
        <div
            v-for="(w, idx) in warningList"
            :key="idx"
            :class="[
                'p-4 rounded-2xl border text-xs sm:text-sm flex items-start gap-3.5 shadow-sm',
                w.type === 'warning'
                    ? 'bg-amber-50 border-amber-300 text-amber-950'
                    : 'bg-sky-50 border-sky-200 text-sky-950'
            ]"
        >
            <font-awesome-icon
                :icon="w.type === 'warning' ? 'triangle-exclamation' : 'circle-info'"
                :class="[
                    'text-base mt-0.5 shrink-0',
                    w.type === 'warning' ? 'text-amber-600' : 'text-sky-600'
                ]"
            />
            <div>
                <strong class="font-black block text-xs sm:text-sm mb-0.5">{{ w.title }}</strong>
                <p class="text-xs leading-relaxed opacity-90">{{ w.desc }}</p>
            </div>
        </div>
    </div>
</template>
