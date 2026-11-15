<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Service $service;
    private State $state;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->service = Service::create([
            'name' => 'Solar Inverter Installation',
            'slug' => 'solar-inverter-installation',
            'is_published' => true,
        ]);

        $this->state = State::create([
            'name' => 'Lagos',
            'slug' => 'lagos',
            'code' => 'LA',
            'is_active' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_projects(): void
    {
        $this->get(route('admin.projects.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_projects_index(): void
    {
        Project::create([
            'title' => 'Test Solar Project in Lekki',
            'slug' => 'test-solar-project-in-lekki',
            'service_id' => $this->service->id,
            'state_id' => $this->state->id,
            'city' => 'Lekki Phase 1',
            'system_size' => '5kVA Inverter',
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.projects.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Auth/Projects/Index')
                ->has('projects.data', 1)
                ->where('projects.data.0.title', 'Test Solar Project in Lekki')
            );
    }

    public function test_admin_can_create_project_with_video_walkthrough(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.projects.store'), [
            'title' => '10kVA Commercial Solar Setup in Ikeja',
            'service_id' => $this->service->id,
            'state_id' => $this->state->id,
            'city' => 'Ikeja',
            'client_name' => 'Chief Okonkwo',
            'system_size' => '10kVA 48V / 10.24kWh Lithium',
            'completed_on' => '2026-10-15',
            'summary' => 'Complete office solar microgrid solution.',
            'body' => 'Installed rooftop panels and inverter rack with zero downtime.',
            'featured_image' => 'https://example.com/solar-proof.jpg',
            'gallery' => [
                'https://example.com/rack.jpg',
                'https://example.com/panels.jpg',
                'https://example.com/clip.mp4',
            ],
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_title' => 'Full System Load Testing',
            'is_featured' => true,
            'is_published' => true,
        ]);

        $response->assertRedirect(route('admin.projects.index'));

        $project = Project::where('slug', '10kva-commercial-solar-setup-in-ikeja')->first();
        $this->assertNotNull($project);
        $this->assertSame('Ikeja', $project->city);
        $this->assertCount(3, $project->gallery);
        $this->assertSame('https://example.com/rack.jpg', $project->gallery[0]);
        $this->assertTrue((bool) $project->is_featured);
        $this->assertTrue((bool) $project->is_published);
        $this->assertCount(1, $project->videos);
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $project->videos->first()->external_url);
    }

    public function test_admin_can_update_and_delete_project(): void
    {
        $project = Project::create([
            'title' => 'Initial Title',
            'slug' => 'initial-title',
            'service_id' => $this->service->id,
            'is_published' => true,
        ]);

        $updateResponse = $this->actingAs($this->user)->put(route('admin.projects.update', $project->id), [
            'title' => 'Updated Project Title',
            'service_id' => $this->service->id,
            'city' => 'Abraka',
            'is_published' => true,
            'is_featured' => false,
        ]);

        $updateResponse->assertRedirect(route('admin.projects.index'));

        $project->refresh();
        $this->assertSame('Updated Project Title', $project->title);
        $this->assertSame('Abraka', $project->city);

        $deleteResponse = $this->actingAs($this->user)->delete(route('admin.projects.destroy', $project->id));
        $deleteResponse->assertRedirect(route('admin.projects.index'));

        $this->assertSoftDeleted($project);
    }
}
