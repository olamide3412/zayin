<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    post: Object,
    related: Array,
});

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
</script>

<template>
    <div>
        <Head :title="`${post.title} | Blog | Zayin Technologies`">
            <meta name="description" :content="post.excerpt || post.title" />
        </Head>

        <!-- Article Hero -->
        <section class="pt-32 md:pt-44 pb-0 bg-white">
            <div class="max-w-4xl mx-auto px-4">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                    <Link :href="route('home')" class="hover:text-primary transition-colors">Home</Link>
                    <span>/</span>
                    <Link :href="route('blog.index')" class="hover:text-primary transition-colors">Blog</Link>
                    <span>/</span>
                    <span class="text-gray-600 truncate max-w-xs">{{ post.title }}</span>
                </nav>

                <!-- Category Badge -->
                <span v-if="post.category" class="inline-block bg-primary/10 text-primary text-sm font-semibold px-4 py-1 rounded-full mb-4">{{ post.category }}</span>

                <!-- Title -->
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6 leading-snug" style="font-family: Georgia, serif;">{{ post.title }}</h1>

                <!-- Meta -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-8 pb-8 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                            {{ (post.author?.name || 'A')[0].toUpperCase() }}
                        </div>
                        <span>{{ post.author?.name || 'Admin' }}</span>
                    </div>
                    <span>·</span>
                    <span>{{ formatDate(post.published_at) }}</span>
                    <span v-if="post.read_time">· {{ post.read_time }} min read</span>
                </div>
            </div>
        </section>

        <!-- Cover Image -->
        <div v-if="post.cover_image" class="max-w-5xl mx-auto px-4 mb-0">
            <img :src="post.cover_image" :alt="post.title" class="w-full h-72 md:h-[450px] object-cover rounded-2xl shadow-md" />
        </div>

        <!-- Article Body -->
        <section class="py-12 bg-white">
            <div class="max-w-4xl mx-auto px-4">
                <div class="prose prose-lg prose-primary max-w-none text-gray-800" v-html="post.body"></div>

                <!-- Back Link -->
                <div class="mt-12 pt-8 border-t border-gray-100">
                    <Link :href="route('blog.index')" class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Blog
                    </Link>
                </div>
            </div>
        </section>

        <!-- Related Posts -->
        <section v-if="related?.length" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-8" style="font-family: Georgia, serif;">Related Engineering Articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link v-for="r in related" :key="r.id" :href="route('blog.show', r.slug)"
                        class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border border-gray-100">
                        <div class="h-36 overflow-hidden bg-gradient-to-br from-primary/10 to-blue-50">
                            <img v-if="r.cover_image" :src="r.cover_image" :alt="r.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-gray-400 mb-1">{{ formatDate(r.published_at) }}</p>
                            <h3 class="font-semibold text-gray-900 text-sm group-hover:text-primary transition-colors line-clamp-2">{{ r.title }}</h3>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>

<style>
/* Prose styles for rendered HTML */
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
.dark .prose blockquote { color: #9ca3af; }
</style>
