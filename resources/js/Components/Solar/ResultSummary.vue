<script setup>
import { computed } from 'vue';
import ResultCard from './ResultCard.vue';
import CalculatorWarnings from './CalculatorWarnings.vue';
import PackageSuggestions from './PackageSuggestions.vue';

const props = defineProps({
    results: {
        type: Object,
        required: true,
    },
    setup: {
        type: Object,
        required: true,
    },
    calculationUuid: {
        type: String,
        default: '',
    },
    shareUrl: {
        type: String,
        default: '',
    },
    packages: {
        type: Array,
        default: () => [],
    },
    companyWhatsApp: {
        type: String,
        default: '2348031234567',
    },
});

const emit = defineEmits(['save', 'requestQuote', 'editAppliances']);

// Pre-fill WhatsApp message per §7
const whatsappMessage = computed(() => {
    const dailyKwh = (props.results.energy.dailyWh / 1000).toFixed(2);
    const invKva = props.results.inverter.kva;
    const battKwh = (props.results.battery.bankWh / 1000).toFixed(1);
    const battType = props.results.battery.type;
    const solarKw = (props.results.solar.arrayW / 1000).toFixed(2);
    const shareLink = props.shareUrl || window.location.href;

    const text = `Hello Zayin Technologies, I used your solar load calculator. Daily use: ${dailyKwh} kWh. Suggested: ${invKva}kVA inverter, ${battKwh}kWh ${battType} battery, ${solarKw}kW solar array. Result details: ${shareLink}. Please advise on installation.`;
    return `https://wa.me/${props.companyWhatsApp.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(text)}`;
});
</script>

