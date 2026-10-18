<template>
    <div class="bg-gray-100 p-4 rounded-xl border border-gray-200 flex flex-col items-center">
        <!-- Toolbar -->
        <div class="w-full flex items-center justify-between pb-3 mb-3 border-b border-gray-200 text-xs">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-white text-gray-700 border border-gray-300">
                    A5 Portrait
                </span>
                <span class="text-gray-500">148 × 210 mm</span>
            </div>

            <div class="flex items-center space-x-2">
                <button
                    type="button"
                    @click="zoom = Math.max(0.4, Number((zoom - 0.1).toFixed(1)))"
                    class="p-1 text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded"
                    title="Zoom Out"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                <span class="text-gray-600 font-semibold w-10 text-center">{{ Math.round(zoom * 100) }}%</span>
                <button
                    type="button"
                    @click="zoom = Math.min(1.4, zoom + 0.1)"
                    class="p-1 text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded"
                    title="Zoom In"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="printIframe"
                    class="ml-2 inline-flex items-center px-2.5 py-1 text-xs font-medium text-white bg-gray-800 hover:bg-gray-900 rounded"
                    title="Print A5"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Sheet container -->
        <div class="overflow-auto max-w-full flex justify-center py-2">
            <div
                class="shadow-2xl bg-white border border-gray-300 transition-transform origin-top"
                :style="{
                    width: '420px',
                    minHeight: '595px',
                    transform: `scale(${zoom})`,
                    marginBottom: `${(zoom - 1) * 595}px`
                }"
            >
                <iframe
                    ref="iframeRef"
                    :src="src"
                    class="w-full h-[595px] border-0"
                    title="Invoice Live Preview"
                ></iframe>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
    src?: string;
}>();

const defaultZoom = typeof window !== 'undefined' && window.innerWidth < 480 ? 0.72 : (typeof window !== 'undefined' && window.innerWidth < 768 ? 0.85 : 1.0);
const zoom = ref(defaultZoom);
const iframeRef = ref<HTMLIFrameElement | null>(null);

function printIframe() {
    if (iframeRef.value?.contentWindow) {
        iframeRef.value.contentWindow.focus();
        iframeRef.value.contentWindow.print();
    }
}

function updateContent(html: string) {
    if (iframeRef.value?.contentDocument) {
        iframeRef.value.contentDocument.open();
        iframeRef.value.contentDocument.write(html);
        iframeRef.value.contentDocument.close();
    }
}

defineExpose({
    updateContent,
    printIframe,
});
</script>
