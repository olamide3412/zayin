<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    posts: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const doSearch = () => {
    router.get(route('admin.blog.index'), {
        search: search.value || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
};

const deleteForm = useForm({});
const deletePost = (id) => {
    if (!confirm('Are you sure you want to delete this post? This cannot be undone.')) return;
    deleteForm.delete(route('admin.blog.destroy', id), {
        onSuccess: () => {},
    });
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Blog Management" />

        <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Blog Posts</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage all hospital blog articles.</p>
            </div>
            <Link :href="route('admin.blog.create')" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-semibold px-5 py-2.5 rounded-xl shadow transition-all duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Post
            </Link>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl p-4 mb-4 flex flex-wrap gap-3 items-center shadow-sm border border-gray-100">
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input v-model="search" @keyup.enter="doSearch" type="text" placeholder="Search posts..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:ring-2 focus:ring-primary/30 focus:outline-none" />
            </div>
            <select v-model="statusFilter" @change="doSearch" class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:ring-2 focus:ring-primary/30 focus:outline-none">
                <option value="">All Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wider text-xs">Title</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wider text-xs hidden md:table-cell">Category</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wider text-xs hidden md:table-cell">Status</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 uppercase tracking-wider text-xs hidden lg:table-cell">Published</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-500 uppercase tracking-wider text-xs">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="!posts?.data?.length">
                            <td colspan="5" class="text-center py-16 text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9"/></svg>
                                No posts found. <Link :href="route('admin.blog.create')" class="text-primary font-medium">Create one?</Link>
                            </td>
                        </tr>
                        <tr v-for="post in posts?.data" :key="post.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gradient-to-br from-primary/20 to-blue-100 flex-shrink-0">
                                        <img v-if="post.cover_image" :src="post.cover_image" :alt="post.title" class="w-full h-full object-cover" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 line-clamp-1">{{ post.title }}</p>
                                        <p class="text-xs text-gray-400 line-clamp-1">{{ post.excerpt }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <span class="text-xs text-gray-600">{{ post.category || '—' }}</span>
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold', post.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700']">
                                    {{ post.status === 'published' ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 hidden lg:table-cell text-sm text-gray-500">{{ formatDate(post.published_at) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('blog.show', post.slug)" target="_blank" class="p-1.5 text-gray-400 hover:text-primary rounded-lg hover:bg-primary/10 transition-colors" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </Link>
                                    <Link :href="route('admin.blog.edit', post.id)" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </Link>
                                    <button @click="deletePost(post.id)" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="posts?.links?.length > 3" class="flex justify-end gap-2 p-4 border-t border-gray-100">
                <template v-for="link in posts.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url"
                        :class="['px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors', link.active ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200 hover:border-primary']"
                        v-html="link.label" />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
