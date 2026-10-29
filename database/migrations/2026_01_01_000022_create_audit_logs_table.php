<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who did it (nullable — system/guest actions)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // What happened
            $table->string('event', 50); // created, updated, deleted, login, logout, custom
            $table->string('auditable_type', 255)->nullable(); // App\Models\Product
            $table->unsignedBigInteger('auditable_id')->nullable(); // product id

            // Change payload
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Context
            $table->text('description')->nullable(); // human readable
            $table->string('url', 500)->nullable();  // which page triggered
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Audit of audit — who created/updated this log itself (rare)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Indexes for filtering
            $table->index('user_id');
            $table->index('event');
            $table->index(['auditable_type', 'auditable_id'], 'audit_auditable_index');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};