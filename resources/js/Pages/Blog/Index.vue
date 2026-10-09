<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';

defineOptions({ layout: Layout });

const props = defineProps({
    posts: Object,
    filters: Object,
    layoutStyle: {
        type: String,
        default: 'hero_split'
    }
});

const page = usePage();

// ─── Hero banner: first homepage slider image ─────────────────────────────
const heroBgImage = computed(() => {
    const slides = page.props.settings?.hero_slides;
    if (slides && Array.isArray(slides) && slides.length > 0) {
        return slides[0]?.value || slides[0];
    }
    return 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=2070&auto=format&fit=crop';
});

const search         = ref(props.filters?.search   || '');
const sortBy         = ref(props.filters?.sort     || 'newest');
const dateFilter     = ref(props.filters?.date     || '');
const activeCategory = ref(props.filters?.category || '');
const currentView    = ref(props.filters?.view     || '');  // '' = hero_split, 'list' = full list

const categories = ['Solar & Inverters', 'Starlink Broadband', 'DSTV & Satellite', 'Battery Storage', 'Field Guides'];

const doSearch = () => {
    router.get(route('blog.index'), {
        search:   search.value   || undefined,
        sort:     sortBy.value   || undefined,
        category: activeCategory.value || undefined,
        date:     dateFilter.value || undefined,
        view:     currentView.value || undefined,
    }, { preserveState: true, replace: true });
};

const setCategory = (cat) => {
    activeCategory.value = activeCategory.value === cat ? '' : cat;
    doSearch();
};

watch(search, (val) => { if (!val) doSearch(); });

// Timestamp: "Thursday, July 23, 2026 at 15:25"
const formatFullTimestamp = (d) => {
    if (!d) return '';
    const dateObj = new Date(d);
    const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
    const month   = dateObj.toLocaleDateString('en-US', { month: 'long' });
    const day     = dateObj.getDate();
    const year    = dateObj.getFullYear();
    const hour    = String(dateObj.getHours()).padStart(2, '0');
    const min     = String(dateObj.getMinutes()).padStart(2, '0');
    return `${dayName}, ${month} ${day}, ${year} at ${hour}:${min}`;
};

const getCoverImage = (post) => {
    if (!post) return null;
    if (post.cover_image) return post.cover_image;
    return null;
};

// Share helpers
const shareUrl      = (slug) => window.location.origin + route('blog.show', slug);
const shareText     = (title) => encodeURIComponent(title);
const linkedInShare = (p) => `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl(p.slug))}`;
const whatsappShare = (p) => `https://wa.me/?text=${shareText(p.title)}%20${encodeURIComponent(shareUrl(p.slug))}`;
const twitterShare  = (p) => `https://twitter.com/intent/tweet?text=${shareText(p.title)}&url=${encodeURIComponent(shareUrl(p.slug))}`;

// Featured = first post, side panel = next 3
const featuredPost = computed(() => props.posts?.data?.[0] || null);
const sidePosts    = computed(() => props.posts?.data?.slice(1, 4) || []);
</script>

