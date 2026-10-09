export const DEFAULT_CONSTANTS = {
    safety_factor: 1.25,
    power_factor: 0.80,
    inverter_efficiency: 0.90,
    system_derate: 0.75,
    dod_lithium: 0.80,
    dod_tubular: 0.50,
    surge_capacity_factor: 2.0,
    default_panel_watts: 550,
    panel_watts_options: [400, 450, 550],
    diversity_factor: 1.0,
    days_of_autonomy: 1,
    lithium_unit_wh: 5120,
    tubular_unit_wh: 2400,
};

export const INVERTER_CATALOG = [
    { kva: 1.0, voltage: 12 },
    { kva: 1.5, voltage: 12 },
    { kva: 2.5, voltage: 24 },
    { kva: 3.5, voltage: 24 },
    { kva: 5.0, voltage: 48 },
    { kva: 7.5, voltage: 48 },
    { kva: 10.0, voltage: 48 },
];

export const CONTROLLER_RATINGS = [30, 40, 50, 60, 80, 100, 120];

export const PSH_BY_ZONE: Record<string, number> = {
    north_west: 5.5,
    north_east: 5.5,
    north_central: 5.0,
    south_west: 4.5,
    south_east: 4.2,
    south_south: 4.2,
};

export const ZONE_LABELS: Record<string, string> = {
    south_west: 'South-West (Lagos, Ogun, Oyo, Osun, Ondo, Ekiti) ~ 4.5h',
    south_south: 'South-South / Niger Delta (Delta, Edo, Rivers, Akwa Ibom) ~ 4.2h',
    south_east: 'South-East (Enugu, Anambra, Imo, Abia, Ebonyi) ~ 4.2h',
    north_central: 'North-Central / FCT (Abuja, Plateau, Benue, Kwara, Niger) ~ 5.0h',
    north_west: 'North-West (Kano, Kaduna, Sokoto, Katsina, Zamfara) ~ 5.5h',
    north_east: 'North-East (Borno, Adamawa, Bauchi, Gombe, Taraba) ~ 5.5h',
};

export const WARNING_MESSAGES: Record<string, { title: string; desc: string; type: 'warning' | 'info' }> = {
    resistive_load: {
        title: 'High Heating Load Detected',
        desc: 'Heating appliances (electric iron, kettle, cooker) draw heavy wattage and drain batteries rapidly. Consider using gas or running them while sunlight is strong.',
        type: 'warning',
    },
    ac_load: {
        title: 'Air Conditioning Load Included',
        desc: 'Air conditioners require robust inverter capacity and battery storage. Inverter-type ACs use up to 40% less energy than standard models.',
        type: 'info',
    },
    motor_surge: {
        title: 'Inductive Motor Start-Up Surge',
        desc: 'Compressors and pumps (freezers, fridges, borehole pumps) draw 3× to 5× their running watts at startup. We have accounted for this in your inverter headroom.',
        type: 'info',
    },
    large_system: {
        title: 'Heavy Commercial / Large Mansion Load (>10 kVA)',
        desc: 'Your load exceeds standard residential sizes. We recommend a multi-inverter parallel cluster or bespoke microgrid. Request an engineering consultation.',
        type: 'warning',
    },
    window_clipped: {
        title: 'Backup Window Notice',
        desc: 'Some appliances have running hours higher than your chosen outage window. Only the backup hours are counted towards battery capacity.',
        type: 'info',
    },
    very_small_load: {
        title: 'Minimal Load Detected',
        desc: 'Your total daily consumption is under 300 Wh. Double-check your appliance quantities and hours.',
        type: 'info',
    },
};
