<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Optional — verified purchase tracking
            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedTinyInteger('rating');       // 1-5
            $table->string('title', 150)->nullable();
            $table->text('comment');

            $table->boolean('is_approved')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('edited_at')->nullable();

            $table->timestamps();

            // Ek user ek product e ekbar review
            $table->unique(['user_id', 'product_id'], 'uniq_user_product_review');

            // Indexes
            $table->index(['product_id', 'is_approved'], 'idx_product_approved');
            $table->index('rating');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};