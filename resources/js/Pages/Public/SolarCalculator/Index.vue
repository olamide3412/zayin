<script setup>
import { ref, computed } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import PresetChips from '@/Components/Solar/PresetChips.vue';
import ApplianceSearch from '@/Components/Solar/ApplianceSearch.vue';
import ApplianceRow from '@/Components/Solar/ApplianceRow.vue';
import SetupForm from '@/Components/Solar/SetupForm.vue';
import ResultSummary from '@/Components/Solar/ResultSummary.vue';
import StickyTotals from '@/Components/Solar/StickyTotals.vue';
import { useSolarCalculator } from '@/Composables/useSolarCalculator';
import { router, usePage } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    presets: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    constants: {
        type: Object,
        default: () => ({}),
    },
    packages: {
        type: Array,
        default: () => [],
    },
    seo: {
        type: Object,
        default: () => ({}),
    },
    savedCalculation: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const companyWhatsApp = computed(() => page.props.support?.phone_whatsapp || page.props.site?.whatsapp || '2348031234567');

const currentStep = ref(1);
const calculationUuid = ref(props.savedCalculation?.uuid || '');
const shareUrl = ref(props.savedCalculation?.uuid ? `${window.location.origin}/solar-calculator/r/${props.savedCalculation.uuid}` : '');
const showShareModal = ref(false);
const copySuccess = ref(false);
const isSaving = ref(false);

const {
    appliances,
    setup,
    results,
    addAppliance,
    removeAppliance,
    clearAll,
} = useSolarCalculator([], {}, props.constants);

// Handle step navigation
const nextStep = () => {
    if (currentStep.value < 3) {
        currentStep.value += 1;
        window.scrollTo({ top: 120, behavior: 'smooth' });
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value -= 1;
        window.scrollTo({ top: 120, behavior: 'smooth' });
    }
};

const goToStep = (step) => {
    currentStep.value = step;
    window.scrollTo({ top: 120, behavior: 'smooth' });
};

// Add preset click handler
const onAddPreset = (preset) => {
    addAppliance({
        preset_id: preset.id,
        name: preset.name,
        watts: preset.watts,
        qty: 1,
        hours: preset.default_hours || 6,
        surge: preset.surge_multiplier || 1.0,
        is_resistive: Boolean(preset.is_resistive),
        icon: preset.icon || 'bolt',
    });
};

// Add custom click handler
const onAddCustom = (custom) => {
    addAppliance(custom);
};

// Save calculation to server for persistence & shareable URL
const saveCalculation = async () => {
    isSaving.value = true;
    try {
        const response = await fetch('/solar-calculator/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                appliances: appliances.value,
                backup_hours: setup.value.backup_hours,
                goal: setup.value.goal,
                battery_type: setup.value.battery_type,
                zone: setup.value.zone,
                days_of_autonomy: setup.value.days_of_autonomy,
                panel_watts: setup.value.panel_watts,
                diversity_factor: setup.value.diversity_factor,
            }),
        });

        if (response.ok) {
            const data = await response.json();
            calculationUuid.value = data.uuid;
            shareUrl.value = data.share_url;
            showShareModal.value = true;
        }
    } catch (e) {
        // Fallback: copy local url
        shareUrl.value = window.location.href;
        showShareModal.value = true;
    } finally {
        isSaving.value = false;
    }
};

const copyShareLink = async () => {
    try {
        await navigator.clipboard.writeText(shareUrl.value);
        copySuccess.value = true;
        setTimeout(() => {
            copySuccess.value = false;
        }, 3000);
    } catch (e) {
        // clipboard unavailable
    }
};

// Redirect to quote form prefilled with calculation uuid and solar service
const onRequestQuote = (matchedPkg) => {
    router.visit('/request-quote', {
        data: {
            service_type: 'solar',
            calculation_uuid: calculationUuid.value || undefined,
            product_id: matchedPkg ? matchedPkg.id : undefined,
            budget_range: matchedPkg ? `₦${(matchedPkg.price / 100).toLocaleString()}` : undefined,
        },
    });
};

