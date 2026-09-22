<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // bKash
        Schema::table('bkash_transactions', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('product_id')->constrained('coupons')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
        });

        // PayPal
        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('product_id')->constrained('coupons')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
        });

        // SSLCommerz
        Schema::table('sslcommerz_transactions', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('product_id')->constrained('coupons')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
        });

        // Cash orders
        Schema::table('cash_orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('product_id')->constrained('coupons')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('amount');
        });
    }

    public function down(): void
    {
        foreach (['bkash_transactions', 'paypal_transactions', 'sslcommerz_transactions', 'cash_orders'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropConstrainedForeignId('coupon_id');
                $table->dropColumn('discount_amount');
            });
        }
    }
};