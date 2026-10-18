<script setup>
import { computed } from 'vue';

const props = defineProps({
    review: {
        type: Object,
        default: null,
    },
    name: {
        type: String,
        default: '',
    },
    text: {
        type: String,
        default: '',
    },
    message: {
        type: String,
        default: '',
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
    occupation: {
        type: String,
        default: '',
    },
});

const displayName = computed(() => {
    return props.review?.name || props.name || 'Verified Client';
});

const displayText = computed(() => {
    return props.review?.message || props.review?.text || props.message || props.text || '';
});

const displayRole = computed(() => {
    return props.review?.occupation || props.review?.role || props.occupation || props.role || '';
});

const displayRating = computed(() => {
    const r = Number(props.review?.rating ?? props.rating ?? 5);
    return isNaN(r) || r < 1 ? 5 : Math.min(5, Math.max(1, r));
});

const displayDate = computed(() => {
    const raw = props.review?.created_at || props.date;
    if (!raw) return '';
    try {
        return new Date(raw).toLocaleDateString('en-NG', { month: 'short', year: 'numeric' });
    } catch {
        return String(raw);
    }
});

const initial = computed(() => {
    const n = displayName.value.trim();
    return n ? n.charAt(0).toUpperCase() : 'Z';
});
</script>

<template>
    <div
        class="review-card relative rounded-2xl p-6 sm:p-7 bg-white border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-amber-400/60 transition-all duration-300 flex flex-col justify-between overflow-hidden group"
    >
        <!-- Large decorative quote watermark -->
        <span class="text-6xl md:text-7xl font-serif leading-none absolute top-3 right-5 select-none text-amber-500/10 group-hover:text-amber-500/20 transition-colors pointer-events-none">
            “
        </span>

        <div class="relative z-10">
            <!-- Star Ratings & Date Header -->
            <div class="flex items-center justify-between gap-2 mb-4">
                <div class="flex items-center gap-1">
                    <svg
                        v-for="n in 5"
                        :key="n"
                        class="w-4 h-4 transition-transform duration-300 group-hover:scale-110"
                        :class="n <= displayRating ? 'text-amber-400 fill-amber-400' : 'text-slate-200 fill-slate-200'"
                        viewBox="0 0 24 24"
                    >
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <span v-if="displayDate" class="text-[11px] text-slate-400 font-medium">
                    {{ displayDate }}
                </span>
            </div>

            <!-- Review Message Body -->
            <p class="text-sm sm:text-[14.5px] text-slate-700 leading-relaxed italic mb-6">
                “{{ displayText }}”
            </p>
        </div>

        <!-- Reviewer Information Footer -->
        <div class="relative z-10 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-slate-900 text-amber-400 font-black flex items-center justify-center text-sm shrink-0 shadow-xs">
                    {{ initial }}
                </div>
                <div class="min-w-0">
                    <h4 class="font-bold text-slate-900 text-sm truncate leading-snug">
                        {{ displayName }}
                    </h4>
                    <p v-if="displayRole" class="text-xs text-slate-500 truncate mt-0.5">
                        {{ displayRole }}
                    </p>
                </div>
            </div>

            <!-- Verified Badge -->
            <span class="inline-flex items-center gap-1 text-[10.5px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-full shrink-0">
                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span>Verified Client</span>
            </span>
        </div>
    </div>
</template>

<style scoped>
.review-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.review-card:hover {
    transform: translateY(-2px);
}
</style>
