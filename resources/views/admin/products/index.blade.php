<x-app-layout>
    @section('title', 'Manage Products')

    <style>
        .admin-page {
            --cyan: #29e7ff;
            --violet: #a78bfa;
            --pink: #ff5fb0;
            --text-hi: #f1f0fb;
            --text-mu: #9a94b8;
            --glass: rgba(255,255,255,0.045);
            --glass-border: rgba(255,255,255,0.09);
        }

        /* ── Card table ── */
        .admin-table-wrap {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-radius: 1rem;
            overflow: hidden;
        }
        .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .admin-table thead th {
            position: sticky; top: 0; z-index: 2;
            background: rgba(11,15,25,0.85);
            backdrop-filter: blur(12px);
            color: var(--text-mu);
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 600;
            padding: 0.9rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }
        .admin-table tbody tr {
            transition: background 0.2s;
        }
        .admin-table tbody tr:hover {
            background: rgba(41,231,255,0.035);
        }
        .admin-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
            color: #cbd5e1;
            font-size: 0.875rem;
        }
        .admin-table tbody tr:last-child td { border-bottom: none; }

        /* ── Image thumb (fix) ── */
        .thumb {
            position: relative;
            width: 56px;
            height: 56px;
            border-radius: 0.75rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .admin-table tbody tr:hover .thumb {
            transform: scale(1.05);
            box-shadow: 0 6px 18px -6px rgba(41,231,255,0.4);
        }
        .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .thumb svg { color: #64748b; }

        /* ── Text bits ── */
        .prod-name {
            font-weight: 600;
            color: var(--text-hi);
            font-size: 0.9rem;
            line-height: 1.25;
            margin-bottom: 0.15rem;
        }
        .prod-cat {
            font-size: 0.72rem;
            color: var(--text-mu);
        }
        .prod-sku {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.72rem;
            color: var(--text-mu);
            background: rgba(255,255,255,0.04);
            padding: 0.25rem 0.55rem;
            border-radius: 0.4rem;
            border: 1px solid rgba(255,255,255,0.06);
            display: inline-block;
        }

        /* ── Price ── */
        .price-main {
            font-weight: 700;
            color: var(--text-hi);
            font-size: 0.95rem;
            font-family: 'Sora', 'Inter', sans-serif;
        }
        .price-sub {
            display: block;
            font-size: 0.7rem;
            color: var(--text-mu);
            margin-top: 0.15rem;
        }

        /* ── Stock badges ── */
        .badge-stock {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .badge-stock::before {
            content: '';
            width: 6px; height: 6px; border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 8px currentColor;
        }
        .badge-stock.ok  { background: rgba(52,211,153,0.12);  color: #34d399; border: 1px solid rgba(52,211,153,0.25); }
        .badge-stock.low { background: rgba(255,95,176,0.12);  color: #ff5fb0; border: 1px solid rgba(255,95,176,0.25); }
        .badge-stock.out { background: rgba(239,68,68,0.12);   color: #f87171; border: 1px solid rgba(239,68,68,0.25); }

        /* ── Rating ── */
        .rating-wrap { display: flex; align-items: center; gap: 0.35rem; }
        .rating-star { color: #fbbf24; font-size: 0.85rem; }
        .rating-num { color: var(--text-hi); font-weight: 600; font-size: 0.85rem; }
        .rating-count { color: var(--text-mu); font-size: 0.7rem; }

        /* ── Status toggle ── */
        .status-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .status-btn::before {
            content: '';
            width: 6px; height: 6px; border-radius: 50%;
            background: currentColor;
        }
        .status-btn.active   { background: rgba(52,211,153,0.12); color: #34d399; border-color: rgba(52,211,153,0.28); }
        .status-btn.active:hover { background: rgba(52,211,153,0.22); }
        .status-btn.inactive { background: rgba(148,163,184,0.12); color: #94a3b8; border-color: rgba(148,163,184,0.22); }
        .status-btn.inactive:hover { background: rgba(148,163,184,0.2); }

        /* ── Action buttons ── */
        .action-btn {
            width: 34px; height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.55rem;
            color: var(--text-mu);
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .action-btn:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.08);
        }
        .action-btn.edit:hover   { color: var(--cyan); background: rgba(41,231,255,0.1); border-color: rgba(41,231,255,0.25); }
        .action-btn.delete:hover { color: #f87171; background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); }

        /* ── Add button ── */
        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            border-radius: 0.7rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: #06050c;
            background: linear-gradient(135deg, #29e7ff, #a78bfa);
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s, box-shadow 0.2s;
            text-decoration: none;
        }
        .btn-add:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 14px 34px -10px rgba(41,231,255,0.8);
        }

        /* ── Search input ── */
        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.7rem;
            padding: 0.65rem 0.95rem;
            font-size: 0.85rem;
            color: var(--text-hi);
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus {
            border-color: rgba(41,231,255,0.5);
            background: rgba(41,231,255,0.04);
        }
        .filter-select option { background: #111827; color: #e2e8f0; }

        .filter-btn {
            padding: 0.65rem 1.15rem;
            border-radius: 0.7rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #29e7ff;
            background: rgba(41,231,255,0.12);
            border: 1px solid rgba(41,231,255,0.32);
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-btn:hover { background: rgba(41,231,255,0.2); border-color: rgba(41,231,255,0.5); }

        /* ── Empty state ── */
        .empty-cell {
            padding: 4rem 1rem !important;
            text-align: center;
            color: var(--text-mu);
        }

        /* ── Admin Header ── */
        .admin-header {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            overflow: hidden;
        }
        @media (min-width: 640px) {
            .admin-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .admin-header::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }
        .admin-header::after {
            content: "";
            position: absolute;
            right: -40px; top: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(41,231,255,0.15), transparent 70%);
            pointer-events: none;
        }

        .admin-header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .admin-header-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(41,231,255,0.18), rgba(167,139,250,0.18));
            border: 1px solid rgba(41,231,255,0.28);
            color: var(--cyan);
            box-shadow: 0 8px 22px -10px rgba(41,231,255,0.55);
            flex-shrink: 0;
        }

        .admin-header-eyebrow {
            font-size: 0.68rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--cyan);
            font-weight: 600;
            margin-bottom: 0.15rem;
        }

        .admin-header-title {
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--text-hi);
            line-height: 1.15;
            letter-spacing: -0.01em;
        }

        .admin-header-sub {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.35rem;
            font-size: 0.8rem;
            color: var(--text-mu);
            flex-wrap: wrap;
        }

        .admin-header-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            background: rgba(41,231,255,0.12);
            border: 1px solid rgba(41,231,255,0.28);
            color: var(--cyan);
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .admin-header-dot {
            color: var(--text-mu);
            opacity: 0.5;
        }

        @media (max-width: 640px) {
            .admin-header { padding: 1.25rem; }
            .admin-header-title { font-size: 1.35rem; }
            .btn-add { width: 100%; justify-content: center; }
        }
    </style>

    <div class="admin-page max-w-7xl mx-auto">

        {{-- ── Header ── --}}
        <div class="admin-header">
            {{-- Left: icon + title --}}
            <div class="admin-header-left">
                <div class="admin-header-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="admin-header-eyebrow">Catalog</div>
                    <h1 class="admin-header-title">Products</h1>
                    <p class="admin-header-sub">
                        <span class="admin-header-pill">{{ $products->total() }} items</span>
                        <span class="admin-header-dot">·</span>
                        Manage your catalog
                    </p>
                </div>
            </div>

            {{-- Right: action --}}
            <a href="{{ route('admin.products.create') }}" class="btn-add">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Product</span>
            </a>
        </div>

        {{-- ── Flash ── --}}
        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Filters ── --}}
        <form method="GET" class="glass rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-[220px]">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, SKU, category..."
                       class="filter-input w-full pl-10">
            </div>
            <select name="status" class="filter-select min-w-[140px]">
                <option value="">All status</option>
                <option value="active"   {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button class="filter-btn">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </span>
            </button>
        </form>

        {{-- ── Table ── --}}
        <div class="admin-table-wrap">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                {{-- Product --}}
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="thumb">
                                            @if ($product->image)
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" loading="lazy">
                                            @else
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="prod-name truncate max-w-[220px]">{{ $product->name }}</p>
                                            <p class="prod-cat truncate max-w-[220px]">{{ $product->category ?? 'Uncategorized' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- SKU --}}
                                <td>
                                    <span class="prod-sku">{{ $product->sku ?? '—' }}</span>
                                </td>

                                {{-- Price --}}
                                <td>
                                    <span class="price-main">৳{{ number_format($product->price_bdt, 0) }}</span>
                                    <span class="price-sub">${{ number_format($product->price_usd, 2) }}</span>
                                </td>

                                {{-- Stock --}}
                                <td>
                                    @php $badge = $product->stock_badge; @endphp
                                    <span class="badge-stock
                                        @if($badge['class'] === 'stock-ok') ok
                                        @elseif($badge['class'] === 'stock-low') low
                                        @else out @endif">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>

                                {{-- Rating --}}
                                <td>
                                    <div class="rating-wrap">
                                        <span class="rating-star">★</span>
                                        <span class="rating-num">{{ number_format($product->rating, 1) }}</span>
                                        <span class="rating-count">({{ $product->review_count }})</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="status-btn {{ $product->is_active ? 'active' : 'inactive' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="action-btn edit" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                              onsubmit="return confirm('Delete this product?');">
                                            @csrf @method('DELETE')
                                            <button class="action-btn delete" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-cell">
                                    <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-base font-semibold text-slate-300 mb-1">No products found</p>
                                    <p class="text-sm">Try adjusting filters or add a new product.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Pagination ── --}}
        <div class="mt-5">{{ $products->links() }}</div>
    </div>
</x-app-layout>