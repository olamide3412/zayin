<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    label: {
        type: String,
        default: 'Media Gallery (Photos & Videos)'
    }
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const uploading = ref(false);
const uploadProgress = ref(0);

const triggerSelect = () => {
    fileInput.value.click();
};

const handleUpload = async (e) => {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    uploading.value = true;
    uploadProgress.value = 0;

    const currentGallery = [...props.modelValue];

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await axios.post(route('admin.upload-media'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (progressEvent) => {
                    const totalPercent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    // Share progress across files
                    uploadProgress.value = Math.round(((i * 100) + totalPercent) / files.length);
                }
            });

            currentGallery.push({
                url: res.data.url,
                type: res.data.type
            });

            emit('update:modelValue', currentGallery);
        } catch (err) {
            console.error('Failed to upload file', file.name, err);
            alert(`Failed to upload ${file.name}. Please ensure it is a valid photo or video under 50MB.`);
        }
    }

    uploading.value = false;
    uploadProgress.value = 0;
    if (fileInput.value) fileInput.value.value = '';
};

const removeMedia = (index) => {
    const currentGallery = [...props.modelValue];
    currentGallery.splice(index, 1);
    emit('update:modelValue', currentGallery);
};
</script>

<template>
    <div class="space-y-4">
        <label class="block text-sm font-semibold text-gray-700">{{ label }}</label>
        
        <!-- Upload Trigger Area -->
        <div 
            @click="triggerSelect" 
            class="border-2 border-dashed border-gray-200 hover:border-primary rounded-2xl p-6 text-center cursor-pointer transition-colors bg-gray-50 group"
        >
            <input 
                ref="fileInput" 
                type="file" 
                multiple 
                accept="image/*,video/*" 
                class="hidden" 
                @change="handleUpload" 
            />
            
            <div class="flex flex-col items-center justify-center gap-2">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700">Add Photos or Videos</span>
                <span class="text-xs text-gray-400">Drag & drop or click to choose files (Max 50MB per video)</span>
            </div>
        </div>

        <!-- Upload Progress -->
        <div v-if="uploading" class="space-y-1 bg-gray-100 p-4 rounded-xl">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>Uploading media files...</span>
                <span>{{ uploadProgress }}%</span>
            </div>
            <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-primary transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
            </div>
        </div>

        <!-- Media Grid Preview -->
        <div v-if="modelValue?.length" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div 
                v-for="(item, index) in modelValue" 
                :key="index" 
                class="relative aspect-video rounded-xl overflow-hidden bg-slate-100 border border-gray-100 group shadow-sm"
            >
                <!-- Render Image -->
                <img 
                    v-if="item.type === 'image'" 
                    :src="item.url" 
                    class="w-full h-full object-cover" 
                    alt="Gallery item"
                />
                
                <!-- Render Video Preview -->
                <div v-else class="w-full h-full relative flex items-center justify-center bg-slate-900">
                    <video :src="item.url" class="w-full h-full object-cover opacity-80" muted></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="w-10 h-10 rounded-full bg-black/60 text-white flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Overlay Delete Action -->
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-10">
                    <button 
                        type="button" 
                        @click="removeMedia(index)" 
                        class="w-8 h-8 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition-colors shadow-md"
                        title="Remove file"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
