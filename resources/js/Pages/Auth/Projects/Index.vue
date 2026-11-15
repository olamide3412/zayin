<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    projects: Object,
    counts: Object,
    services: Array,
    states: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || '');
const filterService = ref(props.filters?.service_id || '');

const applyFilters = () => {
    router.get(route('admin.projects.index'), {
        search: search.value,
        status: filterStatus.value,
        service_id: filterService.value,
    }, { preserveState: true, replace: true });
};

const deleteProject = (project) => {
    if (confirm(`Are you sure you want to delete "${project.title}"?`)) {
        router.delete(route('admin.projects.destroy', project.id));
    }
};
</script>

<template>
    <Head title="Projects & Installations | Administration" />

    <div class="py-4 sm:py-6 max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6 font-sans">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-['Outfit',sans-serif]">
                    Completed Projects & Installations
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Manage client case studies, proof of work, solar specs, and video walkthroughs featured on the public website.
                </p>
            </div>
            <Link 
                :href="route('admin.projects.create')"
                class="inline-flex items-center justify-center bg-primary hover:bg-primary-dark text-white font-extrabold text-xs sm:text-sm px-4 sm:px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow shrink-0 w-full sm:w-auto"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                + Add Project
            </Link>
        </div>

        <!-- Filter Stats Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- Status Pills (Horizontally scrollable on mobile) -->
            <div class="flex items-center space-x-1.5 sm:space-x-2 text-xs font-semibold overflow-x-auto whitespace-nowrap pb-1 -mx-3 px-3 sm:mx-0 sm:px-0 scrollbar-none">
                <button 
                    @click="filterStatus = ''; applyFilters()"
                    :class="[
                        'px-3 sm:px-4 py-2 rounded-xl transition-colors shrink-0',
                        filterStatus === '' ? 'bg-primary text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-gray-200 sm:border-transparent'
                    ]"
                >
                    All ({{ counts?.all || 0 }})
                </button>
                <button 
                    @click="filterStatus = 'published'; applyFilters()"
                    :class="[
                        'px-3 sm:px-4 py-2 rounded-xl transition-colors shrink-0',
                        filterStatus === 'published' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-gray-200 sm:border-transparent'
                    ]"
                >
                    Published ({{ counts?.published || 0 }})
                </button>
                <button 
                    @click="filterStatus = 'featured'; applyFilters()"
                    :class="[
                        'px-3 sm:px-4 py-2 rounded-xl transition-colors shrink-0',
                        filterStatus === 'featured' ? 'bg-amber-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-gray-200 sm:border-transparent'
                    ]"
                >
                    Featured ({{ counts?.featured || 0 }})
                </button>
                <button 
                    @click="filterStatus = 'draft'; applyFilters()"
                    :class="[
                        'px-3 sm:px-4 py-2 rounded-xl transition-colors shrink-0',
                        filterStatus === 'draft' ? 'bg-slate-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-gray-200 sm:border-transparent'
                    ]"
                >
                    Drafts ({{ counts?.draft || 0 }})
                </button>
            </div>

            <!-- Service & Search Inputs (Stacked on mobile) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 w-full sm:w-auto">
                <select
                    v-model="filterService"
                    @change="applyFilters"
                    class="rounded-xl border border-gray-200 px-3 py-2 text-xs sm:text-sm bg-white text-slate-900 w-full"
                >
                    <option value="">All Services</option>
                    <option v-for="srv in services" :key="srv.id" :value="srv.id">{{ srv.name }}</option>
                </select>

                <div class="w-full">
                    <input 
                        v-model="search"
                        @input="applyFilters"
                        type="text"
                        placeholder="Search project, city, client..."
                        class="w-full rounded-xl border border-gray-200 px-3.5 py-2 text-xs sm:text-sm bg-white text-slate-900"
                    />
                </div>
            </div>
        </div>

        <!-- Projects Container: Mobile Cards & Desktop Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <!-- Mobile Card View (< md) -->
            <div class="block md:hidden divide-y divide-gray-100">
                <div v-if="!projects.data || projects.data.length === 0" class="p-8 text-center text-xs text-slate-400">
                    No projects found matching your criteria.
                </div>

                <div 
                    v-for="project in projects.data" 
                    :key="`m-proj-${project.id}`"
                    class="p-4 space-y-3 hover:bg-slate-50/60 transition"
                >
                    <div class="flex items-start gap-3">
                        <img 
                            :src="project.featured_image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=300&auto=format&fit=crop'" 
                            :alt="project.title"
                            class="w-16 h-16 object-cover rounded-xl border border-gray-100 shrink-0 shadow-2xs"
                        />
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                <span 
                                    :class="[
                                        'px-2 py-0.5 text-[10px] font-bold rounded-full',
                                        project.is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'
                                    ]"
                                >
                                    {{ project.is_published ? 'Published' : 'Draft' }}
                                </span>
                                <span v-if="project.is_featured" class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800">
                                    Featured
                                </span>
                            </div>
                            <div class="font-bold text-slate-900 text-sm leading-snug">
                                {{ project.title }}
                            </div>
                            <div class="text-[11px] text-gray-500 mt-1">
                                Client: <span class="font-medium text-slate-700">{{ project.client_name || 'Confidential' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Specs & Location tags -->
                    <div class="flex flex-wrap items-center gap-2 text-xs pt-1">
                        <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold rounded bg-slate-100 text-slate-800">
                            {{ project.service }}
                        </span>
                        <span class="text-gray-500 text-[11px]">
                            📍 {{ [project.city, project.state].filter(Boolean).join(', ') || 'Nigeria' }}
                        </span>
                        <span v-if="project.system_size" class="text-[10px] font-mono text-slate-800 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded ml-auto">
                            ⚡ {{ project.system_size }}
                        </span>
                    </div>

                    <!-- Actions Bar on Mobile -->
                    <div class="flex items-center justify-between pt-2 border-t border-gray-50 text-xs">
                        <span class="text-[10px] text-gray-400">
                            {{ project.completed_on ? `Completed: ${project.completed_on}` : '' }}
                        </span>
                        <div class="flex items-center gap-3">
                            <a 
                                :href="`/projects/${project.slug}`" 
                                target="_blank"
                                class="font-semibold text-slate-600 hover:text-slate-900"
                            >
                                View ↗
                            </a>
                            <Link 
                                :href="route('admin.projects.edit', project.id)" 
                                class="font-bold text-blue-600 hover:text-blue-800"
                            >
                                Edit
                            </Link>
                            <button 
                                type="button"
                                @click="deleteProject(project)" 
                                class="font-bold text-rose-600 hover:text-rose-800"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Table (hidden md:block) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 border-b border-gray-100">
                        <tr>
                            <th class="py-4 px-6">Project</th>
                            <th class="py-4 px-6">Service / Location</th>
                            <th class="py-4 px-6">System Spec</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="project in projects.data" :key="project.id" class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <img 
                                        :src="project.featured_image || 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=300&auto=format&fit=crop'" 
                                        :alt="project.title"
                                        class="w-14 h-14 object-cover rounded-xl border border-gray-100 shrink-0 shadow-2xs"
                                    />
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate max-w-xs sm:max-w-md">
                                            {{ project.title }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            Client: <span class="font-medium text-slate-700">{{ project.client_name || 'Confidential' }}</span>
                                            <span v-if="project.completed_on" class="ml-2">• {{ project.completed_on }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-800">
                                    {{ project.service }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ [project.city, project.state].filter(Boolean).join(', ') || 'Nigeria' }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-mono text-slate-800 bg-amber-50 border border-amber-100 px-2 py-1 rounded">
                                    {{ project.system_size || 'N/A' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1 items-start">
                                    <span 
                                        :class="[
                                            'px-2.5 py-0.5 text-xs font-bold rounded-full',
                                            project.is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600'
                                        ]"
                                    >
                                        {{ project.is_published ? 'Published' : 'Draft' }}
                                    </span>
                                    <span v-if="project.is_featured" class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800">
                                        Featured
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a 
                                    :href="`/projects/${project.slug}`" 
                                    target="_blank"
                                    class="text-xs font-semibold text-slate-500 hover:text-slate-800 hover:underline"
                                >
                                    View
                                </a>
                                <Link 
                                    :href="route('admin.projects.edit', project.id)" 
                                    class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline"
                                >
                                    Edit
                                </Link>
                                <button 
                                    type="button"
                                    @click="deleteProject(project)" 
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!projects.data || projects.data.length === 0">
                            <td colspan="5" class="py-12 text-center text-gray-400 text-sm">
                                No projects found matching your criteria.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 sm:p-4 border-t border-gray-100 flex justify-between items-center" v-if="projects.links">
                <Pagination :links="projects.links" />
            </div>
        </div>

    </div>
</template>
