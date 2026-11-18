<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ImageUpload from '@/Components/ImageUpload.vue';

const props = defineProps({
    product: Object,
    categories: Array,
});

const isEdit = !!props.product;

// Image source mode: 'upload' | 'url'
const imageMode = ref(
    props.product?.image && (props.product.image.startsWith('http://') || props.product.image.startsWith('https://'))
        ? 'url'
        : 'upload'
);

const form = useForm({
    name: props.product?.name || '',
    category_id: props.product?.category_id || (props.categories[0]?.id ?? ''),
    sku: props.product?.sku || '',
    price_naira: props.product?.price_naira || '',
    compare_at_naira: props.product?.compare_at_naira || '',
    stock_qty: props.product?.stock_qty !== undefined ? props.product.stock_qty : 10,
    summary: props.product?.summary || '',
    description: props.product?.description || '',
    warranty_text: props.product?.warranty_text || '1 Year Manufacturer Warranty',
    image: props.product?.image || '',
    is_published: props.product?.is_published !== undefined ? props.product.is_published : true,
    is_featured: props.product?.is_featured || false,
    specs: {
        capacity_kva: props.product?.specs?.capacity_kva || '',
        battery_kwh: props.product?.specs?.battery_kwh || '',
        panels_w: props.product?.specs?.panels_w || '',
        inverter_type: props.product?.specs?.inverter_type || 'Pure Sine Wave Hybrid',
        battery_type: props.product?.specs?.battery_type || 'Lithium LiFePO4',
    },
});

