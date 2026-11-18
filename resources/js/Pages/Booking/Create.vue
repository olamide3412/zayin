<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps({
    services: Array,
    states: Array,
    seo: Object,
});

const page = usePage();
const copied = ref(false);
const submittedBooking = ref(null);

const minDate = computed(() => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
});

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
    landmark: '',
    state_id: '',
    city: '',
    preferred_date: minDate.value,
    preferred_slot: 'morning',
    service_type: 'solar_installation',
    notes: '',
});

const activeReference = computed(() => {
    return submittedBooking.value?.reference || page.props.flash?.booking_reference || null;
});

const activeWhatsAppLink = computed(() => {
    return submittedBooking.value?.whatsapp_link || page.props.flash?.whatsapp_link || null;
});

const activeSuccessMsg = computed(() => {
    return submittedBooking.value?.message || page.props.flash?.success || 'Your appointment has been successfully scheduled.';
});

const copyReference = async () => {
    if (!activeReference.value) return;
    try {
        await navigator.clipboard.writeText(activeReference.value);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2500);
    } catch (e) {
        // Fallback for older browsers
        const el = document.createElement('textarea');
        el.value = activeReference.value;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2500);
    }
};

const resetForNewBooking = () => {
    submittedBooking.value = null;
    form.reset();
    form.clearErrors();
};

