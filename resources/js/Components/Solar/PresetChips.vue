<script setup>
defineProps({
    presets: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['add']);
</script>

<template>
    <div class="space-y-2">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">
            Quick Add Popular Appliances:
        </div>
        <div class="flex flex-wrap gap-2">
            <button
                v-for="p in presets.slice(0, 10)"
                :key="p.id"
                type="button"
                @click="emit('add', p)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-slate-200 text-navy-950 hover:border-amber-500 hover:bg-amber-50 hover:text-amber-900 transition shadow-sm active:scale-95"
            >
                <font-awesome-icon :icon="p.icon || 'bolt'" class="text-amber-500 text-xs" />
                <span>{{ p.name }}</span>
                <span class="text-[10px] text-slate-400 font-normal">({{ p.watts }}W)</span>
            </button>
        </div>
    </div>
</template>
