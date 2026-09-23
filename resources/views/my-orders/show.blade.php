<x-app-layout>
    @section('title', 'Order #' . $order->order_number)

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600|jetbrains-mono:400,700" rel="stylesheet">

    <style>
        .od-page {
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

        .od-wrap { max-width: 1100px; margin: 0 auto; }

        .od-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-mu);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 1.25rem;
            transition: color 0.2s;
        }
        .od-back:hover { color: var(--cyan); }

        .od-grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 1.25rem;
        }
        @media (max-width: 900px) { .od-grid { grid-template-columns: 1fr; } }

        .od-panel {
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        /* Header card */
        .od-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .od-order-num {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-hi);
        }
        .od-order-date {
            font-size: 0.78rem;
            color: var(--text-mu);
            margin-top: 0.25rem;
        }

        .od-status-big {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .od-status-big::before {
            content: '';
            width: 8px; height: 8px; border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 10px currentColor;
        }

        /* Delivery code (HERO) */
        .od-code-box {
            text-align: center;
            padding: 1.5rem 1rem;
            border-radius: 0.95rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 2px dashed rgba(41,231,255,0.4);
            margin: 1.25rem 0;
        }
        .od-code-label {
            font-size: 0.7rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--cyan);
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        .od-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 2.25rem;
            font-weight: 700;
            letter-spacing: 0.4em;
            color: var(--text-hi);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .od-code-note {
            font-size: 0.72rem;
            color: var(--text-mu);
            margin-top: 0.5rem;
            line-height: 1.5;
        }
        .od-code-used {
            color: #6ee7b7;
            font-weight: 700;
            padding: 0.75rem 0;
        }
        .od-code-expired {
            color: #f87171;
            font-weight: 600;
        }

        /* Product row */
        .od-product {
            display: flex;
            gap: 0.9rem;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 1rem;
        }
        .od-product-img {
            width: 72px;
            height: 72px;
            border-radius: 0.75rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .od-product-img img { width: 100%; height: 100%; object-fit: cover; }
        .od-product-name {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            color: var(--text-hi);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }
        .od-product-meta {
            font-size: 0.75rem;
            color: var(--text-mu);
        }

        /* Price breakdown */
        .od-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-mu);
            padding: 0.4rem 0;
        }
        .od-price-row.total {
            border-top: 1px dashed var(--glass-border);
            margin-top: 0.4rem;
            padding-top: 0.75rem;
            color: var(--text-hi);
            font-weight: 700;
            font-size: 0.95rem;
        }
        .od-price-row.total .amt {
            font-family: 'Sora', sans-serif;
            font-size: 1.2rem;
            color: var(--cyan);
        }

        /* Timeline */
        .od-timeline { position: relative; padding-left: 1.75rem; }

        .od-timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: linear-gradient(180deg, var(--cyan), rgba(41,231,255,0.1));
            opacity: 0.35;
        }

        .od-tl-item {
            position: relative;
            padding-bottom: 1.1rem;
        }
        .od-tl-item:last-child { padding-bottom: 0; }
        .od-tl-item::before {
            content: '';
            position: absolute;
            left: -1.75rem;
            top: 4px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--bg-start, #0b0f19);
            border: 2px solid rgba(255,255,255,0.15);
            z-index: 1;
        }
        .od-tl-item.done::before {
            border-color: var(--cyan);
            background: var(--cyan);
            box-shadow: 0 0 12px var(--cyan);
        }
        .od-tl-status {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-hi);
            text-transform: capitalize;
            margin-bottom: 0.15rem;
        }
        .od-tl-item:not(.done) .od-tl-status { color: var(--text-mu); }
        .od-tl-time {
            font-size: 0.72rem;
            color: var(--text-mu);
        }
        .od-tl-note {
            font-size: 0.72rem;
            color: var(--text-mu);
            font-style: italic;
            margin-top: 0.2rem;
        }

        /* Address card */
        .od-address {
            display: flex;
            gap: 0.75rem;
            padding: 0.9rem;
            border-radius: 0.75rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
        }
        .od-address-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.6rem;
            background: rgba(41,231,255,0.12);
            border: 1px solid rgba(41,231,255,0.28);
            color: var(--cyan);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .od-address-body { flex: 1; min-width: 0; }
        .od-address-name {
            font-weight: 600;
            color: var(--text-hi);
            font-size: 0.88rem;
        }
        .od-address-line {
            font-size: 0.78rem;
            color: var(--text-mu);
            margin-top: 0.15rem;
            line-height: 1.5;
        }
    </style>

    <div class="od-page od-wrap">

        <a href="{{ route('my-orders.index') }}" class="od-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to My Orders
        </a>

        @php $badge = $order->statusBadge(); @endphp

        <div class="od-grid">

            {{-- ══ LEFT: Order info ══ --}}
            <div>

                {{-- Header --}}
                <div class="od-panel">
                    <div class="od-header">
                        <div>
                            <div class="od-order-num">#{{ $order->order_number }}</div>
                            <div class="od-order-date">
                                Placed on {{ $order->created_at->format('M d, Y · h:i A') }}
                            </div>
                        </div>
                        <span class="od-status-big"
                              style="color: {{ $badge['color'] }}; background: {{ $badge['color'] }}1a; border: 1px solid {{ $badge['color'] }}44;">
                            {{ $badge['label'] }}
                        </span>
                    </div>

                    {{-- Delivery code HERO --}}
                    @if (! $order->isDeliveryCodeUsed())
                        <div class="od-code-box">
                            <div class="od-code-label">Your Delivery Code</div>
                            @if ($order->isDeliveryCodeExpired())
                                <div class="od-code-expired">Expired — contact support</div>
                            @else
                                <div class="od-code">{{ $order->delivery_code }}</div>
                                <div class="od-code-note">
                                    Share this code with the delivery person when they hand over your order.<br>
                                    <strong style="color: #fbbf24;">Do not share it with anyone else.</strong>
                                </div>
                                <div class="od-code-note" style="margin-top:0.75rem;">
                                    Valid till {{ $order->delivery_code_expires_at->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="od-code-box">
                            <div class="od-code-label">Delivery Code</div>
                            <div class="od-code-used">
                                ✓ Verified on {{ $order->delivery_code_used_at->format('M d, Y · h:i A') }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Product --}}
                <div class="od-panel">
                    <div class="od-product">
                        <div class="od-product-img">
                            @if ($order->product?->image)
                                <img src="{{ Storage::url($order->product->image) }}" alt="{{ $order->product_name }}">
                            @else
                                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#64748b;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div class="od-product-name">{{ $order->product_name }}</div>
                            <div class="od-product-meta">
                                Qty: {{ $order->quantity }} · {{ $order->currency }}
                            </div>
                        </div>
                    </div>

                    {{-- Price breakdown --}}
                    <div class="od-price-row">
                        <span>Subtotal</span>
                        <span>৳{{ number_format($order->subtotal, 0) }}</span>
                    </div>

                    @if ((float) $order->discount_amount > 0)
                        <div class="od-price-row" style="color:#6ee7b7;">
                            <span>
                                Discount
                                @if ($order->coupon_code)
                                    <span style="color:var(--cyan); font-weight:600;">({{ $order->coupon_code }})</span>
                                @endif
                            </span>
                            <span style="font-weight:700;">−৳{{ number_format($order->discount_amount, 0) }}</span>
                        </div>
                    @endif

                    @if ((float) $order->delivery_charge > 0)
                        <div class="od-price-row">
                            <span>Delivery Charge</span>
                            <span>৳{{ number_format($order->delivery_charge, 0) }}</span>
                        </div>
                    @elseif (! $order->isPickup())
                        <div class="od-price-row">
                            <span>Delivery Charge</span>
                            <span style="color:#6ee7b7; font-weight:600;">FREE</span>
                        </div>
                    @endif

                    <div class="od-price-row total">
                        <span>Total {{ $order->payment_status === 'paid' ? '· Paid' : '· Due' }}</span>
                        <span class="amt">৳{{ number_format($order->total_amount, 0) }}</span>
                    </div>
                </div>

                {{-- Address --}}
                <div class="od-panel">
                    <div style="font-size:0.72rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--cyan); margin-bottom:0.9rem;">
                        {{ $order->isPickup() ? 'Pickup From' : 'Deliver To' }}
                    </div>

                    @if ($order->isPickup())
                        <div class="od-address">
                            <div class="od-address-icon">🏪</div>
                            <div class="od-address-body">
                                <div class="od-address-name">Store Pickup</div>
                                <div class="od-address-line">
                                    {{ config('app.store_address', '123 Main Street, Dhaka') }}<br>
                                    Open: 9 AM – 9 PM daily
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="od-address">
                            <div class="od-address-icon">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="od-address-body">
                                <div class="od-address-name">{{ $order->delivery_name }}</div>
                                <div class="od-address-line">
                                    {{ $order->delivery_phone }}<br>
                                    {{ $order->delivery_address }}
                                    @if ($order->delivery_city), {{ $order->delivery_city }}@endif
                                    @if ($order->delivery_postal) - {{ $order->delivery_postal }}@endif
                                    @if ($order->delivery_note)
                                        <br><em style="color:var(--text-mu);">"{{ $order->delivery_note }}"</em>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══ RIGHT: Timeline ══ --}}
            <div>
                <div class="od-panel">
                    <div style="font-size:0.72rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--cyan); margin-bottom:1.15rem;">
                        Order Timeline
                    </div>

                    <div class="od-timeline">
                        @forelse ($order->statusLogs as $log)
                            @php
                                $isDone = true; // sob log done — history
                            @endphp
                            <div class="od-tl-item done">
                                <div class="od-tl-status">{{ ucfirst(str_replace('_', ' ', $log->status)) }}</div>
                                <div class="od-tl-time">{{ $log->created_at->format('M d, Y · h:i A') }}</div>
                                @if ($log->note)
                                    <div class="od-tl-note">{{ $log->note }}</div>
                                @endif
                                @if ($log->user)
                                    <div class="od-tl-time" style="margin-top:0.15rem;">
                                        by {{ $log->user->name }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="od-tl-item done">
                                <div class="od-tl-status">Pending</div>
                                <div class="od-tl-time">{{ $order->created_at->format('M d, Y · h:i A') }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>