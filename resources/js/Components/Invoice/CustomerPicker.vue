<template>
    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Customer / Client
            </h3>
            <button
                type="button"
                @click="toggleMode"
                class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors"
            >
                {{ isCreatingNew ? '← Choose Existing Client' : '+ New Customer' }}
            </button>
        </div>

        <!-- Mode A: Existing Customer Select -->
        <div v-if="!isCreatingNew" class="space-y-2.5">
            <div v-if="!selectedCustomer" ref="comboRef" class="relative">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                    <input
                        id="customer-search-input"
                        ref="inputRef"
                        type="text"
                        v-model="query"
                        @focus="openDropdown"
                        @keydown.down.prevent="moveHighlight(1)"
                        @keydown.up.prevent="moveHighlight(-1)"
                        @keydown.enter.prevent="selectHighlighted"
                        @keydown.esc="isOpen = false"
                        autocomplete="off"
                        class="w-full pl-9 pr-9 py-2 text-sm border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white"
                        placeholder="Search client by name, phone (e.g. 0803...) or email"
                    />
                    <svg
                        v-if="isLoading"
                        class="w-4 h-4 text-primary absolute right-3 top-1/2 -translate-y-1/2 animate-spin"
                        fill="none" viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                </div>

                <div
                    v-if="isOpen"
                    class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-72 overflow-y-auto"
                >
                    <div class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-50 border-b border-gray-100">
                        {{ query.trim() ? `Matches for "${query.trim()}"` : 'Recent clients' }}
                        <span v-if="results.length === 25" class="normal-case font-normal">· showing first 25, keep typing to narrow</span>
                    </div>

                    <button
                        v-for="(c, idx) in results"
                        :key="c.id"
                        type="button"
                        @mousedown.prevent="selectCustomer(c)"
                        @mouseenter="highlighted = idx"
                        class="w-full text-left px-3 py-2 text-xs flex items-center justify-between border-b border-gray-50 last:border-0"
                        :class="highlighted === idx ? 'bg-primary/10' : 'hover:bg-gray-50'"
                    >
                        <span class="font-semibold text-gray-900 truncate">{{ c.name }}</span>
                        <span class="text-gray-500 ml-3 flex-shrink-0">{{ c.phone || c.email || '' }}</span>
                    </button>

                    <div v-if="!isLoading && results.length === 0" class="px-3 py-3 text-xs text-gray-500 text-center">
                        No client found{{ query.trim() ? ` for "${query.trim()}"` : '' }}.
                    </div>

                    <button
                        type="button"
                        @mousedown.prevent="startNewFromQuery"
                        class="w-full text-left px-3 py-2 text-xs font-semibold text-primary bg-primary/5 hover:bg-primary/10 border-t border-gray-100"
                    >
                        + Add "{{ query.trim() || 'new client' }}" as a new customer
                    </button>
                </div>
            </div>

            <!-- Selected Customer Snapshot Card -->
            <div v-if="selectedCustomer" class="p-3 bg-gray-50 border border-gray-200 rounded-md text-xs flex items-start justify-between">
                <div class="space-y-0.5">
                    <div class="font-bold text-gray-900">{{ selectedCustomer.name }}</div>
                    <div v-if="selectedCustomer.phone || selectedCustomer.email" class="text-gray-600 flex flex-wrap gap-x-3">
                        <span v-if="selectedCustomer.phone">📞 {{ selectedCustomer.phone }}</span>
                        <span v-if="selectedCustomer.email">✉️ {{ selectedCustomer.email }}</span>
                    </div>
                    <div v-if="selectedCustomer.address" class="text-gray-500 text-[11px] truncate max-w-md">
                        📍 {{ selectedCustomer.address }}
                    </div>
                </div>
                <button
                    type="button"
                    @click="clearCustomer"
                    class="text-xs text-gray-400 hover:text-rose-600 ml-2"
                    title="Deselect customer"
                >
                    ✕
                </button>
            </div>
        </div>

        <!-- Mode B: Quick Create New Customer -->
        <div v-else class="space-y-2.5 p-3 bg-primary/5 border border-primary/20 rounded-md">
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-primary">New Customer Details</span>
                <button
                    type="button"
                    @click="showMoreFields = !showMoreFields"
                    class="text-[11px] text-gray-600 hover:text-primary underline"
                >
                    {{ showMoreFields ? '- Fewer fields' : '+ Add Address & Tax ID' }}
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-0.5">
                        Name / Company <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="newCustomer.name"
                        class="w-full px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary font-medium"
                        placeholder="e.g. Alhaji Musa or SkyNet Ltd"
                    />
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-0.5">Phone Number</label>
                    <input
                        type="text"
                        v-model="newCustomer.phone"
                        class="w-full px-3 py-1.5 text-sm bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                        placeholder="+234 800 000 0000"
                    />
                </div>
            </div>

            <div v-if="showMoreFields" class="space-y-2 pt-2 border-t border-primary/10 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-0.5">Email Address</label>
                    <input
                        type="email"
                        v-model="newCustomer.email"
                        class="w-full px-3 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                        placeholder="client@example.com"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-0.5">Physical Address</label>
                        <input
                            type="text"
                            v-model="newCustomer.address"
                            class="w-full px-3 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="Plot 10, Victoria Island, Lagos"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-0.5">Tax ID / TIN</label>
                        <input
                            type="text"
                            v-model="newCustomer.tin"
                            class="w-full px-3 py-1.5 text-xs bg-white border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                            placeholder="TIN-..."
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';

