<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviews)
    {
    }

    /**
     * Product list — only products with reviews.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->whereHas('reviews', function ($q) {
                $q->where('is_approved', true);
            })
            ->withCount(['reviews as total_reviews_count' => function ($q) {
                $q->where('is_approved', true);
            }]);

        // Search
        if ($q = $request->query('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        // Sort
        $sort = $request->query('sort', 'most-reviewed');
        match ($sort) {
            'highest-rated' => $query->orderByDesc('rating'),
            'lowest-rated'  => $query->orderBy('rating'),
            'newest'        => $query->latest(),
            default         => $query->orderByDesc('total_reviews_count'),
        };

        $products = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total_products'  => Product::whereHas('reviews')->count(),
            'total_reviews'   => Review::where('is_approved', true)->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'total_replies'   => ReviewReply::where('is_approved', true)->count(),
        ];

        return view('admin.reviews.index', compact('products', 'stats'));
    }

    /**
     * Show all reviews of a specific product.
     */
    public function show(Request $request, Product $product)
    {
        $query = Review::with(['user', 'topLevelReplies.user'])
            ->where('product_id', $product->id);

        // Filter by rating
        if ($rating = $request->query('rating')) {
            $query->where('rating', (int) $rating);
        }

        // Filter by status
        $status = $request->query('status', 'all');
        if ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'pending') {
            $query->where('is_approved', false);
        }

        // Sort
        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'oldest'  => $query->oldest(),
            'highest' => $query->orderByDesc('rating'),
            'lowest'  => $query->orderBy('rating'),
            default   => $query->latest(),
        };

        $reviews = $query->paginate(15)->withQueryString();

        // Rating breakdown
        $ratingBreakdown = $this->reviews->getRatingBreakdown($product);

        return view('admin.reviews.show', compact('product', 'reviews', 'ratingBreakdown'));
    }

    /**
     * Delete a review (with replies).
     */
    public function destroy(Review $review)
    {
        try {
            $this->reviews->deleteReview($review);

            return back()->with('success', 'Review deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Admin review delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete review.');
        }
    }

    /**
     * Approve a review.
     */
    public function approve(Review $review)
    {
        try {
            $this->reviews->approveReview($review);

            return back()->with('success', 'Review approved.');
        } catch (\Throwable $e) {
            Log::error('Review approve failed: ' . $e->getMessage());
            return back()->with('error', 'Could not approve review.');
        }
    }

    /**
     * Reject a review (hide from public).
     */
    public function reject(Review $review)
    {
        try {
            $this->reviews->rejectReview($review);

            return back()->with('success', 'Review rejected and hidden.');
        } catch (\Throwable $e) {
            Log::error('Review reject failed: ' . $e->getMessage());
            return back()->with('error', 'Could not reject review.');
        }
    }

    /**
     * Delete a reply.
     */
    public function destroyReply(ReviewReply $reply)
    {
        try {
            $this->reviews->deleteReply($reply);

            return back()->with('success', 'Reply deleted.');
        } catch (\Throwable $e) {
            Log::error('Admin reply delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete reply.');
        }
    }
}