<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * AJAX endpoint: validate a coupon for the current product.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code'       => 'required|string',
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user    = $request->user();
        $subtotal = (float) $product->final_price_bdt;

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (! $coupon) {
            return response()->json([
                'ok'      => false,
                'message' => 'Invalid coupon code.',
            ], 422);
        }

        [$valid, $error] = $coupon->isValid();
        if (! $valid) {
            return response()->json(['ok' => false, 'message' => $error], 422);
        }

        if ($coupon->hasUserReachedLimit($user->id)) {
            return response()->json([
                'ok'      => false,
                'message' => 'You have already used this coupon.',
            ], 422);
        }

        if ($subtotal < (float) $coupon->min_order) {
            return response()->json([
                'ok'      => false,
                'message' => 'Minimum order ৳' . number_format($coupon->min_order, 0) . ' required.',
            ], 422);
        }

        $discount = $coupon->discountFor($subtotal);
        $newTotal = max($subtotal - $discount, 0);

        return response()->json([
            'ok'       => true,
            'message'  => 'Coupon applied! You saved ৳' . number_format($discount, 0),
            'coupon'   => [
                'code'     => $coupon->code,
                'type'     => $coupon->type,
                'value'    => $coupon->value,
            ],
            'discount' => round($discount, 2),
            'subtotal' => $subtotal,
            'total'    => round($newTotal, 2),
        ]);
    }
}