<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import HeroSection from '@/Components/Home/HeroSection.vue';
import BrandPartners from '@/Components/Home/BrandPartners.vue';
import ServicePillars from '@/Components/Home/ServicePillars.vue';
import FeaturedPackages from '@/Components/Home/FeaturedPackages.vue';
import ProjectShowcase from '@/Components/Home/ProjectShowcase.vue';
import ProcessSection from '@/Components/Home/ProcessSection.vue';
import WhyChooseUs from '@/Components/Home/WhyChooseUs.vue';
import Testimonials from '@/Components/Home/Testimonials.vue';
import CoverageAreas from '@/Components/Home/CoverageAreas.vue';
import CallToAction from '@/Components/Home/CallToAction.vue';

defineOptions({ layout: Layout });

defineProps({
    heroSlides: {
        type: Array,
        default: () => [],
    },
    categories: Array,
    featuredServices: Array,
    featuredProducts: Array,
    featuredProjects: Array,
    testimonials: Array,
    faqs: Array,
    keyStates: Array,
    partners: Array,
    seo: Object,
});
</script>

<template>
    <SeoHead :seo="seo" />

    <div>
        <!-- Hero with Background Slider & Featured Package Card -->
        <HeroSection :hero-slides="heroSlides" :featured-product="featuredProducts?.[0]" />

        <!-- Certified OEM Brands & Technology Partners -->
        <BrandPartners :partners="partners" />

        <!-- 3 Core Service Pillars: Solar, Starlink, DSTV -->
        <ServicePillars :services="featuredServices" />

        <!-- Featured Product Packages with Tabbed Filters -->
        <FeaturedPackages :products="featuredProducts" />

        <!-- Completed Work & Video Proof Showcase -->
        <ProjectShowcase :projects="featuredProjects" />

        <!-- 4-Step Process Section -->
        <ProcessSection />

        <!-- The Zayin Engineering Standard & Why Choose Us -->
        <WhyChooseUs />

        <!-- Verified Nigerian Client Testimonials -->
        <Testimonials :items="testimonials" />

        <!-- Nationwide Coverage & Logistics -->
        <CoverageAreas :keyStates="keyStates" />

        <!-- Final Call to Action -->
        <CallToAction />
    </div>
</template>
