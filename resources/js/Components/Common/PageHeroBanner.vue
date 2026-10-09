<script setup>
import { ref, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    breadcrumbs: {
        type: Array,
        default: () => []
    },
    badge: {
        type: String,
        default: ''
    }
});

const titleVisible = ref(false);

function playFadeIn() {
    titleVisible.value = false;
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            titleVisible.value = true;
        });
    });
}

onMounted(playFadeIn);
watch(() => props.title, playFadeIn);
</script>

<template>
    <section class="relative w-full overflow-hidden bg-gradient-to-r from-[#d5f1fc] via-[#b6e8f8] to-[#40c3df] border-b border-sky-100/60 select-none">
        <!-- Decorative Radiating Petal / Sunburst Watermark Pattern (Matching Reference) -->
        <div class="absolute right-0 top-0 bottom-0 pointer-events-none w-1/2 md:w-5/12 h-full flex items-center justify-end overflow-hidden opacity-45 md:opacity-65">
            <svg class="w-full h-[180%] -right-12 md:-right-16 translate-y-0 text-sky-400/40" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Inner Ring Petals -->
                <g transform="translate(200, 200)">
                    <circle cx="0" cy="0" r="28" fill="currentColor" class="text-sky-400/30" />
                    <!-- Radiating Bars & Petals -->
                    <g v-for="i in 16" :key="'inner-' + i" :transform="`rotate(${i * 22.5})`">
                        <rect x="-3" y="42" width="6" height="22" rx="3" fill="currentColor" class="text-sky-500/50" />
                    </g>
                    <g v-for="i in 16" :key="'mid-' + i" :transform="`rotate(${i * 22.5 + 11.25})`">
                        <path d="M0 76 C -7 90, -9 110, 0 120 C 9 110, 7 90, 0 76 Z" fill="currentColor" class="text-sky-500/60" />
                    </g>
                    <g v-for="i in 16" :key="'outer-' + i" :transform="`rotate(${i * 22.5})`">
                        <rect x="-4" y="132" width="8" height="34" rx="4" fill="currentColor" class="text-sky-600/40" />
                    </g>
                    <g v-for="i in 16" :key="'outer-petal-' + i" :transform="`rotate(${i * 22.5 + 11.25})`">
                        <path d="M0 174 C -12 195, -14 225, 0 240 C 14 225, 12 195, 0 174 Z" fill="currentColor" class="text-sky-600/50" />
                    </g>
                </g>
            </svg>
        </div>

        <!-- Main Content Area -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 md:py-16 min-h-[170px] sm:min-h-[200px] flex flex-col justify-between">
            <div data-aos="fade-up" data-aos-duration="700">
                <!-- Optional Category/Badge -->
                <div v-if="badge" class="mb-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-white/70 text-[#06465C] backdrop-blur-xs border border-white/80 shadow-xs">
                        {{ badge }}
                    </span>
                </div>

                <!-- Page Title with Opacity/Color Fade In -->
                <h1 :class="['page-title text-3xl sm:text-4xl md:text-5xl font-black text-[#06465C] tracking-tight leading-tight font-[\'Outfit\',sans-serif] drop-shadow-xs', { 'is-visible': titleVisible }]">
                    {{ title }}
                </h1>
            </div>

            <!-- Breadcrumb Navigation -->
            <nav v-if="breadcrumbs && breadcrumbs.length" class="mt-6 sm:mt-8 flex items-center flex-wrap gap-1.5 text-xs sm:text-sm font-semibold text-[#06465C]/80" aria-label="Breadcrumb">
                <template v-for="(item, idx) in breadcrumbs" :key="idx">
                    <!-- Link Item -->
                    <Link 
                        v-if="item.href" 
                        :href="item.href" 
                        class="hover:text-[#06465C] hover:underline transition-colors duration-200 truncate max-w-[200px] sm:max-w-none"
                    >
                        {{ item.label }}
                    </Link>

                    <!-- Active Current Page Item -->
                    <span v-else class="text-[#06465C] font-bold truncate max-w-[220px] sm:max-w-md">
                        {{ item.label }}
                    </span>

                    <!-- Separator (Chevron >) -->
                    <span v-if="idx < breadcrumbs.length - 1" class="text-[#06465C]/60 text-xs px-0.5">
                        &gt;
                    </span>
                </template>
            </nav>
        </div>
    </section>
</template>

<style scoped>
.page-title {
    color: var(--color-title-dark, #06465C);
    opacity: 0.25;
    transition: opacity 0.5s ease-out, transform 0.5s ease-out;
}
.page-title.is-visible {
    opacity: 1;
}
</style>
