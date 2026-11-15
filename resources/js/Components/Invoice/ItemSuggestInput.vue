<template>
    <div class="relative w-full" ref="containerRef">
        <div class="relative">
            <input
                type="text"
                :value="modelValue"
                @input="onInput"
                @focus="onFocus"
                @click="onFocus"
                @keydown.down.prevent="navigate(1)"
                @keydown.up.prevent="navigate(-1)"
                @keydown.enter.prevent="selectCurrent"
                @keydown.esc="isOpen = false"
                class="w-full px-3 py-2 pr-8 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary placeholder-gray-400"
                :placeholder="placeholder || 'Type or search catalog items...'"
                autocomplete="off"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-400">
                <svg v-if="isLoading" class="animate-spin h-3.5 w-3.5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
            </div>
        </div>

        <div
            v-if="isOpen && (suggestions.length > 0 || (modelValue && modelValue.trim().length > 1 && !isLoading))"
            class="absolute z-50 left-0 right-0 mt-1 max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-xl py-1 divide-y divide-gray-100"
        >
            <template v-if="suggestions.length > 0">
                <div class="px-2.5 py-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50 flex items-center justify-between">
                    <span>Catalog Matches</span>
                    <span class="font-normal text-[9px] lowercase text-gray-400">arrow keys or click to select</span>
                </div>
                <div
                    v-for="(item, index) in suggestions"
                    :key="item.id || index"
                    @mousedown.prevent="selectItem(item)"
                    @touchstart.prevent="selectItem(item)"
                    @mouseenter="highlightedIndex = index"
                    :class="[
                        'px-3 py-2 cursor-pointer transition-colors text-left',
                        highlightedIndex === index ? 'bg-primary/10 text-primary' : 'hover:bg-gray-50 text-gray-900'
                    ]"
                >
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-sm truncate mr-2">{{ item.name }}</span>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded shrink-0">
                            ₦{{ item.unit_price_naira }}
                        </span>
                    </div>
                    <div v-if="item.description" class="text-xs text-gray-500 truncate mt-0.5">
                        {{ item.description }}
                    </div>
                    <div class="flex items-center space-x-2 text-[10px] text-gray-400 mt-0.5">
                        <span v-if="item.category" class="bg-gray-100 text-gray-600 px-1.5 py-0.2 rounded">{{ item.category }}</span>
                        <span>Unit: {{ item.unit || 'pcs' }}</span>
                        <span v-if="item.usage_count > 0" class="text-blue-500 font-medium">Used {{ item.usage_count }}x</span>
                    </div>
                </div>
            </template>
            <div v-else-if="modelValue && modelValue.trim().length > 1 && !isLoading" class="px-3 py-2.5 text-xs text-gray-500 text-center">
                <div class="text-gray-700 font-medium">No matching catalog items</div>
                <div class="text-[11px] text-gray-400 mt-0.5">This custom item will be added to your catalog when saved.</div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'select', item: any): void;
}>();

const containerRef = ref<HTMLElement | null>(null);
const isOpen = ref(false);
const isLoading = ref(false);
const suggestions = ref<any[]>([]);
const highlightedIndex = ref(-1);
let debounceTimeout: any = null;

function onInput(e: Event) {
    const value = (e.target as HTMLInputElement).value;
    emit('update:modelValue', value);

    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchSuggestions(value);
    }, 200);
}

function onFocus() {
    isOpen.value = true;
    if (props.modelValue && props.modelValue.trim().length > 0) {
        fetchSuggestions(props.modelValue);
    } else {
        // Fetch top recent items
        fetchSuggestions('');
    }
}

async function fetchSuggestions(query: string) {
    isLoading.value = true;
    try {
        const response = await fetch(`/invoice-items/suggest?q=${encodeURIComponent(query)}&limit=8`);
        if (response.ok) {
            const data = await response.json();
            suggestions.value = data;
            isOpen.value = true;
            highlightedIndex.value = -1;
        }
    } catch (err) {
        console.error('Failed to fetch invoice item suggestions:', err);
    } finally {
        isLoading.value = false;
    }
}

function navigate(direction: number) {
    if (!isOpen.value || suggestions.value.length === 0) return;
    highlightedIndex.value = (highlightedIndex.value + direction + suggestions.value.length) % suggestions.value.length;
}

function selectCurrent() {
    if (isOpen.value && highlightedIndex.value >= 0 && highlightedIndex.value < suggestions.value.length) {
        selectItem(suggestions.value[highlightedIndex.value]);
    }
}

function selectItem(item: any) {
    emit('update:modelValue', item.name);
    emit('select', item);
    isOpen.value = false;
    suggestions.value = [];
}

function handleClickOutside(e: MouseEvent) {
    if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
        isOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    clearTimeout(debounceTimeout);
});
</script>
