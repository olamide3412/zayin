<script setup>
import { onMounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import AOS from 'aos';
import Navbar from '@/Components/Navigations/Navbar.vue';
import WhatsAppButton from '@/Components/WhatsAppButton.vue';
import Footer from '@/Components/Footer.vue';
import FlashMessages from '../Components/FlashMessages.vue';
import RouteLoader from '@/Components/Common/RouteLoader.vue';
import MobileStickyBar from '@/Components/MobileStickyBar.vue';

onMounted(() => {
    AOS.refresh();
    router.on('navigate', () => {
        nextTick(() => {
            setTimeout(() => {
                AOS.refreshHard();
            }, 50);
        });
    });
});
</script>

<template>
    <div class="bg-white font-sans text-slate-800 antialiased selection:bg-amber-400 selection:text-slate-950">
        <!-- Global Route Navigation Loader Overlay -->
        <RouteLoader />

        <!-- Unified header (top bar + nav) is inside Navbar component -->
        <Navbar />

        <!-- Top bar (desktop) + nav responsive height + mobile sticky bar padding -->
        <main class="pt-[68px] sm:pt-[72px] lg:pt-[108px] pb-16 lg:pb-0">
            <slot />
            <WhatsAppButton />
            <MobileStickyBar />
        </main>

        <Footer />
        <FlashMessages />
    </div>
</template>
