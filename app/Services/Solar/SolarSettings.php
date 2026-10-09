<?php

namespace App\Services\Solar;

use App\Models\SolarCalcSetting;

class SolarSettings
{
    public function __construct(
        public array $constants,
        public array $inverterCatalog,
        public array $controllerRatings,
        public array $pshByZone,
        public int $version = 1,
    ) {}

    public static function load(): self
    {
        $setting = SolarCalcSetting::getActive();

        return new self(
            constants: $setting->constants ?? [],
            inverterCatalog: $setting->inverter_catalog ?? [],
            controllerRatings: $setting->controller_ratings ?? [],
            pshByZone: $setting->psh_by_zone ?? [],
            version: (int) ($setting->version ?? 1),
        );
    }

    public static function default(): self
    {
        return new self(
            constants: [
                'safety_factor' => 1.25,
                'power_factor' => 0.80,
                'inverter_efficiency' => 0.90,
                'system_derate' => 0.75,
                'dod_lithium' => 0.80,
                'dod_tubular' => 0.50,
                'surge_capacity_factor' => 2.0,
                'default_panel_watts' => 550,
                'panel_watts_options' => [400, 450, 550],
                'diversity_factor' => 1.0,
                'days_of_autonomy' => 1,
                'lithium_unit_wh' => 5120,
                'tubular_unit_wh' => 2400,
            ],
            inverterCatalog: [
                ['kva' => 1.0, 'voltage' => 12],
                ['kva' => 1.5, 'voltage' => 12],
                ['kva' => 2.5, 'voltage' => 24],
                ['kva' => 3.5, 'voltage' => 24],
                ['kva' => 5.0, 'voltage' => 48],
                ['kva' => 7.5, 'voltage' => 48],
                ['kva' => 10.0, 'voltage' => 48],
            ],
            controllerRatings: [30, 40, 50, 60, 80, 100, 120],
            pshByZone: [
                'north_west' => 5.5,
                'north_east' => 5.5,
                'north_central' => 5.0,
                'south_west' => 4.5,
                'south_east' => 4.2,
                'south_south' => 4.2,
            ],
            version: 1,
        );
    }

    public function getConstant(string $key, mixed $default = null): mixed
    {
        return $this->constants[$key] ?? $default;
    }

    public function getPSH(string $zone, ?float $custom = null): float
    {
        if ($custom !== null && $custom > 0) {
            return $custom;
        }

        return (float) ($this->pshByZone[$zone] ?? 4.5);
    }
}
