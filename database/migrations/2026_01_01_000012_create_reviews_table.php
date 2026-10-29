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

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->string('title', 150)->nullable();
            $table->text('comment');

            $table->tinyInteger('is_verified')->default(0)->comment('1=Verified, 0=Not');
            $table->tinyInteger('is_approved')->default(1)->comment('1=Approved, 0=Pending');

            $table->timestamp('edited_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'product_id'], 'uniq_user_product_review');
            $table->index(['product_id', 'is_approved']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};