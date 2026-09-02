<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable()->index();
            $table->string('capture_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('status')->default('pending'); // pending | success | failed | cancelled
            $table->string('paypal_status')->nullable(); // raw PayPal status (COMPLETED, VOIDED, etc.)
            $table->string('payer_email')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_transactions');
    }
};
