<script setup>
defineProps({
    partners: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section v-if="partners && partners.length > 0" class="relative bg-slate-950 border-y border-slate-800/80 py-10 sm:py-12 overflow-hidden">
        <!-- Subtle background glow -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(245,158,11,0.08),rgba(255,255,255,0))] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Tagline -->
            <div class="text-center max-w-3xl mx-auto mb-8">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 text-slate-300 text-xs font-bold tracking-wider uppercase mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Certified OEM Equipment & Brand Ecosystem</span>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-white tracking-tight">
                    Authorized Hardware, Tier-1 Energy Storage & Global Satellite Partners
                </h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">
                    Every installation is engineered with authentic Tier-1 lithium cells, genuine hybrid inverters, and certified dish & terminal mounts.
                </p>
            </div>

            <!-- Brand Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-3 sm:gap-4">
                <component
                    :is="partner.website_url ? 'a' : 'div'"
                    v-for="partner in partners"
                    :key="partner.id"
                    :href="partner.website_url || undefined"
                    :target="partner.website_url ? '_blank' : undefined"
                    rel="noopener noreferrer"
                    class="group relative bg-slate-900/60 hover:bg-slate-800/80 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-4 transition-all duration-300 flex flex-col justify-between hover:shadow-lg hover:shadow-amber-500/5 hover:-translate-y-0.5"
                >
                    <div class="flex items-center space-x-3 mb-2.5">
                        <!-- Icon / Initials Avatar -->
                        <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:border-amber-500/40 transition">
                            <img 
                                v-if="partner.logo_url && partner.logo_url.startsWith('http')" 
                                :src="partner.logo_url" 
                                :alt="partner.name" 
                                class="w-full h-full object-cover rounded-xl opacity-80 group-hover:opacity-100 transition"
                            />
                            <span v-else class="text-sm font-black text-amber-400">
                                {{ partner.name.slice(0, 2).toUpperCase() }}
                            </span>
                        </div>

                        <!-- Partner Name -->
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-white truncate group-hover:text-amber-300 transition flex items-center gap-1">
                                <span>{{ partner.name }}</span>
                                <svg v-if="partner.website_url" class="w-3 h-3 text-slate-500 group-hover:text-amber-400 opacity-0 group-hover:opacity-100 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </h4>
                            <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20 inline-block">
                                Certified
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <p v-if="partner.description" class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed">
                        {{ partner.description }}
                    </p>
                </component>
            </div>

            <!-- Guarantee Footer Banner -->
            <div class="mt-8 pt-6 border-t border-slate-800/60 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Manufacturer Direct Warranties (Up to 5 Years)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Zero Counterfeit Guarantee & Serial-Tracked Units</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Nationwide Engineering Deployment</span>
                </div>
            </div>
        </div>
    </section>
</template>
