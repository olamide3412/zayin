<template>
    <Head title="Invoices & Quotations - Zayin Ltd" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">Invoices & Quotations</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage Nigerian A5 tax invoices, drafts, revisions, and payment records.</p>
                </div>
                <div class="flex items-center flex-wrap gap-2.5">
                    <Link
                        :href="route('invoices.create')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-black uppercase tracking-wider text-slate-950 bg-amber-500 hover:bg-amber-400 rounded-xl shadow-md transition active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ New Invoice</span>
                    </Link>
                </div>
            </div>
        </template>

        <InvoiceNavSubBar />

        <div class="py-6 space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Invoices</div>
                        <div class="text-2xl font-black text-gray-900 mt-1">{{ stats.total_invoices }}</div>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Collected Revenue</div>
                        <div class="text-2xl font-black text-emerald-600 mt-1">₦{{ formatKobo(stats.total_revenue_kobo, false) }}</div>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Outstanding Balance</div>
                        <div class="text-2xl font-black text-amber-600 mt-1">₦{{ formatKobo(stats.total_outstanding_kobo, false) }}</div>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Drafts</div>
                        <div class="text-2xl font-black text-slate-700 mt-1">{{ stats.drafts_count }}</div>
                    </div>
                    <div class="w-12 h-12 bg-slate-50 text-slate-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
                <div class="flex-1 w-full md:w-auto relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        v-model="search"
                        @keyup.enter="applyFilters"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                        placeholder="Search invoice number, customer name, email..."
                    />
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <select
                        v-model="status"
                        @change="applyFilters"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                    >
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="issued">Issued</option>
                        <option value="partially_paid">Partially Paid</option>
                        <option value="paid">Paid</option>
                        <option value="void">Void</option>
                    </select>

                    <button
                        type="button"
                        @click="applyFilters"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors"
                    >
                        Filter
                    </button>

                    <button
                        v-if="hasActiveFilters"
                        type="button"
                        @click="clearFilters"
                        class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <!-- Invoices Container: Custom Mobile Card List (sm:hidden) + Desktop Table (hidden sm:block) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Mobile Card List View -->
                <div class="block sm:hidden divide-y divide-gray-100">
                    <div v-if="invoices.data.length === 0" class="px-5 py-12 text-center text-gray-500 space-y-3">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="font-bold text-gray-900 text-sm">No Invoices Found</div>
                        <p class="text-xs text-gray-500">You haven't generated any invoices yet, or none match your filter.</p>
                        <Link
                            :href="route('invoices.create')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-black uppercase tracking-wider text-slate-950 bg-amber-500 hover:bg-amber-400 rounded-xl shadow-md transition active:scale-95"
                        >
                            + New Invoice
                        </Link>
                    </div>

                    <div
                        v-for="inv in invoices.data"
                        :key="`mobile-${inv.id}`"
                        class="p-4 space-y-3 hover:bg-gray-50/75 transition-colors"
                    >
                        <!-- Top Row: Invoice #, Rev Badge, Status -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center space-x-1.5 min-w-0">
                                <Link
                                    :href="route('invoices.show', inv.id)"
                                    class="font-black text-sm text-gray-900 hover:text-primary truncate"
                                >
                                    {{ inv.invoice_number || 'DRAFT' }}
                                </Link>
                                <span
                                    v-if="inv.revision_number > 1"
                                    class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded shrink-0"
                                >
                                    Rev {{ inv.revision_number }}
                                </span>
                            </div>
                            <StatusBadge :status="inv.status" class="shrink-0" />
                        </div>

                        <!-- Customer & Dates Info -->
                        <div class="space-y-1">
                            <div class="font-bold text-xs text-gray-900 truncate">
                                {{ inv.customer?.name || 'Walk-in Client' }}
                            </div>
                            <div v-if="inv.customer?.phone || inv.customer?.email" class="text-[11px] text-gray-500 truncate">
                                {{ inv.customer?.phone || inv.customer?.email }}
                            </div>
                            <div class="flex items-center gap-3 text-[11px] text-gray-500 pt-0.5">
                                <span><strong class="text-gray-600">Issued:</strong> {{ formatDate(inv.issue_date) }}</span>
                                <span v-if="inv.due_date" class="text-gray-400">•</span>
                                <span v-if="inv.due_date"><strong class="text-gray-600">Due:</strong> {{ formatDate(inv.due_date) }}</span>
                            </div>
                        </div>

                        <!-- Financial Summary Box -->
                        <div class="bg-gray-50/80 rounded-lg p-2.5 flex items-center justify-between border border-gray-100 text-xs">
                            <div>
                                <span class="text-[10px] uppercase font-semibold text-gray-500 block">Total</span>
                                <span class="font-extrabold text-gray-900 text-sm">₦{{ formatKobo(inv.total_kobo, false) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-semibold text-gray-500 block">Balance Due</span>
                                <span
                                    class="font-bold text-sm"
                                    :class="inv.total_kobo - inv.amount_paid_kobo > 0 ? 'text-amber-600' : 'text-emerald-600'"
                                >
                                    ₦{{ formatKobo(Math.max(0, inv.total_kobo - inv.amount_paid_kobo), false) }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons Row (Touch Friendly) -->
                        <div class="grid grid-cols-3 gap-2 pt-1">
                            <Link
                                :href="route('invoices.show', inv.id)"
                                class="inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>View</span>
                            </Link>

                            <a
                                :href="route('invoices.pdf.download', inv.id)"
                                class="inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-emerald-700 bg-emerald-50/80 border border-emerald-200 rounded-lg hover:bg-emerald-100 active:bg-emerald-200 transition shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>PDF</span>
                            </a>

                            <Link
                                v-if="inv.status !== 'void'"
                                :href="route('invoices.edit', inv.id)"
                                class="inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-blue-700 bg-blue-50/80 border border-blue-200 rounded-lg hover:bg-blue-100 active:bg-blue-200 transition shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </Link>
                            <div v-else class="text-center text-xs text-gray-400 py-2">Voided</div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5 whitespace-nowrap">Invoice #</th>
                                <th class="px-5 py-3.5 min-w-[180px]">Customer</th>
                                <th class="px-5 py-3.5 whitespace-nowrap">Date</th>
                                <th class="px-5 py-3.5 whitespace-nowrap">Status</th>
                                <th class="px-5 py-3.5 text-right whitespace-nowrap">Total (₦)</th>
                                <th class="px-5 py-3.5 text-right whitespace-nowrap">Balance (₦)</th>
                                <th class="px-5 py-3.5 text-right whitespace-nowrap w-36 min-w-[140px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="invoices.data.length === 0">
                                <td colspan="7" class="px-5 py-14 text-center text-gray-500">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="font-bold text-gray-900 text-sm">No Invoices Found</div>
                                        <p class="text-xs text-gray-500">You haven't generated any invoices yet, or none match your search filter.</p>
                                        <Link
                                            :href="route('invoices.create')"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-slate-950 bg-amber-500 hover:bg-amber-400 rounded-xl shadow-md transition"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Create New Invoice</span>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr
                                v-for="inv in invoices.data"
                                :key="inv.id"
                                class="hover:bg-gray-50/75 transition-colors"
                            >
                                <td class="px-5 py-4 font-bold text-gray-900 whitespace-nowrap">
                                    <Link :href="route('invoices.show', inv.id)" class="hover:text-primary">
                                        {{ inv.invoice_number || 'DRAFT' }}
                                    </Link>
                                    <span v-if="inv.revision_number > 1" class="ml-1 text-[10px] font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">
                                        Rev {{ inv.revision_number }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 min-w-[180px]">
                                    <div class="font-medium text-gray-900">{{ inv.customer?.name || 'Walk-in Client' }}</div>
                                    <div class="text-xs text-gray-500">{{ inv.customer?.phone || inv.customer?.email }}</div>
                                </td>
                                <td class="px-5 py-4 text-gray-600 text-xs whitespace-nowrap">
                                    <div class="font-medium text-gray-800">{{ formatDate(inv.issue_date) }}</div>
                                    <div v-if="inv.due_date" class="text-gray-400 text-[11px]">Due: {{ formatDate(inv.due_date) }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <StatusBadge :status="inv.status" />
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-gray-900 whitespace-nowrap">
                                    ₦{{ formatKobo(inv.total_kobo, false) }}
                                </td>
                                <td class="px-5 py-4 text-right font-medium whitespace-nowrap">
                                    <span :class="inv.total_kobo - inv.amount_paid_kobo > 0 ? 'text-amber-600' : 'text-emerald-600'">
                                        ₦{{ formatKobo(Math.max(0, inv.total_kobo - inv.amount_paid_kobo), false) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap w-36 min-w-[140px]">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('invoices.show', inv.id)"
                                            class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition"
                                            title="View Details"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </Link>
                                        <a
                                            :href="route('invoices.pdf.download', inv.id)"
                                            class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                                            title="Download PDF"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                        <Link
                                            v-if="inv.status !== 'void'"
                                            :href="route('invoices.edit', inv.id)"
                                            class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Edit Invoice"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="invoices.links && invoices.links.length > 3" class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-gray-500">
                        Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
                    </div>
                    <div class="flex space-x-1 flex-wrap justify-center">
                        <Link
                            v-for="(link, i) in invoices.links"
                            :key="i"
                            :href="link.url || '#'"
                            v-html="link.label"
                            :class="[
                                'px-3 py-1 rounded text-xs',
                                link.active ? 'bg-primary text-white font-bold' : 'text-gray-700 hover:bg-gray-100',
                                !link.url ? 'opacity-40 cursor-not-allowed' : ''
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/Invoice/StatusBadge.vue';
import InvoiceNavSubBar from '@/Components/Invoice/InvoiceNavSubBar.vue';
import { useNaira } from '@/Composables/useNaira';

const props = defineProps<{
    invoices: any;
    filters: any;
    stats: any;
    customers: any[];
}>();

const { formatKobo } = useNaira();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const hasActiveFilters = computed(() => !!(search.value || status.value));

function applyFilters() {
    router.get(
        route('invoices.index'),
        {
            search: search.value || undefined,
            status: status.value || undefined,
        },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    status.value = '';
    applyFilters();
}

function formatDate(dateStr: string | null | undefined): string {
    if (!dateStr) return '';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch {
        return dateStr;
    }
}
</script>
