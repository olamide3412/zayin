<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appliance_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('appliance_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('appliance_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('watts');
            $table->decimal('surge_multiplier', 4, 2)->default(1.0);
            $table->decimal('default_hours', 4, 2)->default(6.0);
            $table->string('icon')->nullable();
            $table->boolean('is_resistive')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('solar_calc_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('version')->default(1);
            $table->json('constants');
            $table->json('inverter_catalog');
            $table->json('controller_ratings');
            $table->json('psh_by_zone');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('solar_calculations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->json('inputs');
            $table->json('results');
            $table->unsignedInteger('settings_version')->default(1);
            $table->string('zone')->default('south_west');
            $table->foreignId('quote_request_id')->nullable()->constrained('quote_requests')->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('quote_requests')) {
            Schema::table('quote_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('quote_requests', 'calculation_id')) {
                    $table->foreignId('calculation_id')->nullable()->after('product_id')->constrained('solar_calculations')->nullOnDelete();
                }
                if (!Schema::hasColumn('quote_requests', 'calculation_uuid')) {
                    $table->uuid('calculation_uuid')->nullable()->after('calculation_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('quote_requests')) {
            Schema::table('quote_requests', function (Blueprint $table) {
                if (Schema::hasColumn('quote_requests', 'calculation_id')) {
                    $table->dropForeign(['calculation_id']);
                    $table->dropColumn(['calculation_id']);
                }
                if (Schema::hasColumn('quote_requests', 'calculation_uuid')) {
                    $table->dropColumn(['calculation_uuid']);
                }
            });
        }

        Schema::dropIfExists('solar_calculations');
        Schema::dropIfExists('solar_calc_settings');
        Schema::dropIfExists('appliance_presets');
        Schema::dropIfExists('appliance_categories');
    }
};