const submit = () => {
    if (isEdit) {
        form.put(route('admin.products.update', props.product.id));
    } else {
        form.post(route('admin.products.store'));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="isEdit ? `Edit ${product.name}` : 'New Product / Package'" />

        <div class="mb-6 flex items-center justify-between">
            <div>
                <Link :href="route('admin.products.index')" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 mb-1">
                    &larr; Back to Products & Packages
                </Link>
                <h1 class="text-2xl font-black text-slate-900">
                    {{ isEdit ? `Edit: ${product.name}` : 'Create New Product / Package' }}
                </h1>
                <p class="text-sm text-slate-500">Configure package specifications, pricing in Naira, and warranty terms.</p>
            </div>
        </div>

        <form @submit.prevent="submit" class="max-w-4xl space-y-6">
            <!-- 1. Basic Info & Category -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Basic Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Product / Package Name *</label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            placeholder="e.g. 3.5kVA / 24V Pure Sine Wave Hybrid Solar Package" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                            required 
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Store Category *</label>
                        <select 
                            v-model="form.category_id" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden"
                            required
                        >
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="text-xs text-red-600 mt-1">{{ form.errors.category_id }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">SKU (Stock Keeping Unit)</label>
                        <input 
                            v-model="form.sku" 
                            type="text" 
                            placeholder="e.g. ZAY-SOL-3KVA" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Short Summary / Teaser</label>
                    <textarea 
                        v-model="form.summary" 
                        rows="2" 
                        placeholder="Power your 3-bedroom flat: lights, TV, fridge, fans, and laptops 24/7."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden"
                    ></textarea>
                </div>
            </div>

            <!-- 2. Pricing & Inventory (Crucial!) -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="text-base font-bold text-slate-900">Pricing & Inventory (Configurable)</h2>
                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-md">Live Store Pricing</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Selling Price (₦ Naira) *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-slate-400">₦</span>
                            <input 
                                v-model="form.price_naira" 
                                type="number" 
                                step="any"
                                @wheel="$event.target.blur()"
                                placeholder="e.g. 1750000" 
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                                required 
                            />
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Stored securely in kobo, displayed in Naira.</p>
                        <p v-if="form.errors.price_naira" class="text-xs text-red-600 mt-1">{{ form.errors.price_naira }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Original / Compare-At Price (₦)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-slate-400">₦</span>
                            <input 
                                v-model="form.compare_at_naira" 
                                type="number" 
                                step="any"
                                @wheel="$event.target.blur()"
                                placeholder="e.g. 1950000" 
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                            />
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Displays as crossed-out discount.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stock Quantity</label>
                        <input 
                            v-model="form.stock_qty" 
                            type="number" 
                            @wheel="$event.target.blur()"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                            required 
                        />
                    </div>
                </div>
            </div>

            <!-- 3. Solar Calculator Matching Specs -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="text-base font-bold text-slate-900">Engineering Specs & Sizing Matching</h2>
                    <span class="text-xs text-slate-500">Used by Solar Calculator matcher</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Inverter Capacity (kVA)</label>
                        <input 
                            v-model="form.specs.capacity_kva" 
                            type="number" 
                            step="0.1" 
                            @wheel="$event.target.blur()"
                            placeholder="e.g. 3.5" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Battery Storage (kWh)</label>
                        <input 
                            v-model="form.specs.battery_kwh" 
                            type="number" 
                            step="0.1" 
                            @wheel="$event.target.blur()"
                            placeholder="e.g. 5.12" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Solar Panels Total (Watts)</label>
                        <input 
                            v-model="form.specs.panels_w" 
                            type="number" 
                            @wheel="$event.target.blur()"
                            placeholder="e.g. 2200" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Warranty Coverage</label>
                        <input 
                            v-model="form.warranty_text" 
                            type="text" 
                            placeholder="e.g. 5 Years Battery / 2 Years Inverter" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                        />
                    </div>
                </div>
            </div>

            <!-- 4. Package / Product Image (Upload or URL) -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Package / Product Image</h2>
                        <p class="text-xs text-slate-500">Upload an image directly from your device, or paste a URL / file path.</p>
                    </div>

                    <!-- Mode Toggle -->
                    <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200 text-xs font-bold">
                        <button
                            type="button"
                            @click="imageMode = 'upload'"
                            :class="[
                                'px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer',
                                imageMode === 'upload' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'
                            ]"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Upload Image</span>
                        </button>
                        <button
                            type="button"
                            @click="imageMode = 'url'"
                            :class="[
                                'px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer',
                                imageMode === 'url' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'
                            ]"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <span>Image URL / Path</span>
                        </button>
                    </div>
                </div>

                <!-- Mode A: Upload from Device -->
                <div v-if="imageMode === 'upload'">
                    <ImageUpload 
                        v-model="form.image" 
                        label="Upload Package Image (PNG, JPG, or WebP)" 
                    />
                    <p class="text-xs text-slate-400 mt-1.5">
                        Selected file will be automatically compressed and saved to the storage disk.
                    </p>
                </div>

                <!-- Mode B: Paste URL or Local Path -->
                <div v-else class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Image URL / Local Path</label>
                        <div class="relative">
                            <input 
                                v-model="form.image" 
                                type="text" 
                                placeholder="e.g. https://images.unsplash.com/... or /storage/packages/solar.jpg" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden" 
                            />
                            <button
                                v-if="form.image"
                                type="button"
                                @click="form.image = ''"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-red-500 font-bold"
                            >
                                Clear
                            </button>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Paste a full web URL or local path to the product image.</p>
                    </div>

                    <!-- Live Preview for URL Mode -->
                    <div v-if="form.image" class="pt-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Image Preview</label>
                        <div class="w-full max-w-xs h-44 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 relative">
                            <img 
                                :src="form.image" 
                                alt="Package Image Preview" 
                                class="w-full h-full object-cover" 
                                @error="(e) => e.target.src = 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?q=80&w=800&auto=format&fit=crop'"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Publishing & Status -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input v-model="form.is_published" type="checkbox" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400" />
                        <span class="text-sm font-bold text-slate-800">Publish in Store & Packages</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400" />
                        <span class="text-sm font-bold text-slate-800">Feature on Homepage</span>
                    </label>
                </div>

                <div class="flex items-center gap-3">
                    <Link :href="route('admin.products.index')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50">
                        Cancel
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-sm shadow-md transition disabled:opacity-50"
                    >
                        <span v-if="form.processing">Saving...</span>
                        <span v-else>{{ isEdit ? 'Save Changes' : 'Create Product' }}</span>
                    </button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
