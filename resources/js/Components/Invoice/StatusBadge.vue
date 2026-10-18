<template>
    <span
        :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider',
            badgeStyles[normalizedStatus] || 'bg-gray-100 text-gray-800'
        ]"
    >
        <span
            :class="[
                'w-1.5 h-1.5 rounded-full mr-1.5',
                dotStyles[normalizedStatus] || 'bg-gray-400'
            ]"
        ></span>
        {{ label || defaultLabels[normalizedStatus] || status }}
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: string;
    label?: string;
}>();

const normalizedStatus = computed(() => (props.status || '').toLowerCase());

const badgeStyles: Record<string, string> = {
    draft: 'bg-slate-100 text-slate-700 border border-slate-300',
    issued: 'bg-blue-50 text-blue-700 border border-blue-200',
    partially_paid: 'bg-amber-50 text-amber-700 border border-amber-200',
    paid: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    void: 'bg-rose-50 text-rose-700 border border-rose-200',
};

const dotStyles: Record<string, string> = {
    draft: 'bg-slate-400',
    issued: 'bg-blue-500',
    partially_paid: 'bg-amber-500',
    paid: 'bg-emerald-500',
    void: 'bg-rose-500',
};

const defaultLabels: Record<string, string> = {
    draft: 'Draft',
    issued: 'Issued',
    partially_paid: 'Partially Paid',
    paid: 'Paid',
    void: 'Void',
};
</script>
