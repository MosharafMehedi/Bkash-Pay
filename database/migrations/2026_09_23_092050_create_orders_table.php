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

            // Owner & identifiers
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();               // ORD-2025-00001
            $table->string('delivery_code', 6)->unique();           // e.g. A3K9Z2
            $table->timestamp('delivery_code_expires_at')->nullable();
            $table->timestamp('delivery_code_used_at')->nullable();
            $table->unsignedTinyInteger('delivery_code_attempts')->default(0);

            // Source (which transaction/gateway created this)
            $table->string('source_type');                          // bkash|paypal|sslcommerz|cash
            $table->unsignedBigInteger('source_id')->nullable();    // transaction table er id

            // Product snapshot
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');                         // snapshot
            $table->integer('quantity')->default(1);

            // Pricing snapshot
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code')->nullable();              // snapshot
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 10)->default('BDT');

            // Delivery type
            $table->enum('order_type', ['delivery', 'pickup'])->default('delivery');
            $table->enum('delivery_method', ['self', 'vendor', 'pickup'])->default('self');

            // Address snapshot
            $table->string('delivery_name');
            $table->string('delivery_phone', 20);
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city', 100)->nullable();
            $table->string('delivery_postal', 20)->nullable();
            $table->text('delivery_note')->nullable();

            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tracking_number')->nullable();

            // Status
            $table->enum('status', [
                'pending',
                'processing',
                'out_for_delivery',
                'delivered',
                'ready_for_pickup',
                'picked_up',
                'cancelled',
                'returned',
            ])->default('pending');

            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');

            // Timeline
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            // Meta
            $table->text('admin_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('source_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};