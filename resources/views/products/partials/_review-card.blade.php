@php
    $currentUser = auth()->user();
    $isOwner     = $currentUser && $review->user_id === $currentUser->id;
    $isAdmin     = $currentUser && $currentUser->hasRole('admin');
@endphp

<div class="pdr-card" id="review-{{ $review->id }}">
    {{-- Header --}}
    <div class="pdr-card-head">
        <div class="pdr-avatar">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>

        <div class="pdr-card-info">
            <div class="pdr-card-name">
                {{ $review->user->name }}
                @if ($review->is_verified)
                    <span class="pdr-badge verified" title="Verified purchase">
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        Verified
                    </span>
                @endif
                @if ($isOwner)
                    <span class="pdr-badge you">You</span>
                @endif
            </div>

            <div class="pdr-card-meta">
                <span class="pdr-stars-sm">
                    @php
                        echo str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating);
                    @endphp
                </span>
                <span>·</span>
                <span>{{ $review->created_at->diffForHumans() }}</span>
                @if ($review->isEdited())
                    <span>·</span>
                    <span class="pdr-edited">edited {{ $review->edited_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        @if ($isOwner || $isAdmin)
            <div class="pdr-card-actions">
                @if ($isOwner)
                    <a href="#reviews" class="pdr-icon-btn" title="Edit"
                       onclick="event.preventDefault();
                           document.getElementById('reviewedNotice').style.display='none';
                           document.getElementById('editReviewCard').style.display='';
                           document.getElementById('editReviewCard').scrollIntoView({behavior:'smooth'});
                       ">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                @endif

                @if ($isOwner || $isAdmin)
                    <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                          onsubmit="return confirm('Delete this review?');" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="pdr-icon-btn danger" title="Delete">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="pdr-card-body">
        @if ($review->title)
            <div class="pdr-review-title">{{ $review->title }}</div>
        @endif
        <div class="pdr-review-comment">{{ $review->comment }}</div>
    </div>

    {{-- Replies --}}
    @if ($review->topLevelReplies->count() > 0)
        <div class="pdr-replies">
            @foreach ($review->topLevelReplies as $reply)
                @include('products.partials._reply-card', ['reply' => $reply])
            @endforeach
        </div>
    @endif

    {{-- Reply form (inline) --}}
    @auth
        <div class="pdr-reply-form-wrap" style="display:none;" id="replyForm-{{ $review->id }}">
            <form method="POST" action="{{ route('replies.store', $review) }}">
                @csrf
                <div class="pdr-reply-form">
                    <input type="text" name="comment" maxlength="1000" minlength="2"
                           placeholder="Write a reply..." required>
                    <button type="submit">Reply</button>
                    <button type="button" class="pdr-cancel-sm"
                            onclick="document.getElementById('replyForm-{{ $review->id }}').style.display='none';">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        <button type="button" class="pdr-reply-trigger"
                onclick="document.getElementById('replyForm-{{ $review->id }}').style.display='';
                         this.style.display='none';">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
            Reply
        </button>
    @endauth
</div>