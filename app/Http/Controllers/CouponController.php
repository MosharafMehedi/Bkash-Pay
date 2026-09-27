<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(protected CheckoutService $checkout)
    {
    }

    /**
     * AJAX: validate coupon for the current cart.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $user = $request->user();

        $summary = $this->checkout->resolveCart($user, $request->code);

        if ($summary['coupon_error']) {
            return response()->json([
                'ok'      => false,
                'message' => $summary['coupon_error'],
            ], 422);
        }

        return response()->json([
            'ok'       => true,
            'message'  => 'Coupon applied! You saved ৳' . number_format($summary['discount_bdt'], 0),
            'coupon'   => [
                'code'  => $summary['coupon_code'],
                'type'  => $summary['coupon']->type,
                'value' => $summary['coupon']->value,
            ],
            'subtotal' => $summary['subtotal_bdt'],
            'discount' => $summary['discount_bdt'],
            'balance'  => $summary['balance_used_bdt'],
            'total'    => $summary['total_bdt'],
            'payable'  => $summary['payable_bdt'],
        ]);
    }
}