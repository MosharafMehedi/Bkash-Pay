<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['bkash_transactions', 'paypal_transactions', 'sslcommerz_transactions'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
                $table->foreignId('product_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['bkash_transactions', 'paypal_transactions', 'sslcommerz_transactions'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
                $table->dropConstrainedForeignId('product_id');
            });
        }
    }
};
