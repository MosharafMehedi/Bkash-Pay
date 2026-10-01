<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('review_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nested reply — 1 level only
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('review_replies')
                ->cascadeOnDelete();

            $table->text('comment');

            $table->boolean('is_admin')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->timestamp('edited_at')->nullable();

            $table->timestamps();

            $table->index(['review_id', 'created_at'], 'idx_review_replies');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
    }
};