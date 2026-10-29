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
    public function addReview(User $user, Product $product, array $data): Review
    {
        if ($this->hasReviewed($user, $product)) {
            throw ValidationException::withMessages([
                'review' => 'You have already reviewed this product.',
            ]);
        }

        if (! $this->canReview($user, $product)) {
            throw ValidationException::withMessages([
                'review' => 'You cannot review this product.',
            ]);
        }

        return DB::transaction(function () use ($user, $product, $data) {
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

            $this->recalculateRating($product);

            return $review;
        });
    }

    public function updateReview(Review $review, array $data): Review
    {
        return DB::transaction(function () use ($review, $data) {
            $review->update([
                'rating'    => (int) $data['rating'],
                'title'     => $data['title'] ?? null,
                'comment'   => $data['comment'],
                'edited_at' => now(),
            ]);

            $this->recalculateRating($review->product);

            return $review->fresh();
        });
    }

    public function deleteReview(Review $review): void
    {
        DB::transaction(function () use ($review) {
            $product = $review->product;
            $review->delete();

            if ($product) {
                $this->recalculateRating($product);
            }
        });
    }

    public function approveReview(Review $review): Review
    {
        $review->update(['is_approved' => true]);
        $this->recalculateRating($review->product);
        return $review->fresh();
    }

    public function rejectReview(Review $review): Review
    {
        $review->update(['is_approved' => false]);
        $this->recalculateRating($review->product);
        return $review->fresh();
    }

    public function addReply(Review $review, User $user, string $comment, ?int $parentId = null): ReviewReply
    {
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

    public function updateReply(ReviewReply $reply, string $comment): ReviewReply
    {
        $reply->update([
            'comment'   => $comment,
            'edited_at' => now(),
        ]);

        return $reply->fresh();
    }

    public function deleteReply(ReviewReply $reply): void
    {
        $reply->delete();
    }

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

    public function hasReviewed(User $user, Product $product): bool
    {
        return Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
    }

    public function getUserReview(User $user, Product $product): ?Review
    {
        return Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();
    }

    public function canReview(User $user, Product $product): bool
    {
        if (! $product->is_active) {
            return false;
        }
        if (! $user->is_active) {
            return false;
        }
        return true;
    }

    public function isVerifiedPurchase(User $user, Product $product): bool
    {
        return Order::where('user_id', $user->id)
            ->whereIn('status', ['delivered', 'picked_up'])
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->exists();
    }

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

    public function getReviews(Product $product, array $filters = [], int $perPage = 10)
    {
        $query = Review::with(['user', 'topLevelReplies.user'])
            ->where('product_id', $product->id)
            ->where('is_approved', true);

        if (! empty($filters['rating'])) {
            $query->where('rating', (int) $filters['rating']);
        }

        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'oldest'  => $query->oldest(),
            'highest' => $query->orderByDesc('rating'),
            'lowest'  => $query->orderBy('rating'),
            default   => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}