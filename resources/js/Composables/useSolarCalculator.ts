import { ref, computed, watch, onMounted } from 'vue';
import type { ApplianceItem, SetupOptions, CalculatorOutput } from '@/lib/solar/types';
import { DEFAULT_CONSTANTS, INVERTER_CATALOG, CONTROLLER_RATINGS, PSH_BY_ZONE } from '@/lib/solar/constants';

const STORAGE_KEY = 'zayin_solar_calc_draft_v1';

export function useSolarCalculator(
    initialAppliances: ApplianceItem[] = [],
    initialSetup?: Partial<SetupOptions>,
    customConstants?: Record<string, any>
) {
    const constants = { ...DEFAULT_CONSTANTS, ...(customConstants || {}) };

    const appliances = ref<ApplianceItem[]>(
        initialAppliances.length > 0
            ? [...initialAppliances]
            : [
                  { name: 'LED bulb', watts: 10, qty: 6, hours: 5, surge: 1.0, is_resistive: false, icon: 'lightbulb' },
                  { name: 'Ceiling fan', watts: 75, qty: 2, hours: 8, surge: 2.0, is_resistive: false, icon: 'fan' },
                  { name: 'TV LED 43"–55"', watts: 80, qty: 1, hours: 6, surge: 1.0, is_resistive: false, icon: 'tv' },
                  { name: 'Refrigerator (medium)', watts: 150, qty: 1, hours: 10, surge: 3.0, is_resistive: false, icon: 'temperature-low' },
              ]
    );

    const setup = ref<SetupOptions>({
        backup_hours: 12,
        goal: 'backup',
        battery_type: 'lithium',
        zone: 'south_west',
        days_of_autonomy: 1,
        panel_watts: 550,
        diversity_factor: 1.0,
        custom_peak_sun_hours: null,
        ...(initialSetup || {}),
    });

    // Reactive calculations mirroring PHP SolarSizingCalculator
    const results = computed<CalculatorOutput>(() => {
        let totalDailyWh = 0;
        let totalRunningPeakW = 0;
        let windowWh = 0;
        let largestSurgeExtraW = 0;

        let hasResistive = false;
        let hasAC = false;
        let hasMotorSurge = false;
        let hasWindowClipped = false;

        const currentBackupHours = Number(setup.value.backup_hours) || 12;

        appliances.value.forEach((item) => {
            const w = Number(item.watts) || 0;
            const qty = Number(item.qty) || 1;
            const h = Number(item.hours) || 0;
            const m = Number(item.surge) || 1.0;
            const isResistive = Boolean(item.is_resistive);
            const name = (item.name || '').toLowerCase();

            const runningLoad = w * qty;
            const itemDailyWh = runningLoad * h;
            totalDailyWh += itemDailyWh;
            totalRunningPeakW += runningLoad;

            const effectiveWindowHours = Math.min(h, currentBackupHours);
            windowWh += runningLoad * effectiveWindowHours;

            if (h > currentBackupHours) {
                hasWindowClipped = true;
            }

            if (m > 1.0) {
                const surgeExtra = runningLoad * (m - 1.0);
                if (surgeExtra > largestSurgeExtraW) {
                    largestSurgeExtraW = surgeExtra;
                }
            }

            if (isResistive) {
                hasResistive = true;
            }

            if (m >= 3.0) {
                hasMotorSurge = true;
            }

            if (name.includes('air conditioner') || name.includes(' ac') || name.startsWith('ac ') || name.includes('split unit')) {
                hasAC = true;
            }
        });

        const diversityFactor = Math.min(1.0, Math.max(0.1, Number(setup.value.diversity_factor) || 1.0));
        const effectivePeakW = Math.round(totalRunningPeakW * diversityFactor * 10) / 10;
        const surgePeakW = Math.round((effectivePeakW + largestSurgeExtraW) * 10) / 10;
        const monthlyKWh = Math.round((totalDailyWh * 30) / 100) / 10; // (Wh * 30 / 1000)

        // 1. Inverter Sizing
        const safetyFactor = Number(constants.safety_factor) || 1.25;
        const powerFactor = Number(constants.power_factor) || 0.8;
        const surgeCapacityFactor = Number(constants.surge_capacity_factor) || 2.0;

        const continuousVA = (effectivePeakW * safetyFactor) / powerFactor;
        const sortedInverters = [...INVERTER_CATALOG].sort((a, b) => a.kva - b.kva);

        let selectedInverter = sortedInverters[0];
        let surgeOk = true;
        let inverterFound = false;

        for (const inv of sortedInverters) {
            const ratedVA = inv.kva * 1000;
            const continuousOk = ratedVA >= continuousVA;
            const surgeCapacityW = ratedVA * powerFactor * surgeCapacityFactor;
            const surgePassed = surgeCapacityW >= surgePeakW;

            if (continuousOk && surgePassed) {
                selectedInverter = inv;
                inverterFound = true;
                break;
            }
        }

        if (!inverterFound) {
            selectedInverter = sortedInverters[sortedInverters.length - 1];
            surgeOk = false;
        }

        // 2. Battery Sizing
        const autonomyDays = Math.max(1, Number(setup.value.days_of_autonomy) || 1);
        const inverterEfficiency = Number(constants.inverter_efficiency) || 0.9;
        const dod = setup.value.battery_type === 'tubular' ? Number(constants.dod_tubular) || 0.5 : Number(constants.dod_lithium) || 0.8;

        const usableWhNeeded = (windowWh * autonomyDays) / inverterEfficiency;
        const rawBankWh = usableWhNeeded / dod;
        const roundedBankWh = Math.round(rawBankWh);

        // System Voltage by battery bank size
        let systemVoltage = 12;
        if (roundedBankWh <= 2400) {
            systemVoltage = 12;
        } else if (roundedBankWh <= 7200) {
            systemVoltage = 24;
        } else {
            systemVoltage = 48;
        }

        // Reconcile system voltage with Inverter native voltage
        let upsizedForBattery = false;
        if (selectedInverter.voltage < systemVoltage) {
            for (const inv of sortedInverters) {
                if (inv.voltage >= systemVoltage && inv.kva >= selectedInverter.kva) {
                    selectedInverter = inv;
                    upsizedForBattery = true;
                    break;
                }
            }
        }

        const finalSystemVoltage = Math.max(systemVoltage, selectedInverter.voltage);
        const bankAh = Math.round((roundedBankWh / finalSystemVoltage) * 10) / 10;

        let unitsNeeded = 1;
        let unitLabel = '';

        if (setup.value.battery_type === 'tubular') {
            const seriesCount = Math.max(1, Math.floor(finalSystemVoltage / 12));
            const parallelCount = Math.max(1, Math.ceil(bankAh / 200));
            unitsNeeded = seriesCount * parallelCount;
            unitLabel = `${finalSystemVoltage}V ${parallelCount * 200}Ah Tubular Bank (${unitsNeeded}x 12V 200Ah)`;
        } else {
            let unitWh = finalSystemVoltage * 100;
            if (finalSystemVoltage === 24) unitWh = 2560;
            else if (finalSystemVoltage === 48) unitWh = 5120;
            unitsNeeded = Math.max(1, Math.ceil(roundedBankWh / unitWh));
            unitLabel = `${finalSystemVoltage}V 100Ah Lithium LiFePO4`;
        }

        // 3. Solar Array Sizing
        const targetPVWh = setup.value.goal === 'full_solar' ? totalDailyWh : (windowWh * autonomyDays) / inverterEfficiency;

        const psh = setup.value.custom_peak_sun_hours && setup.value.custom_peak_sun_hours > 0
            ? Number(setup.value.custom_peak_sun_hours)
            : PSH_BY_ZONE[setup.value.zone] || 4.5;

        const systemDerate = Number(constants.system_derate) || 0.75;
        const requiredPVW = Math.round(targetPVWh / (psh * systemDerate));
        const panelWatts = Number(setup.value.panel_watts) || Number(constants.default_panel_watts) || 550;
        const panels = Math.max(1, Math.ceil(requiredPVW / panelWatts));
        const arrayW = panels * panelWatts;
        const estDailyGenWh = Math.round(arrayW * psh * systemDerate * 10) / 10;

        // 4. Charge Controller (MPPT)
        const mpptAmps = Math.round(((arrayW / finalSystemVoltage) * 1.25) * 10) / 10;
        const sortedControllers = [...CONTROLLER_RATINGS].sort((a, b) => a - b);
        let recommendedA = sortedControllers[sortedControllers.length - 1];
        for (const rating of sortedControllers) {
            if (rating >= mpptAmps) {
                recommendedA = rating;
                break;
            }
        }

        // 5. Warnings
        const warnings: string[] = [];
        if (hasResistive) warnings.push('resistive_load');
        if (hasAC) warnings.push('ac_load');
        if (hasMotorSurge) warnings.push('motor_surge');
        if (selectedInverter.kva >= 10.0 && continuousVA > 10000) warnings.push('large_system');
        if (hasWindowClipped) warnings.push('window_clipped');
        if (totalDailyWh < 300) warnings.push('very_small_load');

        return {
            energy: {
                dailyWh: Math.round(totalDailyWh * 10) / 10,
                monthlyKWh,
                peakW: effectivePeakW,
                surgePeakW,
                surgeExtraW: Math.round(largestSurgeExtraW * 10) / 10,
                windowWh: Math.round(windowWh * 10) / 10,
            },
            inverter: {
                kva: selectedInverter.kva,
                voltage: finalSystemVoltage,
                continuousVA: Math.round(continuousVA * 10) / 10,
                surgeVA: Math.round((surgePeakW / powerFactor) * 10) / 10,
                surgeOk,
                upsizedForBattery,
                note: upsizedForBattery ? 'Upsized to support the battery bank.' : null,
            },
            battery: {
                usableWhNeeded: Math.round(usableWhNeeded),
                bankWh: roundedBankWh,
                bankAh,
                type: setup.value.battery_type,
                dod,
                unitsNeeded,
                unitLabel,
            },
            solar: {
                requiredW: requiredPVW,
                panelW: panelWatts,
                panels,
                arrayW,
                estDailyGenWh,
                peakSunHours: psh,
            },
            controller: {
                mpptAmps,
                recommendedA,
            },
            warnings,
            settingsVersion: 1,
        };
    });

    // Helper functions
    const addAppliance = (item: Partial<ApplianceItem>) => {
        appliances.value.push({
            id: `app_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`,
            name: item.name || 'Custom Appliance',
            watts: Math.max(1, Number(item.watts) || 100),
            qty: Math.max(1, Number(item.qty) || 1),
            hours: Math.min(24, Math.max(0.1, Number(item.hours) || 4)),
            surge: Number(item.surge) || 1.0,
            is_resistive: Boolean(item.is_resistive),
            icon: item.icon || 'bolt',
            preset_id: item.preset_id || null,
        });
    };

    const removeAppliance = (index: number) => {
        if (index >= 0 && index < appliances.value.length) {
            appliances.value.splice(index, 1);
        }
    };

    const clearAll = () => {
        appliances.value = [];
    };

    // Auto-save to localStorage
    const saveDraft = () => {
        if (typeof window === 'undefined') return;
        try {
            const draft = {
                appliances: appliances.value,
                setup: setup.value,
                savedAt: Date.now(),
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
        } catch (e) {
            // Ignore quota errors safely
        }
    };

    const loadDraft = () => {
        if (typeof window === 'undefined') return;
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            const draft = JSON.parse(raw);
            const sevenDaysMs = 7 * 24 * 60 * 60 * 1000;
            if (draft.savedAt && Date.now() - draft.savedAt < sevenDaysMs) {
                if (Array.isArray(draft.appliances) && draft.appliances.length > 0) {
                    appliances.value = draft.appliances;
                }
                if (draft.setup) {
                    setup.value = { ...setup.value, ...draft.setup };
                }
            }
        } catch (e) {
            // Ignore malformed localStorage
        }
    };

    watch([appliances, setup], () => saveDraft(), { deep: true });

    onMounted(() => {
        loadDraft();
    });

    return {
        appliances,
        setup,
        results,
        addAppliance,
        removeAppliance,
        clearAll,
        saveDraft,
        loadDraft,
    };
}
