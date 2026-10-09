<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import CallToAction from '@/Components/Home/CallToAction.vue';

defineOptions({ layout: Layout });

const props = defineProps({
    project: Object,
    relatedProjects: Array,
    seo: Object,
});

const isPlaying = ref(false);

const primaryVideo = props.project.videos?.[0] || null;
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen">
        <!-- Breadcrumbs & Title Bar -->
        <div class="bg-slate-950 text-white py-12 border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-4">
                    <Link href="/" class="hover:text-amber-400">Home</Link>
                    <span>/</span>
                    <Link href="/projects" class="hover:text-amber-400">Projects</Link>
                    <span>/</span>
                    <span class="text-amber-400 font-bold truncate max-w-sm">{{ project.title }}</span>
                </nav>

                <div class="max-w-3xl space-y-2">
                    <div class="flex items-center space-x-3 text-xs">
                        <span class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-bold">
                            {{ project.service?.name }}
                        </span>
                        <span class="text-slate-400 flex items-center gap-1">
                            <font-awesome-icon :icon="['fas', 'location-dot']" class="text-amber-400" />
                            {{ project.city }}, {{ project.state?.name }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-snug">
                        {{ project.title }}
                    </h1>
                </div>
            </div>
        </div>

        <!-- Video Player & Case Study -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
            <!-- Video Player Facade (Low data friendly) -->
            <div class="aspect-video max-w-5xl mx-auto rounded-3xl bg-slate-950 border border-slate-800 shadow-2xl overflow-hidden relative group">
                <div v-if="!isPlaying" class="w-full h-full relative cursor-pointer" @click="isPlaying = true">
                    <img 
                        :src="primaryVideo?.poster_path || project.featured_image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=1200&auto=format&fit=crop'" 
                        :alt="project.title"
                        class="w-full h-full object-cover group-hover:scale-102 transition duration-500 opacity-90" 
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-slate-950/30"></div>

                    <!-- Big Play Button -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center space-y-3">
                        <div class="w-20 h-20 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-2xl shadow-2xl group-hover:scale-110 transition-transform">
                            <font-awesome-icon :icon="['fas', 'play']" class="ml-1" />
                        </div>
                        <span class="text-white text-xs font-bold uppercase tracking-wider bg-slate-900/80 px-4 py-1.5 rounded-full backdrop-blur-md">
                            Click to Stream Video Walkthrough
                        </span>
                    </div>

                    <div v-if="primaryVideo?.duration_seconds" class="absolute bottom-4 right-4 px-2.5 py-1 rounded-md bg-slate-900/90 text-white font-mono text-xs">
                        {{ Math.floor(primaryVideo.duration_seconds / 60) }}:{{ (primaryVideo.duration_seconds % 60).toString().padStart(2, '0') }}
                    </div>
                </div>

                <!-- Video Active Frame -->
                <div v-else class="w-full h-full bg-black flex items-center justify-center">
                    <iframe 
                        v-if="primaryVideo?.external_url && primaryVideo.external_url.includes('youtube')"
                        :src="primaryVideo.external_url.replace('watch?v=', 'embed/') + '?autoplay=1'" 
                        class="w-full h-full"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                    ></iframe>
                    <video 
                        v-else 
                        controls 
                        autoplay 
                        playsinline 
                        class="w-full h-full object-contain"
                    >
                        <source :src="primaryVideo?.video_path || ''" type="video/mp4" />
                        Your browser does not support HTML5 video playback.
                    </video>
                </div>
            </div>

            <!-- Project Details & Narrative -->
            <div class="max-w-4xl mx-auto space-y-8">
                <!-- Specs Bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-6 rounded-3xl bg-white border border-slate-200 shadow-sm text-center">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase">System Size</span>
                        <div class="text-base font-black text-slate-900 mt-0.5">{{ project.system_size || 'Custom Setup' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Location</span>
                        <div class="text-base font-black text-slate-900 mt-0.5">{{ project.city }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Completed</span>
                        <div class="text-base font-black text-slate-900 mt-0.5">{{ project.completed_on || 'Recent' }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Client Profile</span>
                        <div class="text-base font-black text-slate-900 mt-0.5">{{ project.client_name || 'Residential' }}</div>
                    </div>
                </div>

                <!-- Body Narrative -->
                <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm space-y-6">
                    <h2 class="text-2xl font-black text-slate-900">Project Overview & Challenge</h2>
                    <p class="text-slate-700 text-base leading-relaxed">
                        {{ project.summary }}
                    </p>
                    <div class="text-slate-700 text-base leading-relaxed space-y-4 whitespace-pre-line border-t border-slate-100 pt-6">
                        {{ project.body }}
                    </div>

                    <div v-if="primaryVideo?.transcript" class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2">
                        <span class="font-bold text-slate-800 uppercase tracking-wider block">Video Description / Walkthrough Notes:</span>
                        <p class="leading-relaxed">{{ primaryVideo.transcript }}</p>
                    </div>
                </div>

                <!-- Want a similar setup? CTA -->
                <div class="rounded-3xl bg-amber-500 p-8 text-slate-950 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
                    <div class="space-y-1 text-center sm:text-left">
                        <h3 class="text-xl sm:text-2xl font-black">Want a Similar Installation for Your Property?</h3>
                        <p class="text-xs sm:text-sm font-medium text-slate-900 max-w-xl">
                            Our engineers assess your energy consumption or internet coverage and deploy the right equipment.
                        </p>
                    </div>
                    <Link 
                        href="/request-quote"
                        class="px-8 py-3.5 rounded-2xl bg-slate-950 hover:bg-slate-900 text-white font-bold text-sm shrink-0 transition"
                    >
                        Request Quote For This
                    </Link>
                </div>
            </div>
        </div>

        <CallToAction />
    </div>
</template>
