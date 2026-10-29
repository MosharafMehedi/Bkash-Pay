<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AuditLogService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(
        protected CategoryService $categories,
        protected AuditLogService $audit
    ) {}

    /**
     * Product list with filters.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('sku', 'like', "%{$q}%")
                  ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$q}%"));
            });
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        if ($categoryId = $request->query('category')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = $this->categories->getParentOptions();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $categories = $this->categories->getForProductSelect();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store new product.
     */
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

        if (! empty($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        try {
            $product = Product::create($data);

            $this->audit->logAction(
                "Product created: {$product->name} (ID: {$product->id})",
                $product,
                ['new' => $product->only(['name', 'price_bdt', 'stock', 'is_active'])]
            );

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {
            Log::error('Product create failed: ' . $e->getMessage());
            return back()->with('error', 'Could not create product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Edit form.
     */
    public function edit(Product $product)
    {
        $categories = $this->categories->getForProductSelect();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product.
     */
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

        if (! empty($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        try {
            $product->update($data);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete product.
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->image) Storage::disk('public')->delete($product->image);
            if ($product->gallery) {
                foreach ($product->gallery as $img) Storage::disk('public')->delete($img);
            }

            $name = $product->name;
            $id = $product->id;
            $product->delete();

            $this->audit->logAction("Product deleted: {$name} (ID: {$id})");

            return back()->with('success', 'Product deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Product delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete product.');
        }
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Product $product)
    {
        try {
            $product->update(['is_active' => ! $product->is_active]);

            return back()->with('success', 'Status updated.');
        } catch (\Throwable $e) {
            Log::error('Product toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update status.');
        }
    }

    /**
     * Common validation.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name'             => 'required|string|max:255',
            'subtitle'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'price_bdt'        => 'required|numeric|min:0',
            'price_usd'        => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'image'            => 'nullable|image|max:2048',
            'gallery.*'        => 'nullable|image|max:2048',
            'quantity'         => 'required|integer|min:0',
            'stock'            => 'required|integer|min:0',
            'sku'              => 'nullable|string|max:100',
            'category_id'      => 'nullable|exists:categories,id',
            'brand'            => 'nullable|string|max:100',
            'tags'             => 'nullable|string',
            'rating'           => 'nullable|numeric|min:0|max:5',
            'review_count'     => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
            'published_at'     => 'nullable|date',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);
    }
}