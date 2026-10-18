<template>
    <div class="relative w-full" ref="containerRef">
        <input
            type="text"
            :value="modelValue"
            @input="onInput"
            @focus="onFocus"
            @keydown.down.prevent="navigate(1)"
            @keydown.up.prevent="navigate(-1)"
            @keydown.enter.prevent="selectCurrent"
            @keydown.esc="isOpen = false"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary placeholder-gray-400"
            :placeholder="placeholder || 'Type or search catalog items...'"
            autocomplete="off"
        />

        <div
            v-if="isOpen && suggestions.length > 0"
            class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg py-1 divide-y divide-gray-100"
        >
            <div
                v-for="(item, index) in suggestions"
                :key="item.id || index"
                @mousedown.prevent="selectItem(item)"
                @mouseenter="highlightedIndex = index"
                :class="[
                    'px-3 py-2 cursor-pointer transition-colors text-left',
                    highlightedIndex === index ? 'bg-primary/10 text-primary' : 'hover:bg-gray-50 text-gray-900'
                ]"
            >
                <div class="flex items-center justify-between">
                    <span class="font-medium text-sm">{{ item.name }}</span>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                        ₦{{ item.unit_price_naira }}
                    </span>
                </div>
                <div v-if="item.description" class="text-xs text-gray-500 truncate mt-0.5">
                    {{ item.description }}
                </div>
                <div class="flex items-center space-x-2 text-[10px] text-gray-400 mt-0.5">
                    <span v-if="item.category" class="bg-gray-100 text-gray-600 px-1.5 py-0.2 rounded">{{ item.category }}</span>
                    <span>Unit: {{ item.unit }}</span>
                    <span v-if="item.usage_count > 0" class="text-blue-500">Used {{ item.usage_count }}x</span>
                </div>
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
const suggestions = ref<any[]>([]);
const highlightedIndex = ref(-1);
let debounceTimeout: any = null;

function onInput(e: Event) {
    const value = (e.target as HTMLInputElement).value;
    emit('update:modelValue', value);

    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        fetchSuggestions(value);
    }, 250);
}

function onFocus() {
    if (props.modelValue && props.modelValue.trim().length > 0) {
        fetchSuggestions(props.modelValue);
    } else {
        // Fetch top recent items
        fetchSuggestions('');
    }
}

async function fetchSuggestions(query: string) {
    try {
        const response = await fetch(`/invoice-items/suggest?q=${encodeURIComponent(query)}&limit=8`);
        if (response.ok) {
            const data = await response.json();
            suggestions.value = data;
            isOpen.value = data.length > 0;
            highlightedIndex.value = -1;
        }
    } catch (err) {
        console.error('Failed to fetch invoice item suggestions:', err);
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
