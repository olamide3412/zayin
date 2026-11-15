<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageCompressionUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('public');
    }

    public function test_image_upload_compresses_and_converts_to_webp(): void
    {
        $file = UploadedFile::fake()->image('large-installation.jpg', 2400, 1600);

        $response = $this->actingAs($this->user)->postJson(route('admin.upload-image'), [
            'image' => $file,
        ]);

        $response->assertOk()
            ->assertJsonStructure(['url']);

        $url = $response->json('url');
        $this->assertStringEndsWith('.webp', $url);

        $path = str_replace('/storage/', '', $url);
        Storage::disk('public')->assertExists($path);
    }

    public function test_upload_media_compresses_images_and_preserves_videos(): void
    {
        // 1. Upload Image via upload-media
        $imageFile = UploadedFile::fake()->image('roof-panels.png', 1200, 800);
        $imgResponse = $this->actingAs($this->user)->postJson(route('admin.upload-media'), [
            'file' => $imageFile,
        ]);

        $imgResponse->assertOk()
            ->assertJson([
                'type' => 'image',
            ]);
        $this->assertStringEndsWith('.webp', $imgResponse->json('url'));

        // 2. Upload Video clip via upload-media
        $videoFile = UploadedFile::fake()->create('walkthrough.mp4', 500, 'video/mp4');
        $vidResponse = $this->actingAs($this->user)->postJson(route('admin.upload-media'), [
            'file' => $videoFile,
        ]);

        $vidResponse->assertOk()
            ->assertJson([
                'type' => 'video',
            ]);
        $this->assertStringEndsWith('.mp4', $vidResponse->json('url'));
    }
}

