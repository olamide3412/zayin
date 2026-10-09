<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, default: 'Cover Image' },
});
const emit = defineEmits(['update:modelValue']);

const preview = ref(props.modelValue || '');
const uploading = ref(false);
const error = ref('');

watch(() => props.modelValue, (newVal) => {
    preview.value = newVal || '';
});

const onFileChange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        error.value = 'Image must be less than 5MB.';
        return;
    }

    const form = new FormData();
    form.append('image', file);

    uploading.value = true;
    error.value = '';

    try {
        const res = await axios.post(route('admin.upload-image'), form, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        preview.value = res.data.url;
        emit('update:modelValue', res.data.url);
    } catch (err) {
        error.value = err?.response?.data?.message || 'Upload failed. Please try again.';
    } finally {
        uploading.value = false;
    }
};

const removeImage = () => {
    preview.value = '';
    emit('update:modelValue', '');
};
</script>

<template>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">{{ label }}</label>

        <!-- Preview -->
        <div v-if="preview" class="relative mb-3 rounded-xl overflow-hidden border border-gray-200">
            <img :src="preview" alt="Cover image preview" class="w-full h-48 object-cover" />
            <button type="button" @click="removeImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors shadow-md" title="Remove image">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Upload Zone -->
        <label v-if="!preview" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-primary transition-colors bg-gray-50 group">
            <div v-if="!uploading" class="flex flex-col items-center gap-2 text-gray-500 group-hover:text-primary">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium">Click to upload image</span>
                <span class="text-xs">PNG, JPG, WebP up to 5MB</span>
            </div>
            <div v-else class="flex flex-col items-center gap-2 text-primary">
                <svg class="w-8 h-8 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span class="text-sm">Uploading...</span>
            </div>
            <input type="file" class="hidden" accept="image/*" @change="onFileChange" :disabled="uploading" />
        </label>

        <p v-if="error" class="text-red-500 text-xs mt-1">{{ error }}</p>
    </div>
</template>
