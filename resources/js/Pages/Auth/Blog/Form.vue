<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import ImageUpload from '@/Components/ImageUpload.vue';

const props = defineProps({
    post: Object,
});

const isEditing = !!props.post;

const form = useForm({
    title: props.post?.title || '',
    excerpt: props.post?.excerpt || '',
    body: props.post?.body || '',
    cover_image: props.post?.cover_image || '',
    category: props.post?.category || '',
    read_time: props.post?.read_time || 3,
    status: props.post?.status || 'draft',
});

const submit = () => {
    if (isEditing) {
        form.put(route('admin.blog.update', props.post.id));
    } else {
        form.post(route('admin.blog.store'));
    }
};

const categories = ['Eye Care Tips', 'Medical News', 'Patient Stories', 'Hospital Updates', 'Surgical Advances'];
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="isEditing ? 'Edit Blog Post' : 'Create Blog Post'" />

        <!-- Header -->
        <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ isEditing ? 'Edit Post' : 'New Blog Post' }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ isEditing ? 'Update the blog post details below.' : 'Create a new article to publish on the blog.' }}</p>
            </div>
            <Link :href="route('admin.blog.index')" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Blog
            </Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Title -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Post Title *</label>
                    <input v-model="form.title" type="text" placeholder="Enter a compelling post title..." required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-primary/40 focus:outline-none text-base font-medium" />
                    <span v-if="form.errors.title" class="text-red-500 text-xs mt-1 block">{{ form.errors.title }}</span>
                </div>

                <!-- Excerpt -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt / Summary</label>
                    <textarea v-model="form.excerpt" rows="3" placeholder="A brief summary shown in the blog listing (max 500 chars)..." maxlength="500"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800 focus:ring-2 focus:ring-primary/40 focus:outline-none text-sm resize-none"></textarea>
                    <div class="text-right text-xs text-gray-400 mt-1">{{ (form.excerpt || '').length }}/500</div>
                </div>

                <!-- Body Editor -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Article Body *</label>
                    <RichTextEditor v-model="form.body" placeholder="Start writing your article..." />
                    <span v-if="form.errors.body" class="text-red-500 text-xs mt-1 block">{{ form.errors.body }}</span>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-5">
                <!-- Publish Card -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Publish Settings</h3>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" value="draft" class="accent-primary" />
                                <span class="text-sm text-gray-700">Draft</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="form.status" value="published" class="accent-primary" />
                                <span class="text-sm text-gray-700">Published</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                        <select v-model="form.category" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:ring-2 focus:ring-primary/30 focus:outline-none">
                            <option value="">No Category</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Read Time (minutes)</label>
                        <input v-model.number="form.read_time" type="number" min="1" max="60" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:ring-2 focus:ring-primary/30 focus:outline-none" />
                    </div>

                    <div class="flex gap-3 pt-3 border-t border-gray-100">
                        <button type="submit" :disabled="form.processing"
                            class="flex-1 bg-primary hover:bg-primary/90 text-white font-semibold py-2.5 px-4 rounded-xl transition-all shadow disabled:opacity-60 flex items-center justify-center gap-2 text-sm">
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            {{ form.processing ? 'Saving...' : (isEditing ? 'Update Post' : 'Publish Post') }}
                        </button>
                    </div>
                </div>

                <!-- Cover Image -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <ImageUpload v-model="form.cover_image" label="Cover Image" />
                    <span v-if="form.errors.cover_image" class="text-red-500 text-xs mt-1 block">{{ form.errors.cover_image }}</span>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
