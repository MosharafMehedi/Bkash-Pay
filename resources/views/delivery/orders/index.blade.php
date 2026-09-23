<x-app-layout>
    @section('title', 'My Deliveries')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .dl-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .dl-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .dl-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .dl-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        .dl-list { display: flex; flex-direction: column; gap: 0.75rem; }
        .dl-card {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 1.15rem; border-radius: 0.9rem;
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px); transition: all 0.25s;
            text-decoration: none; color: inherit;
        }
        .dl-card:hover { border-color: rgba(41,231,255,0.4); transform: translateY(-2px); }

        .dl-thumb {
            width: 56px; height: 56px; border-radius: 0.7rem; overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .dl-thumb img { width: 100%; height: 100%; object-fit: cover; }

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

        .dl-empty {
            text-align: center; padding: 4rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
        }
    </style>

    <div class="dl-page max-w-5xl mx-auto">

        <div class="dl-header">
            <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Delivery</div>
            <div class="dl-header-title">My Deliveries</div>
            <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                {{ $orders->total() }} orders assigned to you
            </p>
        </div>

        {{-- Filters --}}
        <form method="GET" class="rounded-xl p-4 mb-5 flex flex-wrap gap-3" style="background:rgba(255,255,255,0.03); border:1px solid var(--glass-border);">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search order #, name, phone..."
                   class="filter-input" style="flex:1; min-width:200px;">

            <select name="status" class="filter-select" style="min-width:160px;">
                <option value="">All status</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="ready_for_pickup" {{ request('status') === 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="picked_up" {{ request('status') === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
            </select>

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- List --}}
        @if ($orders->count())
            <div class="dl-list">
                @foreach ($orders as $order)
                    @php $badge = $order->statusBadge(); @endphp
                    <a href="{{ route('delivery.orders.show', $order) }}" class="dl-card">
                        <div class="dl-thumb">
                            @if ($order->product?->image)
                                <img src="{{ Storage::url($order->product->image) }}" alt="">
                            @else
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#64748b;">
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
                            </div>
                        </div>

                        <span class="dl-badge"
                              style="color: {{ $badge['color'] }}; background: {{ $badge['color'] }}1a; border: 1px solid {{ $badge['color'] }}44;">
                            {{ $badge['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="mt-5">{{ $orders->links() }}</div>
        @else
            <div class="dl-empty">
                <p style="font-size:0.9rem; color:var(--text-hi); font-weight:600; margin-bottom:0.4rem;">No orders found</p>
                <p style="font-size:0.82rem;">Try adjusting filters.</p>
            </div>
        @endif
    </div>
</x-app-layout>