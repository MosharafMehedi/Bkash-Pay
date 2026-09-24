<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Resolve pricing, coupon and balance for a checkout.
     *
     * @return array{
     *     product: Product,
     *     subtotal_bdt: float,
     *     subtotal_usd: float,
     *     discount_bdt: float,
     *     discount_usd: float,
     *     balance_bdt: float,
     *     balance_usd: float,
     *     total_bdt: float,
     *     total_usd: float,
     *     coupon: ?Coupon,
     *     coupon_code: ?string,
     *     coupon_error: ?string,
     *     balance_used_bdt: float,
     *     balance_used_usd: float,
     *     payable_bdt: float,
     *     payable_usd: float,
     * }
     */
    public function resolve(User $user, Product $product, ?string $couponCode = null): array
    {
        $subtotalBdt = (float) $product->final_price_bdt;
        $subtotalUsd = (float) $product->price_usd;

        $coupon       = null;
        $couponError  = null;
        $discountBdt  = 0.0;
        $discountUsd  = 0.0;

        // ── Coupon ──
        if ($couponCode) {
            [$coupon, $discountBdt, $discountUsd, $couponError] =
                $this->resolveCoupon($user, $subtotalBdt, $subtotalUsd, $couponCode);
        }

        // ── Wallet balance ──
        $balanceBdt = (float) ($user->balance_bdt ?? 0);
        $balanceUsd = (float) ($user->balance_usd ?? 0);

        // Amount after coupon
        $afterCouponBdt = max($subtotalBdt - $discountBdt, 0);
        $afterCouponUsd = max($subtotalUsd - $discountUsd, 0);

        // Balance covers up to the amount
        $balanceUsedBdt = min($balanceBdt, $afterCouponBdt);
        $balanceUsedUsd = min($balanceUsd, $afterCouponUsd);

        // Payable = after coupon - balance used
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
     * Validate coupon and return [coupon, discountBdt, discountUsd, error].
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

        // BDT discount
        $discountBdt = $coupon->discountFor($subtotalBdt);

        // USD equivalent (same percentage; for fixed we scale by BDT→USD ratio)
        $discountUsd = $subtotalBdt > 0
            ? round($discountBdt * ($subtotalUsd / $subtotalBdt), 2)
            : 0;

        return [$coupon, $discountBdt, $discountUsd, null];
    }

    /**
     * Redeem the coupon (mark usage & increment counter).
     * Call this AFTER payment is confirmed.
     */
    public function redeemCoupon(?Coupon $coupon, User $user, Product $product, string $orderRef, float $discountAmount): void
    {
        if (! $coupon) {
            return;
        }

        DB::transaction(function () use ($coupon, $user, $product, $orderRef, $discountAmount) {
            $coupon->increment('used_count');

            CouponUsage::create([
                'coupon_id'       => $coupon->id,
                'user_id'         => $user->id,
                'product_id'      => $product->id,
                'order_ref'       => $orderRef,
                'discount_amount' => $discountAmount,
            ]);
        });
    }

    /**
     * Deduct wallet balance (only the portion actually used).
     * Call this AFTER payment is confirmed.
     */
    public function deductBalance(User $user, float $amountBdt = 0, float $amountUsd = 0): void
    {
        if ($amountBdt <= 0 && $amountUsd <= 0) {
            return;
        }

        if ($amountBdt > 0) {
            $user->decrement('balance_bdt', $amountBdt);
        }
        if ($amountUsd > 0) {
            $user->decrement('balance_usd', $amountUsd);
        }
    }

    /**
     * Full post-payment settlement: decrement stock, redeem coupon, deduct balance.
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

            // 1. Stock decrement
            if (! $product->decrementStock()) {
                throw new \RuntimeException('Stock unavailable for product #' . $product->id);
            }

            // 2. Coupon usage
            if ($coupon && $discountAmount > 0) {
                $coupon->increment('used_count');

                \App\Models\CouponUsage::create([
                    'coupon_id'       => $coupon->id,
                    'user_id'         => $user->id,
                    'product_id'      => $product->id,
                    'order_ref'       => $orderRef,
                    'discount_amount' => $discountAmount,
                ]);
            }

            // 3. Balance deduct
            if ($balanceUsedBdt > 0) {
                $user->decrement('balance_bdt', $balanceUsedBdt);
            }
            if ($balanceUsedUsd > 0) {
                $user->decrement('balance_usd', $balanceUsedUsd);
            }

            return true;
        });
    }

    /**
     * Auto-save delivery info to user profile if fields are empty.
     */
    public function syncUserProfile(User $user, array $deliveryData): void
    {
        $updates = [];

        if (empty($user->phone) && ! empty($deliveryData['delivery_phone'])) {
            $updates['phone'] = $deliveryData['delivery_phone'];
        }
        if (empty($user->address) && ! empty($deliveryData['delivery_address'])) {
            $updates['address'] = $deliveryData['delivery_address'];
        }
        if (empty($user->city) && ! empty($deliveryData['delivery_city'])) {
            $updates['city'] = $deliveryData['delivery_city'];
        }
        if (empty($user->postal_code) && ! empty($deliveryData['delivery_postal'])) {
            $updates['postal_code'] = $deliveryData['delivery_postal'];
        }

        if (! empty($updates)) {
            $user->update($updates);
        }
    }
}
