<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    text: {
        type: String,
        required: true,
    },
    rating: {
        type: Number,
        default: 5,
    },
    date: {
        type: String,
        default: '',
    },
    role: {
        type: String,
        default: '',
    },
    colorHover: {
        type: String,
        default: '#06465C', // Deep teal brand color from Moorfields reference
    },
});

const backgroundStyle = computed(() => ({
    background: `linear-gradient(to left, #ffffff 50.1%, ${props.colorHover} 50%) right`,
    backgroundSize: '201% 100%',
}));
</script>

<template>
    <div
        class="review-card relative rounded-[14px] p-7 sm:p-9 border border-gray-100/90 shadow-sm hover:shadow-2xl transition-all duration-500 ease-out cursor-pointer flex flex-col justify-between overflow-hidden group"
        :style="backgroundStyle"
    >
        <!-- Large decorative quote watermark -->
        <span class="review-quote-mark text-6xl md:text-7xl font-serif leading-none absolute top-4 left-6 select-none text-primary/20 transition-colors duration-500">
            “
        </span>

        <div class="relative z-10">
            <!-- Star Ratings -->
            <div class="flex items-center gap-1 mb-4">
                <svg
                    v-for="n in 5"
                    :key="n"
                    class="w-4 h-4 transition-transform duration-300 group-hover:scale-105"
                    :class="n <= rating ? 'text-amber-400' : 'text-slate-200'"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <span v-if="date" class="review-date text-[11px] text-slate-400 font-medium ml-2 transition-colors duration-500">
                    {{ date }}
                </span>
            </div>

            <!-- Review Message -->
            <p class="review-text text-sm sm:text-base text-slate-700 leading-relaxed italic mb-6 transition-colors duration-500">
                “{{ text }}”
            </p>
        </div>

        <!-- Reviewer Information -->
        <div class="relative z-10 pt-4 border-t border-gray-100/60 review-border flex items-center justify-between transition-colors duration-500">
            <div>
                <h4 class="review-name font-bold text-slate-900 text-base font-['Outfit',sans-serif] leading-tight transition-colors duration-500">
                    {{ name }}
                </h4>
                <p v-if="role" class="review-role text-xs text-slate-500 mt-0.5 transition-colors duration-500">
                    {{ role }}
                </p>
            </div>
            <span class="review-verified text-[11px] font-bold text-primary px-2.5 py-1 rounded-full bg-sky-50 transition-colors duration-500">
                Verified Patient
            </span>
        </div>
    </div>
</template>

<style scoped>
.review-card {
    transition: background-position 0.5s ease-out, box-shadow 0.4s ease;
}

@media (hover: hover) {
    .review-card:hover {
        background-position: left !important;
    }
    .review-card:hover .review-text {
        color: #f1f5f9 !important;
    }
    .review-card:hover .review-name {
        color: #ffffff !important;
    }
    .review-card:hover .review-role {
        color: #cbd5e1 !important;
    }
    .review-card:hover .review-date {
        color: #94a3b8 !important;
    }
    .review-card:hover .review-quote-mark {
        color: rgba(255, 255, 255, 0.15) !important;
    }
    .review-card:hover .review-border {
        border-color: rgba(255, 255, 255, 0.15) !important;
    }
    .review-card:hover .review-verified {
        background-color: rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
    }
}
</style>
