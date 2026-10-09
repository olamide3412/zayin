<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('service_type')->default('solar'); // solar, starlink, dstv, combination
            $table->string('property_type')->default('residential'); // residential, commercial, industrial
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('budget_range')->nullable();
            $table->json('appliances')->nullable(); // checklist of appliances/needs
            $table->text('details')->nullable();
            $table->string('preferred_contact')->default('whatsapp'); // whatsapp, phone, email
            $table->string('status')->default('new'); // new, contacted, site_visit, quoted, won, lost
            $table->unsignedBigInteger('quoted_amount')->nullable(); // in kobo
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('address');
            $table->string('landmark')->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('city')->nullable();
            $table->date('preferred_date');
            $table->string('preferred_slot')->default('morning'); // morning (9am-12pm), afternoon (1pm-5pm)
            $table->string('service_type')->default('solar_installation');
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price'); // in kobo per annum / visit
            $table->string('billing_period')->default('annual'); // annual, bi-annual, quarterly, per_visit
            $table->integer('visits_per_year')->default(2);
            $table->json('inclusions')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('new'); // new, read, replied
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general'); // solar, starlink, dstv, payment, general
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->text('question');
            $table->longText('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('maintenance_plans');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('quote_requests');
    }
};
