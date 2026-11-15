<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentUrl = computed(() => page.url);

const isCurrent = (pattern) => {
    return currentUrl.value.startsWith(pattern);
};
</script>

<template>
    <div class="bg-white border-b border-gray-200 -mx-4 -mt-6 sm:-mx-6 sm:-mt-6 mb-6 px-4 sm:px-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between overflow-x-auto whitespace-nowrap scrollbar-none py-2 gap-2">
            <!-- Navigation Sub-tabs -->
            <nav class="flex items-center space-x-1 sm:space-x-2 text-xs font-semibold">
                <!-- All Invoices -->
                <Link
                    :href="route('invoices.index')"
                    class="px-3 py-2 rounded-lg transition-colors flex items-center gap-1.5"
                    :class="[
                        currentUrl === '/invoices' || currentUrl.startsWith('/invoices?') || (currentUrl.startsWith('/invoices') && !currentUrl.startsWith('/invoices-') && !currentUrl.startsWith('/invoices/create') && !currentUrl.match(/^\/invoices\/\d+\/edit/))
                            ? 'bg-primary/10 text-primary font-bold'
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Invoices List</span>
                </Link>

                <!-- Customers -->
                <Link
                    :href="route('invoices.customers.index')"
                    class="px-3 py-2 rounded-lg transition-colors flex items-center gap-1.5"
                    :class="[
                        isCurrent('/invoices-customers')
                            ? 'bg-primary/10 text-primary font-bold'
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Customers Directory</span>
                </Link>

                <!-- Item Catalog -->
                <Link
                    :href="route('invoices.catalog.index')"
                    class="px-3 py-2 rounded-lg transition-colors flex items-center gap-1.5"
                    :class="[
                        isCurrent('/invoices-catalog')
                            ? 'bg-primary/10 text-primary font-bold'
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Item Catalog</span>
                </Link>

                <!-- Business Profile & Bank Accounts -->
                <Link
                    :href="route('invoices.settings.business')"
                    class="px-3 py-2 rounded-lg transition-colors flex items-center gap-1.5"
                    :class="[
                        isCurrent('/invoices-settings/business')
                            ? 'bg-primary/10 text-primary font-bold'
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Business Profile & Banks</span>
                </Link>

                <!-- Templates -->
                <Link
                    :href="route('invoices.settings.templates')"
                    class="px-3 py-2 rounded-lg transition-colors flex items-center gap-1.5"
                    :class="[
                        isCurrent('/invoices-settings/templates')
                            ? 'bg-primary/10 text-primary font-bold'
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                    ]"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    <span>A5 Templates</span>
                </Link>
            </nav>
        </div>
    </div>
</template>
