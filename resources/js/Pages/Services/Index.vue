<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import CallToAction from '@/Components/Home/CallToAction.vue';

defineOptions({ layout: Layout });

defineProps({
    categories: Array,
    seo: Object,
});
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen">
        <!-- Page Hero Header -->
        <div class="bg-slate-950 text-white py-16 sm:py-20 border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left">
                <nav class="flex items-center space-x-2 text-xs text-slate-400 mb-4 justify-center sm:justify-start">
                    <Link href="/" class="hover:text-amber-400 transition">Home</Link>
                    <span>/</span>
                    <span class="text-amber-400 font-bold">Services</span>
                </nav>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white">
                    Our Engineering & Technical Services
                </h1>
                <p class="mt-4 text-slate-300 text-base max-w-3xl leading-relaxed">
                    Turnkey energy, high-speed connectivity, and satellite solutions deployed to rigorous standards across homes, residential estates, and commercial facilities in Nigeria.
                </p>
            </div>
        </div>

        <!-- Categories & Services List -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
            <div 
                v-for="cat in categories" 
                :key="cat.id"
                class="space-y-8"
                data-aos="fade-up"
            >
                <div class="border-b border-slate-200 pb-4">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span>{{ cat.name }}</span>
                    </h2>
                    <p class="text-slate-600 text-sm mt-1">{{ cat.description }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div 
                        v-for="service in cat.services" 
                        :key="service.id"
                        class="bg-white rounded-3xl p-8 border border-slate-200 hover:border-amber-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors">
                                    <font-awesome-icon :icon="['fas', service.icon || 'bolt']" />
                                </div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    Certified Service
                                </span>
                            </div>

                            <Link 
                                :href="`/services/${service.slug}`"
                                class="block text-xl font-bold text-slate-900 group-hover:text-amber-600 transition mb-3"
                            >
                                {{ service.name }}
                            </Link>

                            <p class="text-slate-600 text-sm leading-relaxed mb-6">
                                {{ service.summary }}
                            </p>

                            <div v-if="service.benefits" class="space-y-2 mb-6">
                                <div 
                                    v-for="(b, i) in (typeof service.benefits === 'string' ? JSON.parse(service.benefits) : service.benefits).slice(0, 3)" 
                                    :key="i"
                                    class="flex items-center space-x-2 text-xs text-slate-700"
                                >
                                    <font-awesome-icon :icon="['fas', 'check']" class="text-emerald-500 text-[10px]" />
                                    <span>{{ b }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                            <Link 
                                :href="`/services/${service.slug}`" 
                                class="text-sm font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1.5"
                            >
                                <span>Service Details & Process</span>
                                <font-awesome-icon :icon="['fas', 'arrow-right']" />
                            </Link>
                            <Link 
                                href="/request-quote" 
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold"
                            >
                                Get Quote
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CallToAction />
    </div>
</template>
