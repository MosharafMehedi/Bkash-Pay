<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Multi-item support er jonno nullable
            // (single-item orders er jonno ekhono kaj korbe)
            $table->foreignId('product_id')->nullable()->change();
            $table->string('product_name')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable(false)->change();
            $table->string('product_name')->nullable(false)->change();
            $table->integer('quantity')->nullable(false)->change();
        });
    }
};