<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sslcommerz_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tran_id')->nullable()->index();
            $table->string('val_id')->nullable();
            $table->string('bank_tran_id')->nullable();
            $table->string('card_type')->nullable(); // e.g. VISA, MASTERCARD, DBBL-Nexus, bKash
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->string('status')->default('pending'); // pending | success | failed | cancelled
            $table->string('gateway_status')->nullable(); // raw SSLCommerz status (VALID, FAILED, etc.)
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sslcommerz_transactions');
    }
};
