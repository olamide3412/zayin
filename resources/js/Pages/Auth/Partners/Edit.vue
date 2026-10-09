<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ImageUpload from '@/Components/ImageUpload.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    partner: Object,
});

const form = useForm({
    name: props.partner.name || '',
    logo_url: props.partner.logo_url || '',
    website_url: props.partner.website_url || '',
    description: props.partner.description || '',
    status: props.partner.status || 'active',
    sort_order: props.partner.sort_order || 0,
});

const submit = () => {
    form.put(route('admin.partners.update', props.partner.id));
};
</script>

<template>
    <Head :title="'Edit Brand Partner: ' + partner.name + ' | Administration'" />

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 font-sans">
        
        <!-- Header -->
        <div class="flex items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <Link :href="route('admin.partners.index')" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1 mb-2">
                    ← Back to Partners
                </Link>
                <h1 class="text-2xl font-bold text-slate-900 font-['Outfit',sans-serif]">
                    Edit Brand Partner: {{ partner.name }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Update hardware manufacturer or equipment partner details.
                </p>
            </div>
        </div>

        <!-- Global Error Banner -->
        <div v-if="form.hasErrors" class="bg-red-50 border border-red-200 p-4 rounded-2xl text-red-700 text-sm font-semibold flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-bold">Please correct the errors highlighted below:</p>
                <ul class="list-disc list-inside text-xs mt-1 space-y-0.5 font-normal">
                    <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                </ul>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <form @submit.prevent="submit" class="space-y-6">
                
                <!-- Partner Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Brand / Manufacturer Name *
                    </label>
                    <input 
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="e.g. SpaceX Starlink, Felicity Solar, Victron Energy, MultiChoice DStv"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        :class="{ 'border-red-500': form.errors.name }"
                    />
                    <span v-if="form.errors.name" class="text-xs text-red-500 font-bold mt-1 block">{{ form.errors.name }}</span>
                </div>

                <!-- Logo Upload -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Brand Logo
                    </label>
                    <ImageUpload v-model="form.logo_url" />
                    <p class="text-xs text-gray-400 mt-1">Upload a clean PNG/JPG logo or paste image URL.</p>
                    <span v-if="form.errors.logo_url" class="text-xs text-red-500 font-bold mt-1 block">{{ form.errors.logo_url }}</span>
                </div>

                <!-- Website URL -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Website Link (Optional)
                    </label>
                    <input 
                        v-model="form.website_url"
                        type="text"
                        placeholder="https://example.com"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        :class="{ 'border-red-500': form.errors.website_url }"
                    />
                    <span v-if="form.errors.website_url" class="text-xs text-red-500 font-bold mt-1 block">{{ form.errors.website_url }}</span>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Short Equipment / Specialty Description (Optional)
                    </label>
                    <textarea 
                        v-model="form.description"
                        rows="3"
                        placeholder="e.g. Tier-1 LiFePO4 Lithium Battery Storage & Robust Hybrid Inverters..."
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        :class="{ 'border-red-500': form.errors.description }"
                    ></textarea>
                    <span v-if="form.errors.description" class="text-xs text-red-500 font-bold mt-1 block">{{ form.errors.description }}</span>
                </div>

                <!-- Status & Sort Order -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Status
                        </label>
                        <select 
                            v-model="form.status"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm bg-white text-slate-900 font-bold"
                            :class="{ 'border-red-500': form.errors.status }"
                        >
                            <option value="active">Active (Visible on Homepage)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                        <span v-if="form.errors.status" class="text-xs text-red-500 font-bold mt-1 block">{{ form.errors.status }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Sort Order
                        </label>
                        <input 
                            v-model.number="form.sort_order"
                            type="number"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm bg-white text-slate-900"
                            :class="{ 'border-red-500': form.errors.sort_order }"
                        />
                        <span v-if="form.errors.sort_order" class="text-xs text-red-500 font-bold mt-1 block">{{ form.errors.sort_order }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 flex items-center justify-end space-x-3 border-t border-gray-100">
                    <Link 
                        :href="route('admin.partners.index')"
                        class="px-6 py-3 rounded-xl border border-gray-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 rounded-xl bg-primary hover:bg-primary-dark text-white font-extrabold text-sm shadow-md hover:shadow-lg disabled:opacity-50 transition-all flex items-center gap-2"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>{{ form.processing ? 'Updating...' : 'Update Partner' }}</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</template>
