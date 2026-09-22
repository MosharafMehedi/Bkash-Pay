<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CashController extends Controller
{
    /**
     * Cash on Delivery — no gateway. Places order & decrements stock immediately.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        // Atomic decrement — returns false if someone else bought the last item
        if (! $product->decrementStock()) {
            return back()->with('error', 'Sorry, this product just went out of stock.');
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Order placed! Pay ৳' . number_format($product->final_price_bdt, 0) . ' on delivery.');
    }
}