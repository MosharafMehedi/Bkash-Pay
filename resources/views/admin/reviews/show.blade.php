<x-app-layout>
    @section('title', 'Reviews — ' . $product->name)

    <style>
        .ar-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .ar-back {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: var(--text-mu); font-size: 0.85rem; font-weight: 500;
            text-decoration: none; margin-bottom: 1.25rem;
        }
        .ar-back:hover { color: var(--cyan); }

        /* Product header */
        .ar-prod-header {
            display: flex; gap: 1rem; padding: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            margin-bottom: 1.25rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .ar-prod-header-img {
            width: 76px; height: 76px;
            border-radius: 0.85rem; overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .ar-prod-header-img img { width: 100%; height: 100%; object-fit: cover; }
        .ar-prod-header-info { flex: 1; min-width: 0; }
        .ar-prod-header-name {
            font-family: 'Sora', sans-serif; font-size: 1.2rem; font-weight: 700;
            color: var(--text-hi); margin-bottom: 0.35rem;
        }
        .ar-prod-header-meta {
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.8rem; color: var(--text-mu); flex-wrap: wrap;
        }
        .ar-prod-header-stars { color: #fbbf24; letter-spacing: 0.05em; }
        .ar-prod-header-avg {
            font-family: 'Sora', sans-serif; font-weight: 700;
            color: var(--text-hi); font-size: 1rem;
        }

        /* Filters */
        .ar-filters {
            display: flex; gap: 0.75rem; margin-bottom: 1.25rem;
            flex-wrap: wrap; align-items: center;
        }
        .ar-filter-tabs { display: flex; gap: 0.35rem; flex-wrap: wrap; flex: 1; }
        .ar-filter-tab {
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem; font-weight: 600;
            color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .ar-filter-tab:hover { color: var(--text-hi); border-color: rgba(41,231,255,0.4); }
        .ar-filter-tab.active {
            color: var(--cyan);
            background: rgba(41,231,255,0.1);
            border-color: rgba(41,231,255,0.5);
        }
        .ar-select {
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-hi);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            outline: none;
            cursor: pointer;
        }
        .ar-select option { background: #111827; }

        /* Review card */
        .ar-card {
            padding: 1.25rem;
            border-radius: 0.95rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            margin-bottom: 1rem;
        }
        .ar-card-head {
            display: flex; align-items: flex-start; gap: 0.85rem;
            margin-bottom: 0.85rem;
        }
        .ar-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: linear-gradient(135deg, rgba(41,231,255,0.25), rgba(167,139,250,0.25));
            border: 1px solid rgba(41,231,255,0.3);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Sora', sans-serif; font-weight: 700;
            color: var(--cyan); font-size: 1rem; flex-shrink: 0;
        }
        .ar-card-info { flex: 1; min-width: 0; }
        .ar-card-name {
            font-size: 0.9rem; font-weight: 700; color: var(--text-hi);
            display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
            margin-bottom: 0.25rem;
        }
        .ar-card-meta {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.72rem; color: var(--text-mu); flex-wrap: wrap;
        }
        .ar-stars-sm { color: #fbbf24; letter-spacing: 0.03em; font-size: 0.85rem; }

        .ar-badge {
            display: inline-flex; align-items: center; gap: 0.25rem;
            padding: 0.15rem 0.5rem; border-radius: 999px;
            font-size: 0.62rem; font-weight: 700;
            letter-spacing: 0.03em;
        }
        .ar-badge.verified { background: rgba(52,211,153,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.28); }
        .ar-badge.approved { background: rgba(52,211,153,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.28); }
        .ar-badge.pending  { background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
        .ar-badge.admin    { background: rgba(167,139,250,0.15); color: var(--violet); border: 1px solid rgba(167,139,250,0.3); }

        .ar-card-body { margin-top: 0.5rem; }
        .ar-review-title {
            font-size: 0.9rem; font-weight: 600; color: var(--text-hi);
            margin-bottom: 0.35rem;
        }
        .ar-review-comment {
            font-size: 0.85rem; color: #cbd5e1;
            line-height: 1.6; white-space: pre-wrap;
        }

        /* ═══ Replies toggle button ═══ */
        .ar-replies-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.55rem 0.95rem;
            border-radius: 0.6rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--cyan);
            background: rgba(41,231,255,0.08);
            border: 1px solid rgba(41,231,255,0.28);
            cursor: pointer;
            transition: all 0.2s;
        }
        .ar-replies-toggle:hover {
            background: rgba(41,231,255,0.16);
            border-color: rgba(41,231,255,0.5);
        }
        .ar-replies-toggle .arrow {
            width: 14px; height: 14px;
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }
        .ar-replies-toggle.open .arrow {
            transform: rotate(180deg);
        }
        .ar-replies-toggle .count {
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            font-size: 0.7rem;
            background: rgba(41,231,255,0.2);
            color: var(--cyan);
            border: 1px solid rgba(41,231,255,0.35);
        }

        /* ═══ Replies container (collapsible) ═══ */
        .ar-replies {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--glass-border);
            overflow: hidden;
            transition: max-height 0.35s ease, opacity 0.25s ease;
        }
        .ar-replies.collapsed {
            max-height: 0;
            opacity: 0;
            padding-top: 0;
            margin-top: 0;
            border-top: none;
        }

        .ar-reply {
            display: flex; gap: 0.75rem; margin-bottom: 0.85rem;
            padding: 0.75rem; border-radius: 0.6rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.04);
        }
        .ar-reply:last-child { margin-bottom: 0; }
        .ar-reply-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
            color: var(--text-hi); flex-shrink: 0;
        }
        .ar-reply-avatar.admin { background: rgba(167,139,250,0.15); border-color: rgba(167,139,250,0.35); }
        .ar-reply-body { flex: 1; min-width: 0; }
        .ar-reply-name {
            font-size: 0.8rem; font-weight: 600; color: var(--text-hi);
            display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;
        }
        .ar-reply-meta { font-size: 0.68rem; color: var(--text-mu); margin-top: 0.1rem; }
        .ar-reply-comment {
            font-size: 0.82rem; color: #cbd5e1;
            line-height: 1.55; margin-top: 0.4rem; white-space: pre-wrap;
        }

        /* Actions */
        .ar-actions { display: flex; gap: 0.35rem; flex-shrink: 0; }
        .ar-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            color: var(--text-mu); cursor: pointer;
            transition: all 0.2s; text-decoration: none;
        }
        .ar-icon-btn:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); background: rgba(41,231,255,0.08); }
        .ar-icon-btn.danger:hover { color: #f87171; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.08); }
        .ar-icon-btn.success:hover { color: #34d399; border-color: rgba(52,211,153,0.4); background: rgba(52,211,153,0.08); }
        .ar-icon-btn svg { width: 14px; height: 14px; }

        /* Empty */
        .ar-empty {
            text-align: center; padding: 4rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
        }
        .ar-empty-title { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.35rem; }
    </style>

    <div class="ar-page max-w-5xl mx-auto">

        <a href="{{ route('admin.reviews.index') }}" class="ar-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Reviews
        </a>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Product header --}}
        <div class="ar-prod-header">
            <div class="ar-prod-header-img">
                @if ($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                @else
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#64748b;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                @endif
            </div>

            <div class="ar-prod-header-info">
                <div class="ar-prod-header-name">{{ $product->name }}</div>
                <div class="ar-prod-header-meta">
                    @php
                        $avgRating = (float) ($product->rating ?? 0);
                        $rounded = (int) round($avgRating);
                    @endphp
                    <span class="ar-prod-header-stars">
                        {{ str_repeat('★', $rounded) . str_repeat('☆', 5 - $rounded) }}
                    </span>
                    <span class="ar-prod-header-avg">{{ number_format($avgRating, 1) }}</span>
                    <span>·</span>
                    <span>{{ $product->review_count ?? 0 }} reviews</span>
                    <span>·</span>
                    <a href="{{ route('products.show', $product) }}" target="_blank" style="color:var(--cyan); text-decoration:none;">
                        View product →
                    </a>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="ar-filters">
            <div class="ar-filter-tabs">
                <a href="{{ route('admin.reviews.show', $product) }}"
                   class="ar-filter-tab {{ ! request('rating') && ! request('status') ? 'active' : '' }}">All</a>
                <a href="{{ route('admin.reviews.show', $product) }}?status=approved"
                   class="ar-filter-tab {{ request('status') === 'approved' ? 'active' : '' }}">Approved</a>
                <a href="{{ route('admin.reviews.show', $product) }}?status=pending"
                   class="ar-filter-tab {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
                @foreach ([5, 4, 3, 2, 1] as $star)
                    <a href="{{ route('admin.reviews.show', $product) }}?rating={{ $star }}"
                       class="ar-filter-tab {{ request('rating') == $star ? 'active' : '' }}">
                        {{ $star }}★ ({{ $ratingBreakdown[$star] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form method="GET" style="display:flex; gap:0.5rem;">
                @if (request('rating'))
                    <input type="hidden" name="rating" value="{{ request('rating') }}">
                @endif
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <select name="sort" class="ar-select" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="highest" {{ request('sort') === 'highest' ? 'selected' : '' }}>Highest rating</option>
                    <option value="lowest" {{ request('sort') === 'lowest' ? 'selected' : '' }}>Lowest rating</option>
                </select>
            </form>
        </div>

        {{-- Reviews list --}}
        @forelse ($reviews as $review)
            <div class="ar-card">
                <div class="ar-card-head">
                    <div class="ar-avatar">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>

                    <div class="ar-card-info">
                        <div class="ar-card-name">
                            {{ $review->user->name }}
                            @if ($review->is_verified)
                                <span class="ar-badge verified">✓ Verified</span>
                            @endif
                            @if ($review->is_approved)
                                <span class="ar-badge approved">Approved</span>
                            @else
                                <span class="ar-badge pending">Pending</span>
                            @endif
                        </div>
                        <div class="ar-card-meta">
                            <span class="ar-stars-sm">
                                {{ str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating) }}
                            </span>
                            <span>·</span>
                            <span>{{ $review->created_at->format('M d, Y · h:i A') }}</span>
                            @if ($review->isEdited())
                                <span>·</span>
                                <span style="font-style:italic;">edited</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="ar-actions">
                        @if ($review->is_approved)
                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="ar-icon-btn" title="Reject / Hide">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="ar-icon-btn success" title="Approve">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                              onsubmit="return confirm('Delete this review permanently?');" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="ar-icon-btn danger" title="Delete">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Review body --}}
                <div class="ar-card-body">
                    @if ($review->title)
                        <div class="ar-review-title">{{ $review->title }}</div>
                    @endif
                    <div class="ar-review-comment">{{ $review->comment }}</div>
                </div>

                {{-- ═══ Replies dropdown toggle ═══ --}}
                @if ($review->topLevelReplies->count() > 0)
                    @php $replyCount = $review->topLevelReplies->count(); @endphp

                    <button type="button"
                            class="ar-replies-toggle"
                            data-reply-target="replies-{{ $review->id }}"
                            onclick="toggleReplies(this)">
                        <svg class="arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span>{{ $replyCount }} {{ \Illuminate\Support\Str::plural('Reply', $replyCount) }}</span>
                        <span class="count">{{ $replyCount }}</span>
                    </button>

                    {{-- Replies list (collapsed by default) --}}
                    <div class="ar-replies collapsed" id="replies-{{ $review->id }}">
                        @foreach ($review->topLevelReplies as $reply)
                            <div class="ar-reply">
                                <div class="ar-reply-avatar {{ $reply->is_admin ? 'admin' : '' }}">
                                    {{ $reply->is_admin ? '🏪' : strtoupper(substr($reply->user->name, 0, 1)) }}
                                </div>

                                <div class="ar-reply-body">
                                    <div class="ar-reply-name">
                                        {{ $reply->user->name }}
                                        @if ($reply->is_admin)
                                            <span class="ar-badge admin">Store Owner</span>
                                        @endif
                                    </div>
                                    <div class="ar-reply-meta">
                                        {{ $reply->created_at->format('M d, Y · h:i A') }}
                                        @if ($reply->isEdited())
                                            <span style="font-style:italic;"> · edited</span>
                                        @endif
                                    </div>
                                    <div class="ar-reply-comment">{{ $reply->comment }}</div>
                                </div>

                                <form method="POST" action="{{ route('admin.replies.destroy', $reply) }}"
                                      onsubmit="return confirm('Delete this reply?');" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ar-icon-btn danger" title="Delete reply">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="ar-empty">
                <div class="ar-empty-title">No reviews found</div>
                <p>Try adjusting filters.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if ($reviews->hasPages())
            <div class="mt-5">{{ $reviews->links() }}</div>
        @endif
    </div>

    <script>
        function toggleReplies(btn) {
            const targetId = btn.dataset.replyTarget;
            const target   = document.getElementById(targetId);

            if (! target) return;

            const isOpen = btn.classList.toggle('open');
            target.classList.toggle('collapsed', ! isOpen);
        }
    </script>
</x-app-layout>