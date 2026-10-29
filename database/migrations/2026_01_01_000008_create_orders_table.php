<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 30)->unique();
            $table->string('delivery_code', 10)->unique();
            $table->timestamp('delivery_code_expires_at')->nullable();
            $table->timestamp('delivery_code_used_at')->nullable();
            $table->tinyInteger('delivery_code_attempts')->default(0);

            $table->string('source_type', 30);
            $table->unsignedBigInteger('source_id')->nullable();

            // Legacy single product (nullable)
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->integer('quantity')->nullable();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code', 50)->nullable();
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 10)->default('BDT');

            $table->enum('order_type', ['delivery', 'pickup'])->default('delivery');
            $table->enum('delivery_method', ['self', 'vendor', 'pickup'])->default('self');

            $table->string('delivery_name')->nullable();
            $table->string('delivery_phone', 20)->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city', 100)->nullable();
            $table->string('delivery_postal', 20)->nullable();
            $table->text('delivery_note')->nullable();

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tracking_number', 100)->nullable();

            $table->enum('status', [
                'pending', 'processing', 'out_for_delivery', 'delivered',
                'ready_for_pickup', 'picked_up', 'cancelled', 'returned',
            ])->default('pending');

            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->text('admin_note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('source_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};