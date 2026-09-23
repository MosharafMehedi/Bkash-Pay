<?php

namespace App\Services;

use App\Mail\OrderPlacedMail;
use App\Mail\OrderStatusChangedMail;
use App\Models\Coupon;
use App\Models\DeliveryCharge;
use App\Models\DeliveryStatusLog;
use App\Models\Order;
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
     * Generate 6-char alphanumeric delivery code (uppercase, no confusing chars)
     * Example: A3K9Z2
     */
    public function generateDeliveryCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
            // Remove confusing chars (0/O, 1/I/L)
            $code = str_replace(['0', 'O', '1', 'I', 'L'], ['8', 'Q', '9', 'J', 'K'], $code);
        } while (Order::where('delivery_code', $code)->exists());

        return $code;
    }

    /**
     * Create an order from any transaction source (gateway success / COD verify).
     *
     * @param array $data {
     *     user_id, source_type, source_id, product_id, quantity,
     *     subtotal, discount_amount, coupon_id, coupon_code,
     *     delivery_charge, total_amount, currency,
     *     order_type, delivery_method,
     *     delivery_name, delivery_phone, delivery_address,
     *     delivery_city, delivery_postal, delivery_note,
     * }
     */
    public function createFromTransaction(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $product = Product::find($data['product_id']);

            $order = Order::create([
                'user_id'                  => $data['user_id'],
                'order_number'             => $this->generateOrderNumber(),
                'delivery_code'            => $this->generateDeliveryCode(),
                'delivery_code_expires_at' => now()->addDays(7),
                'delivery_code_attempts'   => 0,

                'source_type'              => $data['source_type'],
                'source_id'                => $data['source_id'] ?? null,

                'product_id'               => $data['product_id'],
                'product_name'             => $product->name ?? ($data['product_name'] ?? 'Unknown'),
                'quantity'                 => $data['quantity'] ?? 1,

                'subtotal'                 => $data['subtotal'],
                'discount_amount'          => $data['discount_amount'] ?? 0,
                'coupon_id'                => $data['coupon_id'] ?? null,
                'coupon_code'              => $data['coupon_code'] ?? null,
                'delivery_charge'          => $data['delivery_charge'] ?? 0,
                'total_amount'             => $data['total_amount'],
                'currency'                 => $data['currency'] ?? 'BDT',

                'order_type'               => $data['order_type'] ?? 'delivery',
                'delivery_method'          => $data['delivery_method'] ?? 'self',

                'delivery_name'            => $data['delivery_name'],
                'delivery_phone'           => $data['delivery_phone'],
                'delivery_address'         => $data['delivery_address'] ?? null,
                'delivery_city'            => $data['delivery_city'] ?? null,
                'delivery_postal'          => $data['delivery_postal'] ?? null,
                'delivery_note'            => $data['delivery_note'] ?? null,

                'status'                   => 'pending',
                'payment_status'           => $data['payment_status'] ?? 'unpaid',
            ]);

            // Log initial status
            $this->logStatus($order, 'pending', null, 'Order placed');

            // Send order-placed email with delivery code
            try {
                Mail::to($order->user->email)->send(new OrderPlacedMail($order));
            } catch (\Throwable $e) {
                Log::error('Order placed mail failed: ' . $e->getMessage());
            }

            return $order;
        });
    }

    /**
     * Update order status + log + optional email.
     */
    public function updateStatus(
        Order $order,
        string $newStatus,
        ?User $changedBy = null,
        ?string $note = null,
        bool $sendEmail = true
    ): Order {
        // Prevent re-updating to same status
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

            // Email
            if ($sendEmail && $order->user?->email) {
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
     * Verify a delivery code for a delivery man.
     * Returns [success, message, order].
     */
    public function verifyDeliveryCode(string $code, Order $order): array
    {
        $code = strtoupper(trim($code));

        // Code already used?
        if ($order->isDeliveryCodeUsed()) {
            return [false, 'This order has already been delivered.', $order];
        }

        // Expired?
        if ($order->isDeliveryCodeExpired()) {
            return [false, 'Delivery code has expired.', $order];
        }

        // Attempts exceeded?
        if ($order->delivery_code_attempts >= 3) {
            return [false, 'Too many incorrect attempts. Try again tomorrow.', $order];
        }

        // Match?
        if ($order->delivery_code !== $code) {
            $order->increment('delivery_code_attempts');
            $remaining = 3 - $order->delivery_code_attempts;
            return [false, "Incorrect code. {$remaining} attempts remaining.", $order->fresh()];
        }

        // ✅ Success — mark as used and update status
        DB::transaction(function () use ($order) {
            $order->update([
                'delivery_code_used_at' => now(),
                'delivery_code'         => null,   // consume
            ]);
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
     * Calculate delivery charge for a city + order amount.
     */
    public function calculateDeliveryCharge(?string $city, float $orderAmount): array
    {
        if (! $city) {
            return ['charge' => 0, 'free' => false, 'estimated_days' => null];
        }

        $charge = DeliveryCharge::active()
            ->where('city', $city)
            ->first();

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
     * Cancel order + refund stock (optional).
     */
    public function cancelOrder(Order $order, ?User $cancelledBy = null, ?string $note = null): Order
    {
        if (in_array($order->status, ['delivered', 'picked_up'])) {
            throw new \RuntimeException('Cannot cancel a delivered order.');
        }

        return DB::transaction(function () use ($order, $cancelledBy, $note) {
            // Return stock (if not already returned)
            $product = $order->product;
            if ($product && $order->status !== 'cancelled') {
                $product->increment('stock', $order->quantity);
                $product->increment('quantity', $order->quantity);
            }

            return $this->updateStatus($order, 'cancelled', $cancelledBy, $note);
        });
    }

    /**
     * Mark order as returned (delivery failed).
     */
    public function returnOrder(Order $order, ?User $returnedBy = null, ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $returnedBy, $note) {
            // Return stock
            $product = $order->product;
            if ($product) {
                $product->increment('stock', $order->quantity);
                $product->increment('quantity', $order->quantity);
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