<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { ReviewCard } from '@/Components/Cards';

defineOptions({ layout: Layout });

const props = defineProps({
    testimonials: Object,
});

const showForm = ref(false);
const submitted = ref(false);
const hoverRating = ref(0);

const form = useForm({
    name: '',
    occupation: '',
    rating: 5,
    message: '',
});

const submit = () => {
    form.post(route('testimonials.store'), {
        onSuccess: () => {
            form.reset();
            showForm.value = false;
            submitted.value = true;
        },
    });
};

const starLabel = (n) => ['Poor', 'Fair', 'Good', 'Very Good', 'Exceptional'][n - 1];

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-NG', { month: 'short', year: 'numeric' }) : '';
</script>

<template>
    <div class="bg-slate-50 min-h-screen">
        <Head title="Client Testimonials & Reviews | Zayin Technologies">
            <meta name="description" content="Read verified feedback from Nigerian homeowners and businesses powered by Zayin Technologies solar inverters, Starlink satellite internet, and DSTV setups." />
        </Head>

        <!-- Hero Header -->
        <section class="bg-gradient-to-b from-navy-950 via-navy-900 to-navy-950 text-white py-14 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-4 border border-amber-500/30">
                    Verified Customer Feedback
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight">
                    Trusted by Hundreds Across Nigeria
                </h1>
                <p class="mt-4 text-slate-300 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
                    From residential duplexes in Lekki and Maitama to corporate headquarters and rural clinics. See how our certified solar, Starlink, and DSTV installations perform in real life.
                </p>
            </div>
        </section>

        <!-- Action Bar / Subtitle -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-2xs">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-navy-950">Client Experiences &amp; Ratings</h2>
                    <p class="text-slate-600 mt-1 text-xs sm:text-sm leading-relaxed max-w-2xl">
                        Have we installed solar, configured your Starlink kit, or set up your DSTV Extra View? Share your honest review to help other Nigerian families and enterprises.
                    </p>
                </div>

                <!-- CTA to share testimony -->
                <button 
                    v-if="!showForm && !submitted" 
                    @click="showForm = true"
                    class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-400 text-navy-950 font-black px-8 py-3.5 rounded-xl shadow-sm transition uppercase text-xs tracking-wider shrink-0"
                >
                    <font-awesome-icon icon="edit" />
                    <span>Submit Your Review</span>
                </button>
            </div>
        </div>

        <!-- Submission Success -->
        <div v-if="submitted" class="max-w-2xl mx-auto px-4 py-6">
            <div class="bg-emerald-50 border border-emerald-200 rounded-3xl p-8 text-center shadow-sm">
                <font-awesome-icon icon="circle-check" class="text-4xl text-emerald-600 mb-3" />
                <h3 class="text-xl font-bold text-emerald-900 mb-1">Thank You For Your Review!</h3>
                <p class="text-xs sm:text-sm text-emerald-700">Your feedback has been received and will be published on our verified wall once approved by our moderation team.</p>
            </div>
        </div>

        <!-- Submission Form -->
        <section v-if="showForm" class="py-8 bg-white border-y border-slate-200">
            <div class="max-w-2xl mx-auto px-4">
                <div class="bg-slate-50 rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
                        <div>
                            <h2 class="text-xl font-black text-navy-950">Leave Your Review</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Tell others about your installation and system uptime</p>
                        </div>
                        <button @click="showForm = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-200/50">
                            <font-awesome-icon icon="xmark" class="text-lg" />
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Name & Occupation -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Your Name <span class="text-rose-500">*</span></label>
                                <input v-model="form.name" type="text" placeholder="e.g. Babatunde Fashola" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-amber-200 focus:border-amber-500 transition text-sm outline-none" />
                                <span v-if="form.errors.name" class="text-rose-500 text-xs mt-1">{{ form.errors.name }}</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Role / Location</label>
                                <input v-model="form.occupation" type="text" placeholder="e.g. Homeowner in Lekki / IT Director"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-amber-200 focus:border-amber-500 transition text-sm outline-none" />
                            </div>
                        </div>

                        <!-- Rating -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Overall Rating <span class="text-rose-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <button v-for="n in 5" :key="n" type="button"
                                    @click="form.rating = n"
                                    @mouseenter="hoverRating = n"
                                    @mouseleave="hoverRating = 0"
                                    class="text-2xl transition hover:scale-125 focus:outline-none"
                                    :class="(hoverRating || form.rating) >= n ? 'text-amber-400' : 'text-slate-300'"
                                >
                                    ★
                                </button>
                                <span class="text-xs font-semibold text-slate-500 ml-2">
                                    {{ starLabel(hoverRating || form.rating) }}
                                </span>
                            </div>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Your Experience / Review <span class="text-rose-500">*</span></label>
                            <textarea v-model="form.message" rows="4" required minlength="20"
                                placeholder="Describe how your solar power system, Starlink setup, or DSTV installation has been operating. How was the technician's professionalism and timeliness?"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-amber-200 focus:border-amber-500 transition text-sm outline-none resize-none"></textarea>
                            <div class="flex justify-between items-center text-[11px] text-slate-400 mt-1">
                                <span>Minimum 20 characters</span>
                                <span>{{ form.message.length }} / 1000</span>
                            </div>
                            <span v-if="form.errors.message" class="text-rose-500 text-xs mt-1">{{ form.errors.message }}</span>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" :disabled="form.processing"
                                class="px-8 py-3.5 bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-navy-950 font-black rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center gap-2">
                                <font-awesome-icon v-if="form.processing" icon="spinner" class="animate-spin" />
                                <span>{{ form.processing ? 'Submitting...' : 'Submit Feedback' }}</span>
                            </button>
                            <button type="button" @click="showForm = false"
                                class="px-6 py-3.5 border border-slate-300 hover:bg-white text-slate-600 font-bold rounded-xl text-xs transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Testimonials Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
            <div v-if="testimonials?.data?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <ReviewCard 
                    v-for="item in testimonials.data" 
                    :key="item.id" 
                    :review="item" 
                />
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8">
                <font-awesome-icon icon="comments" class="text-4xl text-slate-300 mb-3" />
                <h3 class="text-base font-bold text-navy-950">No Reviews Published Yet</h3>
                <p class="text-xs text-slate-500 mt-1">Be the first to leave a review of our installations!</p>
                <button @click="showForm = true" class="mt-4 px-6 py-2.5 bg-amber-500 text-navy-950 font-bold rounded-xl text-xs">
                    Write a Review
                </button>
            </div>

            <!-- Pagination if present -->
            <div v-if="testimonials?.links?.length > 3" class="mt-12 flex justify-center">
                <div class="flex gap-1.5 flex-wrap">
                    <Link
                        v-for="(link, idx) in testimonials.links"
                        :key="idx"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition',
                            link.active ? 'bg-navy-950 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200',
                            !link.url ? 'opacity-40 pointer-events-none' : ''
                        ]"
                    />
                </div>
            </div>
        </section>
    </div>
</template>
