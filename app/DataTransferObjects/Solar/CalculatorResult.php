<?php

namespace App\DataTransferObjects\Solar;

class CalculatorResult
{
    /**
     * @param array{
     *   dailyWh: float,
     *   monthlyKWh: float,
     *   peakW: float,
     *   surgePeakW: float,
     *   surgeExtraW: float,
     *   windowWh: float
     * } $energy
     * @param array{
     *   kva: float,
     *   voltage: int,
     *   continuousVA: float,
     *   surgeVA: float,
     *   surgeOk: bool,
     *   upsizedForBattery: bool,
     *   note?: string|null
     * } $inverter
     * @param array{
     *   usableWhNeeded: float,
     *   bankWh: float,
     *   bankAh: float,
     *   type: string,
     *   dod: float,
     *   unitsNeeded: int,
     *   unitLabel: string
     * } $battery
     * @param array{
     *   requiredW: float,
     *   panelW: int,
     *   panels: int,
     *   arrayW: int,
     *   estDailyGenWh: float,
     *   peakSunHours: float
     * } $solar
     * @param array{
     *   mpptAmps: float,
     *   recommendedA: int
     * } $controller
     * @param array<int, string> $warnings
     * @param array<int, array<string, mixed>> $packages
     */
    public function __construct(
        public array $energy,
        public array $inverter,
        public array $battery,
        public array $solar,
        public array $controller,
        public array $warnings = [],
        public array $packages = [],
        public int $settingsVersion = 1,
    ) {}

    public function toArray(): array
    {
        return [
            'energy' => $this->energy,
            'inverter' => $this->inverter,
            'battery' => $this->battery,
            'solar' => $this->solar,
            'controller' => $this->controller,
            'warnings' => $this->warnings,
            'packages' => $this->packages,
            'settingsVersion' => $this->settingsVersion,
        ];
    }
}
