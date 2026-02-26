<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('quote_number')->unique();
            $table->string('reference')->nullable();
            $table->enum('status', ['draft', 'sent', 'viewed', 'signed', 'declined', 'expired', 'converted'])->default('draft');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_type', 5, 2)->nullable();
            $table->enum('discount_type_value', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('sign_token', 64)->nullable()->unique();
            $table->timestamp('signed_at')->nullable();
            $table->string('signed_ip')->nullable();
            $table->text('signature_data')->nullable();
            $table->string('signer_name')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            // NO FOREIGN KEY HERE - just the column
            $table->unsignedBigInteger('converted_invoice_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'issue_date']);
            $table->index('sign_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};