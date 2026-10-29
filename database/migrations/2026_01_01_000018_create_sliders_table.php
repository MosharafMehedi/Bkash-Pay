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
            $table->string('image')->nullable();
            $table->string('bg_image')->nullable();
            $table->string('button_text', 50)->nullable();

            $table->enum('link_type', ['product', 'category', 'custom', 'url'])->default('url');
            $table->string('link_value')->nullable();
            $table->enum('text_position', ['left', 'right', 'center'])->default('left');

            $table->string('badge_text', 50)->nullable();
            $table->string('badge_color', 20)->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->tinyInteger('is_active')->default(1)->comment('1=Active, 0=Inactive');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};