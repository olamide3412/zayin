<template>
    <div class="bg-gray-100 p-3 sm:p-4 rounded-xl border border-gray-200 flex flex-col items-center">
        <!-- Toolbar -->
        <div class="w-full flex items-center justify-between pb-3 mb-3 border-b border-gray-200 text-xs">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-white text-gray-700 border border-gray-300 shadow-2xs">
                    Strict A5 Portrait
                </span>
                <span class="text-gray-500 font-mono text-[11px] hidden sm:inline">148 × 210 mm</span>
            </div>

            <div class="flex items-center space-x-1.5 sm:space-x-2">
                <button
                    type="button"
                    @click="zoom = Math.max(0.4, Number((zoom - 0.05).toFixed(2)))"
                    class="p-1 text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded shadow-2xs hover:bg-gray-50 transition-colors"
                    title="Zoom Out"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                <span class="text-gray-700 font-bold w-12 text-center text-xs tabular-nums">{{ Math.round(zoom * 100) }}%</span>
                <button
                    type="button"
                    @click="zoom = Math.min(1.4, Number((zoom + 0.05).toFixed(2)))"
                    class="p-1 text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded shadow-2xs hover:bg-gray-50 transition-colors"
                    title="Zoom In"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
                <button
                    type="button"
                    @click="zoom = 1.0"
                    class="hidden sm:inline-flex px-1.5 py-1 text-[11px] font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded shadow-2xs"
                    title="Reset to 100% (1:1 actual size)"
                >
                    100%
                </button>
                <button
                    type="button"
                    @click="fitToWidth"
                    class="hidden sm:inline-flex px-1.5 py-1 text-[11px] font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-200 rounded shadow-2xs"
                    title="Auto-fit to available screen width"
                >
                    Fit
                </button>
                <button
                    type="button"
                    @click="printIframe"
                    class="ml-1 sm:ml-2 inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-gray-900 hover:bg-black rounded-lg shadow-sm transition-colors"
                    title="Print strictly A5"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print A5
                </button>
            </div>
        </div>

        <!-- Sheet container (True A5: 560px × 794px = 148mm × 210mm) -->
        <div class="overflow-x-auto max-w-full flex justify-center py-2 w-full">
            <div
                class="transition-all origin-top"
                :style="{
                    width: `${560 * zoom}px`,
                    height: `${sheetHeight * zoom}px`,
                }"
            >
                <div
                    class="shadow-2xl bg-white border border-gray-300 rounded-xs origin-top-left overflow-hidden"
                    :style="{
                        width: '560px',
                        minHeight: `${sheetHeight}px`,
                        transform: `scale(${zoom})`,
                        transformOrigin: 'top left',
                    }"
                >
                    <iframe
                        ref="iframeRef"
                        :src="src"
                        class="w-[560px] border-0 block bg-white"
                        :style="{ minHeight: `${sheetHeight}px`, height: `${sheetHeight}px` }"
                        title="Invoice Live Preview (A5)"
                        @load="onIframeLoad"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
    src?: string;
}>();

const calculateDefaultZoom = () => {
    if (typeof window === 'undefined') return 0.85;
    const w = window.innerWidth;
    if (w < 480) return 0.55;
    if (w < 768) return 0.70;
    if (w < 1280) return 0.78;
    if (w < 1536) return 0.85;
    return 0.95;
};

const zoom = ref(calculateDefaultZoom());
const sheetHeight = ref(794); // Standard A5 height in pixels (210mm)
const iframeRef = ref<HTMLIFrameElement | null>(null);

function fitToWidth() {
    zoom.value = calculateDefaultZoom();
}

function measureSheetHeight() {
    try {
        if (iframeRef.value?.contentDocument?.body) {
            const body = iframeRef.value.contentDocument.body;
            const scrollH = body.scrollHeight || body.offsetHeight;
            if (scrollH && scrollH > 794) {
                sheetHeight.value = scrollH;
            } else {
                sheetHeight.value = 794;
            }
        }
    } catch (e) {
        // Fallback for cross-origin or unloaded
    }
}

function onIframeLoad() {
    measureSheetHeight();
}

function printIframe() {
    if (props.src) {
        const printUrl = `${props.src}${props.src.includes('?') ? '&' : '?'}print=1`;
        const printWindow = window.open(printUrl, '_blank', 'width=650,height=850');
        if (printWindow) {
            printWindow.focus();
            return;
        }
    }

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
        setTimeout(measureSheetHeight, 50);
    }
}

defineExpose({
    updateContent,
    printIframe,
});
</script>
