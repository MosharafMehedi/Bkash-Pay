<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->string('text', 255);
            $table->string('icon', 10)->nullable();
            $table->string('link')->nullable();
            $table->string('bg_color', 20)->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->tinyInteger('is_dismissible')->default(1)->comment('1=Dismissible, 0=Not');
            $table->tinyInteger('is_active')->default(1)->comment('1=Active, 0=Inactive');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};