<template>
    <div class="bg-white min-h-screen">
        <Head title="Engineering Insights & Solar Guides | Zayin Technologies">
            <meta name="description" content="Technical guides on inverter sizing, Starlink mounting, DSTV signal optimization, and lithium battery maintenance in Nigeria." />
        </Head>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!--  HERO BANNER — first homepage slider image                       -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <section class="relative w-full overflow-hidden" style="height: 210px;">
            <img
                :src="heroBgImage"
                alt="Blog"
                class="absolute inset-0 w-full h-full object-cover object-center"
                loading="eager"
            />
            <div class="absolute inset-0 bg-black/45"></div>
            <div class="relative z-10 h-full flex flex-col items-center justify-center text-white gap-3">
                <h1 class="text-4xl md:text-5xl font-black tracking-wide drop-shadow-lg font-['Outfit',sans-serif]">
                    Engineering Blog
                </h1>
                <nav class="flex items-center gap-2 text-sm font-medium text-white/80">
                    <Link :href="route('home')" class="hover:text-white transition-colors">Home</Link>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-white">Engineering Blog</span>
                </nav>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!--  FILTER BAR — Search | Sort | Date | Categories                  -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="sticky top-14 md:top-[100px] z-30 bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center gap-3">

                <!-- Search -->
                <div class="relative flex-1 min-w-[180px] max-w-xs">
                    <input
                        v-model="search"
                        @keyup.enter="doSearch"
                        type="text"
                        placeholder="Search"
                        class="w-full pl-4 pr-10 py-2.5 rounded-md border border-gray-200 bg-white text-sm text-slate-900 focus:ring-2 focus:ring-primary focus:outline-none"
                    />
                    <button @click="doSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>

                <!-- Sort -->
                <div class="relative">
                    <select v-model="sortBy" @change="doSearch" class="pl-4 pr-8 py-2.5 rounded-md border border-gray-200 bg-white text-sm text-slate-700 focus:ring-2 focus:ring-primary focus:outline-none appearance-none">
                        <option value="newest">Sort: Newest</option>
                        <option value="oldest">Sort: Oldest</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M7 12h10M11 17h2"/>
                    </svg>
                </div>

                <!-- Date -->
                <div class="relative">
                    <input v-model="dateFilter" @change="doSearch" type="date" class="pl-4 pr-10 py-2.5 rounded-md border border-gray-200 bg-white text-sm text-slate-700 focus:ring-2 focus:ring-primary focus:outline-none" />
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <div class="flex-1 hidden md:block"></div>

                <!-- Category pills -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="setCategory(cat)"
                        :class="[
                            'px-3 py-1.5 rounded-full text-xs font-bold transition-all border',
                            activeCategory === cat
                                ? 'bg-primary text-white border-primary shadow'
                                : 'bg-white text-slate-600 border-gray-200 hover:border-primary hover:text-primary'
                        ]"
                    >{{ cat }}</button>
                </div>

            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!--  MAIN CONTENT                                                   -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <!-- Empty state -->
            <div v-if="!posts?.data?.length" class="flex flex-col items-center justify-center py-24 gap-4 text-center">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <p class="text-xl font-semibold text-slate-500">No articles found</p>
                <p class="text-xs text-gray-400">Try adjusting your search query or category filter.</p>
            </div>

            <div v-else>

                <!-- ════════════════════════════════════════════════════ -->
                <!--  HERO SPLIT (default)                                -->
                <!-- ════════════════════════════════════════════════════ -->
                <div v-if="props.layoutStyle === 'hero_split'">

                    <div v-if="featuredPost" class="grid grid-cols-1 lg:grid-cols-12 gap-0 rounded-2xl overflow-hidden shadow-2xl" style="min-height: 430px;">

                        <!-- Left: large featured card -->
                        <Link
                            :href="route('blog.show', featuredPost.slug)"
                            class="group relative lg:col-span-7 flex flex-col justify-end bg-slate-900 min-h-[360px] lg:min-h-0"
                        >
                            <img
                                v-if="getCoverImage(featuredPost)"
                                :src="getCoverImage(featuredPost)"
                                :alt="featuredPost.title"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            />
                            <!-- Fallback gradient if no image -->
                            <div v-else class="absolute inset-0 bg-gradient-to-br from-primary/80 to-blue-900"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                            <div class="relative z-10 p-8 text-white text-center">
                                <span v-if="featuredPost.category" class="inline-block bg-primary/80 text-white text-[11px] font-bold px-3 py-0.5 rounded-full mb-3 uppercase">
                                    {{ featuredPost.category }}
                                </span>
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold leading-snug font-['Outfit',sans-serif] drop-shadow-lg">
                                    {{ featuredPost.title }}
                                </h2>
                            </div>
                        </Link>

                        <!-- Right: white/dark panel -->
                        <div class="lg:col-span-5 bg-white border-l border-gray-100 flex flex-col">
                            <div class="flex-1 flex flex-col divide-y divide-gray-100">
                                <div
                                    v-for="post in sidePosts"
                                    :key="post.id"
                                    class="px-5 py-5 flex flex-col gap-3 hover:bg-gray-50 transition-colors"
                                >
                                    <!-- Timestamp -->
                                    <span class="text-[11px] font-medium text-primary tracking-wide block">
                                        {{ formatFullTimestamp(post.published_at) }}
                                    </span>

                                    <Link :href="route('blog.show', post.slug)" class="group/item flex items-start gap-4">
                                        <!-- Thumbnail -->
                                        <div class="w-[90px] h-[68px] rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-gray-200">
                                            <img v-if="getCoverImage(post)" :src="getCoverImage(post)" :alt="post.title" class="w-full h-full object-cover group-hover/item:scale-110 transition-transform duration-300" />
                                            <div v-else class="w-full h-full flex items-center justify-center text-[10px] font-bold text-primary">BLOG</div>
                                        </div>
                                        <!-- Text -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-extrabold text-slate-900 leading-snug line-clamp-2 group-hover/item:text-primary transition-colors font-['Outfit',sans-serif]">
                                                {{ post.title }}
                                            </h3>
                                            <p class="mt-1 text-[11px] leading-relaxed text-slate-600 line-clamp-2">{{ post.excerpt }}</p>
                                        </div>
                                    </Link>
                                </div>
                            </div>

                            <!-- More → button -->
                            <div class="flex justify-end px-5 py-4 border-t border-gray-100">
                                <Link
                                    :href="route('blog.index', { view: 'list' })"
                                    class="inline-flex items-center gap-2 bg-primary/10 hover:bg-primary text-primary hover:text-white border border-primary/30 font-bold px-5 py-2 rounded-full text-xs transition-all"
                                >
                                    <span>More</span><span>→</span>
                                </Link>
                            </div>
                        </div>

                    </div><!-- end split grid -->

                </div><!-- end hero_split -->

                <!-- ════════════════════════════════════════════════════ -->
                <!--  EDITORIAL LIST (view=list / full blog list)          -->
                <!-- ════════════════════════════════════════════════════ -->
                <div v-else class="space-y-0 divide-y divide-gray-100">
                    <div
                        v-for="post in (posts.data || [])"
                        :key="post.id"
                        class="group py-6 flex flex-col sm:flex-row gap-5 hover:bg-gray-50 transition-colors px-1"
                    >
                        <!-- Thumbnail -->
                        <div class="w-full sm:w-[240px] h-[160px] rounded-xl overflow-hidden bg-slate-100 shrink-0">
                            <img
                                v-if="getCoverImage(post)"
                                :src="getCoverImage(post)"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between gap-3">
                            <div class="space-y-2">
                                <span v-if="post.category" class="inline-block bg-primary/10 text-primary text-xs font-bold px-3 py-0.5 rounded-full">{{ post.category }}</span>
                                <Link :href="route('blog.show', post.slug)">
                                    <h2 class="text-lg font-extrabold text-[#1a3a6b] leading-snug hover:underline font-['Outfit',sans-serif] line-clamp-2">
                                        {{ post.title }}
                                    </h2>
                                </Link>
                                <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">{{ post.excerpt }}</p>
                                <p v-if="post.author" class="text-xs text-slate-400">By {{ post.author?.name || 'Admin' }}</p>
                            </div>

                            <!-- Time + Actions -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <span class="text-xs text-slate-500">
                                    <span class="font-semibold">Time :</span> {{ formatFullTimestamp(post.published_at) }}
                                </span>
                                <div class="flex items-center gap-3">
                                    <!-- More Details -->
                                    <Link
                                        :href="route('blog.show', post.slug)"
                                        class="inline-flex items-center gap-1.5 border border-primary text-primary hover:bg-primary hover:text-white text-xs font-bold px-4 py-1.5 rounded-full transition-all"
                                    >
                                        More Details
                                    </Link>
                                    <!-- LinkedIn -->
                                    <a :href="linkedInShare(post)" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-[#0077b5] hover:opacity-80 transition-opacity">
                                        <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452H16.89v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    </a>
                                    <!-- WhatsApp -->
                                    <a :href="whatsappShare(post)" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-[#25d366] hover:opacity-80 transition-opacity">
                                        <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </a>
                                    <!-- X / Twitter -->
                                    <a :href="twitterShare(post)" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-black hover:opacity-80 transition-opacity">
                                        <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── Pagination ────────────────────────────────── -->
                <div v-if="posts?.links?.length > 3" class="flex justify-center mt-8 gap-2 flex-wrap">
                    <template v-for="link in posts.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            :class="[
                                'px-4 py-2 rounded-xl text-xs font-extrabold transition-all border',
                                link.active
                                    ? 'bg-primary text-white border-primary shadow-md'
                                    : 'bg-white text-slate-700 border-gray-200 hover:border-primary hover:text-primary'
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </div>

            </div><!-- end v-else -->
        </div><!-- end main content -->
    </div>
</template>
