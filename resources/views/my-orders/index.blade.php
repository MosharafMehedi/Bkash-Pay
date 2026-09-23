<x-app-layout>
    @section('title', 'My Orders')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .mo-page {
            font-family: 'Inter', sans-serif;
            --cyan: #29e7ff;
            --violet: #a78bfa;
            --pink: #ff5fb0;
            --green: #34d399;
            --text-hi: #f1f0fb;
            --text-mu: #9a94b8;
            --glass: rgba(255,255,255,0.045);
            --glass-border: rgba(255,255,255,0.09);
        }

        .mo-wrap { max-width: 1100px; margin: 0 auto; }

        /* Header */
        .mo-header {
            position: relative;
            border-radius: 1.1rem;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }
        .mo-header::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }
        .mo-header h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-hi);
        }
        .mo-header p {
            font-size: 0.82rem;
            color: var(--text-mu);
            margin-top: 0.25rem;
        }

        /* Stats */
        .mo-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            margin-top: 1.15rem;
            padding-top: 1.15rem;
            border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 640px) { .mo-stats { grid-template-columns: repeat(2, 1fr); } }

        .mo-stat {
            padding: 0.75rem 0.9rem;
            border-radius: 0.7rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
        }
        .mo-stat-label {
            font-size: 0.68rem;
            color: var(--text-mu);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }
        .mo-stat-value {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-hi);
        }
        .mo-stat.cyan  .mo-stat-value { color: var(--cyan); }
        .mo-stat.violet .mo-stat-value { color: var(--violet); }
        .mo-stat.green .mo-stat-value { color: var(--green); }

        /* Filter tabs */
        .mo-tabs {
            display: flex;
            gap: 0.4rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .mo-tab {
            padding: 0.5rem 0.9rem;
            border-radius: 0.6rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            text-decoration: none;
            transition: all 0.2s;
        }
        .mo-tab:hover { color: var(--text-hi); border-color: rgba(41,231,255,0.4); }
        .mo-tab.active {
            color: var(--cyan);
            background: rgba(41,231,255,0.1);
            border-color: rgba(41,231,255,0.5);
        }

        /* Order cards */
        .mo-list { display: flex; flex-direction: column; gap: 0.75rem; }

        .mo-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.15rem;
            border-radius: 0.9rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px);
            transition: all 0.25s;
            text-decoration: none;
            color: inherit;
        }
        .mo-card:hover {
            border-color: rgba(41,231,255,0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -12px rgba(41,231,255,0.3);
        }

        .mo-thumb {
            width: 64px;
            height: 64px;
            border-radius: 0.75rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .mo-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .mo-thumb svg { color: #64748b; width: 26px; height: 26px; }

        .mo-info { flex: 1; min-width: 0; }
        .mo-info-name {
            font-weight: 600;
            color: var(--text-hi);
            font-size: 0.92rem;
            margin-bottom: 0.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mo-info-meta {
            font-size: 0.72rem;
            color: var(--text-mu);
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .mo-info-meta strong { color: var(--text-hi); font-family: monospace; }

        .mo-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .mo-status::before {
            content: '';
            width: 6px; height: 6px; border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 8px currentColor;
        }

        .mo-amount {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-hi);
            white-space: nowrap;
        }
        .mo-arrow {
            color: var(--text-mu);
            transition: transform 0.2s;
        }
        .mo-card:hover .mo-arrow { transform: translateX(3px); color: var(--cyan); }

        /* Empty */
        .mo-empty {
            text-align: center;
            padding: 4rem 1.5rem;
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px dashed var(--glass-border);
            color: var(--text-mu);
        }
        .mo-empty svg {
            width: 60px; height: 60px;
            margin: 0 auto 1rem;
            opacity: 0.4;
        }
        .mo-empty-title {
            font-family: 'Sora', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-hi);
            margin-bottom: 0.35rem;
        }
        .mo-empty-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 1.25rem;
            padding: 0.65rem 1.15rem;
            border-radius: 0.7rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none;
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
        }
        .mo-empty-btn:hover { filter: brightness(1.08); }
    </style>

    <div class="mo-page mo-wrap">

        {{-- Header --}}
        <div class="mo-header">
            <h1>My Orders</h1>
            <p>Track your purchases and deliveries.</p>

            {{-- Stats --}}
            <div class="mo-stats">
                <div class="mo-stat cyan">
                    <div class="mo-stat-label">Total</div>
                    <div class="mo-stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="mo-stat violet">
                    <div class="mo-stat-label">Processing</div>
                    <div class="mo-stat-value">{{ $stats['pending'] }}</div>
                </div>
                <div class="mo-stat violet">
                    <div class="mo-stat-label">Shipping</div>
                    <div class="mo-stat-value">{{ $stats['shipping'] }}</div>
                </div>
                <div class="mo-stat green">
                    <div class="mo-stat-label">Completed</div>
                    <div class="mo-stat-value">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>

        {{-- Filter tabs --}}
        <div class="mo-tabs">
            <a href="{{ route('my-orders.index') }}"
               class="mo-tab {{ ! request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('my-orders.index', ['status' => 'pending']) }}"
               class="mo-tab {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('my-orders.index', ['status' => 'processing']) }}"
               class="mo-tab {{ request('status') === 'processing' ? 'active' : '' }}">Processing</a>
            <a href="{{ route('my-orders.index', ['status' => 'out_for_delivery']) }}"
               class="mo-tab {{ request('status') === 'out_for_delivery' ? 'active' : '' }}">Shipping</a>
            <a href="{{ route('my-orders.index', ['status' => 'delivered']) }}"
               class="mo-tab {{ request('status') === 'delivered' ? 'active' : '' }}">Delivered</a>
            <a href="{{ route('my-orders.index', ['status' => 'cancelled']) }}"
               class="mo-tab {{ request('status') === 'cancelled' ? 'active' : '' }}">Cancelled</a>
        </div>

        {{-- Orders list --}}
        @if ($orders->count())
            <div class="mo-list">
                @foreach ($orders as $order)
                    @php
                        $badge = $order->statusBadge();
                    @endphp

                    <a href="{{ route('my-orders.show', $order) }}" class="mo-card">

                        {{-- Thumb --}}
                        <div class="mo-thumb">
                            @if ($order->product?->image)
                                <img src="{{ Storage::url($order->product->image) }}" alt="{{ $order->product_name }}">
                            @else
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="mo-info">
                            <div class="mo-info-name">{{ $order->product_name }}</div>
                            <div class="mo-info-meta">
                                <span>#<strong>{{ $order->order_number }}</strong></span>
                                <span>·</span>
                                <span>{{ $order->created_at->format('M d, Y') }}</span>
                                <span>·</span>
                                <span>{{ $order->isPickup() ? '🏪 Pickup' : '🚚 Delivery' }}</span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <span class="mo-status" style="color: {{ $badge['color'] }}; background: {{ $badge['color'] }}1a; border: 1px solid {{ $badge['color'] }}44;">
                            {{ $badge['label'] }}
                        </span>

                        {{-- Amount --}}
                        <span class="mo-amount">৳{{ number_format($order->total_amount, 0) }}</span>

                        {{-- Arrow --}}
                        <svg class="mo-arrow" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">{{ $orders->links() }}</div>
        @else
            <div class="mo-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <div class="mo-empty-title">No orders yet</div>
                <p>Start shopping to see your orders here.</p>
                <a href="{{ route('products.index') }}" class="mo-empty-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Browse Products
                </a>
            </div>
        @endif
    </div>
</x-app-layout>