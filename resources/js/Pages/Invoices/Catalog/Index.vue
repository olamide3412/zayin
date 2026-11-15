<template>
    <Head title="Invoice Catalog - Zayin Ltd" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">Items & Services Catalog</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Self-learning catalog for quick invoice autocompletion and price tracking.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        type="button"
                        @click="openCreateModal"
                        class="px-3.5 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 shadow-sm flex items-center justify-center"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </button>
                </div>
            </div>
        </template>

        <InvoiceNavSubBar />

        <div class="py-4 sm:py-6 max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            <!-- Filter & Search Bar -->
            <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row gap-2.5 sm:gap-3 items-stretch sm:items-center justify-between">
                <div class="w-full sm:w-80 md:w-96 relative">
                    <input
                        type="text"
                        v-model="search"
                        @keyup.enter="applyFilters"
                        class="w-full pl-3 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                        placeholder="Search items, descriptions, categories..."
                    />
                </div>

                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <select
                        v-model="category"
                        @change="applyFilters"
                        class="flex-1 sm:flex-none px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                    >
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>

                    <button
                        type="button"
                        @click="applyFilters"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 shrink-0"
                    >
                        Filter
                    </button>
                </div>
            </div>

            <!-- Items Container: Desktop Table & Mobile Cards -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Mobile Card View (block sm:hidden) -->
                <div class="block sm:hidden divide-y divide-gray-100">
                    <div v-if="items.data.length === 0" class="p-8 text-center text-xs text-gray-500">
                        No catalog items found. Click "+ Add Item" to create one.
                    </div>

                    <div
                        v-for="item in items.data"
                        :key="`m-${item.id}`"
                        class="p-4 space-y-2 hover:bg-gray-50/75 transition-colors"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="font-bold text-sm text-gray-900">{{ item.name }}</div>
                                <div v-if="item.description" class="text-xs text-gray-500 line-clamp-2 mt-0.5">
                                    {{ item.description }}
                                </div>
                            </div>
                            <span v-if="item.category" class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700 shrink-0">
                                {{ item.category }}
                            </span>
                        </div>

                        <div class="flex items-baseline justify-between pt-1">
                            <div>
                                <span class="text-xs text-gray-500">Price: </span>
                                <span class="text-base font-black text-gray-900">₦{{ formatKobo(item.default_price_kobo, false) }}</span>
                                <span class="text-xs text-gray-400 ml-1">/ {{ item.unit }}</span>
                            </div>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">
                                {{ item.usage_count }}x used
                            </span>
                        </div>

                        <div class="pt-2 border-t border-gray-100 flex items-center justify-end space-x-2">
                            <button
                                type="button"
                                @click="editItem(item)"
                                class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg"
                            >
                                Edit Item
                            </button>
                            <button
                                type="button"
                                @click="deleteItem(item.id)"
                                class="px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg"
                            >
                                Archive
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table View (hidden sm:block) -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase">
                            <tr>
                                <th class="px-5 py-3.5">Item Name & Details</th>
                                <th class="px-5 py-3.5">Category</th>
                                <th class="px-5 py-3.5 text-center">Unit</th>
                                <th class="px-5 py-3.5 text-right">Price (₦)</th>
                                <th class="px-5 py-3.5 text-center whitespace-nowrap">Used</th>
                                <th class="px-5 py-3.5 text-right whitespace-nowrap w-28 min-w-[110px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="items.data.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                    No catalog items found.
                                </td>
                            </tr>
                            <tr
                                v-for="item in items.data"
                                :key="item.id"
                                class="hover:bg-gray-50/75 transition-colors"
                            >
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-900">{{ item.name }}</div>
                                    <div v-if="item.description" class="text-xs text-gray-500 max-w-md line-clamp-1">
                                        {{ item.description }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span v-if="item.category" class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ item.category }}
                                    </span>
                                    <span v-else class="text-gray-400 text-xs">-</span>
                                </td>
                                <td class="px-5 py-4 text-center text-xs font-medium text-gray-600 whitespace-nowrap">
                                    {{ item.unit }}
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-gray-900 whitespace-nowrap">
                                    ₦{{ formatKobo(item.default_price_kobo, false) }}
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                        {{ item.usage_count }}x
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap w-28 min-w-[110px] space-x-2">
                                    <button
                                        type="button"
                                        @click="editItem(item)"
                                        class="px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteItem(item.id)"
                                        class="px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50 rounded"
                                    >
                                        Archive
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="items.links && items.links.length > 3" class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-gray-500">
                        Showing {{ items.from }} to {{ items.to }} of {{ items.total }} items
                    </div>
                    <div class="flex space-x-1 flex-wrap justify-center">
                        <Link
                            v-for="(link, i) in items.links"
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

        <!-- Create / Edit Item Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-3 sm:p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-4 sm:p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">
                        {{ editingId ? 'Edit Catalog Item' : 'New Catalog Item' }}
                    </h3>
                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Item Title *</label>
                        <input
                            type="text"
                            v-model="modalForm.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="e.g. 5kVA Solar Inverter"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Category</label>
                            <input
                                type="text"
                                v-model="modalForm.category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                placeholder="Inverters, Starlink..."
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Unit *</label>
                            <input
                                type="text"
                                v-model="modalForm.unit"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                placeholder="pcs, kit, roll, m"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Default Price (₦) *</label>
                        <input
                            type="number"
                            step="any"
                            min="0"
                            v-model.number="modalForm.price_naira"
                            @wheel="$event.target.blur()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-bold no-spinners [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            placeholder="0.00"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Description</label>
                        <textarea
                            rows="2"
                            v-model="modalForm.description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="Specifications or warranty note..."
                        ></textarea>
                    </div>

                    <div>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="modalForm.is_vatable"
                                class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4"
                            />
                            <span class="font-semibold text-gray-700">Taxable under VAT (7.5%)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="saveItem"
                        class="px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg shadow-sm"
                    >
                        Save Item
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvoiceNavSubBar from '@/Components/Invoice/InvoiceNavSubBar.vue';
import { useNaira } from '@/Composables/useNaira';

