<x-app-layout>
    @section('title', 'Manage Orders')

    <style>
        .admin-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .admin-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .admin-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .admin-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        /* Stats grid */
        .stats-grid {
            display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.75rem;
            margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 500px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

        .stat-card {
            padding: 0.75rem 0.9rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .stat-label { font-size: 0.65rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.3rem; }
        .stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--text-hi); }
        .stat-card.cyan .stat-value { color: var(--cyan); }
        .stat-card.violet .stat-value { color: var(--violet); }
        .stat-card.green .stat-value { color: var(--green); }
        .stat-card.pink .stat-value { color: var(--pink); }

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
        .admin-table-wrap {
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); border-radius: 1rem; overflow: hidden;
        }
        .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .admin-table thead th {
            background: rgba(11,15,25,0.85); color: var(--text-mu);
            font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;
            padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);
        }
        .admin-table tbody td {
            padding: 0.9rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1; font-size: 0.82rem; vertical-align: middle;
        }
        .admin-table tbody tr:hover { background: rgba(41,231,255,0.035); }

        .order-num { font-family: monospace; font-weight: 700; color: var(--cyan); font-size: 0.78rem; }
        .status-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.25rem 0.65rem; border-radius: 999px;
            font-size: 0.68rem; font-weight: 600; white-space: nowrap;
        }
        .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; box-shadow: 0 0 6px currentColor; }

        .action-btn {
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem; color: var(--text-mu); background: none; cursor: pointer;
            transition: all 0.2s; border: 1px solid transparent; text-decoration: none;
        }
        .action-btn:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.08); }
        .action-btn.view:hover { color: var(--cyan); background: rgba(41,231,255,0.1); border-color: rgba(41,231,255,0.25); }
    </style>

    <div class="admin-page max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="admin-header">
            <div>
                <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Order Management</div>
                <div class="admin-header-title">All Orders</div>
                <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                    {{ $orders->total() }} orders total
                </p>
            </div>

            {{-- Stats --}}
            <div class="stats-grid">
                <div class="stat-card cyan">
                    <div class="stat-label">Total</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="stat-card violet">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                </div>
                <div class="stat-card violet">
                    <div class="stat-label">Processing</div>
                    <div class="stat-value">{{ $stats['processing'] }}</div>
                </div>
                <div class="stat-card cyan">
                    <div class="stat-label">Shipping</div>
                    <div class="stat-value">{{ $stats['shipping'] }}</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-label">Delivered</div>
                    <div class="stat-value">{{ $stats['delivered'] }}</div>
                </div>
                <div class="stat-card pink">
                    <div class="stat-label">Cancelled</div>
                    <div class="stat-value">{{ $stats['cancelled'] }}</div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="rounded-xl p-4 mb-5 flex flex-wrap gap-3" style="background:rgba(255,255,255,0.03); border:1px solid var(--glass-border);">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search order #, name, phone..."
                   class="filter-input" style="flex:1; min-width:220px;">

            <select name="status" class="filter-select" style="min-width:160px;">
                <option value="">All status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="ready_for_pickup" {{ request('status') === 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="picked_up" {{ request('status') === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
            </select>

            <select name="type" class="filter-select" style="min-width:130px;">
                <option value="">All types</option>
                <option value="delivery" {{ request('type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                <option value="pickup" {{ request('type') === 'pickup' ? 'selected' : '' }}>Pickup</option>
            </select>

            <select name="payment" class="filter-select" style="min-width:130px;">
                <option value="">All payments</option>
                <option value="unpaid" {{ request('payment') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="paid" {{ request('payment') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="refunded" {{ request('payment') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- Table --}}
        <div class="admin-table-wrap">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php $badge = $order->statusBadge(); @endphp
                            <tr>
                                <td>
                                    <div class="order-num">{{ $order->order_number }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:600; color:var(--text-hi); font-size:0.82rem;">{{ $order->delivery_name }}</div>
                                    <div style="font-size:0.72rem; color:var(--text-mu);">{{ $order->delivery_phone }}</div>
                                </td>
                                <td>
                                    <div style="font-size:0.8rem; color:var(--text-hi); max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $order->product_name }}
                                    </div>
                                    <div style="font-size:0.68rem; color:var(--text-mu);">×{{ $order->quantity }}</div>
                                </td>
                                <td>
                                    <span style="font-size:0.72rem; color:var(--text-mu);">
                                        {{ $order->isPickup() ? '🏪 Pickup' : '🚚 Delivery' }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-family:'Sora',sans-serif; font-weight:700; color:var(--text-hi);">
                                        ৳{{ number_format($order->total_amount, 0) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $pc = match($order->payment_status) {
                                            'paid' => '#34d399',
                                            'unpaid' => '#fbbf24',
                                            'refunded' => '#f87171',
                                            default => '#94a3b8',
                                        };
                                    @endphp
                                    <span class="status-badge" style="color:{{ $pc }}; background:{{ $pc }}1a; border:1px solid {{ $pc }}44;">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge"
                                          style="color:{{ $badge['color'] }}; background:{{ $badge['color'] }}1a; border:1px solid {{ $badge['color'] }}44;">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td style="font-size:0.72rem; color:var(--text-mu);">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div style="display:flex; justify-content:flex-end;">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="action-btn view" title="View">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding:4rem 1rem; text-align:center; color:var(--text-mu);">
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $orders->links() }}</div>
    </div>
</x-app-layout>