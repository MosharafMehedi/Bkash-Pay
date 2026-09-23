<x-app-layout>
    @section('title', 'Delivery #' . $order->order_number)

    <style>
        .dl-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .dl-back {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: var(--text-mu); font-size: 0.85rem; font-weight: 500;
            text-decoration: none; margin-bottom: 1.25rem;
        }
        .dl-back:hover { color: var(--cyan); }

        .dl-grid { display: grid; grid-template-columns: 1fr 1.1fr; gap: 1.25rem; }
        @media (max-width: 900px) { .dl-grid { grid-template-columns: 1fr; } }

        .dl-panel {
            border-radius: 1.1rem; background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); padding: 1.5rem; margin-bottom: 1.25rem;
        }
        .dl-panel-title {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--cyan); margin-bottom: 1rem;
        }

        .dl-order-head {
            display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;
            margin-bottom: 1rem; flex-wrap: wrap;
        }
        .dl-order-num { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--text-hi); }
        .dl-order-date { font-size: 0.78rem; color: var(--text-mu); margin-top: 0.25rem; }

        .dl-status-big {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1rem; border-radius: 999px;
            font-size: 0.82rem; font-weight: 700;
        }
        .dl-status-big::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: currentColor; }

        .dl-field {
            display: flex; justify-content: space-between; padding: 0.55rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.85rem;
        }
        .dl-field:last-child { border-bottom: none; }
        .dl-field-label { color: var(--text-mu); font-size: 0.78rem; }
        .dl-field-value { color: var(--text-hi); font-weight: 500; text-align: right; max-width: 60%; word-break: break-word; }

        /* Code verify box (BIG — hero) */
        .verify-box {
            padding: 1.5rem 1.25rem; border-radius: 0.95rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 2px dashed rgba(41,231,255,0.4); text-align: center; margin-bottom: 1rem;
        }
        .verify-label {
            font-size: 0.72rem; letter-spacing: 0.18em; text-transform: uppercase;
            color: var(--cyan); font-weight: 700; margin-bottom: 0.75rem;
        }
        .verify-hint {
            font-size: 0.78rem; color: var(--text-mu); line-height: 1.55;
            margin-bottom: 1.15rem;
        }
        .verify-input {
            width: 100%; max-width: 260px;
            padding: 0.9rem 1rem; border-radius: 0.75rem;
            background: rgba(255,255,255,0.04);
            border: 2px solid rgba(41,231,255,0.4);
            color: var(--text-hi);
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.5rem; font-weight: 700; letter-spacing: 0.35em; text-align: center;
            outline: none; text-transform: uppercase;
            transition: border-color 0.2s;
        }
        .verify-input:focus { border-color: var(--cyan); box-shadow: 0 0 0 4px rgba(41,231,255,0.15); }
        .verify-input::placeholder { color: rgba(154,148,184,0.5); letter-spacing: 0.35em; }

        .verify-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; max-width: 260px;
            margin-top: 0.9rem;
            padding: 0.85rem 1.25rem; border-radius: 0.75rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700; font-size: 0.9rem; color: #06050c;
            background: linear-gradient(135deg, var(--green), var(--cyan));
            border: none; cursor: pointer;
            box-shadow: 0 12px 30px -10px rgba(52,211,153,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .verify-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }
        .verify-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .alert {
            padding: 0.75rem 0.95rem; border-radius: 0.7rem;
            font-size: 0.82rem; margin-bottom: 1rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .alert.success { background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.3); color: #6ee7b7; }
        .alert.error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
        .alert.info { background: rgba(41,231,255,0.08); border: 1px solid rgba(41,231,255,0.25); color: var(--cyan); }

        .action-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            width: 100%; padding: 0.85rem 1.25rem; border-radius: 0.75rem;
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.9rem;
            color: #06050c; border: none; cursor: pointer;
            transition: transform 0.2s, filter 0.2s;
        }
        .action-btn.primary {
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
        }
        .action-btn.primary:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .done-box {
            text-align: center; padding: 1.75rem 1rem;
            border-radius: 0.95rem;
            background: rgba(52,211,153,0.08);
            border: 1px solid rgba(52,211,153,0.3);
        }
        .done-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: rgba(52,211,153,0.15); border: 2px solid rgba(52,211,153,0.4);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.9rem; color: var(--green);
        }
        .done-title { font-family: 'Sora', sans-serif; font-size: 1.05rem; font-weight: 700; color: #6ee7b7; margin-bottom: 0.3rem; }
        .done-sub { font-size: 0.82rem; color: var(--text-mu); }
    </style>

    <div class="dl-page max-w-6xl mx-auto">

        <a href="{{ route('delivery.orders.index') }}" class="dl-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to deliveries
        </a>

        @if (session('success'))
            <div class="alert success">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert error">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @php $badge = $order->statusBadge(); @endphp

        <div class="dl-grid">

            {{-- ══ LEFT ══ --}}
            <div>

                {{-- Header --}}
                <div class="dl-panel">
                    <div class="dl-order-head">
                        <div>
                            <div class="dl-order-num">#{{ $order->order_number }}</div>
                            <div class="dl-order-date">{{ $order->created_at->format('M d, Y · h:i A') }}</div>
                        </div>
                        <span class="dl-status-big"
                              style="color: {{ $badge['color'] }}; background: {{ $badge['color'] }}1a; border: 1px solid {{ $badge['color'] }}44;">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Customer --}}
                <div class="dl-panel">
                    <div class="dl-panel-title">Customer</div>
                    <div class="dl-field">
                        <span class="dl-field-label">Name</span>
                        <span class="dl-field-value">{{ $order->delivery_name }}</span>
                    </div>
                    <div class="dl-field">
                        <span class="dl-field-label">Phone</span>
                        <span class="dl-field-value">
                            <a href="tel:{{ $order->delivery_phone }}" style="color:var(--cyan); text-decoration:none; font-weight:600;">
                                {{ $order->delivery_phone }}
                            </a>
                        </span>
                    </div>
                </div>

                {{-- Delivery address --}}
                <div class="dl-panel">
                    <div class="dl-panel-title">{{ $order->isPickup() ? 'Pickup' : 'Delivery Address' }}</div>

                    @if ($order->isPickup())
                        <div class="dl-field">
                            <span class="dl-field-label">Pickup Point</span>
                            <span class="dl-field-value">Store</span>
                        </div>
                        <div class="dl-field">
                            <span class="dl-field-label">Address</span>
                            <span class="dl-field-value">{{ config('app.store_address', '123 Main Street, Dhaka') }}</span>
                        </div>
                    @else
                        <div class="dl-field">
                            <span class="dl-field-label">Address</span>
                            <span class="dl-field-value">{{ $order->delivery_address }}</span>
                        </div>
                        <div class="dl-field">
                            <span class="dl-field-label">City</span>
                            <span class="dl-field-value">{{ $order->delivery_city ?? '—' }}</span>
                        </div>
                        @if ($order->delivery_postal)
                            <div class="dl-field">
                                <span class="dl-field-label">Postal</span>
                                <span class="dl-field-value">{{ $order->delivery_postal }}</span>
                            </div>
                        @endif
                    @endif

                    @if ($order->delivery_note)
                        <div class="dl-field">
                            <span class="dl-field-label">Note</span>
                            <span class="dl-field-value" style="font-style:italic;">"{{ $order->delivery_note }}"</span>
                        </div>
                    @endif
                </div>

                {{-- Order --}}
                <div class="dl-panel">
                    <div class="dl-panel-title">Order Details</div>
                    <div class="dl-field">
                        <span class="dl-field-label">Product</span>
                        <span class="dl-field-value">{{ $order->product_name }} × {{ $order->quantity }}</span>
                    </div>
                    <div class="dl-field">
                        <span class="dl-field-label">Payment Method</span>
                        <span class="dl-field-value">
                            {{ strtoupper($order->source_type) }}
                        </span>
                    </div>
                    <div class="dl-field" style="border-top:1px dashed var(--glass-border); margin-top:0.4rem; padding-top:0.75rem;">
                        <span class="dl-field-label" style="font-weight:700; color:var(--text-hi);">
                            {{ $order->payment_status === 'paid' ? 'Total Paid' : 'Collect from Customer' }}
                        </span>
                        <span class="dl-field-value" style="font-family:'Sora'; font-size:1.2rem; color:{{ $order->payment_status === 'paid' ? '#34d399' : 'var(--pink)' }}; font-weight:700;">
                            ৳{{ number_format($order->total_amount, 0) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT ══ --}}
            <div>

                {{-- Action panel — depends on status --}}
                @if (in_array($order->status, ['delivered', 'picked_up']))
                    {{-- Already completed --}}
                    <div class="dl-panel">
                        <div class="done-box">
                            <div class="done-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="done-title">{{ $order->isPickup() ? 'Picked Up' : 'Delivered' }}</div>
                            <div class="done-sub">
                                Completed on {{ $order->delivered_at?->format('M d, Y · h:i A') }}
                            </div>
                        </div>
                    </div>

                @elseif ($order->status === 'processing')
                    {{-- Mark out for delivery --}}
                    <div class="dl-panel">
                        <div class="dl-panel-title">Next Step</div>

                        @if ($order->isPickup())
                            <div class="alert info">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                This is a pickup order. Wait for the customer to arrive at the store.
                            </div>
                            <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.75rem;">
                                When the customer arrives, ask for their delivery code and verify below.
                            </p>
                        @else
                            <p style="font-size:0.85rem; color:var(--text-mu); line-height:1.6; margin-bottom:1.15rem;">
                                Click below when you pick up the item from the warehouse and start heading towards the customer.
                            </p>

                            <form method="POST" action="{{ route('delivery.orders.out', $order) }}">
                                @csrf
                                <button type="submit" class="action-btn primary">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                              d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                    </svg>
                                    Mark Out for Delivery
                                </button>
                            </form>
                        @endif
                    </div>

                @elseif (in_array($order->status, ['out_for_delivery', 'ready_for_pickup']))
                    {{-- Code verification --}}
                    <div class="dl-panel">
                        <div class="dl-panel-title">Verify Delivery Code</div>

                        <div class="verify-box">
                            <div class="verify-label">Customer's Code</div>
                            <p class="verify-hint">
                                Ask the customer for their 6-character delivery code.<br>
                                Enter it below to complete the delivery.
                            </p>

                            <form method="POST" action="{{ route('delivery.orders.verify', $order) }}" id="verifyForm">
                                @csrf
                                <input type="text"
                                       name="code"
                                       maxlength="6"
                                       minlength="6"
                                       pattern="[A-Za-z0-9]{6}"
                                       autocomplete="off"
                                       autofocus
                                       required
                                       class="verify-input"
                                       placeholder="••••••"
                                       style="text-transform:uppercase;">
                                <button type="submit" class="verify-btn" id="verifyBtn">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Verify &amp; Complete
                                </button>
                            </form>
                        </div>

                        @if ($order->delivery_code_attempts > 0)
                            <div class="alert error" style="margin-top:0.75rem;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                Incorrect attempts: {{ $order->delivery_code_attempts }}/3
                            </div>
                        @endif

                        <div style="font-size:0.72rem; color:var(--text-mu); margin-top:0.85rem; text-align:center;">
                            ⚠️ Only the customer knows this code. Never share your device with them.
                        </div>
                    </div>

                @elseif ($order->status === 'cancelled' || $order->status === 'returned')
                    <div class="dl-panel">
                        <div class="alert info">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            This order is {{ $order->status }}. No action needed.
                        </div>
                    </div>
                @endif

                {{-- Timeline --}}
                <div class="dl-panel">
                    <div class="dl-panel-title">Timeline</div>
                    <div style="position:relative; padding-left:1.5rem;">
                        <div style="position:absolute; left:6px; top:6px; bottom:6px; width:2px; background: linear-gradient(180deg, var(--cyan), rgba(41,231,255,0.1));"></div>

                        @forelse ($order->statusLogs as $log)
                            <div style="position:relative; padding-bottom:1rem;">
                                <div style="position:absolute; left:-1.5rem; top:4px; width:14px; height:14px; border-radius:50%; background:#0b0f19; border:2px solid var(--cyan); box-shadow: 0 0 10px var(--cyan);"></div>
                                <div style="font-size:0.82rem; font-weight:600; color:var(--text-hi); text-transform:capitalize;">
                                    {{ str_replace('_', ' ', $log->status) }}
                                </div>
                                <div style="font-size:0.72rem; color:var(--text-mu);">
                                    {{ $log->created_at->format('M d, Y · h:i A') }}
                                </div>
                                @if ($log->note)
                                    <div style="font-size:0.72rem; color:var(--text-mu); font-style:italic; margin-top:0.15rem;">
                                        "{{ $log->note }}"
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p style="color:var(--text-mu); font-size:0.82rem;">No activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-uppercase the code input
        const codeInput = document.querySelector('.verify-input');
        if (codeInput) {
            codeInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.toUpperCase();
            });

            // Auto-submit on 6 chars
            codeInput.addEventListener('input', (e) => {
                if (e.target.value.length === 6) {
                    setTimeout(() => document.getElementById('verifyForm').submit(), 200);
                }
            });
        }
    </script>
</x-app-layout>