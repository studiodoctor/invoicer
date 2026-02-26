<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('contact_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('vat_number')->nullable();
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');
            $table->string('currency')->default('USD');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'company_name']);
            $table->index(['user_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};