<script setup>
import { ref } from 'vue';

const props = defineProps({
    partners: {
        type: Array,
        default: () => [],
    },
});

// Mobile view mode: 'grid' (2 columns) | 'list' (detailed stacked rows)
// Both modes are vertically stacked with ZERO horizontal swiping so all actions are immediately visible.
const mobileViewMode = ref('grid');

// Fallback image error tracker
const failedLogos = ref({});
const handleLogoError = (id) => {
    failedLogos.value[id] = true;
};
</script>

<template>
    <section v-if="partners && partners.length > 0" class="relative bg-slate-950 border-y border-slate-800/80 py-10 sm:py-14 overflow-hidden">
        <!-- Subtle background glow -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(245,158,11,0.08),rgba(255,255,255,0))] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Tagline -->
            <div class="text-center max-w-3xl mx-auto mb-6 sm:mb-8">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 text-slate-300 text-xs font-bold tracking-wider uppercase mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Certified OEM Equipment &amp; Brand Ecosystem</span>
                </div>
                <h3 class="text-lg sm:text-2xl font-black text-white tracking-tight">
                    Authorized Hardware, Tier-1 Energy Storage &amp; Global Satellite Partners
                </h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-2xl mx-auto">
                    Every installation is engineered with authentic Tier-1 lithium cells, genuine hybrid inverters, and certified dish &amp; terminal mounts.
                </p>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 1. CUSTOM MOBILE VIEW (< sm screens)                       -->
            <!-- Vertically accessible, NO horizontal swipe hidden actions  -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div class="block sm:hidden space-y-4">
                <!-- Mobile View Controls -->
                <div class="flex items-center justify-between px-1">
                    <span class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>All {{ partners.length }} Official Partners</span>
                    </span>
                    <div class="inline-flex rounded-xl bg-slate-900 border border-slate-800 p-0.5 shadow-sm">
                        <button
                            type="button"
                            @click="mobileViewMode = 'grid'"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer"
                            :class="mobileViewMode === 'grid' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                            aria-label="Grid View"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Grid</span>
                        </button>
                        <button
                            type="button"
                            @click="mobileViewMode = 'list'"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer"
                            :class="mobileViewMode === 'list' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                            aria-label="List View"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span>List</span>
                        </button>
                    </div>
                </div>

                <!-- Mode A: Dedicated 2-Column Direct-Action Grid (Default) -->
                <div v-if="mobileViewMode === 'grid'" class="grid grid-cols-2 gap-2.5">
                    <div
                        v-for="partner in partners"
                        :key="partner.id"
                        class="bg-gradient-to-b from-slate-900/90 via-slate-900 to-slate-950 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-3 flex flex-col justify-between shadow-lg"
                    >
                        <div>
                            <!-- Top: Logo & Certified Badge -->
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700/80 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                                    <img 
                                        v-if="partner.logo_url && !failedLogos[partner.id]" 
                                        :src="partner.logo_url" 
                                        :alt="partner.name" 
                                        class="w-full h-full object-cover rounded-lg"
                                        @error="handleLogoError(partner.id)"
                                    />
                                    <span v-else class="text-xs font-black text-amber-400">
                                        {{ partner.name.slice(0, 2).toUpperCase() }}
                                    </span>
                                </div>
                                <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20">
                                    ✓ OEM
                                </span>
                            </div>

                            <!-- Partner Name -->
                            <h4 class="text-xs font-bold text-white line-clamp-1 mb-1">
                                {{ partner.name }}
                            </h4>

                            <!-- Short Description -->
                            <p v-if="partner.description" class="text-[10px] text-slate-300 leading-tight line-clamp-2">
                                {{ partner.description }}
                            </p>
                        </div>

                        <!-- Immediately Visible Action Button -->
                        <div class="mt-3 pt-2.5 border-t border-slate-800/80">
                            <a
                                v-if="partner.website_url"
                                :href="partner.website_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full py-2 px-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-black text-[11px] flex items-center justify-center gap-1 shadow-md shadow-amber-500/20 active:scale-95 transition"
                            >
                                <span>Official Site</span>
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <div
                                v-else
                                class="w-full py-1.5 px-2 rounded-xl bg-slate-800/70 text-slate-400 font-semibold text-[10px] text-center"
                            >
                                Tier-1 Partner
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mode B: Detailed List View (When Toggled) -->
                <div v-else class="space-y-2.5">
                    <div
                        v-for="partner in partners"
                        :key="partner.id"
                        class="bg-gradient-to-b from-slate-900/90 via-slate-900 to-slate-950 border border-slate-800 rounded-2xl p-3.5 shadow-md flex items-center justify-between gap-3"
                    >
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                                <img 
                                    v-if="partner.logo_url && !failedLogos[partner.id]" 
                                    :src="partner.logo_url" 
                                    :alt="partner.name" 
                                    class="w-full h-full object-cover rounded-lg"
                                    @error="handleLogoError(partner.id)"
                                />
                                <span v-else class="text-xs font-black text-amber-400">
                                    {{ partner.name.slice(0, 2).toUpperCase() }}
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="text-xs sm:text-sm font-bold text-white truncate">
                                        {{ partner.name }}
                                    </h4>
                                    <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-1 py-0.5 rounded border border-emerald-500/20 shrink-0">
                                        ✓ OEM
                                    </span>
                                </div>
                                <p v-if="partner.description" class="text-[11px] text-slate-300 mt-0.5 line-clamp-2 leading-relaxed">
                                    {{ partner.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="shrink-0">
                            <a
                                v-if="partner.website_url"
                                :href="partner.website_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-black text-xs flex items-center gap-1 shadow-md shadow-amber-500/20 active:scale-95 transition whitespace-nowrap"
                            >
                                <span>Visit</span>
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <span v-else class="text-[10px] font-semibold text-slate-500">Certified</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- 2. DESKTOP & TABLET VIEW (sm+ screens)                     -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div class="hidden sm:grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <component
                    :is="partner.website_url ? 'a' : 'div'"
                    v-for="partner in partners"
                    :key="partner.id"
                    :href="partner.website_url || undefined"
                    :target="partner.website_url ? '_blank' : undefined"
                    rel="noopener noreferrer"
                    class="group relative bg-slate-900/60 hover:bg-slate-800/80 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-4 transition-all duration-300 flex flex-col justify-between hover:shadow-lg hover:shadow-amber-500/5 hover:-translate-y-0.5"
                >
                    <div>
                        <div class="flex items-center space-x-3 mb-2.5">
                            <!-- Icon / Initials Avatar -->
                            <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:border-amber-500/40 transition overflow-hidden">
                                <img 
                                    v-if="partner.logo_url && !failedLogos[partner.id]" 
                                    :src="partner.logo_url" 
                                    :alt="partner.name" 
                                    class="w-full h-full object-cover rounded-xl opacity-80 group-hover:opacity-100 transition"
                                    @error="handleLogoError(partner.id)"
                                />
                                <span v-else class="text-sm font-black text-amber-400">
                                    {{ partner.name.slice(0, 2).toUpperCase() }}
                                </span>
                            </div>

                            <!-- Partner Name -->
                            <div class="min-w-0 flex-1">
                                <h4 class="text-sm font-bold text-white truncate group-hover:text-amber-300 transition flex items-center justify-between gap-1">
                                    <span class="truncate">{{ partner.name }}</span>
                                    <svg v-if="partner.website_url" class="w-3 h-3 text-slate-500 group-hover:text-amber-400 opacity-0 group-hover:opacity-100 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </h4>
                                <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20 inline-block">
                                    Certified OEM
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p v-if="partner.description" class="text-[11px] text-slate-300 line-clamp-2 leading-relaxed">
                            {{ partner.description }}
                        </p>
                    </div>

                    <!-- Direct Action Link on Desktop -->
                    <div v-if="partner.website_url" class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
                        <span class="text-slate-400 group-hover:text-amber-300 font-semibold transition">Visit Official Website</span>
                        <span class="text-amber-400 font-bold group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </component>
            </div>

            <!-- Guarantee Footer Banner -->
            <div class="mt-8 pt-6 border-t border-slate-800/60">
                <!-- Mobile Guarantee Pills -->
                <div class="grid grid-cols-1 gap-2.5 sm:hidden">
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-900/70 border border-slate-800 text-xs text-slate-300">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Manufacturer Direct Warranties (Up to 5 Years)</span>
                    </div>
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-900/70 border border-slate-800 text-xs text-slate-300">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Zero Counterfeit Guarantee &amp; Serial Tracking</span>
                    </div>
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-900/70 border border-slate-800 text-xs text-slate-300">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Nationwide Engineering Deployment</span>
                    </div>
                </div>

                <!-- Desktop Guarantee Strip -->
                <div class="hidden sm:flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Manufacturer Direct Warranties (Up to 5 Years)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Zero Counterfeit Guarantee &amp; Serial-Tracked Units</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Nationwide Engineering Deployment</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
