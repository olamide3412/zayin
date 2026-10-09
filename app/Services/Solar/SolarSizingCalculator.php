<?php

namespace App\Services\Solar;

use App\DataTransferObjects\Solar\CalculatorInput;
use App\DataTransferObjects\Solar\CalculatorResult;

class SolarSizingCalculator
{
    public function calculate(CalculatorInput $input, SolarSettings $settings): CalculatorResult
    {
        $appliances = $input->appliances;

        $totalDailyWh = 0.0;
        $totalRunningPeakW = 0.0;
        $windowWh = 0.0;
        $largestSurgeExtraW = 0.0;

        $hasResistive = false;
        $hasAC = false;
        $hasMotorSurge = false;
        $hasWindowClipped = false;

        foreach ($appliances as $item) {
            $w = (float) ($item['watts'] ?? 0);
            $qty = (int) ($item['qty'] ?? 1);
            $h = (float) ($item['hours'] ?? 0);
            $m = (float) ($item['surge'] ?? 1.0);
            $isResistive = !empty($item['is_resistive']);
            $name = strtolower($item['name'] ?? '');

            $runningLoad = $w * $qty;
            $itemDailyWh = $runningLoad * $h;
            $totalDailyWh += $itemDailyWh;
            $totalRunningPeakW += $runningLoad;

            // Effective window hours (capped by backupHours)
            $effectiveWindowHours = min($h, $input->backupHours);
            $windowWh += ($runningLoad * $effectiveWindowHours);

            if ($h > $input->backupHours) {
                $hasWindowClipped = true;
            }

            // Surge calculations
            if ($m > 1.0) {
                $surgeExtra = $runningLoad * ($m - 1.0);
                if ($surgeExtra > $largestSurgeExtraW) {
                    $largestSurgeExtraW = $surgeExtra;
                }
            }

            if ($isResistive) {
                $hasResistive = true;
            }

            if ($m >= 3.0) {
                $hasMotorSurge = true;
            }

            if (str_contains($name, 'air conditioner') || str_contains($name, ' ac') || str_starts_with($name, 'ac ') || str_contains($name, 'split unit')) {
                $hasAC = true;
            }
        }

        // Apply diversity factor
        $diversityFactor = (float) $input->diversityFactor;
        if ($diversityFactor <= 0 || $diversityFactor > 1.0) {
            $diversityFactor = 1.0;
        }
        $effectivePeakW = round($totalRunningPeakW * $diversityFactor, 1);
        $surgePeakW = round($effectivePeakW + $largestSurgeExtraW, 1);
        $monthlyKWh = round(($totalDailyWh * 30.0) / 1000.0, 1);

        // ── 1. Inverter Sizing ────────────────────────────────────────────────
        $safetyFactor = (float) $settings->getConstant('safety_factor', 1.25);
        $powerFactor = (float) $settings->getConstant('power_factor', 0.80);
        $surgeCapacityFactor = (float) $settings->getConstant('surge_capacity_factor', 2.0);

        $continuousVA = ($effectivePeakW * $safetyFactor) / $powerFactor;
        $inverterCatalog = $settings->inverterCatalog;
        usort($inverterCatalog, fn($a, $b) => $a['kva'] <=> $b['kva']);

        $selectedInverter = null;
        $surgeOk = true;

        foreach ($inverterCatalog as $inv) {
            $invKva = (float) $inv['kva'];
            $ratedVA = $invKva * 1000.0;
            $continuousOk = ($ratedVA >= $continuousVA);
            $surgeCapacityW = $ratedVA * $powerFactor * $surgeCapacityFactor;
            $surgePassed = ($surgeCapacityW >= $surgePeakW);

            if ($continuousOk && $surgePassed) {
                $selectedInverter = $inv;
                break;
            }
        }

        if (!$selectedInverter) {
            // Exceeds catalog - pick highest
            $selectedInverter = end($inverterCatalog);
            $surgeOk = false;
        }

        // ── 2. Battery Sizing ────────────────────────────────────────────────
        $autonomyDays = max(1, $input->daysOfAutonomy);
        $inverterEfficiency = (float) $settings->getConstant('inverter_efficiency', 0.90);
        $dod = ($input->batteryType === 'tubular')
            ? (float) $settings->getConstant('dod_tubular', 0.50)
            : (float) $settings->getConstant('dod_lithium', 0.80);

        $usableWhNeeded = ($windowWh * $autonomyDays) / $inverterEfficiency;
        $bankWh = $usableWhNeeded / $dod;

        // System Voltage by battery bank size
        if ($bankWh <= 2400.0) {
            $systemVoltage = 12;
        } elseif ($bankWh <= 7200.0) {
            $systemVoltage = 24;
        } else {
            $systemVoltage = 48;
        }

        // Reconcile system voltage with Inverter native voltage
        $upsizedForBattery = false;
        $inverterVoltage = (int) $selectedInverter['voltage'];

        if ($inverterVoltage < $systemVoltage) {
            // Upsize inverter to match battery bank voltage
            foreach ($inverterCatalog as $inv) {
                if ((int) $inv['voltage'] >= $systemVoltage && (float) $inv['kva'] >= (float) $selectedInverter['kva']) {
                    $selectedInverter = $inv;
                    $upsizedForBattery = true;
                    break;
                }
            }
        }

        // Re-align system voltage if inverter voltage is higher
        $finalSystemVoltage = max($systemVoltage, (int) $selectedInverter['voltage']);
        $roundedBankWh = (int) round($bankWh);
        $bankAh = round($roundedBankWh / $finalSystemVoltage, 1);

        // Calculate physical units
        if ($input->batteryType === 'tubular') {
            // Standard 12V 200Ah tubular (2,400 Wh)
            $seriesCount = max(1, (int) ($finalSystemVoltage / 12));
            $parallelCount = max(1, (int) ceil($bankAh / 200.0));
            $unitsNeeded = $seriesCount * $parallelCount;
            $unitLabel = "{$finalSystemVoltage}V " . ($parallelCount * 200) . "Ah Tubular Bank ({$unitsNeeded}x 12V 200Ah)";
        } else {
            // LiFePO4 battery modules (100Ah standard rack/wall unit at system voltage)
            $unitWh = (float) ($finalSystemVoltage * 100); // 12V: 1200Wh, 24V: 2560Wh, 48V: 5120Wh
            if ($finalSystemVoltage === 24) {
                $unitWh = 2560.0;
            } elseif ($finalSystemVoltage === 48) {
                $unitWh = 5120.0;
            }
            $unitsNeeded = max(1, (int) ceil($bankWh / $unitWh));
            $unitLabel = "{$finalSystemVoltage}V 100Ah Lithium LiFePO4";
        }

        // ── 3. Solar Array Sizing ─────────────────────────────────────────────
        $targetPVWh = ($input->goal === 'full_solar')
            ? $totalDailyWh
            : (($windowWh * $autonomyDays) / $inverterEfficiency);

        $psh = $settings->getPSH($input->zone, $input->customPeakSunHours);
        $systemDerate = (float) $settings->getConstant('system_derate', 0.75);

        $requiredPVW = $targetPVWh / ($psh * $systemDerate);
        $panelWatts = $input->panelWatts > 0 ? $input->panelWatts : (int) $settings->getConstant('default_panel_watts', 550);
        $panels = max(1, (int) ceil($requiredPVW / $panelWatts));
        $arrayW = $panels * $panelWatts;
        $estDailyGenWh = round($arrayW * $psh * $systemDerate, 1);

        // ── 4. Charge Controller (MPPT) ──────────────────────────────────────
        $mpptAmps = round(($arrayW / $finalSystemVoltage) * 1.25, 1);
        $controllerRatings = $settings->controllerRatings;
        sort($controllerRatings);

        $recommendedA = end($controllerRatings);
        foreach ($controllerRatings as $rating) {
            if ($rating >= $mpptAmps) {
                $recommendedA = $rating;
                break;
            }
        }

        // ── 5. Warnings ──────────────────────────────────────────────────────
        $warnings = [];
        if ($hasResistive) {
            $warnings[] = 'resistive_load';
        }
        if ($hasAC) {
            $warnings[] = 'ac_load';
        }
        if ($hasMotorSurge) {
            $warnings[] = 'motor_surge';
        }
        if ((float) $selectedInverter['kva'] >= 10.0 && $continuousVA > 10000.0) {
            $warnings[] = 'large_system';
        }
        if ($hasWindowClipped) {
            $warnings[] = 'window_clipped';
        }
        if ($totalDailyWh < 300.0) {
            $warnings[] = 'very_small_load';
        }

        return new CalculatorResult(
            energy: [
                'dailyWh' => round($totalDailyWh, 1),
                'monthlyKWh' => $monthlyKWh,
                'peakW' => $effectivePeakW,
                'surgePeakW' => $surgePeakW,
                'surgeExtraW' => round($largestSurgeExtraW, 1),
                'windowWh' => round($windowWh, 1),
            ],
            inverter: [
                'kva' => (float) $selectedInverter['kva'],
                'voltage' => $finalSystemVoltage,
                'continuousVA' => round($continuousVA, 1),
                'surgeVA' => round($surgePeakW / $powerFactor, 1),
                'surgeOk' => $surgeOk,
                'upsizedForBattery' => $upsizedForBattery,
                'note' => $upsizedForBattery ? 'Upsized to support the battery bank.' : null,
            ],
            battery: [
                'usableWhNeeded' => (int) round($usableWhNeeded),
                'bankWh' => (int) round($bankWh),
                'bankAh' => $bankAh,
                'type' => $input->batteryType,
                'dod' => $dod,
                'unitsNeeded' => $unitsNeeded,
                'unitLabel' => $unitLabel,
            ],
            solar: [
                'requiredW' => (int) round($requiredPVW),
                'panelW' => $panelWatts,
                'panelWatts' => $panelWatts,
                'panels' => $panels,
                'arrayW' => $arrayW,
                'estDailyGenWh' => $estDailyGenWh,
                'peakSunHours' => $psh,
            ],
            controller: [
                'mpptAmps' => $mpptAmps,
                'recommendedA' => $recommendedA,
            ],
            warnings: $warnings,
            packages: [],
            settingsVersion: $settings->version,
        );
    }
}
