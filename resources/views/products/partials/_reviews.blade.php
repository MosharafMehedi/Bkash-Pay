{{-- ═══════════ REVIEWS SECTION ═══════════ --}}
@php
    $reviewService = app(\App\Services\ReviewService::class);
    $totalReviews  = $product->review_count ?? 0;
    $avgRating     = (float) ($product->rating ?? 0);
@endphp

<section class="pdr-section" id="reviews">
    <div class="pdr-header">
        <div>
            <h2 class="pdr-title">Customer Reviews</h2>
            <div class="pdr-summary">
                @if ($totalReviews > 0)
                    <span class="pdr-avg">{{ number_format($avgRating, 1) }}</span>
                    <span class="pdr-stars">
                        @php
                            $rounded = (int) round($avgRating);
                            echo str_repeat('★', $rounded) . str_repeat('☆', 5 - $rounded);
                        @endphp
                    </span>
                    <span class="pdr-count">· {{ $totalReviews }} {{ \Illuminate\Support\Str::plural('review', $totalReviews) }}</span>
                @else
                    <span class="pdr-count">No reviews yet</span>
                @endif
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="pdr-alert success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="pdr-alert error">{{ session('error') }}</div>
    @endif

    <div class="pdr-layout">

        {{-- ══ LEFT: Breakdown + Form ══ --}}
        <div class="pdr-left">

            {{-- Rating breakdown --}}
            @if ($totalReviews > 0)
                <div class="pdr-breakdown">
                    <div class="pdr-breakdown-title">Rating Breakdown</div>

                    @foreach ($ratingBreakdown as $star => $count)
                        @php
                            $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                        @endphp
                        <div class="pdr-bar-row">
                            <span class="pdr-bar-star">{{ $star }}★</span>
                            <div class="pdr-bar-track">
                                <div class="pdr-bar-fill" style="width: {{ $pct }}%;"></div>
                            </div>
                            <span class="pdr-bar-count">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Review form / status --}}
            <div class="pdr-form-wrap">

                @guest
                    <div class="pdr-notice">
                        <div class="pdr-notice-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="pdr-notice-title">Want to share your experience?</div>
                            <a href="{{ route('login') }}" class="pdr-notice-btn">Login to write a review</a>
                        </div>
                    </div>
                @endguest

                @auth
                    @if ($userReview)
                        {{-- Show edit form --}}
                        <div class="pdr-form-card" id="editReviewCard" style="display:none;">
                            <div class="pdr-form-title">Edit Your Review</div>
                            <form method="POST" action="{{ route('reviews.update', $userReview) }}">
                                @csrf @method('PATCH')
                                @include('products.partials._review-form-fields', [
                                    'review' => $userReview,
                                    'submitText' => 'Update Review',
                                    'cancelEdit' => true,
                                ])
                            </form>
                        </div>

                        <div class="pdr-reviewed-notice" id="reviewedNotice">
                            <div class="pdr-reviewed-icon">✓</div>
                            <div>
                                <div class="pdr-reviewed-title">You've reviewed this product</div>
                                <div class="pdr-reviewed-sub">Thanks for sharing your thoughts!</div>
                            </div>
                            <button type="button" class="pdr-btn-edit" onclick="
                                document.getElementById('reviewedNotice').style.display='none';
                                document.getElementById('editReviewCard').style.display='';
                            ">Edit</button>
                        </div>
                    @elseif ($canReview)
                        {{-- Write new review --}}
                        <div class="pdr-form-card">
                            <div class="pdr-form-title">Write a Review</div>
                            <form method="POST" action="{{ route('reviews.store', $product) }}">
                                @csrf
                                @include('products.partials._review-form-fields', [
                                    'review' => null,
                                    'submitText' => 'Submit Review',
                                    'cancelEdit' => false,
                                ])
                            </form>
                        </div>
                    @else
                        <div class="pdr-notice">
                            <div class="pdr-notice-icon">!</div>
                            <div>
                                <div class="pdr-notice-title">You cannot review this product</div>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        {{-- ══ RIGHT: Reviews list ══ --}}
        <div class="pdr-right">

            {{-- Filters --}}
            @if ($totalReviews > 0)
                <form method="GET" action="{{ route('products.show', $product) }}#reviews" class="pdr-filters">
                    <div class="pdr-filter-tabs">
                        <a href="{{ route('products.show', $product) }}#reviews"
                           class="pdr-filter-tab {{ ! request('rating') ? 'active' : '' }}">All</a>
                        @foreach ([5, 4, 3, 2, 1] as $star)
                            <a href="{{ route('products.show', $product) }}?rating={{ $star }}#reviews"
                               class="pdr-filter-tab {{ request('rating') == $star ? 'active' : '' }}">
                                {{ $star }}★ ({{ $ratingBreakdown[$star] ?? 0 }})
                            </a>
                        @endforeach
                    </div>

                    <select name="sort" class="pdr-sort-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="highest" {{ request('sort') === 'highest' ? 'selected' : '' }}>Highest rating</option>
                        <option value="lowest" {{ request('sort') === 'lowest' ? 'selected' : '' }}>Lowest rating</option>
                    </select>

                    @if (request('rating'))
                        <input type="hidden" name="rating" value="{{ request('rating') }}">
                    @endif
                </form>
            @endif

            {{-- Reviews list --}}
            @forelse ($reviews as $review)
                @include('products.partials._review-card', ['review' => $review])
            @empty
                <div class="pdr-empty">
                    <div class="pdr-empty-icon">
                        <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="pdr-empty-title">No reviews yet</div>
                    <div class="pdr-empty-sub">Be the first to review this product!</div>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if ($reviews->hasPages())
                <div class="pdr-pagination">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</section>