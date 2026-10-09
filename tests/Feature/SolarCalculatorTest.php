<?php

namespace Tests\Feature;

use App\Models\ApplianceCategory;
use App\Models\AppliancePreset;
use App\Models\QuoteRequest;
use App\Models\SolarCalculation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolarCalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SolarCalculatorSeeder::class);
        $this->seed(\Database\Seeders\SolarPackagesSeeder::class);
    }

    public function test_solar_calculator_page_loads_with_props(): void
    {
        $response = $this->get(route('solar.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Public/SolarCalculator/Index')
            ->has('categories')
            ->has('presets')
            ->has('constants')
            ->has('zones')
            ->has('packages')
        );
    }

    public function test_calculate_endpoint_returns_json_sizing_results(): void
    {
        $payload = [
            'appliances' => [
                ['name' => 'LED bulb', 'watts' => 10, 'qty' => 8, 'hours' => 5, 'surge' => 1.0, 'is_resistive' => false],
                ['name' => 'Ceiling fan', 'watts' => 75, 'qty' => 3, 'hours' => 8, 'surge' => 2.0, 'is_resistive' => false],
                ['name' => 'TV (LED)', 'watts' => 80, 'qty' => 1, 'hours' => 6, 'surge' => 1.0, 'is_resistive' => false],
                ['name' => 'Fridge', 'watts' => 150, 'qty' => 1, 'hours' => 10, 'surge' => 3.0, 'is_resistive' => false],
            ],
            'backup_hours' => 12,
            'goal' => 'full_solar',
            'battery_type' => 'lithium',
            'zone' => 'south_west',
            'days_of_autonomy' => 1,
            'panel_watts' => 550,
        ];

        $response = $this->postJson(route('solar.calculate'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'energy' => ['dailyWh', 'monthlyKWh', 'peakW', 'surgePeakW', 'windowWh'],
            'inverter' => ['kva', 'voltage', 'surgeOk', 'upsizedForBattery'],
            'battery' => ['usableWhNeeded', 'bankWh', 'bankAh', 'unitsNeeded', 'type', 'dod'],
            'solar' => ['requiredW', 'panels', 'arrayW', 'peakSunHours', 'panelWatts'],
            'controller' => ['mpptAmps', 'recommendedA'],
            'warnings',
            'matchedPackages',
        ]);
    }

    public function test_save_endpoint_persists_calculation_and_returns_urls(): void
    {
        $payload = [
            'appliances' => [
                ['name' => 'LED bulb', 'watts' => 10, 'qty' => 5, 'hours' => 6],
            ],
            'backup_hours' => 8,
            'goal' => 'backup',
            'battery_type' => 'lithium',
            'zone' => 'south_south',
            'lead_name' => 'David Adeleke',
            'lead_email' => 'david@example.com',
            'lead_phone' => '08012345678',
        ];

        $response = $this->postJson(route('solar.save'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'uuid',
            'share_url',
            'pdf_url',
            'result',
        ]);

        $uuid = $response->json('uuid');
        $this->assertDatabaseHas('solar_calculations', [
            'uuid' => $uuid,
        ]);
    }

    public function test_shared_page_renders_saved_calculation(): void
    {
        $calculation = SolarCalculation::create([
            'inputs' => [
                'appliances' => [['name' => 'TV', 'watts' => 100, 'qty' => 1, 'hours' => 5]],
                'backup_hours' => 12,
                'goal' => 'full_solar',
                'battery_type' => 'lithium',
                'zone' => 'south_south',
            ],
            'results' => [
                'energy' => ['dailyWh' => 500, 'monthlyKWh' => 15, 'peakW' => 100, 'surgePeakW' => 100, 'windowWh' => 500],
                'inverter' => ['kva' => 1.5, 'voltage' => 12, 'surgeOk' => true, 'upsizedForBattery' => false],
                'battery' => ['usableWhNeeded' => 550, 'bankWh' => 688, 'bankAh' => 57.3, 'unitsNeeded' => 1, 'type' => 'lithium', 'dod' => 0.8],
                'solar' => ['requiredW' => 150, 'panels' => 1, 'arrayW' => 550, 'peakSunHours' => 4.2, 'panelWatts' => 550],
                'controller' => ['mpptAmps' => 15.0, 'recommendedA' => 20],
                'warnings' => [],
            ],
        ]);

        $response = $this->get(route('solar.shared', ['uuid' => $calculation->uuid]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Public/SolarCalculator/Shared')
            ->has('calculation')
            ->has('matchedPackages')
        );
    }

    public function test_pdf_export_downloads_solar_proposal(): void
    {
        $calculation = SolarCalculation::create([
            'inputs' => [
                'appliances' => [['name' => 'TV', 'watts' => 100, 'qty' => 1, 'hours' => 5]],
                'backup_hours' => 12,
                'goal' => 'full_solar',
                'battery_type' => 'lithium',
                'zone' => 'south_south',
            ],
            'results' => [
                'energy' => ['dailyWh' => 500, 'monthlyKWh' => 15, 'peakW' => 100, 'surgePeakW' => 100, 'windowWh' => 500],
                'inverter' => ['kva' => 1.5, 'voltage' => 12, 'surgeOk' => true, 'upsizedForBattery' => false],
                'battery' => ['usableWhNeeded' => 550, 'bankWh' => 688, 'bankAh' => 57.3, 'unitsNeeded' => 1, 'type' => 'lithium', 'dod' => 0.8],
                'solar' => ['requiredW' => 150, 'panels' => 1, 'arrayW' => 550, 'peakSunHours' => 4.2, 'panelWatts' => 550],
                'controller' => ['mpptAmps' => 15.0, 'recommendedA' => 20],
                'warnings' => [],
            ],
        ]);

        $response = $this->get(route('solar.pdf', ['uuid' => $calculation->uuid]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_quote_request_links_to_calculation_uuid(): void
    {
        $calculation = SolarCalculation::create([
            'inputs' => ['appliances' => []],
            'results' => ['inverter' => ['kva' => 2.5]],
        ]);

        $quoteData = [
            'name' => 'Ovie James',
            'email' => 'ovie@example.com',
            'phone' => '08099887766',
            'service_type' => 'Solar Installation',
            'property_type' => 'residential',
            'city' => 'Abraka',
            'address' => 'Station Road, Abraka',
            'budget_range' => '2000000',
            'details' => 'Please review my calculation and provide a quote.',
            'calculation_uuid' => $calculation->uuid,
        ];

        $response = $this->post(route('quote.store'), $quoteData);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('quote_requests', [
            'email' => 'ovie@example.com',
            'calculation_uuid' => $calculation->uuid,
            'calculation_id' => $calculation->id,
        ]);
    }
}
