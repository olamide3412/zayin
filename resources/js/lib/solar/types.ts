export interface ApplianceItem {
    id?: string;
    preset_id?: number | null;
    name: string;
    watts: number;
    qty: number;
    hours: number;
    surge?: number;
    is_resistive?: boolean;
    icon?: string;
}

export interface SetupOptions {
    backup_hours: number;
    goal: 'backup' | 'full_solar';
    battery_type: 'lithium' | 'tubular';
    zone: string;
    days_of_autonomy: number;
    panel_watts: number;
    diversity_factor: number;
    custom_peak_sun_hours?: number | null;
}

export interface EnergyResult {
    dailyWh: number;
    monthlyKWh: number;
    peakW: number;
    surgePeakW: number;
    surgeExtraW: number;
    windowWh: number;
}

export interface InverterResult {
    kva: number;
    voltage: number;
    continuousVA: number;
    surgeVA: number;
    surgeOk: boolean;
    upsizedForBattery: boolean;
    note?: string | null;
}

export interface BatteryResult {
    usableWhNeeded: number;
    bankWh: number;
    bankAh: number;
    type: 'lithium' | 'tubular';
    dod: number;
    unitsNeeded: number;
    unitLabel: string;
}

export interface SolarResult {
    requiredW: number;
    panelW: number;
    panels: number;
    arrayW: number;
    estDailyGenWh: number;
    peakSunHours: number;
}

export interface ControllerResult {
    mpptAmps: number;
    recommendedA: number;
}

export interface MatchedPackage {
    id: number;
    name: string;
    slug: string;
    price: number;
    compare_at_price: number | null;
    image: string | null;
    tier: 'good' | 'better' | 'best';
    tier_label: string;
    specs: Record<string, any>;
    summary: string | null;
}

export interface CalculatorOutput {
    energy: EnergyResult;
    inverter: InverterResult;
    battery: BatteryResult;
    solar: SolarResult;
    controller: ControllerResult;
    warnings: string[];
    packages?: MatchedPackage[];
    settingsVersion: number;
}

export interface AppliancePresetData {
    id: number;
    category_id: number;
    name: string;
    slug: string;
    watts: number;
    surge_multiplier: number;
    default_hours: number;
    icon?: string;
    is_resistive: boolean;
    sort_order: number;
    notes?: string;
}

export interface CategoryData {
    id: number;
    name: string;
    slug: string;
    icon?: string;
    sort_order: number;
}
