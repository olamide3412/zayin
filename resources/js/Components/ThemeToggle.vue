<script setup>
import { computed } from 'vue'
import { useThemeStore } from '@/Stores/themeStore'

const themeStore = useThemeStore()

const isDark = computed(() => themeStore.isDark)

const toggle = () => {
    const next = isDark.value ? 'light' : 'dark'
    themeStore.setTheme(next)
}
</script>

<template>
    <button
        @click="toggle"
        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
        class="relative w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary/50"
        :class="isDark ? 'bg-primary' : 'bg-gray-200'"
    >
        <!-- Track icons -->
        <span class="absolute inset-y-0 left-1 flex items-center text-[11px]">☀️</span>
        <span class="absolute inset-y-0 right-1 flex items-center text-[11px]">🌙</span>

        <!-- Thumb -->
        <span
            class="absolute top-0.5 w-5 h-5 rounded-full bg-white shadow-md transform transition-transform duration-300"
            :class="isDark ? 'translate-x-6' : 'translate-x-0.5'"
        ></span>
    </button>
</template>

