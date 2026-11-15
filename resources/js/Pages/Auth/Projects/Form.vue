<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ImageUpload from '@/Components/ImageUpload.vue';
import MultiMediaUpload from '@/Components/MultiMediaUpload.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    project: Object,
    services: Array,
    states: Array,
});

const isEditing = computed(() => !!props.project?.id);

const form = useForm({
    title: props.project?.title || '',
    service_id: props.project?.service_id || '',
    state_id: props.project?.state_id || '',
    city: props.project?.city || '',
    client_name: props.project?.client_name || '',
    system_size: props.project?.system_size || '',
    completed_on: props.project?.completed_on || '',
    summary: props.project?.summary || '',
    body: props.project?.body || '',
    featured_image: props.project?.featured_image || '',
    gallery: Array.isArray(props.project?.gallery) ? props.project.gallery : [],
    video_url: props.project?.video_url || '',
    video_title: props.project?.video_title || '',
    is_featured: props.project?.is_featured ?? false,
    is_published: props.project?.is_published ?? true,
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.projects.update', props.project.id));
    } else {
        form.post(route('admin.projects.store'));
    }
};
</script>

<template>
    <Head :title="`${isEditing ? 'Edit' : 'Add New'} Project | Administration`" />

    <div class="py-4 sm:py-8 max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4 sm:space-y-6 font-sans">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <Link :href="route('admin.projects.index')" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1 mb-1.5">
                    ← Back to Projects
                </Link>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-['Outfit',sans-serif]">
                    {{ isEditing ? 'Edit Completed Project' : 'Add New Completed Project' }}
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                    Document client installations, solar array sizes, proof photos, and YouTube walkthrough videos.
                </p>
            </div>
            <a 
                v-if="isEditing && project?.slug" 
                :href="`/projects/${project.slug}`" 
                target="_blank"
                class="inline-flex items-center justify-center text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition w-full sm:w-auto text-center"
            >
                View Live Page ↗
            </a>
        </div>

        <!-- Global Error Banner -->
        <div v-if="form.hasErrors" class="bg-red-50 border border-red-200 p-4 rounded-2xl text-red-700 text-sm font-semibold flex items-center gap-3">
            <svg class="w-6 h-6 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-bold">Please correct the highlighted issues:</p>
                <ul class="list-disc list-inside text-xs mt-1 space-y-0.5 font-normal">
                    <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                </ul>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-8">
            <form @submit.prevent="submit" class="space-y-5 sm:space-y-6">
                
                <!-- Project Title -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Project Headline / Title *
                    </label>
                    <input 
                        v-model="form.title"
                        type="text"
                        required
                        placeholder="e.g. 5kVA 48V Hybrid Solar Installation for 5-Bedroom Duplex"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        :class="{ 'border-red-500': form.errors.title }"
                    />
                    <span v-if="form.errors.title" class="text-xs text-red-500 font-bold mt-1 block">{{ form.errors.title }}</span>
                </div>

                <!-- Service & System Spec -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Service Category
                        </label>
                        <select 
                            v-model="form.service_id"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        >
                            <option value="">Select Service (e.g. Solar, Starlink)</option>
                            <option v-for="srv in services" :key="srv.id" :value="srv.id">{{ srv.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            System Spec / Capacity
                        </label>
                        <input 
                            v-model="form.system_size"
                            type="text"
                            placeholder="e.g. 5kVA Inverter / 5.12kWh Lithium / 3.3kW Solar"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        />
                    </div>
                </div>

                <!-- Location & Client -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            State
                        </label>
                        <select 
                            v-model="form.state_id"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        >
                            <option value="">Select State</option>
                            <option v-for="st in states" :key="st.id" :value="st.id">{{ st.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            City / Area
                        </label>
                        <input 
                            v-model="form.city"
                            type="text"
                            placeholder="e.g. Lekki Phase 1, Abraka, Ikeja"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Client Reference
                        </label>
                        <input 
                            v-model="form.client_name"
                            type="text"
                            placeholder="e.g. Engr. Babatunde A."
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        />
                    </div>
                </div>

                <!-- Completion Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Completion Date
                        </label>
                        <input 
                            v-model="form.completed_on"
                            type="date"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                        />
                    </div>
                </div>

                <!-- Featured Image -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Cover / Primary Photo *
                    </label>
                    <ImageUpload v-model="form.featured_image" />
                    <p class="text-xs text-gray-400 mt-1">Primary preview card image shown on homepage and project listing.</p>
                </div>

                <!-- Gallery: Multiple Photos & Video Clips -->
                <div class="p-4 sm:p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-900">
                                Project Gallery & Video Clips (Max 10)
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Upload up to 10 proof photos (battery racks, roof solar mounts, cable trunking) or MP4/WebM clips.
                            </p>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-500 bg-white border border-gray-200 px-2 py-0.5 rounded">
                            {{ form.gallery.length }} / 10
                        </span>
                    </div>

                    <MultiMediaUpload v-model="form.gallery" :max="10" />
                </div>

                <!-- Video Walkthrough (YouTube) -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                    <div class="font-bold text-xs uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                        <span>📹 Video Proof & Walkthrough (Optional)</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">YouTube / External Video URL</label>
                            <input 
                                v-model="form.video_url"
                                type="url"
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs bg-white text-slate-900"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Video Title / Caption</label>
                            <input 
                                v-model="form.video_title"
                                type="text"
                                placeholder="Walkthrough & Load Test Tour"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs bg-white text-slate-900"
                            />
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Short Summary (Hook)
                    </label>
                    <textarea 
                        v-model="form.summary"
                        rows="2"
                        placeholder="Replaced daily 12-hour generator runs with a silent 5kVA Deye inverter and lithium battery bank."
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50"
                    ></textarea>
                </div>

                <!-- Full Story / Scope of Work -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Detailed Project Case Study
                    </label>
                    <textarea 
                        v-model="form.body"
                        rows="5"
                        placeholder="Explain the client's energy challenge, the equipment installed, cabling trunking, surge protection, and performance outcome..."
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-white text-slate-900 focus:ring-2 focus:ring-primary/50 font-mono text-xs"
                    ></textarea>
                </div>

                <!-- Toggles -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input 
                            v-model="form.is_published"
                            type="checkbox"
                            class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                        />
                        <div>
                            <div class="text-xs font-bold text-slate-900">Publish Immediately</div>
                            <div class="text-[11px] text-gray-500">Show on public /projects showcase</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input 
                            v-model="form.is_featured"
                            type="checkbox"
                            class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                        />
                        <div>
                            <div class="text-xs font-bold text-slate-900">Featured Showcase</div>
                            <div class="text-[11px] text-gray-500">Feature on homepage portfolio preview</div>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2.5 sm:gap-3 pt-4 border-t border-gray-100">
                    <Link 
                        :href="route('admin.projects.index')"
                        class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition"
                    >
                        Cancel
                    </Link>
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full sm:w-auto inline-flex items-center justify-center bg-primary hover:bg-primary-dark text-white font-extrabold text-sm px-6 py-2.5 rounded-xl transition-all shadow-sm hover:shadow disabled:opacity-50"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>{{ isEditing ? 'Update Project' : 'Publish Project' }}</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</template>

