<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    status: String,
})

const page = usePage()
const user = page.props.auth.user

// Form 1: Profile Information
const profileForm = useForm({
    name: user.name || '',
    email: user.email || '',
})

// Form 2: Password Update
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const updateProfile = () => {
    profileForm.put(route('admin.profile.update'), {
        preserveScroll: true,
    })
}

const updatePassword = () => {
    passwordForm.put(route('admin.profile.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Admin Profile Settings" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Profile Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your administrative profile details and update your password.</p>
        </div>

        <div class="max-w-4xl space-y-6">
            <!-- Profile Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile Information
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Update your account's profile name and email address.</p>
                </div>

                <form @submit.prevent="updateProfile" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label>
                            <input 
                                id="name"
                                v-model="profileForm.name" 
                                type="text" 
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-primary/40 focus:outline-none text-sm transition-all font-medium" 
                            />
                            <span v-if="profileForm.errors.name" class="text-red-500 text-xs mt-1 block">{{ profileForm.errors.name }}</span>
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                            <input 
                                id="email"
                                v-model="profileForm.email" 
                                type="email" 
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-primary/40 focus:outline-none text-sm transition-all font-medium" 
                            />
                            <span v-if="profileForm.errors.email" class="text-red-500 text-xs mt-1 block">{{ profileForm.errors.email }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button 
                            type="submit" 
                            :disabled="profileForm.processing"
                            class="bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-6 rounded-xl transition-all shadow disabled:opacity-60 flex items-center gap-2 text-sm"
                        >
                            <svg v-if="profileForm.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ profileForm.processing ? 'Saving...' : 'Save Profile' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Update Password
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Ensure your account is using a long, random password to stay secure.</p>
                </div>

                <form @submit.prevent="updatePassword" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password</label>
                            <input 
                                id="current_password"
                                v-model="passwordForm.current_password" 
                                type="password" 
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-primary/40 focus:outline-none text-sm transition-all" 
                            />
                            <span v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1 block">{{ passwordForm.errors.current_password }}</span>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                            <input 
                                id="password"
                                v-model="passwordForm.password" 
                                type="password" 
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-primary/40 focus:outline-none text-sm transition-all" 
                            />
                            <span v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1 block">{{ passwordForm.errors.password }}</span>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                            <input 
                                id="password_confirmation"
                                v-model="passwordForm.password_confirmation" 
                                type="password" 
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-primary/40 focus:outline-none text-sm transition-all" 
                            />
                            <span v-if="passwordForm.errors.password_confirmation" class="text-red-500 text-xs mt-1 block">{{ passwordForm.errors.password_confirmation }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button 
                            type="submit" 
                            :disabled="passwordForm.processing"
                            class="bg-primary hover:bg-primary/95 text-white font-semibold py-2.5 px-6 rounded-xl transition-all shadow disabled:opacity-60 flex items-center gap-2 text-sm"
                        >
                            <svg v-if="passwordForm.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
