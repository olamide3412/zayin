<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ImageUpload from '@/Components/ImageUpload.vue'

const props = defineProps({
    settings: Object
})

const form = useForm({
    enable_appointments: props.settings?.enable_appointments ?? true,
    hero_slides: (props.settings?.hero_slides ?? []).map((slide, idx) => ({
        id: slide.id ?? `slide-${Date.now()}-${idx}-${Math.random()}`,
        type: slide.type,
        value: slide.value
    })),
    about_mission: props.settings?.about_mission ?? '',
    about_vision: props.settings?.about_vision ?? '',
    about_founder_name: props.settings?.about_founder_name ?? '',
    about_founder_title: props.settings?.about_founder_title ?? '',
    about_founder_bio: props.settings?.about_founder_bio ?? '',
    about_founder_image: props.settings?.about_founder_image ?? '',
    site_logo_url: props.settings?.site_logo_url ?? '',
})

const addSlide = () => {
    form.hero_slides.push({
        id: `slide-${Date.now()}-${Math.random()}`,
        type: 'link',
        value: ''
    })
}

const removeSlide = (index) => {
    form.hero_slides.splice(index, 1)
}

const moveSlide = (index, direction) => {
    const targetIndex = index + direction
    if (targetIndex < 0 || targetIndex >= form.hero_slides.length) return
    const item = form.hero_slides[index]
    form.hero_slides.splice(index, 1)
    form.hero_slides.splice(targetIndex, 0, item)
}

