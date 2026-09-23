<x-app-layout>
    @section('title', 'Delivery Dashboard')

    <style>
        .dl-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .dl-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(52,211,153,0.08), rgba(41,231,255,0.06));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .dl-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--green), var(--cyan)); }
        .dl-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        /* Stats */
        .dl-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.85rem;
            margin-top: 1.15rem; padding-top: 1.15rem; border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 640px) { .dl-stats { grid-template-columns: repeat(2, 1fr); } }

        .dl-stat {
            padding: 0.85rem 0.95rem; border-radius: 0.75rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .dl-stat-label { font-size: 0.68rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.35rem; }
        .dl-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.35rem; color: var(--text-hi); }
        .dl-stat.cyan .dl-stat-value { color: var(--cyan); }
        .dl-stat.violet .dl-stat-value { color: var(--violet); }
        .dl-stat.green .dl-stat-value { color: var(--green); }
        .dl-stat.pink .dl-stat-value { color: var(--pink); }

        .dl-section { margin-bottom: 1.5rem; }
        .dl-section-title {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--cyan); margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.5rem;
        }

        /* Order cards */
        .dl-list { display: flex; flex-direction: column; gap: 0.75rem; }
        .dl-card {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 1.15rem; border-radius: 0.9rem;
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px); transition: all 0.25s;
            text-decoration: none; color: inherit;
        }
        .dl-card:hover {
            border-color: rgba(41,231,255,0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -12px rgba(41,231,255,0.3);
        }

        .dl-thumb {
            width: 56px; height: 56px; border-radius: 0.7rem; overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .dl-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .dl-thumb svg { color: #64748b; width: 24px; height: 24px; }

        .dl-info { flex: 1; min-width: 0; }
        .dl-info-name { font-weight: 600; color: var(--text-hi); font-size: 0.9rem; margin-bottom: 0.2rem; }
        .dl-info-meta { font-size: 0.72rem; color: var(--text-mu); display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .dl-info-meta strong { color: var(--text-hi); font-family: monospace; }

        .dl-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.3rem 0.7rem; border-radius: 999px;
            font-size: 0.68rem; font-weight: 600; white-space: nowrap;
        }
        .dl-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

        .dl-arrow { color: var(--text-mu); transition: transform 0.2s; flex-shrink: 0; }
        .dl-card:hover .dl-arrow { transform: translateX(3px); color: var(--cyan); }

        .dl-empty {
            text-align: center; padding: 3rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
        }
        .dl-empty svg { width: 48px; height: 48px; margin: 0 auto 0.75rem; opacity: 0.4; }
        .dl-empty-title { font-family: 'Sora', sans-serif; font-size: 0.9rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.3rem; }
    </style>

    <div class="dl-page max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="dl-header">
            <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--green); font-weight:600;">Delivery Panel</div>
            <div class="dl-header-title">Hello, {{ auth()->user()->name }}</div>
            <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                {{ auth()->user()->hasRole('vendor') ? 'Vendor' : 'Delivery Man' }} Dashboard
            </p>

            {{-- Stats --}}
            <div class="dl-stats">
                <div class="dl-stat cyan">
                    <div class="dl-stat-label">Assigned</div>
                    <div class="dl-stat-value">{{ $stats['assigned'] }}</div>
                </div>
                <div class="dl-stat violet">
                    <div class="dl-stat-label">Out for Delivery</div>
                    <div class="dl-stat-value">{{ $stats['out'] }}</div>
                </div>
                <div class="dl-stat green">
                    <div class="dl-stat-label">Completed</div>
                    <div class="dl-stat-value">{{ $stats['delivered'] }}</div>
                </div>
                <div class="dl-stat pink">
                    <div class="dl-stat-label">Today</div>
                    <div class="dl-stat-value">{{ $stats['today'] }}</div>
                </div>
            </div>
        </div>

        {{-- Active orders --}}
        <div class="dl-section">
            <div class="dl-section-title">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Active Orders ({{ $activeOrders->count() }})
            </div>

            @if ($activeOrders->count())
                <div class="dl-list">
                    @foreach ($activeOrders as $order)
                        @php $badge = $order->statusBadge(); @endphp
                        <a href="{{ route('delivery.orders.show', $order) }}" class="dl-card">
                            <div class="dl-thumb">
                                @if ($order->product?->image)
                                    <img src="{{ Storage::url($order->product->image) }}" alt="">
                                @else
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="dl-info">
                                <div class="dl-info-name">{{ $order->product_name }}</div>
                                <div class="dl-info-meta">
                                    <span>#<strong>{{ $order->order_number }}</strong></span>
                                    <span>·</span>
                                    <span>{{ $order->delivery_name }}</span>
                                    <span>·</span>
                                    <span>{{ $order->delivery_phone }}</span>
                                    @if ($order->delivery_city)
                                        <span>·</span>
                                        <span>{{ $order->delivery_city }}</span>
                                    @endif
                                </div>
                            </div>

                            <span class="dl-badge"
                                  style="color: {{ $badge['color'] }}; background: {{ $badge['color'] }}1a; border: 1px solid {{ $badge['color'] }}44;">
                                {{ $badge['label'] }}
                            </span>

                            <svg class="dl-arrow" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dl-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="dl-empty-title">No active orders</div>
                    <p>You're all caught up!</p>
                </div>
            @endif
        </div>

        {{-- Recent deliveries --}}
        @if ($recentDeliveries->count())
            <div class="dl-section">
                <div class="dl-section-title">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Recent Deliveries
                </div>

                <div class="dl-list">
                    @foreach ($recentDeliveries as $order)
                        <a href="{{ route('delivery.orders.show', $order) }}" class="dl-card">
                            <div class="dl-thumb">
                                @if ($order->product?->image)
                                    <img src="{{ Storage::url($order->product->image) }}" alt="">
                                @else
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="dl-info">
                                <div class="dl-info-name">{{ $order->product_name }}</div>
                                <div class="dl-info-meta">
                                    <span>#<strong>{{ $order->order_number }}</strong></span>
                                    <span>·</span>
                                    <span>{{ $order->delivered_at?->diffForHumans() }}</span>
                                </div>
                            </div>

                            <span class="dl-badge" style="color:#34d399; background:rgba(52,211,153,0.12); border:1px solid rgba(52,211,153,0.3);">
                                Delivered
                            </span>

                            <svg class="dl-arrow" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>