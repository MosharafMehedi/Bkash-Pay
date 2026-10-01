<x-app-layout>
    @section('title', 'Reviews Management')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .ar-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .ar-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .ar-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .ar-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        /* Stats */
        .ar-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;
            margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 640px) { .ar-stats { grid-template-columns: repeat(2, 1fr); } }

        .ar-stat {
            padding: 0.75rem 0.9rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .ar-stat-label { font-size: 0.65rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.3rem; }
        .ar-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.35rem; color: var(--text-hi); }
        .ar-stat.cyan .ar-stat-value { color: var(--cyan); }
        .ar-stat.violet .ar-stat-value { color: var(--violet); }
        .ar-stat.green .ar-stat-value { color: var(--green); }
        .ar-stat.amber .ar-stat-value { color: #fbbf24; }

        /* Filters */
        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        /* Table */
        .ar-table-wrap {
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); border-radius: 1rem; overflow: hidden;
        }
        .ar-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .ar-table thead th {
            background: rgba(11,15,25,0.85); color: var(--text-mu);
            font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;
            padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);
        }
        .ar-table tbody td {
            padding: 0.9rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1; font-size: 0.82rem; vertical-align: middle;
        }
        .ar-table tbody tr:hover { background: rgba(41,231,255,0.035); }

        .ar-prod {
            display: flex; align-items: center; gap: 0.75rem;
        }
        .ar-prod-img {
            width: 44px; height: 44px;
            border-radius: 0.55rem; overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .ar-prod-img img { width: 100%; height: 100%; object-fit: cover; }
        .ar-prod-img svg { color: #64748b; }

        .ar-prod-name {
            font-weight: 600; color: var(--text-hi);
            font-size: 0.85rem;
            max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        .ar-rating {
            display: inline-flex; align-items: center; gap: 0.35rem;
            font-family: 'Sora', sans-serif; font-weight: 700;
            color: #fbbf24; font-size: 0.9rem;
        }
        .ar-rating .num { color: var(--text-hi); font-size: 0.85rem; }

        .ar-count {
            display: inline-flex; padding: 0.2rem 0.6rem; border-radius: 999px;
            font-size: 0.72rem; font-weight: 700;
            background: rgba(41,231,255,0.12); color: var(--cyan);
            border: 1px solid rgba(41,231,255,0.28);
        }

        .ar-action {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.5rem 0.9rem;
            border-radius: 0.55rem;
            font-size: 0.75rem; font-weight: 600;
            color: var(--cyan);
            background: rgba(41,231,255,0.1);
            border: 1px solid rgba(41,231,255,0.3);
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .ar-action:hover { background: rgba(41,231,255,0.2); }
        .ar-action svg { width: 13px; height: 13px; }

        /* Empty */
        .ar-empty {
            text-align: center; padding: 4rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
        }
        .ar-empty svg { width: 60px; height: 60px; margin: 0 auto 1rem; opacity: 0.4; }
        .ar-empty-title { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.35rem; }
    </style>

    <div class="ar-page max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="ar-header">
            <div>
                <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Reviews Management</div>
                <div class="ar-header-title">Product Reviews</div>
                <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                    Manage customer reviews and replies
                </p>
            </div>

            <div class="ar-stats">
                <div class="ar-stat cyan">
                    <div class="ar-stat-label">Products</div>
                    <div class="ar-stat-value">{{ $stats['total_products'] }}</div>
                </div>
                <div class="ar-stat green">
                    <div class="ar-stat-label">Approved</div>
                    <div class="ar-stat-value">{{ $stats['total_reviews'] }}</div>
                </div>
                <div class="ar-stat amber">
                    <div class="ar-stat-label">Pending</div>
                    <div class="ar-stat-value">{{ $stats['pending_reviews'] }}</div>
                </div>
                <div class="ar-stat violet">
                    <div class="ar-stat-label">Replies</div>
                    <div class="ar-stat-value">{{ $stats['total_replies'] }}</div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="rounded-xl p-4 mb-5 flex flex-wrap gap-3" style="background:rgba(255,255,255,0.03); border:1px solid var(--glass-border);">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search product..."
                   class="filter-input" style="flex:1; min-width:220px;">

            <select name="sort" class="filter-select" style="min-width:180px;" onchange="this.form.submit()">
                <option value="most-reviewed" {{ request('sort') === 'most-reviewed' ? 'selected' : '' }}>Most reviewed</option>
                <option value="highest-rated" {{ request('sort') === 'highest-rated' ? 'selected' : '' }}>Highest rated</option>
                <option value="lowest-rated" {{ request('sort') === 'lowest-rated' ? 'selected' : '' }}>Lowest rated</option>
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
            </select>

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- Table --}}
        @if ($products->count())
            <div class="ar-table-wrap">
                <div style="overflow-x:auto;">
                    <table class="ar-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Reviews</th>
                                <th>Pending</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php
                                    $pendingCount = \App\Models\Review::where('product_id', $product->id)
                                        ->where('is_approved', false)
                                        ->count();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="ar-prod">
                                            <div class="ar-prod-img">
                                                @if ($product->image)
                                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                                @else
                                                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="ar-prod-name">{{ $product->name }}</div>
                                                <div style="font-size:0.68rem; color:var(--text-mu); margin-top:0.15rem;">
                                                    {{ $product->category ?? 'Uncategorized' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ar-rating">
                                            <span>★</span>
                                            <span class="num">{{ number_format($product->rating ?? 0, 1) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ar-count">{{ $product->total_reviews_count }}</span>
                                    </td>
                                    <td>
                                        @if ($pendingCount > 0)
                                            <span class="ar-count" style="background:rgba(251,191,36,0.12); color:#fbbf24; border-color:rgba(251,191,36,0.3);">
                                                {{ $pendingCount }}
                                            </span>
                                        @else
                                            <span style="color:var(--text-mu); font-size:0.72rem;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex; justify-content:flex-end;">
                                            <a href="{{ route('admin.reviews.show', $product) }}" class="ar-action">
                                                View Reviews
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $products->links() }}</div>
        @else
            <div class="ar-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <div class="ar-empty-title">No reviews yet</div>
                <p>Products with reviews will appear here.</p>
            </div>
        @endif
    </div>
</x-app-layout>