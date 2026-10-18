<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for the Nigerian A5 Invoice Generator module.
     */
    public function up(): void
    {
        // 1. Business Profile (Company info, tax identifiers, defaults)
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_alt')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('rc_number')->nullable();
            $table->string('tin')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->unsignedInteger('vat_rate_bps')->default(750); // 7.5% = 750 bps
            $table->enum('default_vat_mode', ['exclusive', 'inclusive'])->default('exclusive');
            $table->text('default_terms')->nullable();
            $table->text('default_notes')->nullable();
            $table->text('footer_text')->nullable();
            $table->timestamps();
        });

        // 2. Business Bank Accounts (Multiple accounts supported)
        Schema::create('business_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Invoice Templates (Customizable styles, colors, fields)
        Schema::create('invoice_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('name')->default('Standard A5 Template');
            $table->enum('layout', ['classic', 'modern', 'minimal'])->default('classic');
            $table->string('accent_color')->default('#0F5132'); // Nigerian emerald green or navy
            $table->string('font_family')->default('DejaVu Sans');
            $table->enum('logo_position', ['left', 'center'])->default('left');
            $table->string('document_title')->default('INVOICE');
            $table->json('show_fields')->nullable();
            $table->boolean('show_watermark')->default(true);
            $table->string('number_prefix')->default('INV-');
            $table->unsignedSmallInteger('number_padding')->default(4);
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });

        // 4. Customers / Clients
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('tin')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Self-Learning Invoice Items Catalog
        Schema::create('invoice_items_catalog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('name');
            $table->string('normalized_name'); // lowercase, stripped punctuation for dedupe
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs');
            $table->unsignedBigInteger('default_price_kobo')->default(0);
            $table->unsignedBigInteger('last_price_kobo')->nullable();
            $table->boolean('is_vatable')->default(true);
            $table->string('category')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index(['business_id', 'normalized_name']);
        });

        // Add FullText index conditionally for MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE invoice_items_catalog ADD FULLTEXT fulltext_catalog_name (name)');
        }

        // 6. Invoices Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('template_id')->nullable()->constrained('invoice_templates')->onDelete('set null');
            $table->string('invoice_number')->nullable();
            $table->enum('status', ['draft', 'issued', 'partially_paid', 'paid', 'overdue', 'void'])->default('draft');
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('payment_terms')->nullable(); // Due on receipt, 7 days, 14 days, 30 days
            $table->string('reference')->nullable(); // PO Number, Folio Ref
            $table->string('subject')->nullable(); // Brief description of invoice
            $table->enum('vat_mode', ['exclusive', 'inclusive'])->default('exclusive');
            $table->unsignedInteger('vat_rate_bps')->default(750);
            $table->unsignedBigInteger('invoice_discount_kobo')->default(0);

            // Stored computed totals (in integer kobo)
            $table->unsignedBigInteger('subtotal_kobo')->default(0);
            $table->unsignedBigInteger('discount_kobo')->default(0);
            $table->unsignedBigInteger('vat_kobo')->default(0);
            $table->unsignedBigInteger('total_kobo')->default(0);
            $table->unsignedBigInteger('paid_kobo')->default(0);
            $table->unsignedBigInteger('balance_kobo')->default(0);

            $table->text('amount_in_words')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->json('bank_account_ids')->nullable(); // Selected banks to show on invoice

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->text('void_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Public sharing token & Tax authority hooks (§12)
            $table->string('public_token', 64)->nullable()->unique();
            $table->string('irn')->nullable(); // Government Invoice Reference Number
            $table->text('qr_payload')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'invoice_number']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'issue_date']);
        });

        // 7. Line Items (Independent snapshot of description/prices)
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('catalog_item_id')->nullable()->constrained('invoice_items_catalog')->onDelete('set null');
            $table->integer('sort_order')->default(0);
            $table->text('description');
            $table->decimal('quantity', 10, 3)->default(1.000);
            $table->string('unit')->default('pcs');
            $table->unsignedBigInteger('unit_price_kobo')->default(0);
            $table->enum('discount_type', ['none', 'fixed', 'percent'])->default('none');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->boolean('is_vatable')->default(true);
            $table->unsignedBigInteger('line_net_kobo')->default(0);
            $table->timestamps();
        });

        // 8. Recorded Payments
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->unsignedBigInteger('amount_kobo');
            $table->string('method'); // cash, bank_transfer, pos, online
            $table->string('reference')->nullable();
            $table->date('paid_on');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 9. Invoice Audit & Revision Log
        Schema::create('invoice_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('before');
            $table->json('after');
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 10. Gap-free Invoice Number Sequences
        Schema::create('invoice_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_number_sequences');
        Schema::dropIfExists('invoice_revisions');
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_items_catalog');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('invoice_templates');
        Schema::dropIfExists('business_bank_accounts');
        Schema::dropIfExists('businesses');
    }
};
