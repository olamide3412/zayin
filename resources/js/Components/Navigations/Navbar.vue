<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { generateWhatappLink } from '@/Utils/whatsappLinkGen';

const page = usePage();

const isOpen = ref(false);
const toggle = () => (isOpen.value = !isOpen.value);
const closeMenu = () => {
    isOpen.value = false;
    mobileServicesOpen.value = false;
    mobilePackagesOpen.value = false;
    mobileCompanyOpen.value = false;
};

watch(isOpen, (open) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = open ? 'hidden' : '';
    }
});

const servicesOpen = ref(false);
const packagesOpen = ref(false);
const companyOpen = ref(false);

const mobileServicesOpen = ref(false);
const mobilePackagesOpen = ref(false);
const mobileCompanyOpen = ref(false);

const servicesRef = ref(null);
const packagesRef = ref(null);
const companyRef = ref(null);

const isSticky = ref(false);
const handleScroll = () => {
    isSticky.value = window.scrollY > 20;
};

const handleClickOutside = (e) => {
    if (servicesRef.value && !servicesRef.value.contains(e.target)) {
        servicesOpen.value = false;
    }
    if (packagesRef.value && !packagesRef.value.contains(e.target)) {
        packagesOpen.value = false;
    }
    if (companyRef.value && !companyRef.value.contains(e.target)) {
        companyOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', handleScroll);
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});

const site = computed(() => page.props.site || {
    name: 'Zayin Technologies',
    phone: '+234 803 123 4567',
    whatsapp: '2348031234567',
    email: 'support@zayin.ng',
    address: 'Abraka, Ethiope East, Delta State, Nigeria',
});

const cartCount = computed(() => page.props.cart?.count || 0);

const whatsappLink = computed(() => {
    const raw = site.value.whatsapp || '2348031234567';
    const clean = String(raw).replace(/[^0-9]/g, '');
    return generateWhatappLink(clean, 'Hello Zayin Technologies, I would like to make an enquiry.');
});

const servicesList = [
    {
        title: 'Solar & Inverter Installation',
        slug: 'solar-installation',
        desc: 'Hybrid solar systems, lithium batteries, and 24/7 power backup.',
        icon: 'sun',
    },
    {
        title: 'Solar Maintenance & Audit',
        slug: 'solar-maintenance',
        desc: 'Panel cleaning, battery diagnostics, and inverter health checks.',
        icon: 'wrench',
    },
    {
        title: 'Starlink & Enterprise Wi-Fi',
        slug: 'starlink-installation',
        desc: 'Zero-obstruction mast mounting and compound mesh Wi-Fi.',
        icon: 'satellite-dish',
    },
    {
        title: 'DSTV Installation & Extra View',
        slug: 'dstv-installation',
        desc: 'Smart LNB alignment, multi-room setups, and signal repairs.',
        icon: 'tv',
    },
];

const packagesList = [
    {
        title: 'Solar Turnkey Packages',
        slug: 'solar',
        desc: 'Complete 1.5kVA, 3.5kVA, 5kVA & 10kVA engineered systems.',
        icon: 'solar-panel',
    },
    {
        title: 'Starlink Kits & Mounts',
        slug: 'starlink',
        desc: 'Standard kits, heavy-duty steel pipe adapters, and masts.',
        icon: 'satellite-dish',
    },
    {
        title: 'DSTV Decoders & Dishes',
        slug: 'dstv',
        desc: 'HD decoders, Explora Ultra 4K, and dish accessories.',
        icon: 'tv',
    },
    {
        title: 'Store & All Accessories',
        slug: '',
        desc: 'Lithium battery banks, pure sine inverters, and parts.',
        icon: 'box',
    },
];

