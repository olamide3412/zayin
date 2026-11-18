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
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-['Outfit',sans-serif]">
                    Technology & OEM Brand Partners
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Manage certified hardware manufacturers, satellite operators, and equipment OEM partners featured on the public website.
                </p>
            </div>
            <Link 
                :href="route('admin.partners.create')"
                class="inline-flex items-center justify-center bg-primary hover:bg-primary-dark text-white font-extrabold text-sm px-5 py-3 rounded-xl transition-all shadow-sm hover:shadow active:scale-98 w-full md:w-auto"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Add Brand Partner</span>
            </Link>
        </div>

        <!-- Filter Stats Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-1.5 text-xs sm:text-sm font-semibold overflow-x-auto whitespace-nowrap pb-1 scrollbar-none">
                <button 
                    @click="filterStatus = ''; applyFilters()"
                    :class="[
                        'px-3.5 py-2 rounded-xl transition-colors',
                        filterStatus === '' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-gray-100'
                    ]"
                >
                    All Partners ({{ counts?.all || 0 }})
                </button>
                <button 
                    @click="filterStatus = 'active'; applyFilters()"
                    :class="[
                        'px-3.5 py-2 rounded-xl transition-colors',
                        filterStatus === 'active' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-gray-100'
                    ]"
                >
                    Active ({{ counts?.active || 0 }})
                </button>
                <button 
                    @click="filterStatus = 'inactive'; applyFilters()"
                    :class="[
                        'px-3.5 py-2 rounded-xl transition-colors',
                        filterStatus === 'inactive' ? 'bg-slate-700 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-gray-100'
                    ]"
                >
                    Inactive ({{ counts?.inactive || 0 }})
                </button>
            </div>

            <!-- Search input -->
            <div class="w-full sm:w-64">
                <div class="relative">
                    <input 
                        v-model="search"
                        @input="applyFilters"
                        type="text"
                        placeholder="Search partners..."
                        class="w-full rounded-xl border border-gray-200 pl-9 pr-4 py-2 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none"
                    />
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- 1. FULL CUSTOM MOBILE & TABLET VIEW (block lg:hidden)           -->
        <!-- Clean responsive cards with zero horizontal scrolling required.  -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div class="block lg:hidden space-y-3.5">
            <!-- Empty state -->
            <div v-if="!partners.data || partners.data.length === 0" class="bg-white rounded-2xl p-8 text-center border border-gray-100 text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-sm font-semibold text-slate-700">No technology brand partners found</p>
                <p class="text-xs text-gray-400 mt-1">Tap "Add Brand Partner" above to register a new partner.</p>
            </div>

            <!-- Partner Mobile Card -->
            <div 
                v-for="partner in partners.data" 
                :key="`m-partner-${partner.id}`"
                class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm space-y-3"
            >
                <!-- Top Row: Logo, Name, Status & Order -->
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 overflow-hidden border border-gray-200 p-1">
                        <img 
                            v-if="partner.logo_url" 
                            :src="partner.logo_url" 
                            :alt="partner.name" 
                            class="w-full h-full object-contain" 
                        />
                        <span v-else class="text-base font-extrabold text-slate-400">
                            {{ partner.name.charAt(0) }}
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-bold text-slate-900 text-base leading-snug truncate">
                                {{ partner.name }}
                            </h3>
                            <span 
                                :class="[
                                    'px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider shrink-0',
                                    partner.status === 'active' 
                                        ? 'bg-emerald-100 text-emerald-800' 
                                        : 'bg-slate-100 text-slate-600'
                                ]"
                            >
                                {{ partner.status }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 mt-1 text-xs text-slate-400">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[10px]">
                                Order #{{ partner.sort_order }}
                            </span>
                            <a 
                                v-if="partner.website_url" 
                                :href="partner.website_url" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="text-primary hover:underline font-semibold inline-flex items-center gap-1 text-[11px]"
                            >
                                <span>Visit Website</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                            <span v-else class="text-[11px] text-gray-400">No URL</span>
                        </div>
                    </div>
                </div>

                <!-- Description (if any) -->
                <p v-if="partner.description" class="text-xs text-gray-600 leading-relaxed bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 line-clamp-2">
                    {{ partner.description }}
                </p>

                <!-- Actions Bar: 100% visible, no scrolling needed! -->
                <div class="pt-2 border-t border-gray-100 flex items-center gap-2">
                    <Link 
                        :href="route('admin.partners.edit', partner.id)" 
                        class="flex-1 py-2 px-3 bg-slate-100 hover:bg-primary hover:text-white text-slate-800 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-98"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit</span>
                    </Link>
                    <button 
                        @click="deletePartner(partner)" 
                        class="py-2 px-3.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-98"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Delete</span>
                    </button>
                </div>
            </div>

            <!-- Mobile Pagination -->
            <div v-if="partners && partners.last_page > 1" class="pt-2">
                <Pagination :paginator="partners" />
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- 2. DESKTOP TABLE VIEW (hidden lg:block)                         -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-extrabold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Logo &amp; Partner Name</th>
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

            <!-- Desktop Pagination -->
            <div v-if="partners && partners.last_page > 1" class="p-4 border-t border-gray-100">
                <Pagination :paginator="partners" />
            </div>
        </div>

    </div>
</template>
