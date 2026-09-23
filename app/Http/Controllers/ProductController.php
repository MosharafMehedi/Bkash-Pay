<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected CheckoutService $checkout,
        protected OrderService $orderService,
    ) {
    }

    public function index()
    {
        $products = Product::active()->orderBy('id')->get();
        return view('products.index', compact('products'));
    }

    public function checkout(Product $product)
    {
        if (! $product->is_active) {
            return redirect()->route('products.index')
                ->with('error', 'This product is not available.');
        }

        if ($product->stock <= 0) {
            return redirect()->route('products.index')
                ->with('error', 'Sorry, this product is out of stock.');
        }

        $summary = $this->checkout->resolve(auth()->user(), $product);

        return view('checkout.show', compact('product', 'summary'));
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