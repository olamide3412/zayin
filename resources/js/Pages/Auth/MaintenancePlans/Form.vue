<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    plan: Object,
    services: Array,
});

const isEdit = !!props.plan;

const inclusionText = ref(props.plan?.inclusions ? props.plan.inclusions.join('\n') : '');

const form = useForm({
    name: props.plan?.name || '',
    service_id: props.plan?.service_id || null,
    description: props.plan?.description || '',
    price_naira: props.plan?.price_naira || '',
    billing_period: props.plan?.billing_period || 'quarterly',
    visits_per_year: props.plan?.visits_per_year || 4,
    inclusions: props.plan?.inclusions || [],
    is_featured: props.plan?.is_featured || false,
    is_active: props.plan?.is_active !== undefined ? props.plan.is_active : true,
    sort_order: props.plan?.sort_order || 1,
});

const submit = () => {
    // Parse inclusions line by line
    form.inclusions = inclusionText.value
        .split('\n')
        .map(s => s.trim())
        .filter(s => s.length > 0);

    if (isEdit) {
        form.put(route('admin.plans.update', props.plan.id));
    } else {
        form.post(route('admin.plans.store'));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="isEdit ? `Edit ${plan.name}` : 'New Maintenance Plan'" />

        <div class="mb-6">
            <Link :href="route('admin.plans.index')" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 mb-1">
                &larr; Back to Maintenance Plans
            </Link>
            <h1 class="text-2xl font-black text-slate-900">
                {{ isEdit ? `Edit: ${plan.name}` : 'Create Maintenance Plan' }}
            </h1>
            <p class="text-sm text-slate-500">Configure maintenance pricing in Naira, service inclusions, and field inspection frequency.</p>
        </div>

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Plan Name *</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        placeholder="e.g. Residential Solar Care Plan" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        required 
                    />
                    <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Subscription Price (₦ Naira) *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-slate-400">₦</span>
                            <input 
                                v-model="form.price_naira" 
                                type="number" 
                                step="any"
                                placeholder="e.g. 45000" 
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                                required 
                            />
                        </div>
                        <p v-if="form.errors.price_naira" class="text-xs text-red-600 mt-1">{{ form.errors.price_naira }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Billing Period *</label>
                        <select 
                            v-model="form.billing_period" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden"
                            required
                        >
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="bi-annually">Bi-Annually</option>
                            <option value="annually">Annually</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Visits per Year *</label>
                        <input 
                            v-model="form.visits_per_year" 
                            type="number" 
                            min="1"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                            required 
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Display Sort Order</label>
                        <input 
                            v-model="form.sort_order" 
                            type="number" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea 
                        v-model="form.description" 
                        rows="2" 
                        placeholder="Preventive maintenance ensuring peak generation efficiency and battery longevity."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Included Features & Checklist (One item per line)
                    </label>
                    <textarea 
                        v-model="inclusionText" 
                        rows="5" 
                        placeholder="De-ionized panel washing&#10;Inverter terminal torque & thermal scan&#10;Lithium battery health audit&#10;Earthing resistance test"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-mono focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden"
                    ></textarea>
                </div>

                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400" />
                        <span class="text-sm font-bold text-slate-800">Active (Visible on Website)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400" />
                        <span class="text-sm font-bold text-slate-800">Featured Badge</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <Link :href="route('admin.plans.index')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50">
                    Cancel
                </Link>
                <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-sm shadow-md transition disabled:opacity-50"
                >
                    <span v-if="form.processing">Saving...</span>
                    <span v-else>{{ isEdit ? 'Save Changes' : 'Create Plan' }}</span>
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
