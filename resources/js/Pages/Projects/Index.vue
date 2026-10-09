<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link, router } from '@inertiajs/vue3';
import CallToAction from '@/Components/Home/CallToAction.vue';

defineOptions({ layout: Layout });

const props = defineProps({
    projects: Object,
    services: Array,
    states: Array,
    filters: Object,
    seo: Object,
});

const serviceFilter = ref(props.filters?.service || '');
const stateFilter = ref(props.filters?.state || '');

const handleFilter = () => {
    router.get('/projects', {
        service: serviceFilter.value || undefined,
        state: stateFilter.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen">
        <!-- Header Banner -->
        <div class="bg-slate-950 text-white py-16 sm:py-20 border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-4">
                    <Link href="/" class="hover:text-amber-400">Home</Link>
                    <span>/</span>
                    <span class="text-amber-400 font-bold">Projects</span>
                </nav>

                <div class="max-w-3xl space-y-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full inline-block">
                        Field Proof & Case Studies
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">
                        Completed Installations Across Nigeria
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        Watch video walkthroughs, load testing, and inspect real craftsmanship from duplexes, clinics, offices, and residential compounds.
                    </p>
                </div>

                <!-- Filters -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <select 
                        v-model="serviceFilter"
                        @change="handleFilter"
                        class="rounded-xl border border-slate-700 bg-slate-900 text-white text-xs px-4 py-2 font-semibold focus:ring-2 focus:ring-amber-500"
                    >
                        <option value="">All Services</option>
                        <option v-for="srv in services" :key="srv.id" :value="srv.slug">
                            {{ srv.name }}
                        </option>
                    </select>

                    <select 
                        v-model="stateFilter"
                        @change="handleFilter"
                        class="rounded-xl border border-slate-700 bg-slate-900 text-white text-xs px-4 py-2 font-semibold focus:ring-2 focus:ring-amber-500"
                    >
                        <option value="">All Locations / States</option>
                        <option v-for="st in states" :key="st.id" :value="st.id">
                            {{ st.name }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div v-if="!projects.data || projects.data.length === 0" class="text-center py-20 bg-white rounded-3xl border border-slate-200">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <font-awesome-icon :icon="['fas', 'video-slash']" />
                </div>
                <h3 class="text-lg font-bold text-slate-800">No project case studies match your filter</h3>
                <p class="text-xs text-slate-500 mt-1">Try resetting the filter above.</p>
                <Link href="/projects" class="mt-4 inline-block px-5 py-2 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold">
                    View All Projects
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="project in projects.data" 
                    :key="project.id"
                    class="rounded-3xl bg-white border border-slate-200 hover:border-amber-400 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group"
                >
                    <div>
                        <!-- Thumbnail with Video Indicator -->
                        <div class="aspect-[16/10] bg-slate-900 relative overflow-hidden">
                            <img 
                                :src="project.featured_image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop'" 
                                :alt="project.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>

                            <!-- Play Icon Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <font-awesome-icon :icon="['fas', 'play']" class="ml-1 text-sm" />
                                </div>
                            </div>

                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-[10px] font-bold text-amber-400">
                                {{ project.service?.name }}
                            </span>

                            <span class="absolute bottom-3 left-3 text-xs font-semibold text-slate-300 flex items-center gap-1">
                                <font-awesome-icon :icon="['fas', 'location-dot']" class="text-amber-400" />
                                <span>{{ project.city }}, {{ project.state?.name }}</span>
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-2.5">
                            <div v-if="project.system_size" class="text-xs font-bold text-amber-600">
                                {{ project.system_size }}
                            </div>

                            <Link 
                                :href="`/projects/${project.slug}`"
                                class="block font-bold text-lg text-slate-900 group-hover:text-amber-600 transition leading-snug line-clamp-2"
                            >
                                {{ project.title }}
                            </Link>

                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ project.summary }}
                            </p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 border-t border-slate-100 mt-4">
                        <Link 
                            :href="`/projects/${project.slug}`"
                            class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center justify-between pt-3"
                        >
                            <span>Watch Walkthrough & Case Study</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="projects.links && projects.links.length > 3" class="mt-12 flex justify-center space-x-1">
                <Link 
                    v-for="(link, i) in projects.links" 
                    :key="i"
                    :href="link.url || '#'"
                    :class="[
                        'px-3.5 py-2 rounded-xl text-xs font-bold transition',
                        link.active ? 'bg-amber-500 text-slate-950' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200',
                        !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : ''
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>

        <CallToAction />
    </div>
</template>
