<?php

namespace App\DataTransferObjects\Solar;

class CalculatorInput
{
    /**
     * @param array<int, array{
     *   name: string,
     *   watts: float|int,
     *   qty: int,
     *   hours: float|int,
     *   surge?: float|int,
     *   is_resistive?: bool,
     *   preset_id?: int|null
     * }> $appliances
     */
    public function __construct(
        public array $appliances,
        public float $backupHours = 12.0,
        public string $goal = 'backup', // 'backup' or 'full_solar'
        public string $batteryType = 'lithium', // 'lithium' or 'tubular'
        public string $zone = 'south_west', // 'north_west', 'north_east', 'north_central', 'south_west', 'south_east', 'south_south'
        public int $daysOfAutonomy = 1,
        public int $panelWatts = 550,
        public float $diversityFactor = 1.0,
        public ?float $customPeakSunHours = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            appliances: array_map(function ($item) {
                return [
                    'name' => (string) ($item['name'] ?? 'Appliance'),
                    'watts' => (float) ($item['watts'] ?? 0),
                    'qty' => (int) ($item['qty'] ?? 1),
                    'hours' => (float) ($item['hours'] ?? 0),
                    'surge' => isset($item['surge']) ? (float) $item['surge'] : 1.0,
                    'is_resistive' => !empty($item['is_resistive']),
                    'preset_id' => isset($item['preset_id']) ? (int) $item['preset_id'] : null,
                ];
            }, $data['appliances'] ?? []),
            backupHours: (float) ($data['backup_hours'] ?? 12.0),
            goal: (string) ($data['goal'] ?? 'backup'),
            batteryType: (string) ($data['battery_type'] ?? 'lithium'),
            zone: (string) ($data['zone'] ?? 'south_west'),
            daysOfAutonomy: (int) ($data['days_of_autonomy'] ?? 1),
            panelWatts: (int) ($data['panel_watts'] ?? 550),
            diversityFactor: (float) ($data['diversity_factor'] ?? 1.0),
            customPeakSunHours: isset($data['custom_peak_sun_hours']) ? (float) $data['custom_peak_sun_hours'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'appliances' => $this->appliances,
            'backup_hours' => $this->backupHours,
            'goal' => $this->goal,
            'battery_type' => $this->batteryType,
            'zone' => $this->zone,
            'days_of_autonomy' => $this->daysOfAutonomy,
            'panel_watts' => $this->panelWatts,
            'diversity_factor' => $this->diversityFactor,
            'custom_peak_sun_hours' => $this->customPeakSunHours,
        ];
    }
}
