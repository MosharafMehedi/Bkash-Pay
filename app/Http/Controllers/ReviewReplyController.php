<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewReply;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReviewReplyController extends Controller
{
    public function __construct(protected ReviewService $reviews)
    {
    }

    /**
     * Add a reply to a review.
     */
    public function store(Request $request, Review $review)
    {
        $data = $request->validate([
            'comment'   => 'required|string|min:2|max:1000',
            'parent_id' => 'nullable|exists:review_replies,id',
        ]);

        try {
            $this->reviews->addReply(
                $review,
                auth()->user(),
                $data['comment'],
                $data['parent_id'] ?? null
            );

            return back()->with('success', 'Reply posted.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            Log::error('Reply add failed: ' . $e->getMessage());
            return back()->with('error', 'Could not post reply.');
        }
    }

    /**
     * Update own reply.
     */
    public function update(Request $request, ReviewReply $reply)
    {
        abort_unless($reply->user_id === auth()->id(), 403);

        $data = $request->validate([
            'comment' => 'required|string|min:2|max:1000',
        ]);

        try {
            $this->reviews->updateReply($reply, $data['comment']);

            return back()->with('success', 'Reply updated.');
        } catch (\Throwable $e) {
            Log::error('Reply update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update reply.');
        }
    }

    /**
     * Delete own reply.
     */
    public function destroy(ReviewReply $reply)
    {
        abort_unless($reply->user_id === auth()->id(), 403);

        try {
            $this->reviews->deleteReply($reply);

            return back()->with('success', 'Reply deleted.');
        } catch (\Throwable $e) {
            Log::error('Reply delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete reply.');
        }
    }
}