const companyList = [
    {
        title: 'About Zayin Technologies',
        href: '/about',
        desc: 'Our engineering heritage, certified installers, and mission.',
        icon: 'circle-info',
    },
    {
        title: 'Solar Maintenance Plans',
        href: '/maintenance-plans',
        desc: 'Preventive bi-monthly panel wash and electrical audits.',
        icon: 'shield-halved',
    },
    {
        title: 'Customer Reviews',
        href: '/testimonials',
        desc: 'Verified homeowner and corporate client feedback.',
        icon: 'comments',
    },
    {
        title: 'Knowledge & FAQs',
        href: '/faq',
        desc: 'Answers to solar, Starlink, and installation questions.',
        icon: 'circle-question',
    },
    {
        title: 'News & Deployments',
        href: '/blog',
        desc: 'Field project updates and company bulletins.',
        icon: 'book-open',
    },
];
</script>

<template>
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <!-- Top Contact & Utility Bar (Desktop) -->
        <div class="hidden lg:block bg-slate-950 text-slate-300 text-xs py-2 px-4 sm:px-6 lg:px-8 border-b border-slate-800/80">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Left: Quick Direct Contact -->
                <div class="flex items-center space-x-5 whitespace-nowrap">
                    <a 
                        :href="`tel:${site.phone}`" 
                        class="inline-flex items-center space-x-1.5 hover:text-amber-400 transition-colors"
                    >
                        <font-awesome-icon :icon="['fas', 'phone']" class="text-amber-400 text-[11px]" />
                        <span class="font-medium tracking-wide">{{ site.phone }}</span>
                    </a>

                    <span class="text-slate-700">|</span>

                    <a 
                        :href="whatsappLink" 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        class="inline-flex items-center space-x-1.5 text-emerald-400 hover:text-emerald-300 transition-colors"
                    >
                        <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-sm" />
                        <span class="font-medium">WhatsApp Support</span>
                    </a>

                    <span class="text-slate-700">|</span>

                    <span class="inline-flex items-center space-x-1.5 text-slate-400">
                        <font-awesome-icon :icon="['fas', 'clock']" class="text-slate-500 text-[11px]" />
                        <span>Mon – Sat: 8am – 6pm</span>
                    </span>
                </div>

                <!-- Right: Location & Quick Utility Links -->
                <div class="flex items-center space-x-5 text-slate-400 whitespace-nowrap">
                    <span class="inline-flex items-center space-x-1.5">
                        <font-awesome-icon :icon="['fas', 'location-dot']" class="text-amber-400 text-[11px]" />
                        <span>{{ site.address }}</span>
                        <span class="text-[10px] bg-slate-800 text-amber-300 font-bold px-1.5 py-0.5 rounded ml-1">Nationwide</span>
                    </span>

                    <span class="text-slate-700">|</span>

                    <Link href="/about" class="hover:text-amber-400 transition-colors">Why Zayin</Link>
                    <Link href="/faq" class="hover:text-amber-400 transition-colors">FAQs</Link>
                </div>
            </div>
        </div>

        <!-- Main Navigation Bar -->
        <nav 
            :class="[
                'w-full transition-all duration-300 bg-white/95 backdrop-blur-md border-b',
                isSticky ? 'shadow-md py-2.5 border-slate-200' : 'py-3.5 border-slate-100'
            ]"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                <!-- Brand Logo (Shrink-0 to avoid crushing) -->
                <Link href="/" class="flex items-center space-x-2.5 group shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-900 to-amber-500 flex items-center justify-center text-white shadow-md shadow-amber-500/10 group-hover:scale-105 transition-transform duration-300 shrink-0">
                        <font-awesome-icon :icon="['fas', 'bolt']" class="text-lg text-amber-300" />
                    </div>
                    <div class="leading-tight">
                        <span class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 flex items-center">
                            ZAYIN<span class="text-amber-500 ml-1">TECH</span>
                        </span>
                        <p class="text-[9px] text-slate-500 tracking-wider font-bold uppercase hidden sm:block">
                            Solar · Starlink · DSTV
                        </p>
                    </div>
                </Link>

                <!-- Desktop Menu Links (Clean, Uncluttered, Single-line) -->
                <div class="hidden lg:flex items-center space-x-1 xl:space-x-2 text-sm font-semibold text-slate-700">
                    <Link 
                        href="/" 
                        class="px-3 py-2 rounded-xl hover:text-amber-600 hover:bg-slate-50 transition-colors whitespace-nowrap"
                    >
                        Home
                    </Link>

                    <!-- Services Dropdown -->
                    <div 
                        ref="servicesRef" 
                        class="relative"
                        @mouseenter="servicesOpen = true"
                        @mouseleave="servicesOpen = false"
                    >
                        <button 
                            @click="servicesOpen = !servicesOpen; packagesOpen = false; companyOpen = false"
                            class="flex items-center space-x-1 px-3 py-2 rounded-xl hover:text-amber-600 hover:bg-slate-50 transition-colors whitespace-nowrap"
                            :class="{ 'text-amber-600 bg-slate-50': servicesOpen }"
                            aria-haspopup="true"
                            :aria-expanded="servicesOpen"
                        >
                            <span>Services</span>
                            <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-[10px] transition-transform duration-200" :class="{ 'rotate-180': servicesOpen }" />
                        </button>

                        <div 
                            v-show="servicesOpen"
                            class="absolute left-0 top-full pt-2 w-80 z-50"
                        >
                            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-2 animate-in fade-in slide-in-from-top-1 duration-150">
                                <div class="px-3 py-1.5 border-b border-slate-100 mb-1">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Engineering Solutions</span>
                                </div>

                                <Link 
                                    v-for="service in servicesList" 
                                    :key="service.slug"
                                    :href="`/services/${service.slug}`"
                                    @click="servicesOpen = false"
                                    class="flex items-start p-2.5 rounded-xl hover:bg-amber-50/70 transition-colors group"
                                >
                                    <div class="mt-0.5 mr-3 w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                        <font-awesome-icon :icon="['fas', service.icon]" class="text-xs" />
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-amber-600 text-xs transition-colors">
                                            {{ service.title }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                            {{ service.desc }}
                                        </div>
                                    </div>
                                </Link>

                                <div class="px-2 pt-2 border-t border-slate-100 mt-1">
                                    <Link 
                                        href="/services" 
                                        @click="servicesOpen = false" 
                                        class="px-2 py-1 text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center justify-between rounded-lg hover:bg-amber-50/50"
                                    >
                                        <span>View All Services</span>
                                        <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-[10px]" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Packages Dropdown -->
                    <div 
                        ref="packagesRef" 
                        class="relative"
                        @mouseenter="packagesOpen = true"
                        @mouseleave="packagesOpen = false"
                    >
                        <button 
                            @click="packagesOpen = !packagesOpen; servicesOpen = false; companyOpen = false"
                            class="flex items-center space-x-1 px-3 py-2 rounded-xl hover:text-amber-600 hover:bg-slate-50 transition-colors whitespace-nowrap"
                            :class="{ 'text-amber-600 bg-slate-50': packagesOpen }"
                            aria-haspopup="true"
                            :aria-expanded="packagesOpen"
                        >
                            <span>Packages</span>
                            <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-[10px] transition-transform duration-200" :class="{ 'rotate-180': packagesOpen }" />
                        </button>

                        <div 
                            v-show="packagesOpen"
                            class="absolute left-0 top-full pt-2 w-72 z-50"
                        >
                            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-2 animate-in fade-in slide-in-from-top-1 duration-150">
                                <Link 
                                    v-for="pkg in packagesList" 
                                    :key="pkg.title"
                                    :href="pkg.slug ? `/packages/${pkg.slug}` : '/packages'"
                                    @click="packagesOpen = false"
                                    class="flex items-start p-2.5 rounded-xl hover:bg-amber-50/70 transition-colors group"
                                >
                                    <div class="mt-0.5 mr-3 w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                        <font-awesome-icon :icon="['fas', pkg.icon || 'box']" class="text-xs" />
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-amber-600 text-xs transition-colors">
                                            {{ pkg.title }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                            {{ pkg.desc }}
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Solar Calculator (Sleek single-line badge tool) -->
                    <Link 
                        href="/solar-calculator" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-slate-700 hover:text-amber-600 hover:bg-amber-50/80 transition-colors font-semibold whitespace-nowrap group"
                    >
                        <font-awesome-icon :icon="['fas', 'calculator']" class="text-amber-500 text-xs group-hover:scale-110 transition-transform" />
                        <span>Solar Calculator</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 tracking-wider">TOOL</span>
                    </Link>

                    <!-- Projects -->
                    <Link 
                        href="/projects" 
                        class="px-3 py-2 rounded-xl hover:text-amber-600 hover:bg-slate-50 transition-colors whitespace-nowrap"
                    >
                        Projects
                    </Link>

                    <!-- Company Dropdown (Consolidates Maintenance, About, FAQs, Reviews, Blog) -->
                    <div 
                        ref="companyRef" 
                        class="relative"
                        @mouseenter="companyOpen = true"
                        @mouseleave="companyOpen = false"
                    >
                        <button 
                            @click="companyOpen = !companyOpen; servicesOpen = false; packagesOpen = false"
                            class="flex items-center space-x-1 px-3 py-2 rounded-xl hover:text-amber-600 hover:bg-slate-50 transition-colors whitespace-nowrap"
                            :class="{ 'text-amber-600 bg-slate-50': companyOpen }"
                            aria-haspopup="true"
                            :aria-expanded="companyOpen"
                        >
                            <span>Company</span>
                            <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-[10px] transition-transform duration-200" :class="{ 'rotate-180': companyOpen }" />
                        </button>

                        <div 
                            v-show="companyOpen"
                            class="absolute right-0 top-full pt-2 w-72 z-50"
                        >
                            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-2 animate-in fade-in slide-in-from-top-1 duration-150">
                                <Link 
                                    v-for="item in companyList" 
                                    :key="item.href"
                                    :href="item.href"
                                    @click="companyOpen = false"
                                    class="flex items-start p-2.5 rounded-xl hover:bg-slate-50 transition-colors group"
                                >
                                    <div class="mt-0.5 mr-3 w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                        <font-awesome-icon :icon="['fas', item.icon]" class="text-xs" />
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-amber-600 text-xs transition-colors">
                                            {{ item.title }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                            {{ item.desc }}
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Direct Link -->
                    <Link 
                        href="/contact" 
                        class="px-3 py-2 rounded-xl hover:text-amber-600 hover:bg-slate-50 transition-colors whitespace-nowrap"
                    >
                        Contact
                    </Link>
                </div>

                <!-- Desktop Actions: Cart + Book Visit (on wide screens) + Primary Quote Button -->
                <div class="hidden lg:flex items-center space-x-2.5 shrink-0">
                    <!-- Cart Trigger -->
                    <Link 
                        href="/cart"
                        class="relative p-2.5 text-slate-700 hover:text-amber-600 hover:bg-slate-100 rounded-xl transition-colors"
                        aria-label="View Shopping Cart"
                    >
                        <font-awesome-icon :icon="['fas', 'shopping-cart']" class="text-base" />
                        <span 
                            v-if="cartCount > 0"
                            class="absolute top-1 right-1 w-4 h-4 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] flex items-center justify-center ring-2 ring-white"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>

                    <!-- Book Visit (Shown gracefully on xl+ screens so 1024px-1279px remains breathable) -->
                    <Link 
                        href="/book-installation"
                        class="hidden xl:inline-flex items-center px-3.5 py-2 rounded-xl text-slate-700 hover:text-slate-950 font-bold text-xs border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-colors shadow-xs whitespace-nowrap"
                    >
                        <font-awesome-icon :icon="['fas', 'calendar-check']" class="mr-1.5 text-slate-500 text-xs" />
                        <span>Book Visit</span>
                    </Link>

                    <!-- Primary High-Impact CTA -->
                    <Link 
                        href="/request-quote"
                        class="inline-flex items-center space-x-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 hover:text-slate-950 font-extrabold text-xs shadow-md shadow-amber-500/25 hover:shadow-lg transition-all duration-200 transform active:scale-95 whitespace-nowrap"
                    >
                        <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" class="text-xs" />
                        <span>Get Free Quote</span>
                    </Link>
                </div>

                <!-- Mobile Header Right (Cart + Hamburger Toggle) -->
                <div class="flex items-center space-x-2 lg:hidden">
                    <Link 
                        href="/cart"
                        class="relative p-2 text-slate-700 rounded-xl hover:bg-slate-100 transition-colors"
                        aria-label="View Shopping Cart"
                    >
                        <font-awesome-icon :icon="['fas', 'shopping-cart']" class="text-lg" />
                        <span 
                            v-if="cartCount > 0"
                            class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full bg-amber-500 text-slate-950 font-bold text-[10px] flex items-center justify-center"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>

                    <button 
                        @click="toggle"
                        class="p-2.5 rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none transition-colors"
                        aria-label="Toggle navigation menu"
                    >
                        <font-awesome-icon :icon="['fas', isOpen ? 'xmark' : 'bars']" class="text-xl" />
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Drawer Backdrop Overlay -->
        <transition 
            enter-active-class="transition-opacity duration-200" 
            enter-from-class="opacity-0" 
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150" 
            leave-from-class="opacity-100" 
            leave-to-class="opacity-0"
        >
            <div 
                v-if="isOpen" 
                @click="closeMenu"
                class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs z-30 lg:hidden"
                aria-hidden="true"
            />
        </transition>

        <!-- Mobile Navigation Drawer -->
        <transition 
            enter-active-class="transition duration-200 ease-out" 
            enter-from-class="opacity-0 -translate-y-2" 
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in" 
            leave-from-class="opacity-100 translate-y-0" 
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div 
                v-show="isOpen"
                class="relative z-40 lg:hidden bg-white border-b border-slate-200 shadow-2xl max-h-[85vh] overflow-y-auto px-5 py-5 space-y-4"
            >
                <!-- Direct Nav Links -->
                <div class="space-y-1 font-semibold text-slate-800 text-sm">
                    <Link href="/" @click="closeMenu" class="block py-2.5 px-3 rounded-xl hover:bg-slate-50 transition-colors">
                        Home
                    </Link>
                    
                    <!-- Mobile Services Accordion -->
                    <div>
                        <button 
                            @click="mobileServicesOpen = !mobileServicesOpen"
                            class="w-full flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-slate-50 transition-colors"
                        >
                            <span>Services</span>
                            <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-xs transition-transform duration-200" :class="{ 'rotate-180': mobileServicesOpen }" />
                        </button>
                        <div v-show="mobileServicesOpen" class="pl-3 pr-2 py-1.5 space-y-1 bg-slate-50 rounded-xl my-1">
                            <Link 
                                v-for="service in servicesList" 
                                :key="service.slug"
                                :href="`/services/${service.slug}`"
                                @click="closeMenu"
                                class="block py-2 px-2 text-xs font-medium text-slate-700 hover:text-amber-600 rounded-lg hover:bg-white"
                            >
                                {{ service.title }}
                            </Link>
                            <Link href="/services" @click="closeMenu" class="block py-2 px-2 text-xs font-bold text-amber-600">
                                View All Services &rarr;
                            </Link>
                        </div>
                    </div>

                    <!-- Mobile Packages Accordion -->
                    <div>
                        <button 
                            @click="mobilePackagesOpen = !mobilePackagesOpen"
                            class="w-full flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-slate-50 transition-colors"
                        >
                            <span>Packages & Store</span>
                            <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-xs transition-transform duration-200" :class="{ 'rotate-180': mobilePackagesOpen }" />
                        </button>
                        <div v-show="mobilePackagesOpen" class="pl-3 pr-2 py-1.5 space-y-1 bg-slate-50 rounded-xl my-1">
                            <Link 
                                v-for="pkg in packagesList" 
                                :key="pkg.title"
                                :href="pkg.slug ? `/packages/${pkg.slug}` : '/packages'"
                                @click="closeMenu"
                                class="block py-2 px-2 text-xs font-medium text-slate-700 hover:text-amber-600 rounded-lg hover:bg-white"
                            >
                                {{ pkg.title }}
                            </Link>
                        </div>
                    </div>

                    <!-- Mobile Solar Calculator Highlight -->
                    <Link 
                        href="/solar-calculator" 
                        @click="closeMenu" 
                        class="flex items-center justify-between py-2.5 px-3 rounded-xl font-bold bg-amber-50 text-amber-900 border border-amber-200 my-1"
                    >
                        <span class="flex items-center gap-2">
                            <font-awesome-icon :icon="['fas', 'calculator']" class="text-amber-600" />
                            <span>Solar Load Calculator</span>
                        </span>
                        <span class="text-[10px] bg-amber-500 text-slate-950 font-extrabold px-1.5 py-0.5 rounded">FREE</span>
                    </Link>

                    <!-- Projects -->
                    <Link href="/projects" @click="closeMenu" class="block py-2.5 px-3 rounded-xl hover:bg-slate-50 transition-colors">
                        Projects & Video Proof
                    </Link>

                    <!-- Mobile Company Accordion -->
                    <div>
                        <button 
                            @click="mobileCompanyOpen = !mobileCompanyOpen"
                            class="w-full flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-slate-50 transition-colors"
                        >
                            <span>Company & Support</span>
                            <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-xs transition-transform duration-200" :class="{ 'rotate-180': mobileCompanyOpen }" />
                        </button>
                        <div v-show="mobileCompanyOpen" class="pl-3 pr-2 py-1.5 space-y-1 bg-slate-50 rounded-xl my-1">
                            <Link 
                                v-for="item in companyList" 
                                :key="item.href"
                                :href="item.href"
                                @click="closeMenu"
                                class="block py-2 px-2 text-xs font-medium text-slate-700 hover:text-amber-600 rounded-lg hover:bg-white"
                            >
                                {{ item.title }}
                            </Link>
                        </div>
                    </div>

                    <Link href="/contact" @click="closeMenu" class="block py-2.5 px-3 rounded-xl hover:bg-slate-50 transition-colors">
                        Contact Us
                    </Link>
                </div>

                <!-- Mobile Action CTA Buttons -->
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <Link 
                        href="/request-quote"
                        @click="closeMenu"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-extrabold text-center text-xs shadow-md flex items-center justify-center space-x-2"
                    >
                        <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" />
                        <span>Request Free Quote</span>
                    </Link>

                    <Link 
                        href="/book-installation"
                        @click="closeMenu"
                        class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-center text-xs flex items-center justify-center space-x-2"
                    >
                        <font-awesome-icon :icon="['fas', 'calendar-check']" />
                        <span>Book Site Visit / Installation</span>
                    </Link>

                    <div class="pt-3 flex items-center justify-around text-xs text-slate-600 border-t border-slate-100">
                        <a :href="`tel:${site.phone}`" class="flex items-center space-x-1.5 hover:text-slate-950">
                            <font-awesome-icon :icon="['fas', 'phone']" class="text-amber-500" />
                            <span>{{ site.phone }}</span>
                        </a>
                        <a :href="whatsappLink" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-1.5 text-emerald-600 font-semibold">
                            <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-sm" />
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </transition>
    </header>
</template>
