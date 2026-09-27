<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected CheckoutService $checkout,
        protected OrderService $orderService,
    ) {
    }

    /**
     * Checkout page — multi-item from cart.
     */
    public function show()
    {
        $user = auth()->user();

        // Cart empty? → redirect
        if ($this->cart->isEmpty($user)) {
            return redirect()->route('products.index')
                ->with('error', 'Your cart is empty. Add products before checkout.');
        }

        $items     = $this->cart->getItems($user);
        $subtotal  = $this->cart->getSubtotal($user);
        $itemCount = $this->cart->getCount($user);

        return view('checkout.show', compact('items', 'subtotal', 'itemCount'));
    }

    /**
     * AJAX: Calculate delivery charge for a city.
     */
    public function deliveryCharge(Request $request)
    {
        $request->validate([
            'city'   => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $result = $this->orderService->calculateDeliveryCharge(
            $request->city,
            (float) $request->amount
        );

        return response()->json($result);
    }
}