const props = defineProps<{
    items: any;
    categories: string[];
    filters: any;
}>();

const { formatKobo } = useNaira();

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const showModal = ref(false);
const editingId = ref<number | null>(null);

const modalForm = ref({
    name: '',
    category: '',
    unit: 'pcs',
    price_naira: 0,
    description: '',
    is_vatable: true,
});

function applyFilters() {
    router.get(
        route('invoices.catalog.index'),
        {
            search: search.value || undefined,
            category: category.value || undefined,
        },
        { preserveState: true }
    );
}

function openCreateModal() {
    editingId.value = null;
    modalForm.value = {
        name: '',
        category: '',
        unit: 'pcs',
        price_naira: 0,
        description: '',
        is_vatable: true,
    };
    showModal.value = true;
}

function editItem(item: any) {
    editingId.value = item.id;
    modalForm.value = {
        name: item.name,
        category: item.category || '',
        unit: item.unit || 'pcs',
        price_naira: (item.default_price_kobo || 0) / 100,
        description: item.description || '',
        is_vatable: item.is_vatable !== false,
    };
    showModal.value = true;
}

function saveItem() {
    if (!modalForm.value.name.trim()) {
        alert('Please enter an item name.');
        return;
    }

    if (editingId.value) {
        router.put(route('invoices.catalog.update', editingId.value), modalForm.value, {
            onSuccess: () => {
                showModal.value = false;
            },
        });
    } else {
        router.post(route('invoices.catalog.store'), modalForm.value, {
            onSuccess: () => {
                showModal.value = false;
            },
        });
    }
}

function deleteItem(id: number) {
    if (confirm('Are you sure you want to archive this catalog item?')) {
        router.delete(route('invoices.catalog.destroy', id));
    }
}
</script>
