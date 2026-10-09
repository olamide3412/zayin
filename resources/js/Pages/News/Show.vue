<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    article: Object,
    related: Array,
});

const categoryColors = {
    news: 'bg-blue-100 text-blue-700',
    event: 'bg-green-100 text-green-700',
    announcement: 'bg-yellow-100 text-yellow-700',
    media: 'bg-purple-100 text-purple-700',
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
</script>

<template>
    <div>
        <Head :title="`${article.title} | News | Zayin Technologies`">
            <meta name="description" :content="article.excerpt || article.title" />
        </Head>

        <!-- Hero -->
        <section class="pt-32 md:pt-44 pb-8 bg-white">
            <div class="max-w-4xl mx-auto px-4">
                <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <Link :href="route('home')" class="hover:text-primary transition-colors">Home</Link>
                    <span>/</span>
                    <Link :href="route('news.index')" class="hover:text-primary transition-colors">News & Bulletins</Link>
                    <span>/</span>
                    <span class="text-gray-600 truncate max-w-xs">{{ article.title }}</span>
                </nav>

                <span :class="['inline-block text-sm font-bold px-4 py-1 rounded-full mb-4 capitalize', categoryColors[article.category] || 'bg-gray-100 text-gray-600']">{{ article.category }}</span>

                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6 leading-snug" style="font-family: Georgia, serif;">{{ article.title }}</h1>

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-8 pb-8 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                            {{ (article.author?.name || 'A')[0].toUpperCase() }}
                        </div>
                        <span>{{ article.author?.name || 'Admin' }}</span>
                    </div>
                    <span>·</span>
                    <span>{{ formatDate(article.published_at) }}</span>
                </div>
            </div>
        </section>

        <!-- Cover Image -->
        <div v-if="article.cover_image" class="max-w-5xl mx-auto px-4 mb-0">
            <img :src="article.cover_image" :alt="article.title" class="w-full h-72 md:h-[450px] object-cover rounded-2xl shadow-md" />
        </div>

        <!-- Body -->
        <section class="py-12 bg-white">
            <div class="max-w-4xl mx-auto px-4">
                <div class="prose prose-lg prose-primary max-w-none text-gray-800" v-html="article.body"></div>

                <!-- Media Gallery -->
                <div v-if="article.media_gallery && article.media_gallery.length > 0" class="mt-12 pt-8 border-t border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6" style="font-family: Georgia, serif;">Field Media & Photos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div v-for="(media, idx) in article.media_gallery" :key="idx" class="rounded-2xl overflow-hidden shadow-md bg-slate-50 border border-gray-100">
                            <!-- Image item -->
                            <a v-if="media.type === 'image'" :href="media.url" target="_blank" class="block aspect-video overflow-hidden group">
                                <img :src="media.url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Gallery Photo" />
                            </a>
                            <!-- Video item -->
                            <div v-else class="aspect-video bg-black flex items-center justify-center">
                                <video :src="media.url" controls class="w-full h-full object-contain focus:outline-none" preload="metadata" playsinline></video>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-100">
                    <Link :href="route('news.index')" class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to News
                    </Link>
                </div>
            </div>
        </section>

        <!-- Related -->
        <section v-if="related?.length" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link v-for="r in related" :key="r.id" :href="route('news.show', r.slug)"
                        class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border border-gray-100">
                        <div class="h-36 overflow-hidden bg-gradient-to-br from-indigo-50 to-blue-50">
                            <img v-if="r.cover_image" :src="r.cover_image" :alt="r.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div class="p-4">
                            <span :class="['text-xs font-bold px-2 py-0.5 rounded-full capitalize', categoryColors[r.category] || 'bg-gray-100 text-gray-600']">{{ r.category }}</span>
                            <h3 class="font-semibold text-gray-900 text-sm group-hover:text-primary transition-colors line-clamp-2 mt-2">{{ r.title }}</h3>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>

<style>
.prose h1 { font-size: 2rem; font-weight: 700; margin: 1.5rem 0 1rem; }
.prose h2 { font-size: 1.5rem; font-weight: 700; margin: 1.5rem 0 0.75rem; }
.prose h3 { font-size: 1.25rem; font-weight: 600; margin: 1.25rem 0 0.5rem; }
.prose p { margin-bottom: 1rem; line-height: 1.75; }
.prose ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
.prose ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
.prose li { margin-bottom: 0.25rem; }
.prose blockquote { border-left: 4px solid #0A96EB; padding-left: 1rem; color: #6b7280; margin: 1.5rem 0; font-style: italic; }
.prose a { color: #0A96EB; text-decoration: underline; }
.prose hr { border-color: #e5e7eb; margin: 2rem 0; }
.prose img { max-width: 100%; border-radius: 0.75rem; margin: 1.5rem auto; }
.dark .prose h1, .dark .prose h2, .dark .prose h3 { color: #f3f4f6; }
.dark .prose p, .dark .prose li { color: #d1d5db; }
</style>
