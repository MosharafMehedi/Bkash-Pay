<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Product grid shown on the dashboard.
     */
    public function index()
    {
        $products = Product::orderBy('id')->get();

        return view('dashboard', compact('products'));
    }

    /**
     * Checkout page for a single product — payment method selector.
     */
    public function checkout(Product $product)
    {
        return view('checkout.show', compact('product'));
    }
}
