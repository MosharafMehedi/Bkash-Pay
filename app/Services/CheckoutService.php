<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Resolve pricing for a single product (legacy).
     */
    public function resolve(User $user, Product $product, ?string $couponCode = null): array
    {
        $subtotalBdt = (float) $product->final_price_bdt;
        $subtotalUsd = (float) $product->price_usd;

        $coupon      = null;
        $couponError = null;
        $discountBdt = 0.0;
        $discountUsd = 0.0;

        if ($couponCode) {
            [$coupon, $discountBdt, $discountUsd, $couponError] =
                $this->resolveCoupon($user, $subtotalBdt, $subtotalUsd, $couponCode);
        }

        $balanceBdt = (float) ($user->balance_bdt ?? 0);
        $balanceUsd = (float) ($user->balance_usd ?? 0);

        $afterCouponBdt = max($subtotalBdt - $discountBdt, 0);
        $afterCouponUsd = max($subtotalUsd - $discountUsd, 0);

        $balanceUsedBdt = min($balanceBdt, $afterCouponBdt);
        $balanceUsedUsd = min($balanceUsd, $afterCouponUsd);

        $payableBdt = max($afterCouponBdt - $balanceUsedBdt, 0);
        $payableUsd = max($afterCouponUsd - $balanceUsedUsd, 0);

        return [
            'product'          => $product,
            'subtotal_bdt'     => round($subtotalBdt, 2),
            'subtotal_usd'     => round($subtotalUsd, 2),
            'discount_bdt'     => round($discountBdt, 2),
            'discount_usd'     => round($discountUsd, 2),
            'balance_bdt'      => round($balanceBdt, 2),
            'balance_usd'      => round($balanceUsd, 2),
            'total_bdt'        => round($afterCouponBdt, 2),
            'total_usd'        => round($afterCouponUsd, 2),
            'coupon'           => $coupon,
            'coupon_code'      => $coupon?->code,
            'coupon_error'     => $couponError,
            'balance_used_bdt' => round($balanceUsedBdt, 2),
            'balance_used_usd' => round($balanceUsedUsd, 2),
            'payable_bdt'      => round($payableBdt, 2),
            'payable_usd'      => round($payableUsd, 2),
        ];
    }

    /**
     * Resolve cart totals — multi-item.
     */
    public function resolveCart(User $user, ?string $couponCode = null): array
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        $subtotalBdt = 0;
        $subtotalUsd = 0;

        foreach ($cartItems as $cartItem) {
            if ($cartItem->product) {
                $subtotalBdt += (float) $cartItem->product->final_price_bdt * $cartItem->quantity;
                $subtotalUsd += (float) $cartItem->product->price_usd * $cartItem->quantity;
            }
        }

        $subtotalBdt = round($subtotalBdt, 2);
        $subtotalUsd = round($subtotalUsd, 2);

        $coupon      = null;
        $couponError = null;
        $discountBdt = 0.0;
        $discountUsd = 0.0;

        if ($couponCode) {
            [$coupon, $discountBdt, $discountUsd, $couponError] =
                $this->resolveCoupon($user, $subtotalBdt, $subtotalUsd, $couponCode);
        }

        $balanceBdt = (float) ($user->balance_bdt ?? 0);
        $balanceUsd = (float) ($user->balance_usd ?? 0);

        $afterCouponBdt = max($subtotalBdt - $discountBdt, 0);
        $afterCouponUsd = max($subtotalUsd - $discountUsd, 0);

        $balanceUsedBdt = min($balanceBdt, $afterCouponBdt);
        $balanceUsedUsd = min($balanceUsd, $afterCouponUsd);

        $payableBdt = max($afterCouponBdt - $balanceUsedBdt, 0);
        $payableUsd = max($afterCouponUsd - $balanceUsedUsd, 0);

        return [
            'cart_items'       => $cartItems,
            'subtotal_bdt'     => $subtotalBdt,
            'subtotal_usd'     => $subtotalUsd,
            'discount_bdt'     => round($discountBdt, 2),
            'discount_usd'     => round($discountUsd, 2),
            'balance_bdt'      => round($balanceBdt, 2),
            'balance_usd'      => round($balanceUsd, 2),
            'total_bdt'        => round($afterCouponBdt, 2),
            'total_usd'        => round($afterCouponUsd, 2),
            'coupon'           => $coupon,
            'coupon_code'      => $coupon?->code,
            'coupon_error'     => $couponError,
            'balance_used_bdt' => round($balanceUsedBdt, 2),
            'balance_used_usd' => round($balanceUsedUsd, 2),
            'payable_bdt'      => round($payableBdt, 2),
            'payable_usd'      => round($payableUsd, 2),
        ];
    }

    /**
     * Validate coupon.
     */
    private function resolveCoupon(User $user, float $subtotalBdt, float $subtotalUsd, string $code): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            return [null, 0, 0, 'Invalid coupon code.'];
        }

        [$valid, $error] = $coupon->isValid();

        if (! $valid) {
            return [null, 0, 0, $error];
        }

        if ($coupon->hasUserReachedLimit($user->id)) {
            return [null, 0, 0, 'You have already used this coupon.'];
        }

        if ($subtotalBdt < (float) $coupon->min_order) {
            return [null, 0, 0, 'Minimum order ৳' . number_format($coupon->min_order, 0) . ' required.'];
        }

        $discountBdt = $coupon->discountFor($subtotalBdt);
        $discountUsd = $subtotalBdt > 0
            ? round($discountBdt * ($subtotalUsd / $subtotalBdt), 2)
            : 0;

        return [$coupon, $discountBdt, $discountUsd, null];
    }

    /**
     * Sync delivery info to user profile (if empty).
     */
    public function syncUserProfile(User $user, array $data): void
    {
        $updates = [];

        if (empty($user->phone) && ! empty($data['delivery_phone'])) {
            $updates['phone'] = $data['delivery_phone'];
        }
        if (empty($user->address) && ! empty($data['delivery_address'])) {
            $updates['address'] = $data['delivery_address'];
        }
        if (empty($user->city) && ! empty($data['delivery_city'])) {
            $updates['city'] = $data['delivery_city'];
        }
        if (empty($user->postal_code) && ! empty($data['delivery_postal'])) {
            $updates['postal_code'] = $data['delivery_postal'];
        }

        if (! empty($updates)) {
            $user->update($updates);
        }
    }

    /**
     * Settle: legacy single-product settle.
     */
    public function settle(
        User $user,
        Product $product,
        ?Coupon $coupon,
        float $discountAmount,
        float $balanceUsedBdt,
        float $balanceUsedUsd,
        string $orderRef
    ): bool {
        return DB::transaction(function () use ($user, $product, $coupon, $discountAmount, $balanceUsedBdt, $balanceUsedUsd, $orderRef) {
            if (! $product->decrementStock()) {
                throw new \RuntimeException('Stock unavailable for product #' . $product->id);
            }

            if ($coupon && $discountAmount > 0) {
                $coupon->increment('used_count');

                CouponUsage::create([
                    'coupon_id'       => $coupon->id,
                    'user_id'         => $user->id,
                    'product_id'      => $product->id,
                    'order_ref'       => $orderRef,
                    'discount_amount' => $discountAmount,
                ]);
            }

            if ($balanceUsedBdt > 0) {
                $user->decrement('balance_bdt', $balanceUsedBdt);
            }
            if ($balanceUsedUsd > 0) {
                $user->decrement('balance_usd', $balanceUsedUsd);
            }

            return true;
        });
    }
}