<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('city')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('client_name')->nullable();
            $table->date('completed_on')->nullable();
            $table->string('system_size')->nullable(); // e.g. "5kVA Solar System", "Starlink Multi-Building Mesh"
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('project_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('transcript')->nullable();
            $table->string('source')->default('upload'); // upload, youtube, vimeo
            $table->string('external_url')->nullable();
            $table->string('video_path')->nullable();
            $table->string('status')->default('ready'); // uploading, processing, ready, failed
            $table->integer('duration_seconds')->nullable();
            $table->string('poster_path')->nullable();
            $table->json('renditions')->nullable(); // e.g. {"480p": "...", "720p": "..."}
            $table->unsignedBigInteger('file_size')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('project_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_product');
        Schema::dropIfExists('project_videos');
        Schema::dropIfExists('projects');
    }
};
