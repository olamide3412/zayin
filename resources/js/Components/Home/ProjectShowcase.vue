<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    projects: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <section class="py-16 sm:py-24 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12" data-aos="fade-up">
                <div>
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest bg-amber-500/10 border border-amber-500/20 px-3.5 py-1.5 rounded-full inline-block mb-3">
                        Social Proof & Field Work
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-white">
                        Completed Installations Across Nigeria
                    </h2>
                    <p class="mt-2 text-slate-400 text-sm sm:text-base">
                        Watch video walkthroughs, load tests, and see real workmanship from Nigerian homes and businesses.
                    </p>
                </div>

                <div class="mt-4 md:mt-0">
                    <Link 
                        href="/projects"
                        class="inline-flex items-center space-x-1.5 text-sm font-bold text-amber-400 hover:text-amber-300 transition"
                    >
                        <span>See All Project Videos</span>
                        <font-awesome-icon :icon="['fas', 'arrow-right']" />
                    </Link>
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div 
                    v-for="project in projects" 
                    :key="project.id"
                    class="rounded-3xl bg-slate-800/80 border border-slate-700/60 overflow-hidden hover:border-amber-500/60 transition-all duration-300 transform hover:-translate-y-1 shadow-xl flex flex-col justify-between group"
                    data-aos="fade-up"
                >
                    <div>
                        <!-- Thumbnail with Video badge -->
                        <div class="aspect-[16/11] relative bg-slate-950 overflow-hidden">
                            <img 
                                :src="project.featured_image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop'" 
                                :alt="project.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>

                            <!-- Play Icon Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-amber-500/90 text-slate-950 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <font-awesome-icon :icon="['fas', 'play']" class="ml-1 text-sm" />
                                </div>
                            </div>

                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-[10px] font-bold text-amber-400">
                                    {{ project.service?.name || 'Installation' }}
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-3 text-[11px] font-semibold text-slate-300 flex items-center gap-1">
                                <font-awesome-icon :icon="['fas', 'location-dot']" class="text-amber-400" />
                                <span>{{ project.city }}, {{ project.state?.name }}</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-2">
                            <div v-if="project.system_size" class="text-xs font-bold text-amber-400">
                                {{ project.system_size }}
                            </div>

                            <Link 
                                :href="`/projects/${project.slug}`"
                                class="block font-bold text-base text-white group-hover:text-amber-400 transition leading-snug line-clamp-2"
                            >
                                {{ project.title }}
                            </Link>

                            <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                                {{ project.summary }}
                            </p>
                        </div>
                    </div>

                    <div class="p-5 pt-0 border-t border-slate-700/60 mt-3">
                        <Link 
                            :href="`/projects/${project.slug}`"
                            class="text-xs font-bold text-amber-400 hover:text-amber-300 flex items-center justify-between pt-3"
                        >
                            <span>Watch Walkthrough Video</span>
                            <font-awesome-icon :icon="['fas', 'arrow-right']" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
