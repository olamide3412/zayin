<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const categoryId = ref(props.filters?.category_id || '');

const doSearch = () => {
    router.get(route('admin.products.index'), {
        search: search.value || undefined,
        category_id: categoryId.value || undefined,
    }, { preserveState: true, replace: true });
};

// Quick price update inline
const quickPrice = ref({});
const savingId = ref(null);

const saveQuickPrice = (product) => {
    savingId.value = product.id;
    const priceVal = quickPrice.value[product.id] !== undefined ? quickPrice.value[product.id] : product.price_naira;

    router.patch(route('admin.products.quick-update', product.id), {
        price_naira: priceVal,
    }, {
        preserveScroll: true,
        onFinish: () => {
            savingId.value = null;
        },
    });
};

const togglePublished = (product) => {
    router.patch(route('admin.products.quick-update', product.id), {
        is_published: !product.is_published,
    }, { preserveScroll: true });
};

const deleteForm = useForm({});
const deleteProduct = (id, name) => {
    if (!confirm(`Are you sure you want to delete "${name}"? This cannot be undone.`)) return;
    deleteForm.delete(route('admin.products.destroy', id), {
        preserveScroll: true,
    });
};

const formatNaira = (val) => {
    if (val === null || val === undefined) return '—';
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Products & Packages Management" />

        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Products, Packages & Pricing</h1>
                <p class="text-sm text-slate-500 mt-0.5">Configure live store packages, inverter systems, lithium batteries, and pricing.</p>
            </div>
            <Link 
                :href="route('admin.products.create')" 
                class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Add Product / Package</span>
            </Link>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-2xl p-4 mb-5 flex flex-wrap gap-3 items-center shadow-xs border border-slate-200">
            <div class="relative flex-1 min-w-[240px]">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input 
                    v-model="search" 
                    @keyup.enter="doSearch" 
                    type="text" 
                    placeholder="Search by package name or SKU..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden transition" 
                />
            </div>
            <select 
                v-model="categoryId" 
                @change="doSearch" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:bg-white focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 outline-hidden transition"
            >
                <option value="">All Categories</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                </option>
            </select>
            <button 
                @click="doSearch"
                class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition"
            >
                Filter
            </button>
        </div>

        <!-- Product Table -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/75 text-xs text-slate-500 font-bold uppercase tracking-wider">
                            <th class="px-5 py-3.5">Product / Package</th>
                            <th class="px-4 py-3.5">Category</th>
                            <th class="px-4 py-3.5">Live Price (₦)</th>
                            <th class="px-4 py-3.5 hidden md:table-cell">Compare Price</th>
                            <th class="px-4 py-3.5 hidden lg:table-cell">Stock</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="products.data.length === 0">
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                No products found. <Link :href="route('admin.products.create')" class="text-amber-600 font-bold hover:underline">Add the first product</Link>
                            </td>
                        </tr>
                        <tr v-for="p in products.data" :key="p.id" class="hover:bg-slate-50/60 transition group">
                            <!-- Name & Details -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                        <img v-if="p.image" :src="p.image" :alt="p.name" class="w-full h-full object-cover" />
                                        <svg v-else class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-amber-600 transition">
                                            {{ p.name }}
                                        </div>
                                        <div class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                                            <span>SKU: {{ p.sku }}</span>
                                            <span v-if="p.specs?.capacity_kva" class="px-1.5 py-0.2 bg-amber-50 text-amber-700 rounded font-semibold text-[10px]">
                                                {{ p.specs.capacity_kva }}kVA
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-4 py-4 text-slate-600 font-medium">
                                {{ p.category }}
                            </td>

                            <!-- Price (Quick Edit in Naira) -->
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="relative w-36">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">₦</span>
                                        <input 
                                            :value="quickPrice[p.id] !== undefined ? quickPrice[p.id] : p.price_naira" 
                                            @input="quickPrice[p.id] = $event.target.value"
                                            @keyup.enter="saveQuickPrice(p)"
                                            type="number" 
                                            class="w-full pl-6 pr-2 py-1.5 text-xs font-bold text-slate-900 border border-slate-200 rounded-lg focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-hidden"
                                        />
                                    </div>
                                    <button 
                                        v-if="quickPrice[p.id] !== undefined && quickPrice[p.id] != p.price_naira"
                                        @click="saveQuickPrice(p)"
                                        :disabled="savingId === p.id"
                                        class="px-2 py-1 bg-amber-500 text-slate-950 font-bold text-xs rounded-lg hover:bg-amber-600 shadow-xs"
                                        title="Save Price"
                                    >
                                        <span v-if="savingId === p.id">...</span>
                                        <span v-else>Save</span>
                                    </button>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1">
                                    Current: {{ formatNaira(p.price_naira) }}
                                </div>
                            </td>

                            <!-- Compare At Price -->
                            <td class="px-4 py-4 text-slate-500 hidden md:table-cell text-xs">
                                <span v-if="p.compare_at_naira" class="line-through text-slate-400">
                                    {{ formatNaira(p.compare_at_naira) }}
                                </span>
                                <span v-else>—</span>
                            </td>

                            <!-- Stock -->
                            <td class="px-4 py-4 text-slate-700 hidden lg:table-cell">
                                <span class="font-bold">{{ p.stock_qty }}</span>
                            </td>

                            <!-- Published Toggle -->
                            <td class="px-4 py-4 text-center">
                                <button 
                                    @click="togglePublished(p)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition"
                                    :class="p.is_published ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full" :class="p.is_published ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    <span>{{ p.is_published ? 'Active' : 'Draft' }}</span>
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link 
                                        :href="route('admin.products.edit', p.id)" 
                                        class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                        title="Edit Product & Specs"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </Link>
                                    <button 
                                        @click="deleteProduct(p.id, p.name)" 
                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Delete Product"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="products.links && products.links.length > 3" class="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Showing {{ products.from }} to {{ products.to }} of {{ products.total }} products
                </div>
                <div class="flex items-center gap-1">
                    <Component 
                        :is="link.url ? Link : 'span'" 
                        v-for="(link, i) in products.links" 
                        :key="i" 
                        :href="link.url" 
                        v-html="link.label" 
                        class="px-3 py-1 text-xs rounded-lg transition font-medium" 
                        :class="link.active ? 'bg-amber-500 text-slate-950 font-bold' : link.url ? 'text-slate-600 hover:bg-slate-100' : 'text-slate-300 cursor-not-allowed'" 
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
