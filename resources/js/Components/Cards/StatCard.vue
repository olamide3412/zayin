<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: [String, Number],
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    colorA: {
        type: String,
        default: '#06465C', // Deep teal brand color
    },
    colorB: {
        type: String,
        default: '#0A96EB', // Primary blue
    },
    icon: {
        type: String,
        default: '',
    },
    prefix: {
        type: String,
        default: '',
    },
    suffix: {
        type: String,
        default: '',
    },
});

const customStyle = computed(() => ({
    background: `linear-gradient(to left, ${props.colorA} 50.1%, ${props.colorB} 50%) right`,
    backgroundSize: '201% 100%',
}));
</script>

<template>
    <div
        class="stat-card relative rounded-[15px] p-8 md:p-9 text-white overflow-hidden cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-500 ease-out select-none"
        :style="customStyle"
    >
        <!-- Decorative subtle background shape -->
        <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/5 pointer-events-none transition-transform duration-500 ease-out stat-card-circle"></div>

        <div class="relative z-10 flex flex-col justify-between h-full">
            <!-- Icon slot / prop -->
            <div v-if="icon || $slots.icon" class="mb-4 text-3xl md:text-4xl text-white/90 stat-icon">
                <slot name="icon">
                    <span v-if="icon">{{ icon }}</span>
                </slot>
            </div>

            <!-- Value / Metric counter -->
            <div class="flex items-baseline gap-1">
                <span v-if="prefix" class="text-2xl md:text-3xl font-bold opacity-90">{{ prefix }}</span>
                <span class="text-4xl sm:text-5xl md:text-6xl font-black font-['Outfit',sans-serif] tracking-tight leading-none text-white">
                    {{ value }}
                </span>
                <span v-if="suffix" class="text-2xl md:text-3xl font-bold text-sky-200">{{ suffix }}</span>
            </div>

            <!-- Label -->
            <h4 class="text-lg sm:text-xl font-bold text-white mt-3 font-['Outfit',sans-serif] leading-snug">
                {{ label }}
            </h4>

            <!-- Optional Sub-description -->
            <p v-if="description" class="text-xs sm:text-sm text-white/80 mt-1.5 leading-relaxed font-medium">
                {{ description }}
            </p>
        </div>
    </div>
</template>

<style scoped>
.stat-card {
    transition: background-position 0.5s ease-out, box-shadow 0.4s ease, transform 0.3s ease;
}

@media (hover: hover) {
    .stat-card:hover {
        background-position: left !important;
    }
    .stat-card:hover .stat-card-circle {
        transform: scale(1.35) rotate(15deg);
    }
}
</style>