// FAQs for accordion & SEO
const faqs = [
    {
        q: 'How do I know what size inverter I need for my home in Nigeria?',
        a: 'Your inverter size depends on the total continuous wattage of all appliances running simultaneously, plus safety headroom (25%) and start-up surges for compressors or pumps. For example, a home running LED bulbs, fans, a TV, and a medium refrigerator typically operates reliably on a 2.5kVA or 3.5kVA pure sine wave inverter.',
    },
    {
        q: 'What is the difference between Lithium (LiFePO4) and Tubular batteries?',
        a: 'Lithium LiFePO4 batteries allow an 80% to 90% usable depth of discharge, charge within 2 to 3 hours, and last over 10 years (6,000+ cycles). Tubular deep-cycle batteries are cheaper upfront but only allow 50% depth of discharge, take 8 to 10 hours to recharge, and last 2 to 3 years under typical Nigerian conditions.',
    },
    {
        q: 'Can a solar inverter system power an air conditioner (AC)?',
        a: 'Yes! A 1HP or 1.5HP Inverter Air Conditioner draws between 750W and 1,200W once stabilized and requires a system rated at 3.5kVA to 5kVA with at least 5kWh lithium storage and 2kW+ solar panel array for daytime operation.',
    },
    {
        q: 'Why should electric irons and kettles be managed carefully on solar?',
        a: 'Electric irons (1,000W–1,500W) and kettles (1,500W–2,200W) are resistive heating elements that pull immense continuous current directly from your battery bank. Using them when sunlight is strong allows solar panels to power them directly, preserving battery life for night use.',
    },
    {
        q: 'Does Zayin Technologies install systems outside Lagos and Delta State?',
        a: 'Yes. We are headquartered in Abraka, Ethiope East, Delta State, and operate nationwide across all 36 States and the FCT with certified engineers and insured hardware delivery.',
    },
];

