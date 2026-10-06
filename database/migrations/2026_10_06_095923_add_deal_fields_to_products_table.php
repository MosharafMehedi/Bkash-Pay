<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_deal')->default(false)->after('is_featured');
            $table->decimal('deal_price', 10, 2)->nullable()->after('is_deal');
            $table->timestamp('deal_ends_at')->nullable()->after('deal_price');

            $table->index(['is_deal', 'deal_ends_at']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_deal', 'deal_ends_at']);
            $table->dropColumn(['is_deal', 'deal_price', 'deal_ends_at']);
        });
    }
};