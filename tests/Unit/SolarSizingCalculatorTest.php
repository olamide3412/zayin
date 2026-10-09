<?php

namespace Tests\Unit;

use App\DataTransferObjects\Solar\CalculatorInput;
use App\Services\Solar\SolarSettings;
use App\Services\Solar\SolarSizingCalculator;
use Tests\TestCase;

class SolarSizingCalculatorTest extends TestCase
{
    private SolarSizingCalculator $calculator;
    private SolarSettings $settings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new SolarSizingCalculator();
        $this->settings = SolarSettings::default();
    }

    public function test_worked_example_matches_specification_accurately(): void
    {
        $input = CalculatorInput::fromArray([
            'appliances' => [
                ['name' => 'LED bulb', 'watts' => 10, 'qty' => 8, 'hours' => 5, 'surge' => 1.0, 'is_resistive' => false],
                ['name' => 'Ceiling fan', 'watts' => 75, 'qty' => 3, 'hours' => 8, 'surge' => 2.0, 'is_resistive' => false],
                ['name' => 'TV (LED)', 'watts' => 80, 'qty' => 1, 'hours' => 6, 'surge' => 1.0, 'is_resistive' => false],
                ['name' => 'Decoder', 'watts' => 20, 'qty' => 1, 'hours' => 6, 'surge' => 1.0, 'is_resistive' => false],
                ['name' => 'Fridge (medium, duty-cycled)', 'watts' => 150, 'qty' => 1, 'hours' => 10, 'surge' => 3.0, 'is_resistive' => false],
                ['name' => 'Wi-Fi router', 'watts' => 12, 'qty' => 1, 'hours' => 24, 'surge' => 1.0, 'is_resistive' => false],
                ['name' => 'Laptop', 'watts' => 65, 'qty' => 1, 'hours' => 6, 'surge' => 1.0, 'is_resistive' => false],
            ],
            'backup_hours' => 12,
            'goal' => 'full_solar',
            'battery_type' => 'lithium',
            'zone' => 'south_west',
            'days_of_autonomy' => 1,
            'panel_watts' => 550,
            'diversity_factor' => 1.0,
        ]);

        $result = $this->calculator->calculate($input, $this->settings);

        // Daily Wh = 4,978 Wh
        $this->assertEquals(4978.0, $result->energy['dailyWh']);
        $this->assertEquals(149.3, $result->energy['monthlyKWh']);
        $this->assertEquals(632.0, $result->energy['peakW']);
        $this->assertEquals(932.0, $result->energy['surgePeakW']);
        $this->assertEquals(4834.0, $result->energy['windowWh']);

        // Inverter = 2.5kVA / 24V (upsized from 1kVA for 24V battery bank)
        $this->assertEquals(2.5, $result->inverter['kva']);
        $this->assertEquals(24, $result->inverter['voltage']);
        $this->assertTrue($result->inverter['upsizedForBattery']);
        $this->assertTrue($result->inverter['surgeOk']);

        // Battery = usable 5,371 Wh, bank 6,714 Wh, 280 Ah @ 24V, 3 units of 24V 100Ah
        $this->assertEquals(5371, $result->battery['usableWhNeeded']);
        $this->assertEquals(6714, $result->battery['bankWh']);
        $this->assertEquals(279.8, round($result->battery['bankAh'], 1));
        $this->assertEquals(3, $result->battery['unitsNeeded']);
        $this->assertEquals('lithium', $result->battery['type']);

        // Solar = 1,475 W required, 3 panels (1,650 W array)
        $this->assertEquals(1475, $result->solar['requiredW']);
        $this->assertEquals(3, $result->solar['panels']);
        $this->assertEquals(1650, $result->solar['arrayW']);
        $this->assertEquals(4.5, $result->solar['peakSunHours']);

        // MPPT Controller = 85.9 A -> 100 A standard
        $this->assertEquals(85.9, $result->controller['mpptAmps']);
        $this->assertEquals(100, $result->controller['recommendedA']);

        // Warnings
        $this->assertContains('motor_surge', $result->warnings);
    }

    public function test_resistive_and_ac_warnings_are_triggered(): void
    {
        $input = CalculatorInput::fromArray([
            'appliances' => [
                ['name' => 'Electric iron', 'watts' => 1000, 'qty' => 1, 'hours' => 1, 'surge' => 1.0, 'is_resistive' => true],
                ['name' => 'Air conditioner 1 HP', 'watts' => 750, 'qty' => 1, 'hours' => 4, 'surge' => 3.0, 'is_resistive' => false],
            ],
            'backup_hours' => 8,
            'goal' => 'backup',
            'battery_type' => 'lithium',
        ]);

        $result = $this->calculator->calculate($input, $this->settings);

        $this->assertContains('resistive_load', $result->warnings);
        $this->assertContains('ac_load', $result->warnings);
        $this->assertContains('motor_surge', $result->warnings);
    }

    public function test_tubular_battery_uses_half_depth_of_discharge(): void
    {
        $inputLithium = CalculatorInput::fromArray([
            'appliances' => [
                ['name' => 'TV', 'watts' => 100, 'qty' => 1, 'hours' => 10, 'surge' => 1.0],
            ],
            'backup_hours' => 10,
            'battery_type' => 'lithium',
        ]);

        $inputTubular = CalculatorInput::fromArray([
            'appliances' => [
                ['name' => 'TV', 'watts' => 100, 'qty' => 1, 'hours' => 10, 'surge' => 1.0],
            ],
            'backup_hours' => 10,
            'battery_type' => 'tubular',
        ]);

        $resLithium = $this->calculator->calculate($inputLithium, $this->settings);
        $resTubular = $this->calculator->calculate($inputTubular, $this->settings);

        // Tubular bank Wh should be larger than lithium for the exact same load due to 0.50 vs 0.80 DoD
        $this->assertGreaterThan($resLithium->battery['bankWh'], $resTubular->battery['bankWh']);
        $this->assertEquals(0.50, $resTubular->battery['dod']);
        $this->assertEquals(0.80, $resLithium->battery['dod']);
    }

    public function test_golden_fixtures_all_pass(): void
    {
        $fixturePath = base_path('tests/Fixtures/solar_calculator_cases.json');
        $this->assertFileExists($fixturePath);

        $cases = json_decode(file_get_contents($fixturePath), true);
        $this->assertNotEmpty($cases);

        foreach ($cases as $case) {
            $input = CalculatorInput::fromArray($case['inputs']);
            $result = $this->calculator->calculate($input, $this->settings);

            $expected = $case['expected'];

            if (isset($expected['energy']['dailyWh'])) {
                $this->assertEquals($expected['energy']['dailyWh'], $result->energy['dailyWh'], "Failed on case {$case['id']} dailyWh");
            }
            if (isset($expected['inverter']['kva'])) {
                $this->assertEquals($expected['inverter']['kva'], $result->inverter['kva'], "Failed on case {$case['id']} kva");
            }
            if (isset($expected['inverter']['voltage'])) {
                $this->assertEquals($expected['inverter']['voltage'], $result->inverter['voltage'], "Failed on case {$case['id']} voltage");
            }
            if (isset($expected['battery']['type'])) {
                $this->assertEquals($expected['battery']['type'], $result->battery['type'], "Failed on case {$case['id']} battery type");
            }
            if (isset($expected['solar']['panels'])) {
                $this->assertEquals($expected['solar']['panels'], $result->solar['panels'], "Failed on case {$case['id']} panels");
            }
        }
    }
}