const saveSettings = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="System Settings" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure global website options, hero banners, and company information.</p>
        </div>

        <div class="max-w-4xl">
            <form @submit.prevent="saveSettings" class="space-y-6">
                <!-- Company Brand & Logo Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Company Brand & Logo
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Upload and customize the official company logo displayed across the website, navigation bar, login screen, admin dashboard, and invoices.</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="max-w-md">
                            <ImageUpload 
                                v-model="form.site_logo_url" 
                                label="Upload Company Logo (PNG, SVG, or JPG)" 
                            />
                            <p class="text-xs text-gray-400 mt-1.5">
                                Recommended: Square or horizontal transparent PNG/SVG (e.g. 512×512px). Leave blank to use the built-in Zayin Ltd emblem.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Appointment Settings Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Site Visit & Installation Booking Control
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <label class="text-base font-semibold text-gray-900 block">
                                    Enable Online Site Visit & Installation Booking
                                </label>
                                <p class="text-sm text-gray-500 mt-1">
                                    Toggle whether public visitors can schedule and request technician site visits and engineering installations online.
                                    When disabled, all booking entry points, buttons, and navigation links on the website are hidden.
                                </p>
                            </div>
                            
                            <!-- Toggle Switch -->
                            <button 
                                type="button"
                                @click="form.enable_appointments = !form.enable_appointments"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                :class="form.enable_appointments ? 'bg-primary' : 'bg-gray-200'"
                            >
                                <span 
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="form.enable_appointments ? 'translate-x-5' : 'translate-x-0'"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- About Us Page Management Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            About Us Page Content (Mission, Vision & Founder)
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <p class="text-sm text-gray-500">
                            Customize the Mission, Vision, and Founder showcase sections on the public About Us page. HTML formatting tags are supported.
                        </p>

                        <!-- Mission Statement -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Company Mission Statement
                            </label>
                            <textarea 
                                v-model="form.about_mission" 
                                rows="3"
                                placeholder="Enter mission statement (HTML formatting supported)..."
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-primary focus:outline-none font-mono"
                            ></textarea>
                            <p class="text-xs text-gray-400 mt-1">HTML tags like &lt;p&gt;, &lt;strong&gt;, &lt;em&gt; are supported.</p>
                        </div>

                        <!-- Vision Statement -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Company Vision Statement
                            </label>
                            <textarea 
                                v-model="form.about_vision" 
                                rows="3"
                                placeholder="Enter vision statement (HTML formatting supported)..."
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-primary focus:outline-none font-mono"
                            ></textarea>
                        </div>

                        <div class="border-t border-gray-100 pt-6 space-y-6">
                            <h4 class="font-extrabold text-slate-900 text-base font-['Outfit',sans-serif]">
                                Founder & Leadership Showcase
                            </h4>

                            <!-- Founder Image Upload -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    Founder Photo / Portrait
                                </label>
                                <ImageUpload v-model="form.about_founder_image" label="Upload Founder Photo" />
                                <p class="text-xs text-gray-400 mt-1">Upload a high quality portrait of the founder (JPG/PNG).</p>
                            </div>

                            <!-- Founder Name & Title -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Founder Name
                                    </label>
                                    <input 
                                        v-model="form.about_founder_name"
                                        type="text"
                                        placeholder="e.g. Engr. Olamide Zayin"
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-primary focus:outline-none font-semibold"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Founder Title / Role
                                    </label>
                                    <input 
                                        v-model="form.about_founder_title"
                                        type="text"
                                        placeholder="e.g. Managing Director & Lead Systems Engineer, Zayin Ltd"
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-primary focus:outline-none"
                                    />
                                </div>
                            </div>

                            <!-- Founder Bio / Message -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Founder Biography / Executive Message
                                </label>
                                <textarea 
                                    v-model="form.about_founder_bio" 
                                    rows="5"
                                    placeholder="Enter founder message or biography (HTML formatting supported)..."
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-primary focus:outline-none font-mono"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Slider Settings Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Hero Slider Images
                        </h3>
                        <button 
                            type="button" 
                            @click="addSlide"
                            class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary/95 text-white font-semibold py-1.5 px-4 rounded-xl text-sm transition-all shadow-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Slide
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <p class="text-sm text-gray-500">
                            Configure the banner slides displayed in the home page hero section. If no custom slides are added, the system automatically falls back to default unsplash banners.
                        </p>

                        <!-- Empty State -->
                        <div v-if="form.hero_slides.length === 0" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/55">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-600">No Custom Hero Slides</span>
                            <p class="text-xs text-gray-400 mt-1 max-w-sm text-center">Currently displaying default engineering slides. Click "Add Slide" to begin customizing.</p>
                        </div>

                        <!-- Slides list -->
                        <div v-else class="space-y-4">
                            <div 
                                v-for="(slide, index) in form.hero_slides" 
                                :key="slide.id"
                                class="p-4 border border-gray-100 bg-gray-50/50 rounded-2xl flex flex-col md:flex-row gap-4 items-start relative group transition-all hover:border-gray-200"
                            >
                                <!-- Drag / Order controls -->
                                <div class="flex md:flex-col gap-1.5 items-center justify-center self-stretch bg-gray-100/50 rounded-xl px-2 py-1 md:py-2">
                                    <button 
                                        type="button" 
                                        @click="moveSlide(index, -1)"
                                        :disabled="index === 0"
                                        class="p-1 text-gray-500 hover:text-primary disabled:opacity-30 disabled:hover:text-gray-500 transition-colors"
                                        title="Move Up"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                    <span class="text-xs font-bold text-gray-400 min-w-4 text-center">
                                        #{{ index + 1 }}
                                    </span>
                                    <button 
                                        type="button" 
                                        @click="moveSlide(index, 1)"
                                        :disabled="index === form.hero_slides.length - 1"
                                        class="p-1 text-gray-500 hover:text-primary disabled:opacity-30 disabled:hover:text-gray-500 transition-colors"
                                        title="Move Down"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Slide Details -->
                                <div class="flex-1 space-y-3 w-full">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <!-- Type selector buttons -->
                                        <div class="inline-flex rounded-lg bg-gray-100 p-0.5 border border-gray-200/50">
                                            <button 
                                                type="button"
                                                @click="slide.type = 'link'; slide.value = ''"
                                                class="px-3 py-1 rounded-md text-xs font-semibold transition-all"
                                                :class="slide.type === 'link' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                            >
                                                External Link
                                            </button>
                                            <button 
                                                type="button"
                                                @click="slide.type = 'upload'; slide.value = ''"
                                                class="px-3 py-1 rounded-md text-xs font-semibold transition-all"
                                                :class="slide.type === 'upload' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                            >
                                                Upload Image
                                            </button>
                                        </div>

                                        <!-- Delete Button -->
                                        <button 
                                            type="button"
                                            @click="removeSlide(index)"
                                            class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50"
                                            title="Delete slide"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Content Field -->
                                    <div class="mt-2 w-full">
                                        <!-- Link Input -->
                                        <div v-if="slide.type === 'link'" class="space-y-1">
                                            <input 
                                                v-model="slide.value" 
                                                type="url" 
                                                placeholder="https://example.com/image.jpg"
                                                class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-white text-gray-800 focus:ring-2 focus:ring-primary/45 focus:outline-none text-sm transition-all"
                                            />
                                        </div>

                                        <!-- Upload Input -->
                                        <div v-else-if="slide.type === 'upload'">
                                            <ImageUpload 
                                                v-model="slide.value" 
                                                label="Upload Slide Image"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Preview Pane -->
                                <div 
                                    v-if="slide.type === 'link' && slide.value" 
                                    class="w-full md:w-32 h-24 rounded-xl overflow-hidden border border-gray-200/60 flex-shrink-0"
                                >
                                    <img :src="slide.value" alt="Preview" class="w-full h-full object-cover bg-gray-100" @error="(e) => e.target.style.display = 'none'" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="flex justify-end gap-3 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-6 rounded-xl transition-all shadow disabled:opacity-60 flex items-center gap-2 text-sm"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ form.processing ? 'Saving...' : 'Save Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
