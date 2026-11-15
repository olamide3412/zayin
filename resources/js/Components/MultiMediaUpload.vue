<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    max: {
        type: Number,
        default: 10,
    },
});

const emit = defineEmits(['update:modelValue']);

const uploading = ref(false);
const error = ref('');
const fileInput = ref(null);

const triggerUpload = () => {
    fileInput.value?.click();
};

const handleFiles = async (e) => {
    const files = Array.from(e.target.files || []);
    if (!files.length) return;

    error.value = '';
    const currentList = [...(props.modelValue || [])];

    if (currentList.length >= props.max) {
        error.value = `You have already reached the maximum limit of ${props.max} items.`;
        if (fileInput.value) fileInput.value.value = '';
        return;
    }

    const availableSlots = props.max - currentList.length;
    const filesToUpload = files.slice(0, availableSlots);

    if (files.length > availableSlots) {
        error.value = `Only ${availableSlots} more item(s) could be uploaded (maximum limit is ${props.max}).`;
    }

    uploading.value = true;

    for (const file of filesToUpload) {
        if (file.size > 20 * 1024 * 1024) {
            error.value = `"${file.name}" exceeds 20MB limit.`;
            continue;
        }

        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await axios.post(route('admin.upload-media'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            if (res.data?.url) {
                currentList.push(res.data.url);
            }
        } catch (err) {
            error.value = err?.response?.data?.message || `Failed to upload ${file.name}`;
        }
    }

    emit('update:modelValue', currentList);
    uploading.value = false;

    // Reset input
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const removeMedia = (index) => {
    const updated = [...(props.modelValue || [])];
    updated.splice(index, 1);
    emit('update:modelValue', updated);
};
</script>

<template>
    <div class="space-y-3">
        <!-- Hidden file input allowing multiple selections (images & short clips) -->
        <input 
            ref="fileInput"
            type="file" 
            multiple 
            accept="image/*,video/mp4,video/webm"
            @change="handleFiles" 
            class="hidden" 
        />

        <!-- Media Grid Preview -->
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
            <div 
                v-for="(item, idx) in modelValue" 
                :key="idx" 
                class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-slate-900 shadow-2xs"
            >
                <video 
                    v-if="item.endsWith('.mp4') || item.endsWith('.webm')" 
                    :src="item" 
                    class="w-full h-full object-cover" 
                    muted 
                    playsinline
                ></video>
                <img 
                    v-else 
                    :src="item" 
                    alt="Gallery item" 
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300" 
                />

                <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <button 
                        type="button" 
                        @click="removeMedia(idx)" 
                        class="p-1.5 bg-rose-600 text-white rounded-lg text-xs font-bold hover:bg-rose-700 shadow"
                        title="Remove photo"
                    >
                        ✕ Remove
                    </button>
                </div>
            </div>

            <!-- Upload More Trigger Card (shown when under max limit) -->
            <button 
                v-if="modelValue.length < max"
                type="button"
                @click="triggerUpload"
                :disabled="uploading"
                class="aspect-square rounded-xl border-2 border-dashed border-gray-300 hover:border-primary hover:bg-primary/5 transition flex flex-col items-center justify-center text-gray-500 hover:text-primary p-3 text-center disabled:opacity-50"
            >
                <svg v-if="!uploading" class="w-6 h-6 mb-1 text-gray-400 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <svg v-else class="animate-spin w-5 h-5 mb-1 text-primary" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-bold">
                    {{ uploading ? 'Uploading...' : '+ Add Media' }}
                </span>
                <span class="text-[10px] text-gray-400 mt-0.5">{{ max - modelValue.length }} slot(s) left</span>
            </button>
        </div>

        <p v-if="modelValue.length >= max" class="text-xs text-amber-600 font-semibold mt-1">
            Maximum gallery limit reached (10/10). Remove an existing item to upload a new one.
        </p>
        <p v-if="error" class="text-xs text-rose-500 font-bold mt-1">{{ error }}</p>
    </div>
</template>

