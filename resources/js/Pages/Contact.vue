<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps({
    seo: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const companyPhone = computed(() => page.props.support?.phone || '+234 800 000 0000');
const companyEmail = computed(() => page.props.support?.email || 'support@zayin.ng');
const companyWhatsApp = computed(() => page.props.support?.whatsapp || '+2348000000000');
const companyAddress = computed(() => page.props.support?.location || page.props.site?.address || 'Abraka, Ethiope East, Delta State, Nigeria');

const offices = computed(() => [
    {
        city: 'Abraka (Headquarters)',
        address: companyAddress.value,
        phone: companyPhone.value,
        coverage: 'Delta State, Edo, Niger Delta & Nationwide Dispatch Across Nigeria',
    },
    {
        city: 'Lagos (Operations Hub)',
        address: 'Lekki Phase 1 / Allen Avenue, Ikeja, Lagos State',
        phone: '+234 802 987 6543',
        coverage: 'Lagos Island, Mainland, Ogun & Western corridors',
    },
    {
        city: 'Abuja (FCT Hub)',
        address: 'Central Business District, Abuja, FCT',
        phone: '+234 805 444 3322',
        coverage: 'Abuja FCT, Nasarawa, Niger & Northern regional deployments',
    },
]);

const form = useForm({
    name: '',
    phone: '',
    email: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-3">
                    Reach Out
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-navy-950 tracking-tight">
                    Get in Touch with Our Engineers
                </h1>
                <p class="mt-4 text-slate-600 text-sm sm:text-base leading-relaxed">
                    Have questions about an upcoming installation, need an urgent site repair, or want to speak directly to a technical consultant? We are ready to assist you.
                </p>
            </div>

            <!-- Flash Success Banner -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-8 max-w-4xl mx-auto p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm"
            >
                <font-awesome-icon icon="circle-check" class="text-emerald-600 text-xl flex-shrink-0" />
                <span class="font-medium">{{ $page.props.flash.success }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                <!-- Contact Form (7 cols) -->
                <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
                    <h2 class="text-xl font-bold text-navy-950 mb-2">Send Us a Direct Message</h2>
                    <p class="text-xs text-slate-500 mb-8">
                        Fill in your details below and an engineering consultant will contact you within 1 hour during business hours.
                    </p>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Full Name <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. Babatunde Adeleke"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 text-sm transition outline-none"
                                    required
                                />
                                <div v-if="form.errors.name" class="text-xs text-rose-500 mt-1 font-semibold">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Phone / WhatsApp Number <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="0803 000 0000"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 text-sm transition outline-none"
                                    required
                                />
                                <div v-if="form.errors.phone" class="text-xs text-rose-500 mt-1 font-semibold">{{ form.errors.phone }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Email Address
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="you@domain.com"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 text-sm transition outline-none"
                                />
                                <div v-if="form.errors.email" class="text-xs text-rose-500 mt-1 font-semibold">{{ form.errors.email }}</div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Subject / Interest
                                </label>
                                <select
                                    v-model="form.subject"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 text-sm transition outline-none bg-white"
                                >
                                    <option value="">Select a subject...</option>
                                    <option value="Solar Sizing & Inverter System">Solar Sizing & Inverter System</option>
                                    <option value="Starlink Installation & Mast">Starlink Installation & Mast</option>
                                    <option value="DSTV Multi-Switch / Signal Fix">DSTV Multi-Switch / Signal Fix</option>
                                    <option value="Emergency Maintenance / Repair">Emergency Maintenance / Repair</option>
                                    <option value="Partnership / Contractor Inquiry">Partnership / Contractor Inquiry</option>
                                </select>
                                <div v-if="form.errors.subject" class="text-xs text-rose-500 mt-1 font-semibold">{{ form.errors.subject }}</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Message / Site Location Details <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                v-model="form.message"
                                rows="5"
                                placeholder="Tell us about your home/business setup, the appliances or devices you need powered or connected, and your state/LGA..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 text-sm transition outline-none resize-none"
                                required
                            ></textarea>
                            <div v-if="form.errors.message" class="text-xs text-rose-500 mt-1 font-semibold">{{ form.errors.message }}</div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full sm:w-auto px-8 py-4 bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-navy-950 font-black rounded-xl text-sm transition shadow-md uppercase tracking-wider flex items-center justify-center gap-2"
                        >
                            <font-awesome-icon v-if="form.processing" icon="spinner" class="animate-spin" />
                            <span>{{ form.processing ? 'Submitting...' : 'Send Message to Engineers' }}</span>
                        </button>
                    </form>
                </div>

                <!-- Office Locations & Direct Contacts (5 cols) -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Quick Direct Contact Card -->
                    <div class="bg-navy-950 text-white rounded-3xl p-6 sm:p-8 shadow-sm">
                        <h3 class="text-lg font-bold mb-4">Direct Communication Channels</h3>
                        
                        <div class="space-y-4 text-xs">
                            <a 
                                :href="`tel:${companyPhone}`"
                                class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition border border-white/10"
                            >
                                <div class="w-10 h-10 rounded-lg bg-amber-500 text-navy-950 flex items-center justify-center font-bold text-base flex-shrink-0">
                                    <font-awesome-icon icon="phone" />
                                </div>
                                <div>
                                    <div class="text-slate-400 uppercase text-[10px] font-bold">Toll-Free Call Center</div>
                                    <div class="text-sm font-bold text-white">{{ companyPhone }}</div>
                                </div>
                            </a>

                            <a 
                                :href="`https://wa.me/${companyWhatsApp.replace(/[^0-9]/g, '')}?text=Hello%20Zayin%20Technologies,%20I%20would%20like%20to%20make%20an%20inquiry.`"
                                target="_blank"
                                class="flex items-center gap-4 p-3 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 transition border border-emerald-500/30 text-emerald-300"
                            >
                                <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    <font-awesome-icon :icon="['fab', 'whatsapp']" />
                                </div>
                                <div>
                                    <div class="text-emerald-400 uppercase text-[10px] font-bold">Instant WhatsApp Support</div>
                                    <div class="text-sm font-bold text-white">Chat With An Engineer</div>
                                </div>
                            </a>

                            <a 
                                :href="`mailto:${companyEmail}`"
                                class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition border border-white/10"
                            >
                                <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center font-bold text-base flex-shrink-0">
                                    <font-awesome-icon icon="envelope" />
                                </div>
                                <div>
                                    <div class="text-slate-400 uppercase text-[10px] font-bold">Email Inquiries</div>
                                    <div class="text-sm font-bold text-white">{{ companyEmail }}</div>
                                </div>
                            </a>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-800 text-xs text-slate-400 flex items-center gap-2">
                            <font-awesome-icon icon="clock" class="text-amber-400" />
                            <span>Mon – Sat: 8:00 AM – 6:00 PM (Emergency 24/7 on WhatsApp)</span>
                        </div>
                    </div>

                    <!-- Regional Offices & Nationwide Coverage -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-navy-950 uppercase tracking-wider">
                                Headquarters & Hubs
                            </h3>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full border border-amber-300">
                                Nationwide Service
                            </span>
                        </div>

                        <!-- Nationwide coverage banner -->
                        <div class="mb-5 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs">
                            <div class="font-bold text-navy-950 flex items-center gap-2 mb-1">
                                <font-awesome-icon icon="truck-fast" class="text-amber-600 text-sm" />
                                <span>We Work Nationwide Across Nigeria</span>
                            </div>
                            <p class="text-slate-600 text-[11px] leading-relaxed">
                                Our central office is located in <strong>Abraka, Ethiope East, Delta State</strong>. Our certified field engineers and logistics vehicles deploy nationwide across all 36 States and the FCT for installations, surveys, and system maintenance.
                            </p>
                        </div>

                        <div class="space-y-5">
                            <div v-for="office in offices" :key="office.city" class="text-xs">
                                <div class="font-bold text-navy-950 text-sm flex items-center gap-1.5">
                                    <font-awesome-icon icon="location-dot" class="text-amber-500" />
                                    {{ office.city }}
                                </div>
                                <p class="text-slate-600 mt-1 leading-relaxed">{{ office.address }}</p>
                                <div class="text-[11px] text-slate-400 mt-1">Direct: <span class="font-medium text-slate-700">{{ office.phone }}</span></div>
                                <div class="text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded inline-block mt-1">
                                    Covers: {{ office.coverage }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
