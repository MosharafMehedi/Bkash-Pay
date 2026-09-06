<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bkash_transactions', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('invoice_number');
        });

        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('invoice_number');
        });

        Schema::table('sslcommerz_transactions', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::table('bkash_transactions', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });

        Schema::table('paypal_transactions', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });

        Schema::table('sslcommerz_transactions', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });
    }
};
