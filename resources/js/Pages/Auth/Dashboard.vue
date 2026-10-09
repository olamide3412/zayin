<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    stats: Object
})
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Admin Dashboard | Zayin Technologies" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Welcome to Zayin Technologies Portal</h1>
            <p class="text-sm text-gray-500 mt-1">Real-time overview of orders, engineering quotes, technician dispatches, and reviews.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Total Orders -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-navy-900">
                <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Equipment Orders</h3>
                <p class="text-3xl font-extrabold text-navy-950 mt-2">{{ stats?.total_orders || 0 }}</p>
                <p class="text-xs text-slate-400 mt-1">E-commerce & direct packages</p>
            </div>
            
            <!-- Pending Orders -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500">
                <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Awaiting Payment</h3>
                <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ stats?.pending_orders || 0 }}</p>
                <p class="text-xs text-slate-400 mt-1">Transfers requiring verification</p>
            </div>

            <!-- New Quotes -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">New Solar/Site Quotes</h3>
                <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ stats?.new_quotes || 0 }}</p>
                <p class="text-xs text-slate-400 mt-1">Awaiting load audit response</p>
            </div>

            <!-- Technician Bookings -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Technician Bookings</h3>
                <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ stats?.pending_bookings || 0 }}</p>
                <p class="text-xs text-slate-400 mt-1">Pending dispatch confirmation</p>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-8">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-3">Live Pricing & Catalog Management</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link :href="route('admin.products.index')" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-amber-400 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold mb-3 group-hover:bg-amber-500 group-hover:text-slate-950 transition">
                        ₦
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Products & Live Pricing</h4>
                    <p class="text-xs text-slate-500 mt-1">Configure solar packages, inverters, lithium batteries, and prices in Naira.</p>
                </Link>

                <Link :href="route('admin.plans.index')" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-amber-400 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-3 group-hover:bg-emerald-500 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Maintenance Care Plans</h4>
                    <p class="text-xs text-slate-500 mt-1">Set annual/quarterly solar maintenance subscription fees and visit quotas.</p>
                </Link>

                <Link :href="route('admin.state-fees.index')" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-amber-400 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold mb-3 group-hover:bg-blue-500 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Delivery & Installation Fees</h4>
                    <p class="text-xs text-slate-500 mt-1">Configure state-by-state checkout delivery and installation charges across Nigeria.</p>
                </Link>

                <Link :href="route('admin.solar-settings.index')" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:border-amber-400 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold mb-3 group-hover:bg-purple-500 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Solar Sizing Engine</h4>
                    <p class="text-xs text-slate-500 mt-1">Tune inverter catalogs, DoD limits, peak sun hours, and appliance presets.</p>
                </Link>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-3">Content & Operations</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <Link :href="route('admin.blog.index')" class="p-5 rounded-xl bg-white border border-slate-200 shadow-2xs hover:border-amber-400 transition">
                    <h4 class="font-bold text-slate-900 text-sm">Manage Blog Posts</h4>
                    <p class="text-xs text-slate-500 mt-1">Publish engineering tips, solar advice, and satellite guides.</p>
                </Link>

                <Link :href="route('admin.testimonials.index')" class="p-5 rounded-xl bg-white border border-slate-200 shadow-2xs hover:border-amber-400 transition">
                    <h4 class="font-bold text-slate-900 text-sm">Review Client Testimonials</h4>
                    <p class="text-xs text-slate-500 mt-1">Approve or moderate reviews submitted by Nigerian clients.</p>
                </Link>

                <Link :href="route('admin.settings.index')" class="p-5 rounded-xl bg-white border border-slate-200 shadow-2xs hover:border-amber-400 transition">
                    <h4 class="font-bold text-slate-900 text-sm">Company Settings</h4>
                    <p class="text-xs text-slate-500 mt-1">Update bank accounts, WhatsApp hotlines, and office addresses.</p>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
