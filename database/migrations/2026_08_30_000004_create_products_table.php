<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();

            $table->decimal('price_bdt', 10, 2);
            $table->decimal('price_usd', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();

            $table->string('image')->nullable();
            $table->json('gallery')->nullable();

            $table->integer('quantity')->default(0);
            $table->integer('stock')->default(0);
            $table->string('sku')->nullable()->unique();

            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->json('tags')->nullable();

            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'published_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};