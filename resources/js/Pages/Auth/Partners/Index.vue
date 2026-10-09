<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    partners: Object,
    filters: Object,
    counts: Object,
});

const deletePartner = (partner) => {
    if (confirm(`Are you sure you want to delete "${partner.name}"?`)) {
        router.delete(route('admin.partners.destroy', partner.id));
    }
};

const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || '');

const applyFilters = () => {
    router.get(route('admin.partners.index'), {
        search: search.value,
        status: filterStatus.value,
    }, { preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Technology & Brand Partners | Administration" />

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 font-sans">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 font-['Outfit',sans-serif]">
                    Technology & OEM Brand Partners
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Manage certified hardware manufacturers, satellite operators, and equipment OEM partners featured on the public website.
                </p>
            </div>
            <Link 
                :href="route('admin.partners.create')"
                class="inline-flex items-center justify-center bg-primary hover:bg-primary-dark text-white font-extrabold text-sm px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Brand Partner
            </Link>
        </div>

        <!-- Filter Stats Bar -->
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center space-x-2 text-sm font-semibold">
                <button 
                    @click="filterStatus = ''; applyFilters()"
                    :class="[
                        'px-4 py-2 rounded-xl transition-colors',
                        filterStatus === '' ? 'bg-primary text-white' : 'bg-white text-slate-700 hover:bg-slate-100'
                    ]"
                >
                    All Partners ({{ counts?.all || 0 }})
                </button>
                <button 
                    @click="filterStatus = 'active'; applyFilters()"
                    :class="[
                        'px-4 py-2 rounded-xl transition-colors',
                        filterStatus === 'active' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100'
                    ]"
                >
                    Active ({{ counts?.active || 0 }})
                </button>
                <button 
                    @click="filterStatus = 'inactive'; applyFilters()"
                    :class="[
                        'px-4 py-2 rounded-xl transition-colors',
                        filterStatus === 'inactive' ? 'bg-slate-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100'
                    ]"
                >
                    Inactive ({{ counts?.inactive || 0 }})
                </button>
            </div>

            <!-- Search input -->
            <div class="w-full sm:w-64">
                <input 
                    v-model="search"
                    @input="applyFilters"
                    type="text"
                    placeholder="Search partners..."
                    class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm bg-white text-slate-900"
                />
            </div>
        </div>

        <!-- Partners Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-extrabold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Logo & Partner Name</th>
                            <th class="px-6 py-4">Website</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Order</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        <tr v-for="partner in partners.data" :key="partner.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden border border-gray-200 p-1">
                                        <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.name" class="w-full h-full object-contain" />
                                        <span v-else class="text-base font-extrabold text-slate-400">
                                            {{ partner.name.charAt(0) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-base">
                                            {{ partner.name }}
                                        </h3>
                                        <p v-if="partner.description" class="text-xs text-gray-500 line-clamp-1 max-w-sm mt-0.5">
                                            {{ partner.description }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a 
                                    v-if="partner.website_url" 
                                    :href="partner.website_url" 
                                    target="_blank" 
                                    class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1"
                                >
                                    <span>Visit Site</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <span v-else class="text-xs text-gray-400">N/A</span>
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    :class="[
                                        'px-2.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider',
                                        partner.status === 'active' 
                                            ? 'bg-emerald-100 text-emerald-800' 
                                            : 'bg-slate-100 text-slate-600'
                                    ]"
                                >
                                    {{ partner.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                {{ partner.sort_order }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <Link 
                                    :href="route('admin.partners.edit', partner.id)" 
                                    class="px-3 py-1.5 bg-slate-100 hover:bg-primary hover:text-white rounded-lg text-xs font-bold transition-colors inline-block"
                                >
                                    Edit
                                </Link>
                                <button 
                                    @click="deletePartner(partner)" 
                                    class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg text-xs font-bold transition-colors"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!partners.data || partners.data.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                No technology brand partners found. Click "Add Brand Partner" to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="partners && partners.last_page > 1" class="p-4 border-t border-gray-100">
                <Pagination :paginator="partners" />
            </div>
        </div>

    </div>
</template>
