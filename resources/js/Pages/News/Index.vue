<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { NewsCard } from '@/Components/Cards';

defineOptions({ layout: Layout });

const props = defineProps({
    articles: Object,
    filters: Object,
    layoutStyle: {
        type: String,
        default: 'hero_split'
    }
});

const page = usePage();

// ─── Hero banner: first slide image from settings (same as homepage slider) ──
const heroBgImage = computed(() => {
    const slides = page.props.settings?.hero_slides;
    if (slides && Array.isArray(slides) && slides.length > 0) {
        return slides[0]?.value || slides[0];
    }
    return 'https://images.unsplash.com/photo-1516549655169-df83a0774514?q=80&w=2070&auto=format&fit=crop';
});

const search    = ref(props.filters?.search || '');
const sortBy    = ref(props.filters?.sort   || 'newest');
const dateFilter = ref(props.filters?.date  || '');
const activeCategory = ref(props.filters?.category || 'all');
const currentView    = ref(props.filters?.view     || '');  // '' = hero_split, 'list' = editorial list

// Tabs matching screenshot: News | Events | Videos
const categories = [
    { value: 'news',   label: 'News',   icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z' },
    { value: 'event',  label: 'Events', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { value: 'media',  label: 'Videos', icon: 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
];

const doSearch = () => {
    router.get(route('news.index'), {
        search:   search.value   || undefined,
        sort:     sortBy.value   || undefined,
        category: activeCategory.value === 'all' ? undefined : activeCategory.value,
        date:     dateFilter.value || undefined,
        view:     currentView.value || undefined,   // keep list mode across searches
    }, { preserveState: true, replace: true });
};

const setCategory = (cat) => {
    activeCategory.value = cat;
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

const getCoverImage = (article) => {
    if (!article) return null;
    if (article.cover_image) return article.cover_image;
    if (article.media_gallery?.length > 0) {
        const img = article.media_gallery.find(m => m.type === 'image');
        if (img) return img.url;
    }
    return null;
};

// Share helpers
const shareUrl  = (slug) => window.location.origin + route('news.show', slug);
const shareText = (title) => encodeURIComponent(title);
const linkedInShare  = (a) => `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl(a.slug))}`;
const whatsappShare  = (a) => `https://wa.me/?text=${shareText(a.title)}%20${encodeURIComponent(shareUrl(a.slug))}`;
const twitterShare   = (a) => `https://twitter.com/intent/tweet?text=${shareText(a.title)}&url=${encodeURIComponent(shareUrl(a.slug))}`;

// Featured hero + side panel
const featuredArticle   = computed(() => props.articles?.data?.[0] || null);
const sideArticles      = computed(() => props.articles?.data?.slice(1, 4) || []);
const remainingArticles = computed(() => props.articles?.data?.slice(4) || []);
</script>

<template>
    <div class="bg-white min-h-screen">
        <Head title="Media Center & News | Zayin Technologies">
            <meta name="description" content="Media Center – news, field updates, and corporate announcements from Zayin Technologies." />
        </Head>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!--  HERO BANNER — full-width hospital image + "News" + breadcrumb  -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <section class="relative w-full overflow-hidden" style="height: 210px;">
            <!-- Background: first homepage slider image -->
            <img
                :src="heroBgImage"
                alt="Media Center"
                class="absolute inset-0 w-full h-full object-cover object-center"
                loading="eager"
            />
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/45"></div>

            <!-- Text content -->
            <div class="relative z-10 h-full flex flex-col items-center justify-center text-white gap-3">
                <h1 class="text-4xl md:text-5xl font-black tracking-wide drop-shadow-lg font-['Outfit',sans-serif]">
                    {{ activeCategory === 'event' ? 'Events' : activeCategory === 'media' ? 'Videos' : 'News' }}
                </h1>
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm font-medium text-white/80">
                    <Link :href="route('home')" class="hover:text-white transition-colors">Home</Link>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-white">{{ activeCategory === 'event' ? 'Events' : activeCategory === 'media' ? 'Videos' : 'News' }}</span>
                </nav>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!--  FILTER BAR — Search | Sort | Date  (matching screenshot)       -->
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
                    <select
                        v-model="sortBy"
                        @change="doSearch"
                        class="pl-4 pr-8 py-2.5 rounded-md border border-gray-200 bg-white text-sm text-slate-700 focus:ring-2 focus:ring-primary focus:outline-none appearance-none"
                    >
                        <option value="newest">Sort: Newest</option>
                        <option value="oldest">Sort: Oldest</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M7 12h10M11 17h2"/>
                    </svg>
                </div>

                <!-- Date -->
                <div class="relative">
                    <input
                        v-model="dateFilter"
                        @change="doSearch"
                        type="date"
                        class="pl-4 pr-10 py-2.5 rounded-md border border-gray-200 bg-white text-sm text-slate-700 focus:ring-2 focus:ring-primary focus:outline-none"
                    />
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>

                <!-- Spacer -->
                <div class="flex-1 hidden md:block"></div>

                <!-- Category Tabs -->
                <div class="flex items-center gap-2">
                    <button
                        v-for="cat in categories"
                        :key="cat.value"
                        @click="setCategory(cat.value)"
                        :class="[
                            'inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold transition-all',
                            activeCategory === cat.value
                                ? 'bg-primary text-white shadow'
                                : 'bg-white text-slate-600 border border-gray-200 hover:border-primary hover:text-primary'
                        ]"
                    >
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="cat.icon"/>
                        </svg>
                        {{ cat.label }}
                    </button>
                </div>

            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════ -->
        <!--  MAIN CONTENT                                                   -->
        <!-- ═══════════════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <!-- Empty state -->
            <div v-if="!articles?.data?.length" class="flex flex-col items-center justify-center py-24 gap-4 text-center">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <p class="text-xl font-semibold text-slate-500">No articles found</p>
                <p class="text-xs text-gray-400">Try a different category or clear the search.</p>
            </div>

            <div v-else>

                <!-- ─── STYLE: hero_split ─────────────────────────────── -->
                <div v-if="props.layoutStyle === 'hero_split'" class="space-y-10">

                    <!-- Top hero + side panel -->
                    <div v-if="featuredArticle" class="grid grid-cols-1 lg:grid-cols-12 gap-0 rounded-2xl overflow-hidden shadow-2xl" style="min-height: 430px;">

                        <!-- Left: large hero card -->
                        <Link
                            :href="route('news.show', featuredArticle.slug)"
                            class="group relative lg:col-span-7 flex flex-col justify-end bg-slate-900 min-h-[360px] lg:min-h-0"
                        >
                            <img
                                v-if="getCoverImage(featuredArticle)"
                                :src="getCoverImage(featuredArticle)"
                                :alt="featuredArticle.title"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                            <div class="relative z-10 p-8 text-white text-center">
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold leading-snug font-['Outfit',sans-serif] drop-shadow-lg">
                                    {{ featuredArticle.title }}
                                </h2>
                            </div>
                        </Link>

                        <!-- Right: dark navy panel -->
                        <div class="lg:col-span-5 bg-white border-l border-gray-100 flex flex-col">
                            <div class="flex-1 flex flex-col divide-y divide-gray-100">
                                <div
                                    v-for="item in sideArticles"
                                    :key="item.id"
                                    class="px-5 py-5 flex flex-col gap-3 hover:bg-gray-50 transition-colors"
                                >
                                    <span class="text-[11px] font-medium text-primary tracking-wide block">
                                        {{ formatFullTimestamp(item.published_at) }}
                                    </span>
                                    <Link :href="route('news.show', item.slug)" class="group/item flex items-start gap-4">
                                        <div class="w-[90px] h-[68px] rounded-lg bg-slate-900 overflow-hidden shrink-0 border border-white/10">
                                            <img v-if="getCoverImage(item)" :src="getCoverImage(item)" :alt="item.title" class="w-full h-full object-cover group-hover/item:scale-110 transition-transform duration-300" />
                                            <div v-else class="w-full h-full flex items-center justify-center text-[10px] font-bold text-sky-300">NEWS</div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-extrabold text-slate-900 leading-snug line-clamp-2 group-hover/item:text-primary transition-colors font-['Outfit',sans-serif]">
                                                {{ item.title }}
                                            </h3>
                                            <p class="mt-1 text-[11px] leading-relaxed text-slate-600 line-clamp-2">{{ item.excerpt }}</p>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                            <!-- More → -->
                            <div class="flex justify-end px-5 py-4 border-t border-gray-100">
                                <Link
                                    :href="route('news.index', { view: 'list' })"
                                    class="inline-flex items-center gap-2 bg-primary/10 hover:bg-primary text-primary hover:text-white border border-primary/30 font-bold px-5 py-2 rounded-full text-xs transition-all"
                                >
                                    <span>More</span><span>→</span>
                                </Link>
                            </div>
                        </div>
                    </div><!-- end top split -->

                </div><!-- end hero_split -->



                <!-- ─── STYLE: magazine_grid ──────────────────────────── -->
                <div v-else-if="props.layoutStyle === 'magazine_grid'" class="space-y-10">
                    <div v-if="featuredArticle" class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-md grid grid-cols-1 lg:grid-cols-2">
                        <div class="relative h-72 lg:h-auto overflow-hidden">
                            <img v-if="getCoverImage(featuredArticle)" :src="getCoverImage(featuredArticle)" :alt="featuredArticle.title" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
                        </div>
                        <div class="p-8 flex flex-col justify-center space-y-4">
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-['Outfit',sans-serif]">
                                <Link :href="route('news.show', featuredArticle.slug)" class="hover:text-primary transition-colors">{{ featuredArticle.title }}</Link>
                            </h2>
                            <p class="text-slate-600 text-sm leading-relaxed line-clamp-3">{{ featuredArticle.excerpt }}</p>
                            <span class="text-xs font-semibold text-slate-400">{{ formatFullTimestamp(featuredArticle.published_at) }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <NewsCard
                            v-for="article in (props.articles?.data || [])"
                            :key="article.id"
                            :title="article.title"
                            :excerpt="article.excerpt"
                            :image="getCoverImage(article)"
                            :date="formatFullTimestamp(article.published_at)"
                            :category="article.category || activeCategory"
                            :href="route('news.show', article.slug)"
                        />
                    </div>
                </div>

                <!-- ─── STYLE: editorial_list (full list, similar to screenshot) ── -->
                <div v-else class="space-y-0 divide-y divide-gray-100">
                    <div
                        v-for="article in (props.articles?.data || [])"
                        :key="article.id"
                        class="group py-6 flex flex-col sm:flex-row gap-5 hover:bg-gray-50 transition-colors px-1"
                    >
                        <div class="w-full sm:w-[240px] h-[160px] rounded-xl overflow-hidden bg-slate-100 shrink-0">
                            <img v-if="getCoverImage(article)" :src="getCoverImage(article)" :alt="article.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <div v-else class="w-full h-full flex items-center justify-center text-xs font-bold text-slate-400">NO IMAGE</div>
                        </div>
                        <div class="flex-1 min-w-0 flex flex-col justify-between gap-3">
                            <div class="space-y-2">
                                <Link :href="route('news.show', article.slug)">
                                    <h2 class="text-lg font-extrabold text-[#1a3a6b] leading-snug hover:underline font-['Outfit',sans-serif] line-clamp-2">{{ article.title }}</h2>
                                </Link>
                                <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">{{ article.excerpt }}</p>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <span class="text-xs text-slate-500"><span class="font-semibold">Time :</span> {{ formatFullTimestamp(article.published_at) }}</span>
                                <div class="flex items-center gap-3">
                                    <Link :href="route('news.show', article.slug)" class="inline-flex items-center gap-1.5 border border-primary text-primary hover:bg-primary hover:text-white text-xs font-bold px-4 py-1.5 rounded-full transition-all">More Details</Link>
                                    <a :href="linkedInShare(article)" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-[#0077b5] hover:opacity-80 transition-opacity"><svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452H16.89v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                                    <a :href="whatsappShare(article)" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-[#25d366] hover:opacity-80 transition-opacity"><svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                                    <a :href="twitterShare(article)" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-black hover:opacity-80 transition-opacity"><svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── Pagination ─────────────────────────────────────── -->
                <div v-if="articles?.links?.length > 3" class="flex justify-center mt-8 gap-2 flex-wrap">
                    <template v-for="link in articles.links" :key="link.label">
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
