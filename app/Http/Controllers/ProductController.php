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

    public function show(Product $product)
    {
        // Only active products visible to public
        if (! $product->is_active) {
            abort(404);
        }

        // Related products — same category, exclude current, max 4
        $relatedProducts = collect();

        if ($product->category) {
            $relatedProducts = Product::active()
                ->where('category', $product->category)
                ->where('id', '!=', $product->id)
                ->inStock()
                ->orderByDesc('rating')
                ->take(4)
                ->get();
        }

        // If not enough, fill with random
        if ($relatedProducts->count() < 4) {
            $needed = 4 - $relatedProducts->count();

            $extra = Product::active()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->inStock()
                ->inRandomOrder()
                ->take($needed)
                ->get();

            $relatedProducts = $relatedProducts->merge($extra);
        }

        return view('products.show', compact('product', 'relatedProducts'));
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