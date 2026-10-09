<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    services: Array,
    states: Array,
    seo: Object,
});

const page = usePage();

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
    landmark: '',
    state_id: '',
    city: '',
    preferred_date: '',
    preferred_slot: 'morning',
    service_type: 'solar_installation',
    notes: '',
});

const submitBooking = () => {
    form.post('/book-installation', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-12 sm:py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-10">
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
                v-if="page.props.flash?.booking_reference"
                class="mb-8 rounded-3xl bg-emerald-600 text-white p-8 shadow-2xl space-y-4 text-center animate-in fade-in duration-300"
            >
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl mx-auto">
                    <font-awesome-icon :icon="['fas', 'calendar-check']" />
                </div>
                <h2 class="text-2xl font-black">Booking Confirmed!</h2>
                <p class="text-emerald-100 text-sm max-w-md mx-auto">
                    {{ page.props.flash?.success }}
                </p>
                <div class="text-xs font-mono font-bold bg-emerald-700 px-4 py-2 rounded-xl inline-block">
                    Reference ID: {{ page.props.flash?.booking_reference }}
                </div>
                <div v-if="page.props.flash?.whatsapp_link" class="pt-2">
                    <a 
                        :href="page.props.flash?.whatsapp_link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center space-x-2 px-6 py-3.5 rounded-2xl bg-white text-emerald-950 font-bold text-sm shadow-lg hover:bg-emerald-50 transition"
                    >
                        <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-lg text-emerald-600" />
                        <span>Confirm Appointment on WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-10">
                <form @submit.prevent="submitBooking" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Your Full Name *</label>
                            <input 
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="e.g. Babatunde Johnson"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Phone Number (WhatsApp) *</label>
                            <input 
                                v-model="form.phone"
                                type="text"
                                required
                                placeholder="e.g. 0802 345 6789"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Email Address (Optional)</label>
                            <input 
                                v-model="form.email"
                                type="email"
                                placeholder="babatunde@example.com"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Service Type *</label>
                            <select 
                                v-model="form.service_type"
                                required
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="solar_installation">Solar & Inverter Installation</option>
                                <option value="solar_maintenance">Solar Panel Cleaning & Maintenance</option>
                                <option value="starlink_installation">Starlink Rooftop Mast Setup</option>
                                <option value="dstv_installation">DSTV Installation & Extra View</option>
                                <option value="dish_realignment">DSTV Dish Realignment (Fix Signal)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Address & Landmark -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div class="sm:col-span-2">
                            <label class="text-xs font-bold text-slate-700 block mb-1">Street Address *</label>
                            <input 
                                v-model="form.address"
                                type="text"
                                required
                                placeholder="e.g. 15 Adebayo Doherty Street"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Nearest Landmark *</label>
                            <input 
                                v-model="form.landmark"
                                type="text"
                                required
                                placeholder="e.g. Near Slot / Zenith Bank"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">State *</label>
                            <select 
                                v-model="form.state_id"
                                required
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="">Select State</option>
                                <option v-for="st in states" :key="st.id" :value="st.id">
                                    {{ st.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Date & Slot -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Preferred Date *</label>
                            <input 
                                v-model="form.preferred_date"
                                type="date"
                                required
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                            />
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1">Preferred Time Window *</label>
                            <select 
                                v-model="form.preferred_slot"
                                required
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500"
                            >
                                <option value="morning">Morning (9:00 AM – 1:00 PM)</option>
                                <option value="afternoon">Afternoon (1:00 PM – 5:00 PM)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Notes / Specific Instructions</label>
                        <textarea 
                            v-model="form.notes"
                            rows="2"
                            placeholder="Tell us about gate access, building height, or existing cables..."
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                        ></textarea>
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition transform active:scale-95 disabled:opacity-50 flex items-center justify-center space-x-2"
                        >
                            <font-awesome-icon :icon="['fas', 'calendar-check']" />
                            <span>{{ form.processing ? 'Booking...' : 'Confirm My Appointment' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
