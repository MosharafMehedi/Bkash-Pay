<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_charges', function (Blueprint $table) {
            $table->id();

            $table->string('city', 100)->unique();
            $table->decimal('charge', 10, 2)->default(0);
            $table->decimal('free_above', 10, 2)->nullable();
            $table->integer('estimated_days')->nullable();

            $table->tinyInteger('is_active')->default(1)->comment('1=Active, 0=Inactive');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_charges');
    }
};