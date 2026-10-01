<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    // ═══════════════════════════════════════════════════════════
    //  REVIEWS
    // ═══════════════════════════════════════════════════════════

    /**
     * Add a review for a product.
     */
    public function addReview(User $user, Product $product, array $data): Review
    {
        // Already reviewed?
        if ($this->hasReviewed($user, $product)) {
            throw ValidationException::withMessages([
                'review' => 'You have already reviewed this product.',
            ]);
        }

        // Can review?
        if (! $this->canReview($user, $product)) {
            throw ValidationException::withMessages([
                'review' => 'You cannot review this product.',
            ]);
        }

        return DB::transaction(function () use ($user, $product, $data) {
            // Find delivered order for verified badge
            $deliveredOrder = Order::where('user_id', $user->id)
                ->whereIn('status', ['delivered', 'picked_up'])
                ->whereHas('items', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->latest('delivered_at')
                ->first();

            $review = Review::create([
                'user_id'     => $user->id,
                'product_id'  => $product->id,
                'order_id'    => $deliveredOrder?->id,
                'rating'      => (int) $data['rating'],
                'title'       => $data['title'] ?? null,
                'comment'     => $data['comment'],
                'is_approved' => true,
                'is_verified' => $deliveredOrder !== null,
            ]);

            // Recalculate product rating
            $this->recalculateRating($product);

            return $review;
        });
    }

    /**
     * Update an existing review.
     */
    public function updateReview(Review $review, array $data): Review
    {
        return DB::transaction(function () use ($review, $data) {
            $review->update([
                'rating'    => (int) $data['rating'],
                'title'     => $data['title'] ?? null,
                'comment'   => $data['comment'],
                'edited_at' => now(),
            ]);

            // Recalculate if rating changed
            $this->recalculateRating($review->product);

            return $review->fresh();
        });
    }

    /**
     * Delete a review + recalculate rating.
     */
    public function deleteReview(Review $review): void
    {
        DB::transaction(function () use ($review) {
            $product = $review->product;

            // Delete (cascade deletes replies)
            $review->delete();

            // Recalculate
            if ($product) {
                $this->recalculateRating($product);
            }
        });
    }

    /**
     * Approve a review.
     */
    public function approveReview(Review $review): Review
    {
        $review->update(['is_approved' => true]);
        $this->recalculateRating($review->product);

        return $review->fresh();
    }

    /**
     * Reject a review (hidden from public).
     */
    public function rejectReview(Review $review): Review
    {
        $review->update(['is_approved' => false]);
        $this->recalculateRating($review->product);

        return $review->fresh();
    }

    // ═══════════════════════════════════════════════════════════
    //  REPLIES
    // ═══════════════════════════════════════════════════════════

    /**
     * Add a reply to a review.
     * Only 1-level nesting allowed.
     */
    public function addReply(Review $review, User $user, string $comment, ?int $parentId = null): ReviewReply
    {
        // Validate parent (must be top-level)
        if ($parentId) {
            $parent = ReviewReply::find($parentId);

            if (! $parent || $parent->review_id !== $review->id) {
                throw ValidationException::withMessages([
                    'reply' => 'Invalid parent reply.',
                ]);
            }

            if ($parent->parent_id !== null) {
                throw ValidationException::withMessages([
                    'reply' => 'Cannot reply to a reply.',
                ]);
            }
        }

        // Is admin?
        $isAdmin = $user->hasRole('admin');

        return ReviewReply::create([
            'review_id'   => $review->id,
            'user_id'     => $user->id,
            'parent_id'   => $parentId,
            'comment'     => $comment,
            'is_admin'    => $isAdmin,
            'is_approved' => true,
        ]);
    }

    /**
     * Update own reply.
     */
    public function updateReply(ReviewReply $reply, string $comment): ReviewReply
    {
        $reply->update([
            'comment'   => $comment,
            'edited_at' => now(),
        ]);

        return $reply->fresh();
    }

    /**
     * Delete a reply.
     */
    public function deleteReply(ReviewReply $reply): void
    {
        $reply->delete();
    }

    // ═══════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════

    /**
     * Recalculate product's rating + review_count.
     */
    public function recalculateRating(?Product $product): void
    {
        if (! $product) {
            return;
        }

        $stats = Review::where('product_id', $product->id)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $product->update([
            'rating'       => round($stats->avg_rating ?? 0, 2),
            'review_count' => $stats->total ?? 0,
        ]);
    }

    /**
     * Has this user already reviewed the product?
     */
    public function hasReviewed(User $user, Product $product): bool
    {
        return Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
    }

    /**
     * Get user's existing review for a product (if any).
     */
    public function getUserReview(User $user, Product $product): ?Review
    {
        return Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();
    }

    /**
     * Can this user review this product?
     */
    public function canReview(User $user, Product $product): bool
    {
        // Product must be active
        if (! $product->is_active) {
            return false;
        }

        if ($user->status != 1) {
            return false;
        }

        return true;
    }

    /**
     * Is this a verified purchase? (delivered order exists)
     */
    public function isVerifiedPurchase(User $user, Product $product): bool
    {
        return Order::where('user_id', $user->id)
            ->whereIn('status', ['delivered', 'picked_up'])
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->exists();
    }

    /**
     * Get rating breakdown (count per star).
     * Returns [5 => 12, 4 => 6, 3 => 3, 2 => 1, 1 => 1]
     */
    public function getRatingBreakdown(Product $product): array
    {
        $rows = Review::where('product_id', $product->id)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $breakdown = [];
        for ($star = 5; $star >= 1; $star--) {
            $breakdown[$star] = $rows[$star] ?? 0;
        }

        return $breakdown;
    }

    /**
     * Get reviews with filters + pagination.
     */
    public function getReviews(Product $product, array $filters = [], int $perPage = 10)
    {
        $query = Review::with(['user', 'topLevelReplies.user'])
            ->where('product_id', $product->id)
            ->where('is_approved', true);

        // Filter by rating
        if (! empty($filters['rating'])) {
            $query->where('rating', (int) $filters['rating']);
        }

        // Sort
        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'oldest'       => $query->oldest(),
            'highest'      => $query->orderByDesc('rating'),
            'lowest'       => $query->orderBy('rating'),
            default        => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
