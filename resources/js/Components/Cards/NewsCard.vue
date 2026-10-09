<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    excerpt: {
        type: String,
        default: '',
    },
    image: {
        type: String,
        default: '',
    },
    href: {
        type: String,
        default: '#',
    },
    date: {
        type: String,
        default: '',
    },
    category: {
        type: String,
        default: '',
    },
    author: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Link
        :href="href"
        class="news-card group block bg-white rounded-[14px] p-4 sm:p-5 border border-gray-100/90 shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer flex flex-col justify-between"
    >
        <div>
            <!-- Image Container with overflow hidden for the zoom effect -->
            <div class="news-card-image relative w-full h-52 sm:h-56 rounded-[10px] overflow-hidden mb-4 bg-slate-100">
                <img
                    v-if="image"
                    :src="image"
                    :alt="title"
                    class="w-full h-full object-cover"
                    loading="lazy"
                />
                <div v-else class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-950 flex items-center justify-center text-white">
                    <span class="text-sm font-bold text-slate-400">Makkah Eye Hospital</span>
                </div>

                <!-- Category badge -->
                <div v-if="category" class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-[11px] font-extrabold text-primary shadow-sm uppercase tracking-wider">
                    {{ category }}
                </div>
            </div>

            <!-- Date & Metadata -->
            <div v-if="date" class="text-[11px] font-semibold text-primary mb-2 flex items-center gap-2">
                <span>{{ date }}</span>
                <span v-if="author" class="text-slate-300">•</span>
                <span v-if="author" class="text-slate-500 font-normal">By {{ author }}</span>
            </div>

            <!-- Headline Title -->
            <h3 class="news-card-title text-base sm:text-lg font-extrabold leading-snug font-['Outfit',sans-serif]">
                {{ title }}
            </h3>

            <!-- Excerpt -->
            <p v-if="excerpt" class="mt-2 text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                {{ excerpt }}
            </p>
        </div>

        <!-- Card Footer Link -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-bold text-primary">
            <span>Read Full Story</span>
            <span class="transform group-hover:translate-x-1.5 transition-transform duration-300">→</span>
        </div>
    </Link>
</template>

<style scoped>
.news-card-image {
    overflow: hidden;
    border-radius: 10px;
}

.news-card-image img {
    transition: transform 0.5s ease-in-out;
}

@media (hover: hover) {
    .news-card:hover .news-card-image img {
        transform: scale(1.08);
    }
}

.news-card-title {
    color: #303030;
    transition: color 0.2s ease;
}

.news-card:hover .news-card-title {
    color: #0A96EB;
}
</style>
