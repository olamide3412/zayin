<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import FlashMessages from '@/Components/FlashMessages.vue';

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
    service_type: 'solar',
    property_type: 'residential',
    state_id: '',
    city: '',
    address: '',
    budget_range: '',
    appliances: [],
    details: '',
    preferred_contact: 'whatsapp',
});

const applianceOptions = [
    { id: 'ac_inverter', label: 'Inverter Air Conditioner (1HP / 1.5HP)', icon: 'snowflake' },
    { id: 'refrigerator', label: 'Refrigerator / Deep Freezer', icon: 'temperature-arrow-down' },
    { id: 'tv_sound', label: 'TV, Sound Bar & DSTV Decoder', icon: 'tv' },
    { id: 'pumping_machine', label: 'Water Pumping Machine (0.75HP–1HP)', icon: 'faucet-drip' },
    { id: 'lighting_fans', label: 'Lighting & Standing/Ceiling Fans', icon: 'fan' },
    { id: 'computers', label: 'Computers & Workstations', icon: 'laptop' },
];

const toggleAppliance = (id) => {
    const idx = form.appliances.indexOf(id);
    if (idx > -1) {
        form.appliances.splice(idx, 1);
    } else {
        form.appliances.push(id);
    }
};

const submitQuote = () => {
    form.post('/request-quote', {
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-10">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-100 px-3.5 py-1.5 rounded-full inline-block mb-3">
                    Fast & Transparent
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Request a Free Quote & Solar Sizing
                </h1>
                <p class="mt-3 text-slate-600 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                    Select your appliances and property details below. Our electrical engineers will size an exact system and send your quotation with zero commitment.
                </p>
            </div>

            <!-- Success Card -->
            <div 
                v-if="page.props.flash?.quote_reference"
                class="mb-8 rounded-3xl bg-emerald-600 text-white p-8 shadow-2xl space-y-4 text-center animate-in fade-in duration-300"
            >
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl mx-auto">
                    <font-awesome-icon :icon="['fas', 'circle-check']" />
                </div>
                <h2 class="text-2xl font-black">Quote Request Received!</h2>
                <p class="text-emerald-100 text-sm max-w-lg mx-auto">
                    {{ page.props.flash?.success }}
                </p>
                <div class="text-xs font-mono font-bold bg-emerald-700/80 px-4 py-2 rounded-xl inline-block">
                    Reference ID: {{ page.props.flash?.quote_reference }}
                </div>
                <div v-if="page.props.flash?.whatsapp_link" class="pt-2">
                    <a 
                        :href="page.props.flash?.whatsapp_link"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center space-x-2 px-6 py-3.5 rounded-2xl bg-white text-emerald-900 font-bold text-sm shadow-lg hover:bg-emerald-50 transition"
                    >
                        <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-lg text-emerald-600" />
                        <span>Send Details Directly on WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 space-y-10">
                <form @submit.prevent="submitQuote" class="space-y-8">
                    <!-- Step 1: Service Type -->
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-slate-900">
                            1. What solution are you looking for?
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <button
                                type="button"
                                @click="form.service_type = 'solar'"
                                :class="[
                                    'p-4 rounded-2xl border text-center transition font-bold text-xs flex flex-col items-center space-y-2',
                                    form.service_type === 'solar'
                                        ? 'border-amber-500 bg-amber-50 text-amber-900 ring-2 ring-amber-500'
                                        : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
                                ]"
                            >
                                <font-awesome-icon :icon="['fas', 'solar-panel']" class="text-xl" />
                                <span>Solar System</span>
                            </button>

                            <button
                                type="button"
                                @click="form.service_type = 'starlink'"
                                :class="[
                                    'p-4 rounded-2xl border text-center transition font-bold text-xs flex flex-col items-center space-y-2',
                                    form.service_type === 'starlink'
                                        ? 'border-sky-500 bg-sky-50 text-sky-900 ring-2 ring-sky-500'
                                        : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
                                ]"
                            >
                                <font-awesome-icon :icon="['fas', 'satellite-dish']" class="text-xl" />
                                <span>Starlink Setup</span>
                            </button>

                            <button
                                type="button"
                                @click="form.service_type = 'dstv'"
                                :class="[
                                    'p-4 rounded-2xl border text-center transition font-bold text-xs flex flex-col items-center space-y-2',
                                    form.service_type === 'dstv'
                                        ? 'border-blue-600 bg-blue-50 text-blue-900 ring-2 ring-blue-600'
                                        : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
                                ]"
                            >
                                <font-awesome-icon :icon="['fas', 'tv']" class="text-xl" />
                                <span>DSTV Solutions</span>
                            </button>

                            <button
                                type="button"
                                @click="form.service_type = 'combination'"
                                :class="[
                                    'p-4 rounded-2xl border text-center transition font-bold text-xs flex flex-col items-center space-y-2',
                                    form.service_type === 'combination'
                                        ? 'border-slate-900 bg-slate-900 text-white ring-2 ring-slate-900'
                                        : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100'
                                ]"
                            >
                                <font-awesome-icon :icon="['fas', 'cubes']" class="text-xl" />
                                <span>Multiple / All</span>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Appliance Checklist (If Solar is Selected) -->
                    <div v-if="form.service_type === 'solar' || form.service_type === 'combination'" class="space-y-4 pt-4 border-t border-slate-100">
                        <label class="block text-sm font-bold text-slate-900">
                            2. Which major appliances do you want to power?
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div 
                                v-for="app in applianceOptions" 
                                :key="app.id"
                                @click="toggleAppliance(app.id)"
                                :class="[
                                    'p-4 rounded-2xl border cursor-pointer transition flex items-center space-x-3 text-xs font-semibold',
                                    form.appliances.includes(app.id)
                                        ? 'border-amber-500 bg-amber-50 text-slate-900 ring-1 ring-amber-500'
                                        : 'border-slate-200 hover:bg-slate-50 text-slate-700'
                                ]"
                            >
                                <div 
                                    :class="[
                                        'w-5 h-5 rounded-md flex items-center justify-center text-xs shrink-0',
                                        form.appliances.includes(app.id) ? 'bg-amber-500 text-slate-950 font-bold' : 'border border-slate-300'
                                    ]"
                                >
                                    <font-awesome-icon v-if="form.appliances.includes(app.id)" :icon="['fas', 'check']" />
                                </div>
                                <font-awesome-icon :icon="['fas', app.icon]" class="text-slate-400 text-base" />
                                <span>{{ app.label }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Property Type & Location -->
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <label class="block text-sm font-bold text-slate-900">
                            3. Property Type & Location
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs text-slate-500 font-semibold block mb-1">Property Type</label>
                                <select 
                                    v-model="form.property_type"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500"
                                >
                                    <option value="residential">Residential Home / Flat</option>
                                    <option value="duplex">Detached Duplex / Villa</option>
                                    <option value="commercial">Office / Commercial Space</option>
                                    <option value="clinic_school">Clinic / School / Church</option>
                                    <option value="estate">Estate / Compound</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs text-slate-500 font-semibold block mb-1">State</label>
                                <select 
                                    v-model="form.state_id"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500"
                                >
                                    <option value="">Select State</option>
                                    <option v-for="st in states" :key="st.id" :value="st.id">
                                        {{ st.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-xs text-slate-500 font-semibold block mb-1">City / Area</label>
                                <input 
                                    v-model="form.city"
                                    type="text"
                                    placeholder="e.g. Lekki, Maitama, Bodija"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Contact Information -->
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <label class="block text-sm font-bold text-slate-900">
                            4. Your Contact Information
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-slate-500 font-semibold block mb-1">Full Name *</label>
                                <input 
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="e.g. Tunde Adeleke"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <div>
                                <label class="text-xs text-slate-500 font-semibold block mb-1">Phone Number / WhatsApp *</label>
                                <input 
                                    v-model="form.phone"
                                    type="text"
                                    required
                                    placeholder="e.g. 0803 123 4567"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <div>
                                <label class="text-xs text-slate-500 font-semibold block mb-1">Email Address</label>
                                <input 
                                    v-model="form.email"
                                    type="email"
                                    placeholder="tunde@example.com"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <div>
                                <label class="text-xs text-slate-500 font-semibold block mb-1">Budget Range (Optional)</label>
                                <select 
                                    v-model="form.budget_range"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500"
                                >
                                    <option value="">Flexible / Not Sure</option>
                                    <option value="under_1m">Under ₦1,000,000</option>
                                    <option value="1m_3m">₦1,000,000 – ₦3,000,000</option>
                                    <option value="3m_5m">₦3,000,000 – ₦5,000,000</option>
                                    <option value="5m_10m">₦5,000,000 – ₦10,000,000</option>
                                    <option value="above_10m">Above ₦10,000,000 (Commercial)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-slate-500 font-semibold block mb-1">Additional Project Details / Notes</label>
                            <textarea 
                                v-model="form.details"
                                rows="3"
                                placeholder="Describe any specific needs, hours of backup required, or roof height..."
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition transform active:scale-95 flex items-center justify-center space-x-2 disabled:opacity-50"
                        >
                            <font-awesome-icon :icon="['fas', 'file-invoice-dollar']" />
                            <span>{{ form.processing ? 'Calculating & Submitting...' : 'Generate My Custom Quote' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