type CustomerLite = { id: number; name: string; phone?: string; email?: string; address?: string; tin?: string };

const props = defineProps<{
    customers: CustomerLite[]; // 25 most recent, used before the user types
    initialCustomer?: CustomerLite | null; // e.g. the invoice's current customer on Edit
    customerId: number | null;
    newCustomer: {
        name: string;
        email?: string;
        phone?: string;
        address?: string;
        tin?: string;
    };
}>();

const emit = defineEmits<{
    (e: 'update:customerId', val: number | null): void;
}>();

const isCreatingNew = ref(false);
const showMoreFields = ref(Boolean(props.newCustomer.address || props.newCustomer.tin));

const query = ref('');
const results = ref<CustomerLite[]>([...(props.customers || [])]);
const isOpen = ref(false);
const isLoading = ref(false);
const highlighted = ref(0);
const comboRef = ref<HTMLElement | null>(null);
const inputRef = ref<HTMLInputElement | null>(null);

// Keep the full record of whichever customer was picked, since it may not be in props.customers
const pickedCustomer = ref<CustomerLite | null>(props.initialCustomer || null);

const selectedCustomer = computed(() => {
    if (!props.customerId) return null;
    const id = Number(props.customerId);
    if (pickedCustomer.value?.id === id) return pickedCustomer.value;
    return props.customers.find((c) => c.id === id) || null;
});

let debounceTimer: ReturnType<typeof setTimeout> | null = null;
let requestSeq = 0;

async function fetchCustomers(term: string) {
    const seq = ++requestSeq;
    isLoading.value = true;
    try {
        const { data } = await axios.get(route('invoices.customers.search'), { params: { q: term } });
        if (seq !== requestSeq) return; // ignore out-of-order responses
        results.value = data;
        highlighted.value = 0;
    } catch {
        if (seq === requestSeq) results.value = [];
    } finally {
        if (seq === requestSeq) isLoading.value = false;
    }
}

watch(query, (val) => {
    isOpen.value = true;
    if (debounceTimer) clearTimeout(debounceTimer);
    const term = val.trim();
    if (term === '') {
        results.value = [...(props.customers || [])];
        highlighted.value = 0;
        requestSeq++; // cancel any in-flight search
        isLoading.value = false;
        return;
    }
    debounceTimer = setTimeout(() => fetchCustomers(term), 250);
});

function openDropdown() {
    isOpen.value = true;
}

function moveHighlight(step: number) {
    if (!isOpen.value) {
        isOpen.value = true;
        return;
    }
    const n = results.value.length;
    if (n === 0) return;
    highlighted.value = (highlighted.value + step + n) % n;
}

function selectHighlighted() {
    const c = results.value[highlighted.value];
    if (c) selectCustomer(c);
}

function selectCustomer(c: CustomerLite) {
    pickedCustomer.value = c;
    emit('update:customerId', c.id);
    isOpen.value = false;
    query.value = '';
}

function startNewFromQuery() {
    const term = query.value.trim();
    isOpen.value = false;
    isCreatingNew.value = true;
    emit('update:customerId', null);
    if (term) {
        // Typed digits go to phone, anything else to name
        if (/^[+\d\s-]{6,}$/.test(term)) props.newCustomer.phone = term;
        else props.newCustomer.name = term;
    }
    query.value = '';
}

function toggleMode() {
    isCreatingNew.value = !isCreatingNew.value;
    if (isCreatingNew.value) {
        emit('update:customerId', null);
    }
}

async function clearCustomer() {
    emit('update:customerId', null);
    pickedCustomer.value = null;
    await nextTick();
    inputRef.value?.focus();
}

function onClickOutside(e: MouseEvent) {
    if (comboRef.value && !comboRef.value.contains(e.target as Node)) {
        isOpen.value = false;
    }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside));
onBeforeUnmount(() => {
    document.removeEventListener('mousedown', onClickOutside);
    if (debounceTimer) clearTimeout(debounceTimer);
});
</script>
