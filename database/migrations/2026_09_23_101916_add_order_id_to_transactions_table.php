<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('bkash_transactions', function (Blueprint $table) {
        $table->foreignId('order_id')->nullable()->after('id')->constrained('orders')->nullOnDelete();
    });
    Schema::table('paypal_transactions', function (Blueprint $table) {
        $table->foreignId('order_id')->nullable()->after('id')->constrained('orders')->nullOnDelete();
    });
    Schema::table('sslcommerz_transactions', function (Blueprint $table) {
        $table->foreignId('order_id')->nullable()->after('id')->constrained('orders')->nullOnDelete();
    });
    Schema::table('cash_orders', function (Blueprint $table) {
        $table->foreignId('order_id')->nullable()->after('id')->constrained('orders')->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bkash_transactions', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
        Schema::table('sslcommerz_transactions', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
        Schema::table('cash_orders', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
