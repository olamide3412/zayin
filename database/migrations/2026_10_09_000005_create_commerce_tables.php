<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->unsignedBigInteger('unit_price'); // in kobo
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // e.g. ZAY-2026-0001
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone');
            $table->string('status')->default('awaiting_payment'); // awaiting_payment, paid, processing, scheduled, completed, cancelled, refunded
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, partial, failed, refunded
            $table->string('fulfilment_type')->default('installation'); // delivery, installation, pickup
            $table->unsignedBigInteger('subtotal'); // in kobo
            $table->unsignedBigInteger('delivery_fee')->default(0);
            $table->unsignedBigInteger('install_fee')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('total'); // in kobo
            $table->unsignedBigInteger('amount_paid')->default(0);
            $table->string('currency')->default('NGN');
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('landmark')->nullable();
            $table->json('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('name_snapshot');
            $table->string('sku_snapshot')->nullable();
            $table->integer('qty')->default(1);
            $table->unsignedBigInteger('unit_price'); // in kobo
            $table->unsignedBigInteger('total'); // in kobo
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('gateway')->default('paystack'); // paystack, bank_transfer, cash
            $table->string('reference')->unique();
            $table->unsignedBigInteger('amount'); // in kobo
            $table->string('status')->default('initiated'); // initiated, pending, success, failed
            $table->json('gateway_response')->nullable();
            $table->string('proof_file')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
