<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('subtitle', 255)->nullable();

            // Images
            $table->string('image')->nullable();               // main product image
            $table->string('bg_image')->nullable();            // background

            // CTA
            $table->string('button_text', 50)->nullable();
            $table->enum('link_type', ['product', 'category', 'custom', 'url'])->default('url');
            $table->string('link_value')->nullable();

            // Layout
            $table->enum('text_position', ['left', 'right', 'center'])->default('left');
            $table->string('badge_text', 50)->nullable();
            $table->string('badge_color', 20)->nullable();

            // Scheduling
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};