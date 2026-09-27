<?php

namespace App\Services;

use App\Mail\OrderPlacedMail;
use App\Mail\OrderStatusChangedMail;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\DeliveryCharge;
use App\Models\DeliveryStatusLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Generate order number: ORD-2025-00001
     */
    public function generateOrderNumber(): string
    {
        $year = now()->year;

        $lastOrder = Order::withTrashed()
            ->where('order_number', 'like', "ORD-{$year}-%")
            ->orderByDesc('id')
            ->first();

        $nextSeq = 1;
        if ($lastOrder) {
            $lastSeq = (int) substr($lastOrder->order_number, -5);
            $nextSeq = $lastSeq + 1;
        }

        return sprintf('ORD-%d-%05d', $year, $nextSeq);
    }

    /**
     * Generate 6-char alphanumeric delivery code.
     */
    public function generateDeliveryCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
            $code = str_replace(['0', 'O', '1', 'I', 'L'], ['8', 'Q', '9', 'J', 'K'], $code);
        } while (Order::where('delivery_code', $code)->exists());

        return $code;
    }

    /**
     * Create an order from the user's cart.
     */
    public function createFromCart(
        User $user,
        string $sourceType,
        ?int $sourceId = null,
        array $deliveryData = [],
        string $paymentStatus = 'unpaid',
        float $deliveryCharge = 0,
        float $discountAmount = 0,
        ?int $couponId = null,
        ?string $couponCode = null
    ): Order {
        return DB::transaction(function () use (
            $user, $sourceType, $sourceId, $deliveryData,
            $paymentStatus, $deliveryCharge, $discountAmount, $couponId, $couponCode
        ) {
            // Load cart items
            $cartItems = CartItem::with('product')
                ->where('user_id', $user->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \RuntimeException('Cart is empty.');
            }

            // Calculate subtotal
            $subtotal = 0;
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                if (! $product || ! $product->is_active) {
                    throw new \RuntimeException('Some products in your cart are no longer available.');
                }

                if ($product->stock < $cartItem->quantity) {
                    throw new \RuntimeException("Not enough stock for {$product->name}.");
                }

                $subtotal += $cartItem->line_total;
            }

            $subtotal    = round($subtotal, 2);
            $totalAmount = max($subtotal + $deliveryCharge - $discountAmount, 0);

            // Create order
            $order = Order::create([
                'user_id'                  => $user->id,
                'order_number'             => $this->generateOrderNumber(),
                'delivery_code'            => $this->generateDeliveryCode(),
                'delivery_code_expires_at' => now()->addDays(7),
                'delivery_code_attempts'   => 0,

                'source_type'              => $sourceType,
                'source_id'                => $sourceId,

                // Legacy columns (nullable)
                'product_id'               => null,
                'product_name'             => null,
                'quantity'                 => null,

                'subtotal'                 => $subtotal,
                'discount_amount'          => $discountAmount,
                'coupon_id'                => $couponId,
                'coupon_code'              => $couponCode,
                'delivery_charge'          => $deliveryCharge,
                'total_amount'             => $totalAmount,
                'currency'                 => 'BDT',

                'order_type'               => $deliveryData['order_type'] ?? 'delivery',
                'delivery_method'          => $deliveryData['delivery_method'] ?? 'self',

                'delivery_name'            => $deliveryData['delivery_name'] ?? $user->name ?? 'Customer',
                'delivery_phone'           => $deliveryData['delivery_phone'] ?? $user->phone ?? 'N/A',
                'delivery_address'         => $deliveryData['delivery_address'] ?? null,
                'delivery_city'            => $deliveryData['delivery_city'] ?? null,
                'delivery_postal'          => $deliveryData['delivery_postal'] ?? null,
                'delivery_note'            => $deliveryData['delivery_note'] ?? null,

                'status'                   => 'pending',
                'payment_status'           => $paymentStatus,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'quantity'     => $cartItem->quantity,
                    'unit_price'   => $product->final_price_bdt,
                    'total_price'  => $cartItem->line_total,
                ]);
            }

            // Log initial status
            $this->logStatus($order, 'pending', null, 'Order placed');

            // Send order placed email
            try {
                Mail::to($order->user->email)->send(new OrderPlacedMail($order));
            } catch (\Throwable $e) {
                Log::error('Order placed mail failed: ' . $e->getMessage());
            }

            return $order;
        });
    }

    /**
     * Update order status + log + email.
     */
    public function updateStatus(
        Order $order,
        string $newStatus,
        ?User $changedBy = null,
        ?string $note = null,
        bool $sendEmail = true
    ): Order {
        if ($order->status === $newStatus) {
            return $order;
        }

        return DB::transaction(function () use ($order, $newStatus, $changedBy, $note, $sendEmail) {
            $timeline = $this->timelineFieldFor($newStatus);

            $update = ['status' => $newStatus];
            if ($timeline && ! $order->{$timeline}) {
                $update[$timeline] = now();
            }

            // COD → paid when delivered/picked up
            if (in_array($newStatus, ['delivered', 'picked_up']) && $order->payment_status === 'unpaid') {
                $update['payment_status'] = 'paid';
            }

            $order->update($update);

            // Log
            $this->logStatus($order, $newStatus, $changedBy?->id, $note);

            // Email — ONLY for delivery complete statuses
            $notifyStatuses = ['delivered', 'picked_up'];

            if ($sendEmail && in_array($newStatus, $notifyStatuses) && $order->user?->email) {
                try {
                    Mail::to($order->user->email)->send(new OrderStatusChangedMail($order, $newStatus));
                } catch (\Throwable $e) {
                    Log::error('Order status mail failed: ' . $e->getMessage());
                }
            }

            return $order->fresh();
        });
    }

    /**
     * Verify delivery code.
     */
    public function verifyDeliveryCode(string $code, Order $order): array
    {
        $code = strtoupper(trim($code));

        if ($order->isDeliveryCodeUsed()) {
            return [false, 'This order has already been delivered.', $order];
        }

        if ($order->isDeliveryCodeExpired()) {
            return [false, 'Delivery code has expired.', $order];
        }

        if ($order->delivery_code_attempts >= 3) {
            return [false, 'Too many incorrect attempts. Try again tomorrow.', $order];
        }

        if ($order->delivery_code !== $code) {
            $order->increment('delivery_code_attempts');
            $remaining = 3 - $order->delivery_code_attempts;
            return [false, "Incorrect code. {$remaining} attempts remaining.", $order->fresh()];
        }

        DB::transaction(function () use ($order) {
            $order->update(['delivery_code_used_at' => now()]);
        });

        $targetStatus = $order->isPickup() ? 'picked_up' : 'delivered';

        $this->updateStatus(
            $order->fresh(),
            $targetStatus,
            auth()->user(),
            'Delivery code verified'
        );

        return [true, 'Delivery verified successfully!', $order->fresh()];
    }

    /**
     * Settle cart — stock decrement + coupon + balance.
     */
    public function settleCart(
        User $user,
        Order $order,
        ?Coupon $coupon = null,
        float $discountAmount = 0,
        float $balanceUsedBdt = 0,
        float $balanceUsedUsd = 0
    ): bool {
        return DB::transaction(function () use ($user, $order, $coupon, $discountAmount, $balanceUsedBdt, $balanceUsedUsd) {
            // Stock decrement per item
            foreach ($order->items as $orderItem) {
                $product = Product::find($orderItem->product_id);

                if ($product) {
                    $affected = Product::where('id', $product->id)
                        ->where('stock', '>=', $orderItem->quantity)
                        ->update([
                            'stock'    => DB::raw('stock - ' . (int) $orderItem->quantity),
                            'quantity' => DB::raw('GREATEST(quantity - ' . (int) $orderItem->quantity . ', 0)'),
                        ]);

                    if (! $affected) {
                        throw new \RuntimeException("Stock unavailable for {$product->name}.");
                    }
                }
            }

            // Coupon redeem
            if ($coupon && $discountAmount > 0) {
                $coupon->increment('used_count');

                CouponUsage::create([
                    'coupon_id'       => $coupon->id,
                    'user_id'         => $user->id,
                    'product_id'      => null,
                    'order_ref'       => $order->order_number,
                    'discount_amount' => $discountAmount,
                ]);
            }

            // Balance deduct
            if ($balanceUsedBdt > 0) {
                $user->decrement('balance_bdt', $balanceUsedBdt);
            }
            if ($balanceUsedUsd > 0) {
                $user->decrement('balance_usd', $balanceUsedUsd);
            }

            // Cart clear
            CartItem::where('user_id', $user->id)->delete();

            return true;
        });
    }

    /**
     * Calculate delivery charge for a city + order amount.
     */
    public function calculateDeliveryCharge(?string $city, float $orderAmount): array
    {
        if (! $city) {
            return ['charge' => 0, 'free' => false, 'estimated_days' => null];
        }

        $charge = DeliveryCharge::active()->where('city', $city)->first();

        if (! $charge) {
            return ['charge' => 0, 'free' => false, 'estimated_days' => null, 'not_found' => true];
        }

        $finalCharge = $charge->chargeFor($orderAmount);
        $isFree      = $finalCharge == 0 && $charge->charge > 0;

        return [
            'charge'         => (float) $finalCharge,
            'free'           => $isFree,
            'estimated_days' => $charge->estimated_days,
            'base_charge'    => (float) $charge->charge,
            'free_above'     => $charge->free_above ? (float) $charge->free_above : null,
        ];
    }

    /**
     * Cancel order + return stock.
     */
    public function cancelOrder(Order $order, ?User $cancelledBy = null, ?string $note = null): Order
    {
        if (in_array($order->status, ['delivered', 'picked_up'])) {
            throw new \RuntimeException('Cannot cancel a delivered order.');
        }

        return DB::transaction(function () use ($order, $cancelledBy, $note) {
            // Return stock per item
            foreach ($order->items as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product && $order->status !== 'cancelled') {
                    $product->increment('stock', $orderItem->quantity);
                    $product->increment('quantity', $orderItem->quantity);
                }
            }

            return $this->updateStatus($order, 'cancelled', $cancelledBy, $note);
        });
    }

    /**
     * Return order.
     */
    public function returnOrder(Order $order, ?User $returnedBy = null, ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $returnedBy, $note) {
            foreach ($order->items as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->increment('stock', $orderItem->quantity);
                    $product->increment('quantity', $orderItem->quantity);
                }
            }

            return $this->updateStatus($order, 'returned', $returnedBy, $note);
        });
    }

    // ── Private helpers ──

    private function logStatus(Order $order, string $status, ?int $changedBy = null, ?string $note = null): void
    {
        DeliveryStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $status,
            'changed_by' => $changedBy,
            'note'       => $note,
        ]);
    }

    private function timelineFieldFor(string $status): ?string
    {
        return match ($status) {
            'processing'       => 'confirmed_at',
            'out_for_delivery' => 'shipped_at',
            'delivered',
            'picked_up'        => 'delivered_at',
            'cancelled'        => 'cancelled_at',
            'returned'         => 'returned_at',
            default            => null,
        };
    }
}