<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    plans: Array,
    services: Array,
});

const deleteForm = useForm({});
const deletePlan = (id, name) => {
    if (!confirm(`Are you sure you want to delete the plan "${name}"?`)) return;
    deleteForm.delete(route('admin.plans.destroy', id), { preserveScroll: true });
};

const formatNaira = (val) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Maintenance Plans Management" />

        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Solar Maintenance Plans & Pricing</h1>
                <p class="text-sm text-slate-500 mt-0.5">Configure scheduled maintenance packages, subscription fees, and inspection visit limits.</p>
            </div>
            <Link 
                :href="route('admin.plans.create')" 
                class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Add Maintenance Plan</span>
            </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="plan in plans" 
                :key="plan.id" 
                class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:border-amber-400 transition"
            >
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                            {{ plan.billing_period }}
                        </span>
                        <span 
                            class="text-xs font-bold px-2 py-0.5 rounded-md"
                            :class="plan.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500'"
                        >
                            {{ plan.is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </div>

                    <h2 class="text-xl font-black text-slate-900 mb-1">{{ plan.name }}</h2>
                    <p class="text-xs text-slate-500 mb-4 line-clamp-2">{{ plan.description || 'Comprehensive solar diagnostic and panel care.' }}</p>

                    <!-- Price Display -->
                    <div class="bg-amber-50/60 rounded-xl p-4 border border-amber-200/60 mb-4">
                        <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Plan Subscription Fee</div>
                        <div class="text-2xl font-black text-slate-900 mt-0.5">
                            {{ formatNaira(plan.price_naira) }}
                            <span class="text-xs font-semibold text-slate-500">/ {{ plan.billing_period }}</span>
                        </div>
                        <div class="text-xs text-amber-800 font-bold mt-1">
                            ✓ {{ plan.visits_per_year }} Certified Field Visits / Year
                        </div>
                    </div>

                    <!-- Inclusions -->
                    <div v-if="plan.inclusions && plan.inclusions.length" class="space-y-1.5 text-xs text-slate-600 mb-6">
                        <div v-for="(inc, idx) in plan.inclusions.slice(0, 4)" :key="idx" class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span>{{ inc }}</span>
                        </div>
                        <div v-if="plan.inclusions.length > 4" class="text-slate-400 text-[11px] pt-1">
                            + {{ plan.inclusions.length - 4 }} more included services
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                    <Link 
                        :href="route('admin.plans.edit', plan.id)" 
                        class="px-4 py-2 bg-slate-100 hover:bg-amber-500 hover:text-slate-950 text-slate-700 font-bold text-xs rounded-xl transition"
                    >
                        Edit Plan & Price
                    </Link>
                    <button 
                        @click="deletePlan(plan.id, plan.name)" 
                        class="p-2 text-slate-400 hover:text-red-600 rounded-lg transition"
                        title="Delete Plan"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
