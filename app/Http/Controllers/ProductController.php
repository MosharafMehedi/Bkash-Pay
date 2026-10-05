<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ReviewService $reviews)
    {
    }

    /**
     * Product grid with category filter.
     */
    public function index(Request $request)
    {
        $query = Product::active()->with('category');

        // Filter by category (includes children)
        if ($categorySlug = $request->query('category')) {
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {
                $categoryIds = [$category->id];

                // Include children categories
                $childIds = Category::where('parent_id', $category->id)
                    ->pluck('id')
                    ->toArray();

                $categoryIds = array_merge($categoryIds, $childIds);

                $query->whereIn('category_id', $categoryIds);
            }
        }

        $products = $query->orderBy('id')->get();

        // Categories for filter tabs
        $categories = Category::active()
            ->whereNull('parent_id')
            ->ordered()
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Product details page.
     */
    public function show(Product $product)
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load('category');

        // Related products (same category)
        $relatedProducts = collect();

        if ($product->category_id) {
            $relatedProducts = Product::active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->inStock()
                ->orderByDesc('rating')
                ->take(4)
                ->get();
        }

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

        // Reviews
        $filters = [
            'rating' => request('rating'),
            'sort'   => request('sort', 'newest'),
        ];

        $reviews          = $this->reviews->getReviews($product, $filters, 10);
        $ratingBreakdown  = $this->reviews->getRatingBreakdown($product);
        $userReview       = auth()->check()
            ? $this->reviews->getUserReview(auth()->user(), $product)
            : null;
        $canReview        = auth()->check()
            && $this->reviews->canReview(auth()->user(), $product)
            && ! $userReview;

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'reviews',
            'ratingBreakdown',
            'userReview',
            'canReview'
        ));
    }
}