const submitBooking = () => {
    form.post('/book-installation', {
        preserveScroll: false,
        onSuccess: (pageResp) => {
            const flash = pageResp.props.flash || {};
            submittedBooking.value = {
                reference: flash.booking_reference,
                whatsapp_link: flash.whatsapp_link,
                message: flash.success,
            };
            form.reset();
            window.scrollTo({ top: 120, behavior: 'smooth' });
        },
        onError: () => {
            window.scrollTo({ top: 180, behavior: 'smooth' });
        }
    });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-12 sm:py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-100 px-3.5 py-1.5 rounded-full inline-block mb-3">
                    Certified Technicians
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Book an Installation or Site Survey
                </h1>
                <p class="mt-3 text-slate-600 text-sm sm:text-base max-w-lg mx-auto leading-relaxed">
                    Schedule a certified electrical or satellite technician to visit your home or business premises.
                </p>
            </div>

            <!-- Confirmation Message -->
            <div 
                v-if="activeReference"
                class="mb-8 rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white p-6 sm:p-8 shadow-2xl space-y-5 text-center animate-in fade-in zoom-in-95 duration-300 border border-emerald-500"
            >
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl mx-auto text-emerald-100 ring-4 ring-white/10">
                    <font-awesome-icon :icon="['fas', 'calendar-check']" />
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black">Booking Confirmed!</h2>
                    <p class="text-emerald-100 text-sm sm:text-base max-w-md mx-auto mt-1">
                        {{ activeSuccessMsg }}
                    </p>
                </div>

                <!-- Reference Code Box -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
                    <div class="inline-flex items-center space-x-2 bg-emerald-900/60 border border-emerald-400/30 px-5 py-2.5 rounded-2xl">
                        <span class="text-xs uppercase tracking-wider text-emerald-300 font-medium">Reference Code:</span>
                        <span class="text-base font-mono font-black tracking-widest text-white">{{ activeReference }}</span>
                    </div>
                    <button 
                        type="button"
                        @click="copyReference"
                        class="px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-xs font-bold transition flex items-center space-x-1.5 text-white active:scale-95"
                    >
                        <font-awesome-icon :icon="['fas', copied ? 'check' : 'copy']" />
                        <span>{{ copied ? 'Copied!' : 'Copy Code' }}</span>
                    </button>
                </div>

                <!-- Next steps info -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 text-xs text-emerald-50 text-left max-w-lg mx-auto space-y-1.5 border border-white/10">
                    <p class="font-bold flex items-center gap-2 text-white">
                        <font-awesome-icon :icon="['fas', 'circle-info']" />
                        <span>What happens next?</span>
                    </p>
                    <p>1. Our operations team will call or WhatsApp you to confirm your address and technician dispatch.</p>
                    <p>2. You can click the button below to instantly connect with our support desk on WhatsApp.</p>
                </div>

                <!-- Actions -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a 
                        v-if="activeWhatsAppLink"
                        :href="activeWhatsAppLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full sm:w-auto inline-flex items-center justify-center space-x-2.5 px-6 py-3.5 rounded-2xl bg-white text-emerald-950 font-bold text-sm shadow-xl hover:bg-emerald-50 transition transform hover:-translate-y-0.5 active:scale-95"
                    >
                        <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-xl text-emerald-600" />
                        <span>Confirm Appointment on WhatsApp</span>
                    </a>

                    <button 
                        type="button"
                        @click="resetForNewBooking"
                        class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-3.5 rounded-2xl bg-emerald-800/80 hover:bg-emerald-800 text-white font-bold text-xs transition border border-emerald-500/40"
                    >
                        <font-awesome-icon :icon="['fas', 'plus']" />
                        <span>Book Another Appointment</span>
                    </button>
                </div>
            </div>

            <!-- Global Error Banner -->
            <div 
                v-if="Object.keys(form.errors).length > 0 || page.props.flash?.error"
                class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 animate-in fade-in duration-200"
            >
                <div class="flex items-start space-x-3">
                    <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="text-rose-500 text-lg mt-0.5 shrink-0" />
                    <div>
                        <h3 class="text-sm font-bold text-rose-900">Please review the required details below:</h3>
                        <p class="text-xs text-rose-700 mt-0.5">
                            {{ page.props.flash?.error || 'Some required fields were left empty or formatted incorrectly. Please fix them before proceeding.' }}
                        </p>
                        <ul class="mt-2 list-disc list-inside text-xs text-rose-600 space-y-0.5" v-if="Object.keys(form.errors).length > 0">
                            <li v-for="(errMsg, errField) in form.errors" :key="errField">
                                {{ errMsg }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sm:p-10">
                <form @submit.prevent="submitBooking" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Your Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="e.g. Babatunde Johnson"
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none focus:ring-2',
                                    form.errors.name ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.name }}</p>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Phone Number (WhatsApp) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                v-model="form.phone"
                                type="tel"
                                required
                                placeholder="e.g. 0802 345 6789"
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none focus:ring-2',
                                    form.errors.phone ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Email Address <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input 
                                v-model="form.email"
                                type="email"
                                placeholder="babatunde@example.com"
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none focus:ring-2',
                                    form.errors.email ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.email }}</p>
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Service Type <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                v-model="form.service_type"
                                required
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 font-medium transition focus:outline-none focus:ring-2 bg-white',
                                    form.errors.service_type ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            >
                                <option value="solar_installation">Solar & Inverter Installation</option>
                                <option value="solar_maintenance">Solar Panel Cleaning & Maintenance</option>
                                <option value="starlink_installation">Starlink Rooftop Mast Setup</option>
                                <option value="dstv_installation">DSTV Installation & Extra View</option>
                                <option value="dish_realignment">DSTV Dish Realignment (Fix Signal)</option>
                            </select>
                            <p v-if="form.errors.service_type" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.service_type }}</p>
                        </div>
                    </div>

                    <!-- Address & Landmark -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                        <div class="sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Street Address <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                v-model="form.address"
                                type="text"
                                required
                                placeholder="e.g. 15 Adebayo Doherty Street"
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none focus:ring-2',
                                    form.errors.address ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            />
                            <p v-if="form.errors.address" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.address }}</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Nearest Landmark <span class="text-slate-400 font-normal">(Help technician locate premises)</span>
                            </label>
                            <input 
                                v-model="form.landmark"
                                type="text"
                                placeholder="e.g. Near Slot / Zenith Bank / Total Fuel Station"
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none focus:ring-2',
                                    form.errors.landmark ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            />
                            <p v-if="form.errors.landmark" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.landmark }}</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                State <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                v-model="form.state_id"
                                required
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 font-medium transition focus:outline-none focus:ring-2 bg-white',
                                    form.errors.state_id ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            >
                                <option value="">Select State</option>
                                <option v-for="st in states" :key="st.id" :value="st.id">
                                    {{ st.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.state_id" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.state_id }}</p>
                        </div>
                    </div>

                    <!-- Date & Slot -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Preferred Date <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                v-model="form.preferred_date"
                                type="date"
                                :min="minDate"
                                required
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none focus:ring-2',
                                    form.errors.preferred_date ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            />
                            <p v-if="form.errors.preferred_date" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.preferred_date }}</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">
                                Preferred Time Window <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                v-model="form.preferred_slot"
                                required
                                :class="[
                                    'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 font-medium transition focus:outline-none focus:ring-2 bg-white',
                                    form.errors.preferred_slot ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                                ]"
                            >
                                <option value="morning">Morning (9:00 AM – 1:00 PM)</option>
                                <option value="afternoon">Afternoon (1:00 PM – 5:00 PM)</option>
                            </select>
                            <p v-if="form.errors.preferred_slot" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.preferred_slot }}</p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">
                            Notes / Specific Instructions <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <textarea 
                            v-model="form.notes"
                            rows="2"
                            placeholder="Tell us about gate access, building height, roof type, or existing cables..."
                            :class="[
                                'w-full rounded-xl border px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none focus:ring-2',
                                form.errors.notes ? 'border-rose-400 bg-rose-50/30 focus:ring-rose-500' : 'border-slate-200 focus:ring-amber-500'
                            ]"
                        ></textarea>
                        <p v-if="form.errors.notes" class="mt-1 text-xs text-rose-600 font-medium">{{ form.errors.notes }}</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-white font-black text-sm shadow-xl shadow-amber-500/20 transition transform active:scale-98 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                        >
                            <font-awesome-icon 
                                :icon="['fas', form.processing ? 'circle-notch' : 'calendar-check']" 
                                :class="{'fa-spin': form.processing}"
                            />
                            <span>{{ form.processing ? 'Submitting Appointment Request...' : 'Confirm My Appointment' }}</span>
                        </button>
                        <p class="text-center text-[11px] text-slate-400 mt-2">
                            No upfront payment required for booking. Our technician will verify the site before work commences.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
