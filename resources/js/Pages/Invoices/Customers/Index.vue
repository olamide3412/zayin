<template>
    <Head title="Invoice Customers - Zayin Global Limited" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">Customers Directory</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Manage customer profiles and billing contact details.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('invoices.index')"
                        class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm"
                    >
                        ← Back to Invoices
                    </Link>
                    <button
                        type="button"
                        @click="openCreateModal"
                        class="px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 shadow-sm flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Customer
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Search Bar -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div class="w-full md:w-96 relative">
                    <input
                        type="text"
                        v-model="search"
                        @keyup.enter="applySearch"
                        class="w-full pl-3 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                        placeholder="Search customer name, email, phone, TIN..."
                    />
                </div>
                <button
                    type="button"
                    @click="applySearch"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900"
                >
                    Search
                </button>
            </div>

            <!-- Customers Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase">
                            <tr>
                                <th class="px-5 py-3.5">Name</th>
                                <th class="px-5 py-3.5">Phone</th>
                                <th class="px-5 py-3.5">Email</th>
                                <th class="px-5 py-3.5">TIN</th>
                                <th class="px-5 py-3.5 text-center">Invoices</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="customers.data.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                    No customers found. Click "Add Customer" to add one.
                                </td>
                            </tr>
                            <tr
                                v-for="c in customers.data"
                                :key="c.id"
                                class="hover:bg-gray-50/75 transition-colors"
                            >
                                <td class="px-5 py-4 font-bold text-gray-900">
                                    {{ c.name }}
                                    <div v-if="c.address" class="text-xs font-normal text-gray-500 truncate max-w-xs">{{ c.address }}</div>
                                </td>
                                <td class="px-5 py-4 text-gray-700 text-xs">{{ c.phone || '-' }}</td>
                                <td class="px-5 py-4 text-gray-700 text-xs">{{ c.email || '-' }}</td>
                                <td class="px-5 py-4 text-gray-700 text-xs font-mono">{{ c.tin || '-' }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                        {{ c.invoices_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <button
                                        type="button"
                                        @click="editCustomer(c)"
                                        class="px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        v-if="c.invoices_count === 0"
                                        type="button"
                                        @click="deleteCustomer(c.id)"
                                        class="px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50 rounded"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create / Edit Customer Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">
                        {{ editingId ? 'Edit Customer' : 'Add New Customer' }}
                    </h3>
                    <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Name / Business *</label>
                        <input
                            type="text"
                            v-model="modalForm.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="Customer Name"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Phone</label>
                            <input
                                type="text"
                                v-model="modalForm.phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                placeholder="+234 803 000 0000"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Email</label>
                            <input
                                type="email"
                                v-model="modalForm.email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                                placeholder="email@example.com"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tax ID / TIN</label>
                        <input
                            type="text"
                            v-model="modalForm.tin"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="TIN-..."
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Address</label>
                        <textarea
                            rows="2"
                            v-model="modalForm.address"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="Physical address..."
                        ></textarea>
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
                        @click="saveCustomer"
                        class="px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg shadow-sm"
                    >
                        Save Customer
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

const props = defineProps<{
    customers: any;
    filters: any;
}>();

const search = ref(props.filters.search || '');
const showModal = ref(false);
const editingId = ref<number | null>(null);

const modalForm = ref({
    name: '',
    phone: '',
    email: '',
    tin: '',
    address: '',
});

function applySearch() {
    router.get(route('invoices.customers.index'), { search: search.value || undefined }, { preserveState: true });
}

function openCreateModal() {
    editingId.value = null;
    modalForm.value = { name: '', phone: '', email: '', tin: '', address: '' };
    showModal.value = true;
}

function editCustomer(c: any) {
    editingId.value = c.id;
    modalForm.value = {
        name: c.name,
        phone: c.phone || '',
        email: c.email || '',
        tin: c.tin || '',
        address: c.address || '',
    };
    showModal.value = true;
}

function saveCustomer() {
    if (!modalForm.value.name.trim()) {
        alert('Please enter a customer name.');
        return;
    }

    if (editingId.value) {
        router.put(route('invoices.customers.update', editingId.value), modalForm.value, {
            onSuccess: () => {
                showModal.value = false;
            },
        });
    } else {
        router.post(route('invoices.customers.store'), modalForm.value, {
            onSuccess: () => {
                showModal.value = false;
            },
        });
    }
}

function deleteCustomer(id: number) {
    if (confirm('Are you sure you want to delete this customer?')) {
        router.delete(route('invoices.customers.destroy', id));
    }
}
</script>
