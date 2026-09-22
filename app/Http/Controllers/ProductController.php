<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::active()
            ->orderBy('id')
            ->get();

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

        return view('checkout.show', compact('product'));
    }
}