const openFaq = ref(null);
const toggleFaq = (idx) => {
    openFaq.value = openFaq.value === idx ? null : idx;
};
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen pb-32">
        <!-- Hero Header -->
        <section class="bg-gradient-to-b from-navy-950 via-navy-900 to-navy-950 text-white py-12 lg:py-16 relative overflow-hidden border-b border-slate-800">
            <div class="absolute inset-0 bg-[radial-gradient(#F59E0B_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-3 border border-amber-500/30">
                    <font-awesome-icon icon="solar-panel" />
                    Free Sizing Tool for Nigerian Homes &amp; Shops
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight max-w-4xl mx-auto leading-tight">
                    Solar Calculator: How Much Solar Do You Need in Nigeria?
                </h1>
                <p class="mt-4 text-slate-300 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
                    Select your appliances, pick your backup hours, and get an instantaneous, certified recommendation for your inverter size, lithium battery bank, and solar panels.
                </p>

                <!-- 3-Step Wizard Navigation Pills -->
                <div class="mt-8 inline-flex items-center p-1.5 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl max-w-md w-full sm:w-auto">
                    <button
                        type="button"
                        @click="goToStep(1)"
                        :class="[
                            'flex-1 sm:flex-initial px-4 sm:px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2',
                            currentStep === 1
                                ? 'bg-amber-500 text-navy-950 shadow-md'
                                : 'text-slate-400 hover:text-white'
                        ]"
                    >
                        <span>① Appliances</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-navy-950/20" v-if="appliances.length > 0">
                            {{ appliances.length }}
                        </span>
                    </button>

                    <button
                        type="button"
                        @click="goToStep(2)"
                        :class="[
                            'flex-1 sm:flex-initial px-4 sm:px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2',
                            currentStep === 2
                                ? 'bg-amber-500 text-navy-950 shadow-md'
                                : 'text-slate-400 hover:text-white'
                        ]"
                    >
                        <span>② Setup</span>
                    </button>

                    <button
                        type="button"
                        @click="goToStep(3)"
                        :class="[
                            'flex-1 sm:flex-initial px-4 sm:px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-2',
                            currentStep === 3
                                ? 'bg-amber-500 text-navy-950 shadow-md'
                                : 'text-slate-400 hover:text-white'
                        ]"
                    >
                        <span>③ Result</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Main Tool Container -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-20">
            <!-- STEP 1: APPLIANCES -->
            <div v-show="currentStep === 1" class="space-y-6 animate-fade-in">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-2">
                                Step 1 of 3
                            </span>
                            <h2 class="text-2xl font-black text-navy-950 tracking-tight">Select Appliances in Your Property</h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                Add lighting, fans, televisions, refrigeration, computing, or custom equipment.
                            </p>
                        </div>

                        <button
                            v-if="appliances.length > 0"
                            type="button"
                            @click="clearAll"
                            class="text-xs text-rose-600 hover:text-rose-700 font-bold self-start sm:self-auto hover:underline"
                        >
                            Clear All ({{ appliances.length }})
                        </button>
                    </div>

                    <!-- Preset Chips -->
                    <PresetChips :presets="presets" @add="onAddPreset" />

                    <!-- Search & Custom Appliance trigger -->
                    <ApplianceSearch
                        :presets="presets"
                        :categories="categories"
                        @add-preset="onAddPreset"
                        @add-custom="onAddCustom"
                    />

                    <!-- Active Appliances List -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                                Active Load Profile ({{ appliances.length }} Items)
                            </h3>
                            <span class="text-xs font-semibold text-slate-500">
                                Total: {{ results.energy.peakW }} W running load
                            </span>
                        </div>

                        <div v-if="appliances.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <ApplianceRow
                                v-for="(item, idx) in appliances"
                                :key="item.id || idx"
                                :item="item"
                                :index="idx"
                                @remove="removeAppliance"
                            />
                        </div>

                        <div v-else class="p-12 rounded-3xl border-2 border-dashed border-slate-200 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto">
                                <font-awesome-icon icon="plug" />
                            </div>
                            <h4 class="font-bold text-navy-950 text-sm">No Appliances Added Yet</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                                Tap any preset chip above or use the search box to begin building your electrical load schedule.
                            </p>
                        </div>
                    </div>

                    <!-- Navigation to Step 2 -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button
                            type="button"
                            @click="nextStep"
                            :disabled="appliances.length === 0"
                            class="px-8 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 disabled:opacity-40 text-navy-950 font-black text-xs uppercase tracking-wider transition shadow-md flex items-center gap-2 active:scale-95"
                        >
                            <span>Configure Backup &amp; Location (Step 2)</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 2: SETUP & GOAL -->
            <div v-show="currentStep === 2" class="space-y-6 animate-fade-in">
                <SetupForm :setup="setup" />

                <!-- Navigation between steps -->
                <div class="flex justify-between items-center">
                    <button
                        type="button"
                        @click="prevStep"
                        class="px-6 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs uppercase tracking-wider transition border border-slate-200 active:scale-95"
                    >
                        &larr; Back to Appliances
                    </button>
                    <button
                        type="button"
                        @click="nextStep"
                        class="px-8 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-black text-xs uppercase tracking-wider transition shadow-md active:scale-95 flex items-center gap-2"
                    >
                        <span>Calculate Recommended System (Step 3)</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>

            <!-- STEP 3: RESULTS -->
            <div v-show="currentStep === 3" class="space-y-6 animate-fade-in">
                <ResultSummary
                    :results="results"
                    :setup="setup"
                    :calculation-uuid="calculationUuid"
                    :share-url="shareUrl"
                    :packages="packages"
                    :company-whats-app="companyWhatsApp"
                    @save="saveCalculation"
                    @request-quote="onRequestQuote"
                    @edit-appliances="goToStep(1)"
                />
            </div>
        </main>

        <!-- Sticky Totals Bar (Always Active) -->
        <StickyTotals
            :current-step="currentStep"
            :peak-w="results.energy.peakW"
            :daily-wh="results.energy.dailyWh"
            :appliance-count="appliances.length"
            @next="nextStep"
            @prev="prevStep"
            @go-to-step="goToStep"
        />

        <!-- SEO Longform Content & FAQs Section (§6) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20 pt-16 border-t border-slate-200">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Left 7 cols: Rich Educational Guide -->
                <div class="lg:col-span-7 space-y-6 text-slate-700 text-sm leading-relaxed">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-full border border-amber-200 inline-block">
                        Engineering Guide
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-navy-950 tracking-tight leading-tight">
                        How to Size a Solar &amp; Inverter System for a Nigerian Home
                    </h2>
                    <p>
                        Determining the appropriate solar power setup for your property requires more than merely guessing based on bedroom count. In Nigeria, erratic grid voltage and prolonged power blackouts demand a meticulous calculation based on running load, inductive startup surge, and daytime solar replenishment.
                    </p>

                    <h3 class="text-lg font-bold text-navy-950 mt-4">1. Understanding Continuous Power (Watts) vs Apparent Power (kVA)</h3>
                    <p>
                        Inverter capacity is measured in <strong>kVA (kilo-volt-amperes)</strong>, which accounts for the power factor of household appliances (typically 0.8 in alternating current systems). A 3.5kVA inverter delivers approximately 2,800 to 3,000 Watts continuous power. Adding a 25% safety margin prevents premature inverter shutdowns and component degradation during warm tropical afternoons.
                    </p>

                    <h3 class="text-lg font-bold text-navy-950 mt-4">2. Why Lithium Iron Phosphate (LiFePO4) is the Gold Standard</h3>
                    <p>
                        While older lead-acid tubular batteries cost less at first, their strict 50% Depth of Discharge (DoD) requires twice as many units to deliver the same runtime. Furthermore, lead-acid batteries degrade rapidly if left partially charged during prolonged rains. Grade-A LiFePO4 lithium batteries allow 80% to 90% daily discharge without memory effect and provide 6,000+ lifecycle charges—easily lasting over a decade.
                    </p>

                    <h3 class="text-lg font-bold text-navy-950 mt-4">3. Regional Sunlight in Nigeria (Peak Sun Hours)</h3>
                    <p>
                        Solar panel arrays must be sized according to local irradiance:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-xs text-slate-600">
                        <li><strong>Northern Nigeria (North-West &amp; North-East):</strong> Receives an average of 5.5 Peak Sun Hours daily with minimal cloud cover.</li>
                        <li><strong>Middle Belt (North-Central):</strong> Receives approximately 5.0 Peak Sun Hours daily.</li>
                        <li><strong>South-West (Lagos, Ogun, Oyo):</strong> Receives approximately 4.5 Peak Sun Hours daily due to coastal cloud dispersion.</li>
                        <li><strong>South-South &amp; South-East (Niger Delta):</strong> Averages 4.2 Peak Sun Hours daily, demanding high-efficiency monocrystalline half-cut modules to harvest diffused light during rainy months.</li>
                    </ul>
                </div>

                <!-- Right 5 cols: Interactive FAQ Accordion -->
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-full border border-amber-200 inline-block">
                        Frequently Asked Questions
                    </span>
                    <h3 class="text-xl font-black text-navy-950">Solar Installation FAQs</h3>

                    <div class="space-y-3 mt-4">
                        <div
                            v-for="(faq, i) in faqs"
                            :key="i"
                            class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm"
                        >
                            <button
                                type="button"
                                @click="toggleFaq(i)"
                                class="w-full p-4 text-left font-bold text-navy-950 text-xs sm:text-sm flex items-center justify-between gap-3 hover:bg-slate-50 transition"
                            >
                                <span>{{ faq.q }}</span>
                                <font-awesome-icon
                                    :icon="openFaq === i ? 'chevron-up' : 'chevron-down'"
                                    class="text-amber-500 text-xs shrink-0"
                                />
                            </button>
                            <div v-show="openFaq === i" class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                                {{ faq.a }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Share Result Modal -->
        <div
            v-if="showShareModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/70 backdrop-blur-sm animate-fade-in"
        >
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 text-center space-y-4 relative">
                <button
                    type="button"
                    @click="showShareModal = false"
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm transition"
                >
                    &times;
                </button>

                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto">
                    <font-awesome-icon icon="circle-check" />
                </div>

                <h3 class="text-xl font-black text-navy-950">Calculation Saved!</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Your customized system specification has been archived. Anyone with this unique link can review your exact electrical load schedule and sizing recommendation.
                </p>

                <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-xs font-mono text-slate-700 break-all select-all">
                    {{ shareUrl }}
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        @click="copyShareLink"
                        class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-navy-950 font-black text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center gap-2"
                    >
                        <font-awesome-icon :icon="copySuccess ? 'check' : 'copy'" />
                        <span>{{ copySuccess ? 'Copied to Clipboard!' : 'Copy Shareable Link' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
