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

            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('subtitle', 255)->nullable();
            $table->text('description')->nullable();

            $table->decimal('price_bdt', 10, 2);
            $table->decimal('price_usd', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();

            $table->integer('quantity')->default(0);
            $table->integer('stock')->default(0);
            $table->string('sku', 100)->nullable()->unique();

            $table->string('brand', 100)->nullable();
            $table->json('tags')->nullable();

            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);

            $table->tinyInteger('is_featured')->default(0)->comment('1=Featured, 0=Not');
            $table->tinyInteger('is_deal')->default(0)->comment('1=Deal, 0=Not');
            $table->decimal('deal_price', 10, 2)->nullable();
            $table->timestamp('deal_ends_at')->nullable();

            $table->tinyInteger('is_active')->default(1)->comment('1=Active, 0=Inactive');

            $table->string('image')->nullable();
            $table->json('gallery')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'published_at']);
            $table->index('is_featured');
            $table->index('is_deal');
            $table->index('category_id');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};