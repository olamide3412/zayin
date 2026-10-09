<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import FlashMessages from '@/Components/FlashMessages.vue'

const isSidebarOpen = ref(true)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}
</script>

<template>
    <div class="flex h-screen bg-gray-100 font-sans">
        <FlashMessages />
        
        <!-- Sidebar -->
        <aside 
            :class="[
                'bg-white text-gray-800 shadow-xl transition-all duration-300 ease-in-out z-20 flex flex-col',
                isSidebarOpen ? 'w-64' : 'w-20'
            ]"
        >
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 border-b border-gray-100 px-4">
                <div class="flex items-center" v-if="isSidebarOpen">
                    <span class="text-lg font-bold text-primary truncate">Administration</span>
                </div>
                <div v-else class="mx-auto">
                    <span class="text-xl font-bold text-primary">A</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-2">
                    
                    <!-- Dashboard Link -->
                    <li>
                        <Link 
                            :href="route('dashboard')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url === '/dashboard', 'hover:bg-gray-100': $page.url !== '/dashboard'}"
                            title="Dashboard"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Dashboard</span>
                        </Link>
                    </li>

                    <!-- Products & Packages (Live Pricing) -->
                    <li>
                        <Link 
                            :href="route('admin.products.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/products'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/products')}"
                            title="Products & Packages"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Products & Pricing</span>
                        </Link>
                    </li>

                    <!-- Maintenance Plans & Pricing -->
                    <li>
                        <Link 
                            :href="route('admin.plans.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/maintenance-plans'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/maintenance-plans')}"
                            title="Maintenance Plans"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Maintenance Plans</span>
                        </Link>
                    </li>

                    <!-- Delivery & Installation Fees -->
                    <li>
                        <Link 
                            :href="route('admin.state-fees.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/delivery-fees'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/delivery-fees')}"
                            title="Delivery & Install Fees"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Delivery & Install Fees</span>
                        </Link>
                    </li>

                    <!-- Solar Calculator Engine Config -->
                    <li>
                        <Link 
                            :href="route('admin.solar-settings.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/solar-calculator-settings'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/solar-calculator-settings')}"
                            title="Solar Calculator Engine"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Solar Calc Config</span>
                        </Link>
                    </li>


                    <!-- Blog Link -->
                    <li>
                        <Link 
                            :href="route('admin.blog.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/blog'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/blog')}"
                            title="Blog"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Blog</span>
                        </Link>
                    </li>

                    <!-- Testimonials Link -->
                    <li>
                        <Link 
                            :href="route('admin.testimonials.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/testimonials'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/testimonials')}"
                            title="Testimonials"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Testimonials</span>
                        </Link>
                    </li>

                    <!-- News & Media Link -->
                    <li>
                        <Link 
                            :href="route('admin.news.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/news'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/news')}"
                            title="News & Media"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">News & Media</span>
                        </Link>
                    </li>

                    <!-- Partners Link -->
                    <li>
                        <Link 
                            :href="route('admin.partners.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/partners'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/partners')}"
                            title="Technology & Brand Partners"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">OEM & Tech Partners</span>
                        </Link>
                    </li>

                    <!-- Settings Link -->
                    <li>
                        <Link 
                            :href="route('admin.settings.index')" 
                            class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors"
                            :class="{'bg-primary text-white': $page.url.startsWith('/admin/settings'), 'hover:bg-gray-100': !$page.url.startsWith('/admin/settings')}"
                            title="Settings"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="ml-3 font-medium transition-opacity duration-300" v-show="isSidebarOpen">Settings</span>
                        </Link>
                    </li>

                </ul>
            </div>

            <!-- Profile & Logout (Bottom pinned) -->
            <div class="border-t border-gray-100 p-4">
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    class="flex items-center w-full px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Logout"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="ml-3 font-medium transition-opacity duration-300 text-left" v-show="isSidebarOpen">Logout</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Topbar Content Header -->
            <header class="h-16 bg-white shadow-sm flex items-center px-4 md:px-6 justify-between border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <button 
                        @click="toggleSidebar" 
                        class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </button>
                    <Link href="/" class="hidden sm:inline-flex items-center text-xs font-bold text-slate-500 hover:text-primary transition-colors gap-1">
                        <span>← Back to Website</span>
                    </Link>
                </div>

                <!-- Right Controls: Currently logged in User display -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <Link :href="route('admin.profile.edit')" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                        <span class="text-xs sm:text-sm font-semibold text-gray-700 hidden xs:inline">{{ $page.props.auth?.user?.name || $page.props.auth?.user?.email }}</span>
                        <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center text-white font-bold cursor-pointer text-xs">
                            {{ ($page.props.auth?.user?.name || $page.props.auth?.user?.email || 'A')[0].toUpperCase() }}
                        </div>
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-6">
                <slot />
            </main>
        </div>
        
    </div>
</template>
