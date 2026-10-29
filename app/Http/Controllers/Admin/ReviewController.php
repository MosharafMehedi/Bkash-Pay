<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Services\AuditLogService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviews,
        protected AuditLogService $audit
    ) {}

    public function index(Request $request)
    {
        $query = Product::query()
            ->whereHas('reviews', fn ($q) => $q->where('is_approved', true))
            ->withCount(['reviews as total_reviews_count' => fn ($q) => $q->where('is_approved', true)]);

        if ($q = $request->query('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        $sort = $request->query('sort', 'most-reviewed');
        match ($sort) {
            'highest-rated' => $query->orderByDesc('rating'),
            'lowest-rated'  => $query->orderBy('rating'),
            'newest'        => $query->latest(),
            default         => $query->orderByDesc('total_reviews_count'),
        };

        $products = $query->paginate(15)->withQueryString();

        $stats = [
            'total_products'  => Product::whereHas('reviews')->count(),
            'total_reviews'   => Review::where('is_approved', true)->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'total_replies'   => ReviewReply::where('is_approved', true)->count(),
        ];

        return view('admin.reviews.index', compact('products', 'stats'));
    }

    public function show(Request $request, Product $product)
    {
        $query = Review::with(['user', 'topLevelReplies.user'])
            ->where('product_id', $product->id);

        if ($rating = $request->query('rating')) {
            $query->where('rating', (int) $rating);
        }

        $status = $request->query('status', 'all');
        if ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'pending') {
            $query->where('is_approved', false);
        }

        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'oldest'  => $query->oldest(),
            'highest' => $query->orderByDesc('rating'),
            'lowest'  => $query->orderBy('rating'),
            default   => $query->latest(),
        };

        $reviews = $query->paginate(15)->withQueryString();
        $ratingBreakdown = $this->reviews->getRatingBreakdown($product);

        return view('admin.reviews.show', compact('product', 'reviews', 'ratingBreakdown'));
    }

    public function destroy(Review $review)
    {
        try {
            $id = $review->id;
            $productName = $review->product?->name ?? 'unknown';

            $this->reviews->deleteReview($review);

            $this->audit->logAction("Review deleted (ID: {$id}, Product: {$productName})");

            return back()->with('success', 'Review deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Admin review delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete review.');
        }
    }

    public function approve(Review $review)
    {
        try {
            $this->reviews->approveReview($review);

            $this->audit->logAction(
                "Review approved (ID: {$review->id})",
                $review,
                ['new' => ['is_approved' => true]]
            );

            return back()->with('success', 'Review approved.');
        } catch (\Throwable $e) {
            Log::error('Review approve failed: ' . $e->getMessage());
            return back()->with('error', 'Could not approve review.');
        }
    }

    public function reject(Review $review)
    {
        try {
            $this->reviews->rejectReview($review);

            $this->audit->logAction(
                "Review rejected (ID: {$review->id})",
                $review,
                ['new' => ['is_approved' => false]]
            );

            return back()->with('success', 'Review rejected and hidden.');
        } catch (\Throwable $e) {
            Log::error('Review reject failed: ' . $e->getMessage());
            return back()->with('error', 'Could not reject review.');
        }
    }

    public function destroyReply(ReviewReply $reply)
    {
        try {
            $id = $reply->id;
            $this->reviews->deleteReply($reply);

            $this->audit->logAction("Review reply deleted (ID: {$id})");

            return back()->with('success', 'Reply deleted.');
        } catch (\Throwable $e) {
            Log::error('Admin reply delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete reply.');
        }
    }
}