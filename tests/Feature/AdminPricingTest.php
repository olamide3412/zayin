<?php

namespace Tests\Feature;

use App\Models\MaintenancePlan;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SolarCalcSetting;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPricingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@zayin.ng',
        ]);

        $this->seed(\Database\Seeders\SolarCalculatorSeeder::class);
        $this->seed(\Database\Seeders\SolarPackagesSeeder::class);
    }

    public function test_unauthenticated_users_cannot_access_admin_pricing_routes(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));
        $this->get(route('admin.plans.index'))->assertRedirect(route('login'));
        $this->get(route('admin.state-fees.index'))->assertRedirect(route('login'));
        $this->get(route('admin.solar-settings.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_and_update_product_pricing(): void
    {
        $category = ProductCategory::firstOrCreate(
            ['slug' => 'solar-packages'],
            ['name' => 'Solar Packages']
        );

        $product = Product::create([
            'category_id' => $category->id,
            'name' => '3.5kVA Test Package',
            'slug' => '3-5kva-test-package',
            'sku' => 'TEST-3KVA',
            'price' => 175000000, // ₦1,750,000 in kobo
            'stock_qty' => 5,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $response->assertStatus(200);

        // Update price via quick-update in Naira (₦1,850,000)
        $updateResponse = $this->actingAs($this->admin)->patch(
            route('admin.products.quick-update', $product->id),
            ['price_naira' => 1850000]
        );

        $updateResponse->assertSessionHas('message');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => 185000000, // Stored in kobo
        ]);
    }

    public function test_admin_can_view_and_update_maintenance_plan_pricing(): void
    {
        $plan = MaintenancePlan::create([
            'name' => 'Solar Care Premium',
            'slug' => 'solar-care-premium',
            'price' => 5000000, // ₦50,000 in kobo
            'billing_period' => 'quarterly',
            'visits_per_year' => 4,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.plans.index'));
        $response->assertStatus(200);

        // Update plan price in Naira (₦60,000)
        $updateResponse = $this->actingAs($this->admin)->put(
            route('admin.plans.update', $plan->id),
            [
                'name' => 'Solar Care Premium Plus',
                'price_naira' => 60000,
                'billing_period' => 'quarterly',
                'visits_per_year' => 4,
                'is_active' => true,
            ]
        );

        $updateResponse->assertSessionHas('message');
        $this->assertDatabaseHas('maintenance_plans', [
            'id' => $plan->id,
            'name' => 'Solar Care Premium Plus',
            'price' => 6000000, // Stored in kobo
        ]);
    }

    public function test_admin_can_view_and_update_regional_delivery_and_install_fees(): void
    {
        $state = State::create([
            'name' => 'Delta',
            'slug' => 'delta',
            'code' => 'DE',
            'delivery_fee' => 1500000, // ₦15,000
            'install_fee' => 3500000,  // ₦35,000
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.state-fees.index'));
        $response->assertStatus(200);

        // Update fees in Naira
        $updateResponse = $this->actingAs($this->admin)->patch(
            route('admin.state-fees.update', $state->id),
            [
                'delivery_fee_naira' => 20000,
                'install_fee_naira' => 40000,
                'is_active' => true,
            ]
        );

        $updateResponse->assertSessionHas('message');
        $this->assertDatabaseHas('states', [
            'id' => $state->id,
            'delivery_fee' => 2000000, // ₦20,000 in kobo
            'install_fee' => 4000000,  // ₦40,000 in kobo
        ]);
    }

    public function test_admin_can_update_solar_calculator_engine_constants(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.solar-settings.index'));
        $response->assertStatus(200);

        $payload = [
            'constants' => [
                'safety_factor' => 1.30,
                'power_factor' => 0.85,
                'inverter_efficiency' => 0.92,
                'system_derate' => 0.78,
                'dod_lithium' => 0.85,
                'dod_tubular' => 0.50,
            ],
            'inverter_catalog' => [
                ['kva' => 1.5, 'voltage' => 12],
                ['kva' => 3.5, 'voltage' => 24],
                ['kva' => 5.0, 'voltage' => 48],
            ],
            'controller_ratings' => [40, 60, 80, 100],
            'psh_by_zone' => [
                'south_south' => 4.5,
                'south_west' => 4.6,
            ],
        ];

        $updateResponse = $this->actingAs($this->admin)->post(
            route('admin.solar-settings.update'),
            $payload
        );

        $updateResponse->assertSessionHas('message');
        $activeSetting = SolarCalcSetting::getActive();
        $this->assertEquals(1.30, $activeSetting->constants['safety_factor']);
        $this->assertEquals(0.85, $activeSetting->constants['power_factor']);
    }
}
