<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->text('details')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->default('unit');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['invoice_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};