<template>
    <div class="space-y-8 animate-fade-in">
        <!-- Step Header & Quick Metrics -->
        <div class="bg-navy-950 text-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-2 border border-amber-500/30">
                            Recommended Specification
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            Your Tailored Solar & Battery Sizing
                        </h2>
                    </div>

                    <button
                        type="button"
                        @click="emit('editAppliances')"
                        class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-bold transition border border-white/10 active:scale-95 self-start sm:self-auto"
                    >
                        &larr; Modify Appliances
                    </button>
                </div>

                <!-- 4 Quick Top Metrics -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-slate-800">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Daily Energy Needed</div>
                        <div class="text-2xl sm:text-3xl font-black text-amber-400 mt-1">
                            {{ (results.energy.dailyWh / 1000).toFixed(2) }} <span class="text-xs text-slate-300 font-normal">kWh</span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">≈ {{ results.energy.monthlyKWh }} kWh/month</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Continuous Running Load</div>
                        <div class="text-2xl sm:text-3xl font-black text-white mt-1">
                            {{ results.energy.peakW }} <span class="text-xs text-slate-300 font-normal">W</span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Surge Peak: {{ results.energy.surgePeakW }} W</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Target Outage Duration</div>
                        <div class="text-2xl sm:text-3xl font-black text-emerald-400 mt-1">
                            {{ setup.backup_hours }} <span class="text-xs text-slate-300 font-normal">hours</span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">{{ setup.goal === 'full_solar' ? 'Full Off-Grid Mode' : 'Outage Backup' }}</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Architecture Bus</div>
                        <div class="text-2xl sm:text-3xl font-black text-sky-400 mt-1">
                            {{ results.inverter.voltage }} <span class="text-xs text-slate-300 font-normal">Volts DC</span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Safe DC Bus Sizing</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warnings Component -->
        <CalculatorWarnings :warnings="results.warnings" />

        <!-- 4 Sizing Component Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1. Inverter Card -->
            <ResultCard
                title="Inverter Capacity"
                :value="`${results.inverter.kva} kVA`"
                :subvalue="`Pure Sine Wave · ${results.inverter.voltage}V Architecture`"
                icon="bolt"
                :badge="results.inverter.upsizedForBattery ? 'Upsized For Battery' : 'Continuous Safe Fit'"
                :explanation="results.inverter.note || 'Handles continuous power demand plus peak inductive motor startup surges with 25% safety margin.'"
                :formula="`Required VA = (${results.energy.peakW}W × 1.25) / 0.8 = ${results.inverter.continuousVA} VA`"
                :calculation-steps="[
                    `Continuous load: ${results.energy.peakW} W with 1.25× safety margin = ${Math.round(results.energy.peakW * 1.25)} W`,
                    `Apparent Power at 0.8 power factor = ${results.inverter.continuousVA} VA`,
                    `Motor surge check: Peak ${results.energy.surgePeakW} W within 2.0× inverter surge buffer: ${results.inverter.surgeOk ? 'Passed' : 'Upsized'}`,
                    results.inverter.upsizedForBattery ? 'Upsized to match high battery bank voltage' : 'Matches native battery bank voltage',
                ]"
            />

            <!-- 2. Battery Bank Card -->
            <ResultCard
                title="Battery Storage Bank"
                :value="`${(results.battery.bankWh / 1000).toFixed(1)} kWh`"
                :subvalue="`≈ ${results.battery.bankAh} Ah @ ${results.inverter.voltage}V · ${results.battery.unitsNeeded}x Units`"
                icon="car-battery"
                :badge="`${Math.round(results.battery.dod * 100)}% Usable Depth of Discharge`"
                :explanation="`Guarantees ${setup.backup_hours} hours continuous runtime without grid supply at 90% inverter efficiency.`"
                :formula="`Bank Wh = (${results.energy.windowWh}Wh × 1 day / 0.90) / ${results.battery.dod} DoD = ${results.battery.bankWh} Wh`"
                :calculation-steps="[
                    `Outage window load: ${results.energy.windowWh} Wh`,
                    `Usable energy needed after inverter losses (90%): ${results.battery.usableWhNeeded} Wh`,
                    `Physical bank at ${Math.round(results.battery.dod * 100)}% DoD: ${results.battery.bankWh} Wh (${results.battery.bankAh} Ah @ ${results.inverter.voltage}V)`,
                    `Recommended configuration: ${results.battery.unitsNeeded}x ${results.battery.unitLabel}`,
                ]"
            />

            <!-- 3. Solar Array Card -->
            <ResultCard
                title="Solar Panel Array"
                :value="`${results.solar.arrayW} Watts`"
                :subvalue="`${results.solar.panels}x ${results.solar.panelW}W Monocrystalline Panels`"
                icon="solar-panel"
                badge="Tier-1 High Yield"
                :explanation="`Generates approx. ${(results.solar.estDailyGenWh / 1000).toFixed(1)} kWh per sunny day based on ${results.solar.peakSunHours} peak sun hours in your zone.`"
                :formula="`Array Watts = Daily Wh / (${results.solar.peakSunHours} PSH × 0.75 derate) = ${results.solar.requiredW} W → ${results.solar.panels} Panels`"
                :calculation-steps="[
                    `Target daily generation: ${(results.energy.dailyWh / 1000).toFixed(2)} kWh`,
                    `System derate losses (dust, heat, wiring): 25% (0.75 performance ratio)`,
                    `Regional sunlight: ${results.solar.peakSunHours} Peak Sun Hours / day`,
                    `Number of panels: ceil(${results.solar.requiredW} W / ${results.solar.panelW} W) = ${results.solar.panels} panels (${results.solar.arrayW} W total)`,
                ]"
            />

            <!-- 4. MPPT Charge Controller Card -->
            <ResultCard
                title="Charge Controller (MPPT)"
                :value="`${results.controller.recommendedA} Amps`"
                :subvalue="`Smart MPPT Tracker (${results.controller.mpptAmps}A calculated)`"
                icon="microchip"
                badge="Max Power Point"
                explanation="Extracts maximum solar wattage from the panel array to safely charge the battery bank with rapid absorption cycles."
                :formula="`MPPT Amps = (${results.solar.arrayW}W / ${results.inverter.voltage}V) × 1.25 = ${results.controller.mpptAmps} A`"
                :calculation-steps="[
                    `Array peak DC power: ${results.solar.arrayW} W at ${results.inverter.voltage}V system bus`,
                    `Charge current: ${Math.round((results.solar.arrayW / results.inverter.voltage) * 10) / 10} A`,
                    `NEC 1.25× continuous current safety margin: ${results.controller.mpptAmps} A`,
                    `Selected standard controller rating: ${results.controller.recommendedA} A MPPT`,
                ]"
            />
        </div>

        <!-- Action CTAs: WhatsApp, Save & Share, PDF, Quote -->
        <div class="bg-slate-50 rounded-3xl p-6 sm:p-8 border border-slate-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <h3 class="text-lg font-black text-navy-950">Next Steps & Engineering Support</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Share this specification with our engineers or download your official estimate sheet.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2.5">
                    <!-- WhatsApp Share -->
                    <a
                        :href="whatsappMessage"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider transition shadow-md flex items-center gap-2 active:scale-95"
                    >
                        <font-awesome-icon :icon="['fab', 'whatsapp']" class="text-base" />
                        <span>Chat With Engineer on WhatsApp</span>
                    </a>

                    <!-- Save & Share Link -->
                    <button
                        type="button"
                        @click="emit('save')"
                        class="px-5 py-3 rounded-xl bg-navy-950 hover:bg-navy-900 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center gap-2 active:scale-95"
                    >
                        <font-awesome-icon icon="share-nodes" />
                        <span>Save &amp; Get Share Link</span>
                    </button>

                    <!-- PDF Download (if UUID exists) -->
                    <a
                        v-if="calculationUuid"
                        :href="`/solar-calculator/r/${calculationUuid}/pdf`"
                        target="_blank"
                        class="px-4 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs uppercase tracking-wider transition border border-slate-300 flex items-center gap-2 active:scale-95"
                    >
                        <font-awesome-icon icon="file-pdf" class="text-rose-600" />
                        <span>PDF Report</span>
                    </a>
                </div>
            </div>

            <!-- Mandatory Legal & Engineering Disclaimer (§10) -->
            <div class="pt-6 text-xs text-slate-500 leading-relaxed flex items-start gap-3">
                <font-awesome-icon icon="shield-halved" class="text-amber-500 text-base shrink-0 mt-0.5" />
                <div>
                    <strong class="text-slate-700 font-bold">Official Engineering Disclaimer:</strong>
                    This calculation is an estimate based on the appliances, hours, and typical equipment values you entered. Actual system performance varies with appliance age, real nameplate power factors, seasonal weather, roof tilt/shading, wire run distances, and brand specifications. A certified Zayin Technologies field engineer will conduct a physical site survey before your final commissioning.
                </div>
            </div>
        </div>

        <!-- Matched Packages Section -->
        <PackageSuggestions
            :packages="packages"
            :calculation-uuid="calculationUuid"
            @request-quote="emit('requestQuote', $event)"
        />
    </div>
</template>
