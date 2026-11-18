<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { generateWhatappLink } from '@/Utils/whatsappLinkGen';

const props = defineProps({
    heroSlides: {
        type: Array,
        default: () => [],
    },
    featuredProduct: {
        type: Object,
        default: null,
    },
});

const page = usePage();

const site = computed(() => page.props.site || {
    phone: '+234 803 123 4567',
    whatsapp: '2348031234567',
});

// ─── HERO BACKGROUND SLIDESHOW (From Admin Settings) ────────────────────────
const defaultBackdrop = 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?q=80&w=1920&auto=format&fit=crop';

const defaultSlides = [
    { value: 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?q=80&w=1920&auto=format&fit=crop' },
    { value: 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?q=80&w=1920&auto=format&fit=crop' },
    { value: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=1920&auto=format&fit=crop' },
];

const activeSlides = computed(() => {
    const custom = (props.heroSlides || [])
        .filter(s => s && s.value && typeof s.value === 'string' && s.value.trim() !== '')
        .map(s => ({ value: s.value }));
    return custom.length > 0 ? custom : defaultSlides;
});

const currentSlideIndex = ref(0);
let timer = null;

const startSlideTimer = () => {
    stopSlideTimer();
    if (activeSlides.value.length > 1) {
        timer = setInterval(() => {
            currentSlideIndex.value = (currentSlideIndex.value + 1) % activeSlides.value.length;
        }, 6000);
    }
};

const stopSlideTimer = () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
};

const setSlide = (idx) => {
    currentSlideIndex.value = idx;
    startSlideTimer();
};

onMounted(() => {
    startSlideTimer();
});

onUnmounted(() => {
    stopSlideTimer();
});

// ─── RIGHT-HAND TURNKEY PACKAGE CARD IMAGE (Independent from hero slides) ────
const packageCardImage = computed(() => {
    if (props.featuredProduct?.image) {
        return props.featuredProduct.image;
    }
    return 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?q=80&w=800&auto=format&fit=crop';
});

const whatsappUrl = computed(() => {
    const raw = site.value.whatsapp || '2348031234567';
    const clean = String(raw).replace(/[^0-9]/g, '');
    return generateWhatappLink(clean, 'Hello Zayin Ltd, I would like to enquire about your Solar, Starlink or DSTV installation packages.');
});
</script>

<template>
    <section 
        class="relative bg-slate-950 text-white overflow-hidden py-16 sm:py-24 lg:py-28 min-h-[640px] flex flex-col justify-center"
        @mouseenter="stopSlideTimer"
        @mouseleave="startSlideTimer"
    >
        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- 1. FULL HERO BACKGROUND SLIDER (Dynamic from Admin Settings)    -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                v-for="(slide, idx) in activeSlides"
                :key="idx"
                class="absolute inset-0 transition-all duration-1000 ease-in-out"
                :class="currentSlideIndex === idx ? 'opacity-40 scale-100' : 'opacity-0 scale-105'"
                style="transition-property: opacity, transform; transition-duration: 1200ms;"
            >
                <img 
                    :src="slide.value" 
                    alt="Zayin Engineering Hero Background"
                    class="w-full h-full object-cover object-center"
                    @error="(e) => e.target.src = defaultBackdrop"
                />
            </div>

            <!-- Multi-layer protective overlays: guarantees text is 100% readable -->
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-950/80 to-slate-950"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_20%,rgba(245,158,11,0.14),transparent_70%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:24px_24px]"></div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- 2. FOREGROUND CONTENT & GRID                                 -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column: Copy & CTAs (7 cols) -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left" data-aos="fade-right">
                    <!-- Badge -->
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs sm:text-sm font-bold tracking-wide">
                        <font-awesome-icon :icon="['fas', 'bolt']" class="text-amber-400" />
                        <span>Nigeria's Trusted Engineering &amp; Tech Partner</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15] text-white">
                        Power Your Home 24/7.
                        <span class="block bg-gradient-to-r from-amber-400 via-amber-300 to-yellow-500 bg-clip-text text-transparent">
                            Connect Faster. Never Cut Out.
                        </span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Say goodbye to soaring generator fuel costs, buffering internet, and frustrating TV signal drops. We install engineered <strong class="text-white font-semibold">Hybrid Solar Systems</strong>, zero-obstruction <strong class="text-white font-semibold">Starlink Satellite Wi-Fi</strong>, and certified <strong class="text-white font-semibold">DSTV Extra View</strong> setups across Nigeria.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <Link 
                            href="/request-quote"
                            class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-white font-black text-base shadow-xl shadow-amber-500/20 hover:shadow-amber-500/30 transition transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center space-x-2.5"
                        >
                            <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" />
                            <span>Get Free Instant Quote</span>
                        </Link>

                        <a 
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-emerald-600/90 hover:bg-emerald-600 text-white font-bold text-base border border-emerald-500/30 shadow-lg shadow-emerald-950/50 transition transform hover:-translate-y-0.5 flex items-center justify-center space-x-2.5"
                        >
                            <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-xl" />
                            <span>Chat on WhatsApp</span>
                        </a>

                        <Link 
                            href="/packages"
                            class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 font-bold text-base border border-slate-700 transition flex items-center justify-center space-x-2"
                        >
                            <span>Browse Packages</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-xs" />
                        </Link>
                    </div>

                    <!-- Trust Stats Bar -->
                    <div class="pt-6 border-t border-slate-800/80 grid grid-cols-3 gap-4 max-w-xl mx-auto lg:mx-0 text-center lg:text-left">
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-amber-400">0 Seconds</div>
                            <div class="text-xs text-slate-400 mt-0.5">Switchover Transfer</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-sky-400">200+ Mbps</div>
                            <div class="text-xs text-slate-400 mt-0.5">Starlink Speeds</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-black text-emerald-400">5 Years</div>
                            <div class="text-xs text-slate-400 mt-0.5">Battery Warranty</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Independent Package Showcase Card (5 cols) -->
                <div class="lg:col-span-5 relative" data-aos="fade-left">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Main Preview Card -->
                        <div class="rounded-3xl bg-slate-800/70 backdrop-blur-xl border border-slate-700/80 p-6 shadow-2xl relative overflow-hidden group">
                            <!-- Background Image with Overlay -->
                            <div class="aspect-[16/10] rounded-2xl overflow-hidden mb-5 relative bg-slate-900">
                                <img 
                                    :src="packageCardImage" 
                                    :alt="featuredProduct?.name || 'Turnkey Engineering Package'" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    @error="(e) => e.target.src = 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?q=80&w=800&auto=format&fit=crop'"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-amber-500 text-white text-xs font-black uppercase tracking-wider shadow-sm">
                                    {{ featuredProduct?.category?.name || 'Turnkey Package' }}
                                </span>
                                <span class="absolute bottom-3 left-3 text-xs font-semibold text-white bg-slate-900/85 backdrop-blur-md px-3 py-1 rounded-full border border-slate-700/60">
                                    <font-awesome-icon :icon="['fas', 'location-dot']" class="text-amber-400 mr-1" />
                                    Lekki Phase 1, Lagos
                                </span>
                            </div>

                            <!-- Card Content -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">
                                        {{ featuredProduct?.specs?.capacity_kva ? `${featuredProduct.specs.capacity_kva}kVA Hybrid` : '5kVA / 48V Premium Hybrid' }}
                                    </span>
                                    <span class="text-xs text-slate-400">1HP Inverter AC Ready</span>
                                </div>
                                <h3 class="text-lg font-bold text-white leading-snug line-clamp-1">
                                    {{ featuredProduct?.name || 'Complete Household Solar System' }}
                                </h3>
                                <p class="text-xs text-slate-300 leading-relaxed line-clamp-2">
                                    {{ featuredProduct?.summary || 'Includes Pure Sine Wave Hybrid Inverter, LiFePO4 Lithium Battery, Tier-1 Mono Solar Panels, and Turnkey Engineering Setup.' }}
                                </p>

                                <div class="pt-3 border-t border-slate-700/60 flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Installed Price</div>
                                        <div class="text-xl font-black text-amber-400">
                                            {{ featuredProduct?.price ? `₦${Number(featuredProduct.price).toLocaleString()}` : '₦3,450,000' }}
                                        </div>
                                    </div>
                                    <Link 
                                        :href="featuredProduct ? `/packages/${featuredProduct.category?.slug || 'all'}/${featuredProduct.slug}` : '/packages'"
                                        class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-amber-500 hover:text-white text-white font-bold text-xs transition shadow-sm"
                                    >
                                        View Inclusions &rarr;
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Floating Badge: Starlink Verified -->
                        <div class="absolute -bottom-5 -left-4 sm:-left-6 bg-slate-900/90 backdrop-blur-md border border-slate-700 rounded-2xl p-3 shadow-2xl flex items-center space-x-3 text-xs">
                            <div class="w-9 h-9 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-base">
                                <font-awesome-icon :icon="['fas', 'satellite-dish']" />
                            </div>
                            <div>
                                <div class="font-bold text-white">Starlink Pro Mounts</div>
                                <div class="text-slate-400 text-[10px]">Zero obstruction elevation</div>
                            </div>
                        </div>

                        <!-- Mini Floating Badge: Warranty -->
                        <div class="absolute -top-4 -right-4 sm:-right-6 bg-slate-900/90 backdrop-blur-md border border-slate-700 rounded-2xl p-3 shadow-2xl flex items-center space-x-3 text-xs">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-base">
                                <font-awesome-icon :icon="['fas', 'shield-halved']" />
                            </div>
                            <div>
                                <div class="font-bold text-white">Verified Warranty</div>
                                <div class="text-slate-400 text-[10px]">Certified electrical engineers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- 3. SLIDER INDICATOR DOTS (Visible when > 1 slide)            -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div v-if="activeSlides.length > 1" class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
            <button
                v-for="(s, idx) in activeSlides"
                :key="idx"
                type="button"
                @click="setSlide(idx)"
                class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                :class="currentSlideIndex === idx ? 'w-8 bg-amber-400 shadow-sm shadow-amber-400/50' : 'w-2 bg-slate-600/70 hover:bg-slate-400'"
                :aria-label="`Go to slide ${idx + 1}`"
            />
        </div>
    </section>
</template>
