<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bkash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable()->index();
            $table->string('trx_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->string('status')->default('pending'); // pending | success | failed | cancelled
            $table->string('transaction_status')->nullable(); // raw bKash transactionStatus
            $table->string('customer_msisdn')->nullable(); // payer's bKash number, if returned
            $table->json('raw_response')->nullable(); // full bKash response for debugging
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bkash_transactions');
    }
};
