<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_website')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_postal_code')->nullable();
            $table->string('company_country')->default('US');
            $table->string('vat_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('default_currency', 3)->default('USD');
            $table->string('date_format')->default('Y-m-d');
            $table->decimal('default_tax_rate', 5, 2)->default(0);
            $table->string('invoice_prefix')->default('INV-');
            $table->string('quote_prefix')->default('QUO-');
            $table->integer('invoice_start_number')->default(1);
            $table->integer('quote_start_number')->default(1);
            $table->integer('quote_validity_days')->default(30);
            $table->integer('invoice_due_days')->default(30);
            $table->text('default_invoice_notes')->nullable();
            $table->text('default_invoice_terms')->nullable();
            $table->text('default_quote_notes')->nullable();
            $table->text('default_quote_terms')->nullable();
            $table->text('payment_instructions')->nullable();
            $table->string('primary_color')->default('#0ea5e9');
            $table->string('secondary_color')->default('#64748b');
            $table->boolean('show_logo_on_documents')->default(true);
            $table->boolean('show_payment_instructions')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};