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

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();

            $table->string('tran_id')->nullable();
            $table->string('val_id')->nullable();
            $table->string('bank_tran_id')->nullable();
            $table->string('card_type')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('BDT');
            $table->string('status', 30)->default('pending');
            $table->string('gateway_status')->nullable();
            $table->json('raw_response')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('tran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sslcommerz_transactions');
    }
};