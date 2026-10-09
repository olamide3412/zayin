<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\User;
use Database\Seeders\PartnerSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPartnerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(PartnerSeeder::class);
        $this->admin = User::where('username', 'admin')->first();
    }

    public function test_admin_can_view_technology_partners_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.partners.index'));
        $response->assertOk();
        $response->assertSee('SpaceX Starlink');
    }

    public function test_admin_can_create_new_brand_partner(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.partners.store'), [
            'name' => 'Canadian Solar Tier-1',
            'website_url' => 'canadiansolar.com',
            'description' => 'High-efficiency mono-perc solar panels and bifacial cells.',
            'status' => 'active',
            'sort_order' => 9,
        ]);

        $response->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseHas('partners', [
            'name' => 'Canadian Solar Tier-1',
            'website_url' => 'https://canadiansolar.com',
        ]);
    }

    public function test_admin_can_update_brand_partner(): void
    {
        $partner = Partner::first();

        $response = $this->actingAs($this->admin)->put(route('admin.partners.update', $partner->id), [
            'name' => 'SpaceX Starlink Global',
            'website_url' => 'https://starlink.com',
            'description' => 'Updated satellite description.',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseHas('partners', [
            'id' => $partner->id,
            'name' => 'SpaceX Starlink Global',
        ]);
    }

    public function test_admin_can_delete_brand_partner(): void
    {
        $partner = Partner::first();

        $response = $this->actingAs($this->admin)->delete(route('admin.partners.destroy', $partner->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('partners', ['id' => $partner->id]);
    }

    public function test_homepage_includes_active_brand_partners(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
    }
}
