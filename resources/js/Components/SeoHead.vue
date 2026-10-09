<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    seo: {
        type: Object,
        default: () => ({})
    }
});

const title = computed(() => props.seo?.title || 'Zayin Technologies | Solar, Starlink & DSTV Nigeria');
const description = computed(() => props.seo?.description || "Nigeria's trusted installer for engineered solar systems, Starlink satellite internet setups, and DSTV satellite TV.");
const canonical = computed(() => props.seo?.canonical || '');
const robots = computed(() => props.seo?.robots || 'index,follow');
const ogImage = computed(() => props.seo?.ogImage || '');
const ogType = computed(() => props.seo?.ogType || 'website');
const jsonLdString = computed(() => {
    if (!props.seo?.jsonLd || Object.keys(props.seo.jsonLd).length === 0) return null;
    return JSON.stringify(props.seo.jsonLd);
});
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta name="robots" :content="robots" />

        <link v-if="canonical" rel="canonical" :href="canonical" />
        <link rel="alternate" hreflang="en-NG" :href="canonical || 'https://zayin.ng'" />
        <link rel="alternate" hreflang="x-default" :href="canonical || 'https://zayin.ng'" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" :content="ogType" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:locale" content="en_NG" />
        <meta v-if="canonical" property="og:url" :content="canonical" />
        <meta v-if="ogImage" property="og:image" :content="ogImage" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta v-if="ogImage" name="twitter:image" :content="ogImage" />

        <!-- JSON-LD Structured Data -->
        <component 
            is="script" 
            v-if="jsonLdString"
            type="application/ld+json"
            v-text="jsonLdString"
        />
    </Head>
</template>
