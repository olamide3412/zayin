<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    testimonials: Object,
    filters: Object,
    counts: Object,
});

const statusFilter = ref(props.filters?.status || '');

const doFilter = () => {
    router.get(route('admin.testimonials.index'), {
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
};

const approveForm = useForm({});
const rejectForm = useForm({});
const deleteForm = useForm({});

const approve = (id) => approveForm.patch(route('admin.testimonials.approve', id));
const reject = (id) => rejectForm.patch(route('admin.testimonials.reject', id));
const destroy = (id) => {
    if (!confirm('Delete this testimonial? This cannot be undone.')) return;
    deleteForm.delete(route('admin.testimonials.destroy', id));
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const statusBadge = {
    pending: 'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Testimonials Management" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Testimonials</h1>
            <p class="text-sm text-gray-500 mt-0.5">Review and approve patient testimonials before they appear publicly.</p>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ counts?.pending || 0 }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ counts?.approved || 0 }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ counts?.all || 0 }}</p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex gap-2 mb-4">
            <button v-for="f in [{ val: '', label: 'All' }, { val: 'pending', label: 'Pending' }, { val: 'approved', label: 'Approved' }, { val: 'rejected', label: 'Rejected' }]" :key="f.val"
                @click="statusFilter = f.val; doFilter()"
                :class="['px-4 py-2 rounded-lg text-sm font-medium transition-colors border', statusFilter === f.val ? 'bg-primary text-white border-primary shadow' : 'bg-white text-gray-600 border-gray-200 hover:border-primary hover:text-primary']">
                {{ f.label }}
            </button>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <div v-if="!testimonials?.data?.length" class="col-span-full text-center py-20 text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                No testimonials found.
            </div>

            <div v-for="t in testimonials?.data" :key="t.id"
                class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-blue-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ t.name[0].toUpperCase() }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ t.name }}</p>
                            <p v-if="t.occupation" class="text-xs text-gray-400">{{ t.occupation }}</p>
                        </div>
                    </div>
                    <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold capitalize', statusBadge[t.status]]">{{ t.status }}</span>
                </div>

                <!-- Stars -->
                <div class="flex gap-0.5 mb-3">
                    <svg v-for="n in 5" :key="n" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" :class="n <= t.rating ? 'text-yellow-400' : 'text-gray-200'">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>

                <!-- Message -->
                <p class="text-sm text-gray-600 italic flex-1 mb-4 line-clamp-4">"{{ t.message }}"</p>

                <p class="text-xs text-gray-400 mb-4">Submitted {{ formatDate(t.created_at) }}</p>

                <!-- Actions -->
                <div class="flex gap-2 pt-3 border-t border-gray-100">
                    <button v-if="t.status !== 'approved'" @click="approve(t.id)" :disabled="approveForm.processing"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-green-50 hover:bg-green-100 text-green-700 font-medium text-xs px-3 py-2 rounded-lg transition-colors disabled:opacity-60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve
                    </button>
                    <button v-if="t.status !== 'rejected'" @click="reject(t.id)" :disabled="rejectForm.processing"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-medium text-xs px-3 py-2 rounded-lg transition-colors disabled:opacity-60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject
                    </button>
                    <button @click="destroy(t.id)" :disabled="deleteForm.processing"
                        class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors disabled:opacity-60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="testimonials?.links?.length > 3" class="flex justify-end mt-6 gap-2">
            <template v-for="link in testimonials.links" :key="link.label">
                <Link v-if="link.url" :href="link.url"
                    :class="['px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors', link.active ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-200 hover:border-primary']"
                    v-html="link.label" />
            </template>
        </div>
    </AuthenticatedLayout>
</template>
