<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('sku', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('products', 'public');
            }
            $data['gallery'] = $gallery;
        }

        // Tags as array
        if (!empty($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('gallery')) {
            if ($product->gallery) {
                foreach ($product->gallery as $old) Storage::disk('public')->delete($old);
            }
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('products', 'public');
            }
            $data['gallery'] = $gallery;
        }

        if (!empty($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        if ($product->gallery) {
            foreach ($product->gallery as $img) Storage::disk('public')->delete($img);
        }
        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);
        return back()->with('success', 'Status updated.');
    }

    /**
     * Common validation
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name'            => 'required|string|max:255',
            'subtitle'        => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'price_bdt'       => 'required|numeric|min:0',
            'price_usd'       => 'required|numeric|min:0',
            'discount_price'  => 'nullable|numeric|min:0',
            'image'           => 'nullable|image|max:2048',
            'gallery.*'       => 'nullable|image|max:2048',
            'quantity'        => 'required|integer|min:0',
            'stock'           => 'required|integer|min:0',
            'sku'             => 'nullable|string|max:100',
            'category'        => 'nullable|string|max:100',
            'brand'           => 'nullable|string|max:100',
            'tags'            => 'nullable|string',
            'rating'          => 'nullable|numeric|min:0|max:5',
            'review_count'    => 'nullable|integer|min:0',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
            'published_at'    => 'nullable|date',
            'meta_title'      => 'nullable|string|max:255',
            'meta_description'=> 'nullable|string',
        ]);
    }
}