<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviews)
    {
    }

    /**
     * Store a new review.
     */
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:150',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        try {
            $this->reviews->addReview(auth()->user(), $product, $data);

            return back()->with('success', 'Thank you for your review!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Review add failed: ' . $e->getMessage());
            return back()->with('error', 'Could not submit review. Please try again.');
        }
    }

    /**
     * Update own review.
     */
    public function update(Request $request, Review $review)
    {
        abort_unless($review->user_id === auth()->id(), 403);

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:150',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        try {
            $this->reviews->updateReview($review, $data);

            return back()->with('success', 'Review updated.');
        } catch (\Throwable $e) {
            Log::error('Review update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update review.');
        }
    }

    /**
     * Delete own review.
     */
    public function destroy(Review $review)
    {
        abort_unless($review->user_id === auth()->id(), 403);

        try {
            $this->reviews->deleteReview($review);

            return back()->with('success', 'Review deleted.');
        } catch (\Throwable $e) {
            Log::error('Review delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete review.');
        }
    }
}