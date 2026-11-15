<template>
    <Head title="Invoice Templates - Zayin Ltd" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">Invoice Templates</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Customize A5 layouts, brand accents, and invoice title headers.</p>
                </div>
            </div>
        </template>

        <InvoiceNavSubBar />

        <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="tmpl in templates"
                    :key="tmpl.id"
                    class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4 relative"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ tmpl.layout }}</span>
                        <span v-if="tmpl.is_default" class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary text-white">
                            Default Active
                        </span>
                    </div>

                    <h3 class="text-base font-black text-gray-900">{{ tmpl.name }}</h3>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Document Header Title</label>
                            <input
                                type="text"
                                v-model="tmpl.document_title"
                                class="w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-primary focus:border-primary uppercase"
                            />
                        </div>

                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Accent Color</label>
                            <div class="flex items-center space-x-2">
                                <input
                                    type="color"
                                    v-model="tmpl.accent_color"
                                    class="h-8 w-10 border border-gray-300 rounded cursor-pointer"
                                />
                                <input
                                    type="text"
                                    v-model="tmpl.accent_color"
                                    class="w-28 px-2 py-1 border border-gray-300 rounded font-mono uppercase"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Prefix</label>
                                <input
                                    type="text"
                                    v-model="tmpl.number_prefix"
                                    class="w-full px-2 py-1 border border-gray-300 rounded font-mono uppercase"
                                />
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Padding</label>
                                <input
                                    type="number"
                                    min="3"
                                    max="8"
                                    v-model.number="tmpl.number_padding"
                                    class="w-full px-2 py-1 border border-gray-300 rounded"
                                />
                            </div>
                        </div>

                        <div class="pt-2 space-y-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="tmpl.show_fields.tin"
                                    class="rounded border-gray-300 text-primary focus:ring-primary h-3.5 w-3.5"
                                />
                                <span class="text-gray-700">Display Company TIN</span>
                            </label>

                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="tmpl.show_watermark"
                                    class="rounded border-gray-300 text-primary focus:ring-primary h-3.5 w-3.5"
                                />
                                <span class="text-gray-700">Display VOID / PAID Watermark</span>
                            </label>

                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="tmpl.is_default"
                                    class="rounded border-gray-300 text-primary focus:ring-primary h-3.5 w-3.5"
                                />
                                <span class="text-gray-700 font-semibold">Make this the default template</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end">
                        <button
                            type="button"
                            @click="saveTemplate(tmpl)"
                            class="px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 shadow-sm"
                        >
                            Save Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvoiceNavSubBar from '@/Components/Invoice/InvoiceNavSubBar.vue';

const props = defineProps<{
    templates: any[];
}>();

props.templates.forEach((tmpl) => {
    if (!tmpl.show_fields || typeof tmpl.show_fields !== 'object') {
        tmpl.show_fields = {
            rc_number: true,
            tin: true,
            bank_accounts: true,
            amount_in_words: true,
            signature_line: true,
            notes: true,
            terms: true,
        };
    } else if (tmpl.show_fields.tin === undefined) {
        tmpl.show_fields.tin = true;
    }
});

function saveTemplate(tmpl: any) {
    router.put(route('invoices.settings.templates.update', tmpl.id), tmpl);
}
</script>
