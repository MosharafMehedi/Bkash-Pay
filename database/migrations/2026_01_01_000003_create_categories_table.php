<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon', 20)->nullable();

            $table->tinyInteger('is_featured')->default(0)->comment('1=Featured, 0=Not Featured');
            $table->tinyInteger('is_active')->default(1)->comment('1=Active, 0=Inactive');

            $table->integer('sort_order')->default(0);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'is_active']);
            $table->index('sort_order');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};