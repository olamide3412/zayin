<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { generateWhatappLink } from '@/Utils/whatsappLinkGen';

const page = usePage();

const phone = computed(() => {
    return page.props.site?.phone || page.props.settings?.phone || '+2348030000000';
});

const whatsappPhone = computed(() => {
    const raw = page.props.site?.whatsapp || page.props.settings?.whatsapp || '2348030000000';
    return String(raw).replace(/[^0-9]/g, '');
});

const whatsappUrl = computed(() => {
    return generateWhatappLink(whatsappPhone.value, 'Hello Zayin, I would like to get a quote/enquire about your Solar, Starlink or DSTV services.');
});
</script>

<template>
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 py-2 shadow-2xl flex items-center justify-between gap-2">
        <!-- Call Button -->
        <a 
            :href="`tel:${phone}`"
            class="flex-1 flex flex-col items-center justify-center py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition"
            aria-label="Call Zayin"
        >
            <font-awesome-icon :icon="['fas', 'phone']" class="text-slate-700 text-sm mb-0.5" />
            <span>Call</span>
        </a>

        <!-- WhatsApp Button -->
        <a 
            :href="whatsappUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="flex-1 flex flex-col items-center justify-center py-1.5 px-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-sm"
            aria-label="Chat on WhatsApp"
        >
            <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-sm mb-0.5" />
            <span>WhatsApp</span>
        </a>

        <!-- Request Quote -->
        <Link 
            href="/request-quote"
            class="flex-1 flex flex-col items-center justify-center py-1.5 px-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition shadow-sm"
        >
            <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" class="text-sm mb-0.5" />
            <span>Get Quote</span>
        </Link>
    </div>
</template>
