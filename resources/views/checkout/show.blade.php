<x-app-layout>
    @section('title', 'Checkout — ' . $product->name)

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600|jetbrains-mono:400,700" rel="stylesheet">

    <style>
        .co-page {
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

        .co-wrap { max-width: 1240px; margin: 0 auto; }

        .co-back {
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
        .co-back:hover { color: var(--cyan); }

        /* ═══ 3-Column Grid ═══ */
        .co-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.25rem;
        }
        @media (max-width: 1200px) { .co-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 900px)  { .co-grid { grid-template-columns: 1fr; } }

        .co-panel {
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            padding: 1.5rem;
        }

        /* ── LEFT — summary ── */
        .co-summary-media {
            width: 100%;
            aspect-ratio: 16 / 10;
            border-radius: 0.85rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .co-summary-media img { width: 100%; height: 100%; object-fit: cover; }
        .co-summary-media svg { color: #64748b; }

        .co-badge {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            background: rgba(41,231,255,0.12);
            color: var(--cyan);
            border: 1px solid rgba(41,231,255,0.25);
            margin-bottom: 0.65rem;
        }

        .co-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-hi);
            line-height: 1.25;
            margin-bottom: 0.5rem;
        }
        .co-desc {
            color: var(--text-mu);
            font-size: 0.82rem;
            line-height: 1.55;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .co-price-box {
            border-radius: 0.85rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 0.9rem 1rem;
            margin-bottom: 1rem;
        }
        .co-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            color: var(--text-mu);
            padding: 0.35rem 0;
        }
        .co-price-row.total {
            border-top: 1px dashed var(--glass-border);
            margin-top: 0.4rem;
            padding-top: 0.7rem;
            font-weight: 600;
            color: var(--text-hi);
            font-size: 0.88rem;
        }
        .co-price-row.total .co-amount {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--cyan);
        }
        .co-price-row.discount { color: #6ee7b7; }
        .co-price-row.discount .co-discount { font-weight: 700; color: #6ee7b7; }
        .co-coupon-tag {
            color: var(--cyan);
            font-weight: 600;
            font-size: 0.7rem;
            margin-left: 0.35rem;
        }
        .co-remove-coupon {
            background: none;
            border: none;
            color: #f87171;
            font-size: 0.68rem;
            cursor: pointer;
            margin-left: 0.5rem;
            text-decoration: underline;
            padding: 0;
        }

        .co-trust {
            display: flex;
            gap: 0.85rem;
            flex-wrap: wrap;
            font-size: 0.7rem;
            color: var(--text-mu);
            font-weight: 500;
        }
        .co-trust-item { display: inline-flex; align-items: center; gap: 0.35rem; }
        .co-trust-item svg { width: 13px; height: 13px; color: var(--cyan); }

        /* ── Coupon ── */
        .co-coupon { margin-bottom: 1rem; }
        .co-coupon-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-mu);
            margin-bottom: 0.45rem;
        }
        .co-coupon-label svg { width: 13px; height: 13px; color: var(--cyan); }
        .co-coupon-row { display: flex; gap: 0.5rem; }
        .co-coupon-row input {
            flex: 1;
            padding: 0.6rem 0.8rem;
            border-radius: 0.6rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            color: var(--text-hi);
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            outline: none;
            transition: border-color 0.2s;
        }
        .co-coupon-row input::placeholder {
            color: var(--text-mu);
            letter-spacing: 0;
            text-transform: none;
            font-family: 'Inter', sans-serif;
        }
        .co-coupon-row input:focus { border-color: rgba(41,231,255,0.5); }
        .co-coupon-row input:disabled { opacity: 0.7; cursor: not-allowed; }
        .co-coupon-row button {
            padding: 0.6rem 0.95rem;
            border-radius: 0.6rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            border: none;
            cursor: pointer;
            transition: filter 0.15s;
            white-space: nowrap;
        }
        .co-coupon-row button:hover:not(:disabled) { filter: brightness(1.08); }
        .co-coupon-row button:disabled { opacity: 0.6; cursor: wait; }

        .co-coupon-msg {
            margin-top: 0.4rem;
            font-size: 0.72rem;
            min-height: 1em;
        }
        .co-coupon-msg.ok    { color: #6ee7b7; }
        .co-coupon-msg.error { color: #fca5a5; }
        .co-coupon-msg.info  { color: var(--cyan); }

        .co-coupon.applied .co-coupon-row input {
            border-color: rgba(52,211,153,0.5);
            background: rgba(52,211,153,0.05);
        }

        /* ── MIDDLE — delivery form ── */
        .co-section-head { margin-bottom: 1rem; }
        .co-section-head h2 {
            font-family: 'Sora', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-hi);
            margin-bottom: 0.15rem;
        }
        .co-section-head p {
            font-size: 0.78rem;
            color: var(--text-mu);
        }

        /* Order type toggle */
        .co-type-toggle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-bottom: 1.1rem;
        }
        .co-type-option {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.75rem;
            border-radius: 0.7rem;
            border: 1.5px solid var(--glass-border);
            background: rgba(255,255,255,0.02);
            cursor: pointer;
            transition: all 0.2s;
        }
        .co-type-option:hover { border-color: rgba(41,231,255,0.35); }
        .co-type-option.active {
            border-color: var(--cyan);
            background: rgba(41,231,255,0.06);
            box-shadow: 0 0 0 3px rgba(41,231,255,0.08);
        }
        .co-type-option input { display: none; }
        .co-type-icon {
            width: 34px;
            height: 34px;
            border-radius: 0.55rem;
            background: rgba(41,231,255,0.12);
            border: 1px solid rgba(41,231,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cyan);
            flex-shrink: 0;
        }
        .co-type-title {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-hi);
            line-height: 1.15;
        }
        .co-type-sub {
            font-size: 0.62rem;
            color: var(--text-mu);
            margin-top: 0.1rem;
        }

        /* Form fields */
        .co-field { margin-bottom: 0.75rem; }
        .co-field label {
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.3rem;
        }
        .co-field label .req { color: #f87171; }
        .co-field input,
        .co-field select,
        .co-field textarea {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.55rem;
            padding: 0.55rem 0.75rem;
            font-size: 0.82rem;
            color: var(--text-hi);
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .co-field input::placeholder,
        .co-field textarea::placeholder { color: var(--text-mu); }
        .co-field input:focus,
        .co-field select:focus,
        .co-field textarea:focus { border-color: rgba(41,231,255,0.5); }
        .co-field select option { background: #111827; }
        .co-field textarea { resize: vertical; }

        /* Pickup info */
        .co-pickup-box {
            display: flex;
            gap: 0.75rem;
            padding: 0.9rem;
            border-radius: 0.7rem;
            background: rgba(52,211,153,0.06);
            border: 1px solid rgba(52,211,153,0.28);
            margin-bottom: 0.7rem;
        }
        .co-pickup-icon { font-size: 1.5rem; flex-shrink: 0; }
        .co-pickup-title {
            font-weight: 700;
            color: #6ee7b7;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }
        .co-pickup-address {
            font-size: 0.78rem;
            color: var(--text-hi);
            margin-bottom: 0.15rem;
        }
        .co-pickup-hours {
            font-size: 0.68rem;
            color: var(--text-mu);
        }
        .co-pickup-note {
            font-size: 0.72rem;
            color: var(--text-mu);
            line-height: 1.5;
        }

        /* ── RIGHT — payment ── */
        .co-user {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.65rem 0.85rem;
            border-radius: 0.65rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            font-size: 0.78rem;
            color: var(--text-mu);
            margin-bottom: 1rem;
        }
        .co-user .email { color: var(--text-hi); font-weight: 600; }

        .co-methods {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            margin-bottom: 1rem;
        }
        .co-method {
            position: relative;
            display: flex;
            align-items: center;
            padding: 0.75rem 0.9rem;
            border-radius: 0.75rem;
            border: 1.5px solid var(--glass-border);
            background: rgba(255,255,255,0.02);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s, transform 0.2s;
        }
        .co-method:hover {
            border-color: rgba(41,231,255,0.35);
            transform: translateY(-1px);
        }
        .co-method.selected {
            border-color: var(--cyan);
            background: rgba(41,231,255,0.06);
            box-shadow: 0 0 0 3px rgba(41,231,255,0.08);
        }

        .co-method input[type="radio"] {
            appearance: none;
            width: 16px; height: 16px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.25);
            margin-right: 0.75rem;
            position: relative;
            cursor: pointer;
            flex-shrink: 0;
            transition: border-color 0.2s;
        }
        .co-method input[type="radio"]:checked { border-color: var(--cyan); }
        .co-method input[type="radio"]:checked::before {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 8px var(--cyan);
        }

        .co-logo {
            width: 34px; height: 34px;
            border-radius: 0.55rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.62rem;
            color: #fff;
            margin-right: 0.75rem;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }
        .logo-bkash      { background: linear-gradient(135deg, #e2136e, #ff5fb0); }
        .logo-sslcommerz { background: linear-gradient(135deg, #0072bc, #29e7ff); color: #06050c; }
        .logo-paypal     { background: linear-gradient(135deg, #003087, #1546a0); }
        .logo-cash       { background: linear-gradient(135deg, #10b981, #34d399); color: #06050c; }

        .co-method-info { flex: 1; min-width: 0; }
        .co-method-title {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-hi);
        }
        .co-method-desc {
            font-size: 0.68rem;
            color: var(--text-mu);
            margin-top: 0.1rem;
        }

        .co-submit {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.8rem 1.1rem;
            border-radius: 0.75rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            border: none;
            cursor: pointer;
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s, box-shadow 0.2s;
        }
        .co-submit:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 16px 38px -10px rgba(41,231,255,0.85);
        }
        .co-submit:active { transform: translateY(0); }
        .co-submit svg { width: 15px; height: 15px; }

        .co-secure {
            margin-top: 0.75rem;
            text-align: center;
            font-size: 0.68rem;
            color: var(--text-mu);
        }

        .co-error {
            padding: 0.7rem 0.9rem;
            margin-bottom: 1rem;
            border-radius: 0.65rem;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            font-size: 0.78rem;
        }

        .co-success {
            padding: 0.7rem 0.9rem;
            margin-bottom: 1rem;
            border-radius: 0.65rem;
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.3);
            color: #6ee7b7;
            font-size: 0.78rem;
        }
    </style>

    <div class="co-page co-wrap">

        <a href="{{ route('products.index') }}" class="co-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Store
        </a>

        @if (session('success'))
            <div class="co-success">✓ {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="co-error">{{ session('error') }}</div>
        @endif

        <form id="checkout-form" method="POST" action="{{ route('bkash.pay') }}">
            @csrf

            <div class="co-grid">

                {{-- ══ LEFT: Product summary ══ --}}
                <div class="co-panel">
                    <div class="co-summary-media">
                        @if ($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                        @else
                            <svg width="42" height="42" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        @endif
                    </div>

                    @if ($product->subtitle)
                        <span class="co-badge">{{ $product->subtitle }}</span>
                    @endif

                    <h1 class="co-title">{{ $product->name }}</h1>
                    <p class="co-desc">{{ $product->description }}</p>

                    <div class="co-price-box">
                        <div class="co-price-row">
                            <span>Subtotal</span>
                            <span id="subtotal-val">৳{{ number_format($product->final_price_bdt, 0) }}</span>
                        </div>
                        <div class="co-price-row">
                            <span>Tax / Fees</span>
                            <span>৳0</span>
                        </div>

                        {{-- Discount row --}}
                        <div class="co-price-row discount" id="discountRow" style="display:none;">
                            <span>
                                Discount
                                <span class="co-coupon-tag" id="couponCodeLabel"></span>
                                <button type="button" class="co-remove-coupon" id="removeCoupon">remove</button>
                            </span>
                            <span class="co-discount" id="discountAmount">−৳0</span>
                        </div>

                        {{-- Delivery charge row --}}
                        <div class="co-price-row" id="deliveryChargeRow" style="display:none;">
                            <span>Delivery Charge</span>
                            <span id="deliveryChargeAmount">৳0</span>
                        </div>

                        <div class="co-price-row total">
                            <span>Total Due</span>
                            <span class="co-amount" id="amount-display">৳{{ number_format($product->final_price_bdt, 0) }}</span>
                        </div>
                    </div>

                    <div class="co-trust">
                        <span class="co-trust-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Encrypted
                        </span>
                        <span class="co-trust-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Instant Access
                        </span>
                    </div>
                </div>

                {{-- ══ MIDDLE: Delivery Information ══ --}}
                <div class="co-panel">

                    <div class="co-section-head">
                        <h2>Delivery Information</h2>
                        <p>Where should we send your order?</p>
                    </div>

                    {{-- Order type toggle --}}
                    <div class="co-type-toggle">
                        <label class="co-type-option {{ old('order_type', 'delivery') === 'delivery' ? 'active' : '' }}">
                            <input type="radio" name="order_type" value="delivery"
                                   {{ old('order_type', 'delivery') === 'delivery' ? 'checked' : '' }}>
                            <div class="co-type-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:20px;height:20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                            </div>
                            <div>
                                <div class="co-type-title">Home Delivery</div>
                                <div class="co-type-sub">To my address</div>
                            </div>
                        </label>

                        <label class="co-type-option {{ old('order_type') === 'pickup' ? 'active' : '' }}">
                            <input type="radio" name="order_type" value="pickup"
                                   {{ old('order_type') === 'pickup' ? 'checked' : '' }}>
                            <div class="co-type-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:20px;height:20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="co-type-title">Pickup</div>
                                <div class="co-type-sub">From store</div>
                            </div>
                        </label>
                    </div>

                    {{-- Delivery fields --}}
                    <div id="deliveryFields">
                        <div class="co-field">
                            <label>Full Name <span class="req">*</span></label>
                            <input type="text" name="delivery_name"
                                   value="{{ old('delivery_name', auth()->user()->name) }}"
                                   placeholder="Receiver's full name">
                        </div>

                        <div class="co-field">
                            <label>Phone Number <span class="req">*</span></label>
                            <input type="text" name="delivery_phone"
                                   value="{{ old('delivery_phone', auth()->user()->phone) }}"
                                   placeholder="01XXXXXXXXX">
                        </div>

                        <div class="co-field">
                            <label>Delivery City <span class="req">*</span></label>
                            <select name="delivery_city" id="citySelect">
                                <option value="">Select city</option>
                                @foreach (\App\Models\DeliveryCharge::active()->orderBy('city')->get() as $dc)
                                    <option value="{{ $dc->city }}"
                                            data-charge="{{ $dc->charge }}"
                                            data-free-above="{{ $dc->free_above }}"
                                            {{ old('delivery_city', auth()->user()->city) === $dc->city ? 'selected' : '' }}>
                                        {{ $dc->city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="co-field">
                            <label>Full Address <span class="req">*</span></label>
                            <textarea name="delivery_address" rows="2"
                                      placeholder="House, road, area...">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                        </div>

                        <div class="co-field">
                            <label>Postal Code</label>
                            <input type="text" name="delivery_postal"
                                   value="{{ old('delivery_postal', auth()->user()->postal_code) }}"
                                   placeholder="Optional">
                        </div>

                        <div class="co-field">
                            <label>Delivery Note</label>
                            <textarea name="delivery_note" rows="2"
                                      placeholder="Special instructions (optional)">{{ old('delivery_note') }}</textarea>
                        </div>
                    </div>

                    {{-- Pickup info --}}
                    <div id="pickupInfo" style="display:none;">
                        <div class="co-pickup-box">
                            <div class="co-pickup-icon">🏪</div>
                            <div>
                                <div class="co-pickup-title">Pickup from our store</div>
                                <div class="co-pickup-address">
                                    {{ config('app.store_address', '123 Main Street, Dhaka') }}
                                </div>
                                <div class="co-pickup-hours">Open: 9 AM – 9 PM daily</div>
                            </div>
                        </div>
                        <p class="co-pickup-note">
                            You'll receive a delivery code via email. Show it when picking up your order.
                        </p>
                    </div>
                </div>

                {{-- ══ RIGHT: Payment ══ --}}
                <div class="co-panel">

                    <div class="co-section-head">
                        <h2>Payment Method</h2>
                        <p>Select how you'd like to pay.</p>
                    </div>

                    <div class="co-user">
                        <span>Account</span>
                        <span class="email">{{ auth()->user()->email }}</span>
                    </div>

                    {{-- Coupon box --}}
                    <div class="co-coupon" id="couponBox">
                        <div class="co-coupon-label">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Have a coupon?
                        </div>
                        <div class="co-coupon-row">
                            <input type="text" id="couponInput" placeholder="Enter code" autocomplete="off" maxlength="50">
                            <button type="button" id="applyCoupon">Apply</button>
                        </div>
                        <div class="co-coupon-msg" id="couponMsg"></div>
                    </div>

                    <div class="co-methods">
                        {{-- bKash --}}
                        <label class="co-method selected">
                            <input type="radio" name="method" value="bkash" checked>
                            <div class="co-logo logo-bkash">bKash</div>
                            <div class="co-method-info">
                                <div class="co-method-title">bKash Online</div>
                                <div class="co-method-desc">Mobile Wallet · BDT</div>
                            </div>
                        </label>

                        {{-- SSLCommerz --}}
                        <label class="co-method">
                            <input type="radio" name="method" value="sslcommerz">
                            <div class="co-logo logo-sslcommerz">SSL</div>
                            <div class="co-method-info">
                                <div class="co-method-title">SSLCommerz</div>
                                <div class="co-method-desc">Cards, Net Banking, Wallets</div>
                            </div>
                        </label>

                        {{-- PayPal --}}
                        <label class="co-method">
                            <input type="radio" name="method" value="paypal">
                            <div class="co-logo logo-paypal">PayPal</div>
                            <div class="co-method-info">
                                <div class="co-method-title">PayPal / Card</div>
                                <div class="co-method-desc">International · USD</div>
                            </div>
                        </label>

                        {{-- Cash on Delivery --}}
                        <label class="co-method">
                            <input type="radio" name="method" value="cash">
                            <div class="co-logo logo-cash">CASH</div>
                            <div class="co-method-info">
                                <div class="co-method-title">Cash on Delivery</div>
                                <div class="co-method-desc">Pay when you receive</div>
                            </div>
                        </label>
                    </div>

                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="coupon_code" id="couponHidden" value="">
                    <input type="hidden" name="discount_amount" id="discountHidden" value="0">
                    <input type="hidden" name="delivery_charge" id="deliveryChargeHidden" value="0">

                    <button type="submit" class="co-submit">
                        <span>Pay</span>
                        <span id="btn-amount">৳{{ number_format($product->final_price_bdt, 0) }}</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>

                    <div class="co-secure">🔒 Guaranteed safe &amp; secure checkout</div>
                </div>
            </div>
        </form>
    </div>

    <script>
    (function () {
        // ═══ Config ═══
        const routes = {
            bkash:      "{{ route('bkash.pay') }}",
            paypal:     "{{ route('paypal.pay') }}",
            sslcommerz: "{{ route('sslcommerz.pay') }}",
            cash:       "{{ route('cash.pay') }}",
        };
        const couponRoute         = "{{ route('coupon.apply') }}";
        const deliveryChargeRoute = "{{ route('checkout.deliveryCharge') }}";
        const csrfToken           = "{{ csrf_token() }}";
        const productId           = {{ $product->id }};

        const priceBdt = {{ (float) $product->final_price_bdt }};
        const priceUsd = {{ (float) $product->final_price_usd }};

        // ═══ Elements ═══
        const form          = document.getElementById('checkout-form');
        const amountDisplay = document.getElementById('amount-display');
        const subtotalVal   = document.getElementById('subtotal-val');
        const btnAmount     = document.getElementById('btn-amount');
        const methods       = document.querySelectorAll('.co-method');

        const couponBox     = document.getElementById('couponBox');
        const couponInput   = document.getElementById('couponInput');
        const applyBtn      = document.getElementById('applyCoupon');
        const couponMsg     = document.getElementById('couponMsg');
        const discountRow   = document.getElementById('discountRow');
        const discountAmt   = document.getElementById('discountAmount');
        const couponLbl     = document.getElementById('couponCodeLabel');
        const couponHidden  = document.getElementById('couponHidden');
        const discountHidden= document.getElementById('discountHidden');
        const removeCoupon  = document.getElementById('removeCoupon');

        const deliveryChargeRow    = document.getElementById('deliveryChargeRow');
        const deliveryChargeAmt    = document.getElementById('deliveryChargeAmount');
        const deliveryChargeHidden = document.getElementById('deliveryChargeHidden');

        // ═══ State ═══
        let appliedCoupon = null;
        let currentMethod = 'bkash';
        let currentDeliveryCharge = 0;
        let currentOrderType = 'delivery';

        const fmtBdt = (n) => '৳' + Number(n).toFixed(0);
        const fmtUsd = (n) => '$' + Number(n).toFixed(2);

        // ═══ Totals ═══
        function updateTotals() {
            const isUsd = currentMethod === 'paypal';
            const base  = isUsd ? priceUsd : priceBdt;

            let discount = (! isUsd && appliedCoupon) ? appliedCoupon.discount : 0;
            let deliveryCharge = (isUsd || currentOrderType === 'pickup') ? 0 : currentDeliveryCharge;
            let total = Math.max(base - discount, 0) + deliveryCharge;

            const fmt = isUsd ? fmtUsd : fmtBdt;

            subtotalVal.textContent = fmt(base);
            amountDisplay.textContent = fmt(total);
            btnAmount.textContent = fmt(total);

            // Discount row
            if (discount > 0) {
                discountRow.style.display = '';
                discountAmt.textContent = '−' + fmtBdt(discount);
                couponLbl.textContent = '(' + appliedCoupon.code + ')';
                couponHidden.value = appliedCoupon.code;
                discountHidden.value = discount;
            } else {
                discountRow.style.display = 'none';
                couponHidden.value = '';
                discountHidden.value = '0';
            }

            // Delivery charge row
            if (deliveryCharge > 0) {
                deliveryChargeRow.style.display = '';
                deliveryChargeAmt.textContent = fmtBdt(deliveryCharge);
            } else if (currentOrderType === 'delivery' && currentDeliveryCharge === 0 && document.getElementById('citySelect')?.value) {
                deliveryChargeRow.style.display = '';
                deliveryChargeAmt.innerHTML = '<span style="color:#6ee7b7;font-weight:700;">FREE</span>';
            } else {
                deliveryChargeRow.style.display = 'none';
            }

            deliveryChargeHidden.value = deliveryCharge;
        }

        // ═══ Method selection ═══
        methods.forEach((m) => {
            m.addEventListener('click', () => {
                methods.forEach((x) => x.classList.remove('selected'));
                m.classList.add('selected');

                const radio = m.querySelector('input[type="radio"]');
                radio.checked = true;
                currentMethod = radio.value;
                form.action = routes[radio.value];

                updateTotals();
            });
        });

        // ═══ Order type toggle ═══
        document.querySelectorAll('input[name="order_type"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('.co-type-option').forEach((el) => el.classList.remove('active'));
                radio.closest('.co-type-option').classList.add('active');

                const deliveryFields = document.getElementById('deliveryFields');
                const pickupInfo = document.getElementById('pickupInfo');

                currentOrderType = radio.value;

                if (radio.value === 'pickup') {
                    deliveryFields.style.display = 'none';
                    pickupInfo.style.display = '';
                    currentDeliveryCharge = 0;
                } else {
                    deliveryFields.style.display = '';
                    pickupInfo.style.display = 'none';
                    fetchDeliveryCharge();
                }

                updateTotals();
            });
        });

        // ═══ City change → fetch delivery charge ═══
        const citySelect = document.getElementById('citySelect');
        if (citySelect) {
            citySelect.addEventListener('change', fetchDeliveryCharge);
        }

        function fetchDeliveryCharge() {
            const city = citySelect?.value;
            if (! city) {
                currentDeliveryCharge = 0;
                updateTotals();
                return;
            }

            const baseAmount = (currentMethod === 'paypal') ? priceUsd : priceBdt;

            fetch(deliveryChargeRoute + "?city=" + encodeURIComponent(city) + "&amount=" + baseAmount, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                currentDeliveryCharge = Number(data.charge) || 0;
                updateTotals();
            })
            .catch(() => {
                currentDeliveryCharge = 0;
                updateTotals();
            });
        }

        // ═══ Coupon ═══
        applyBtn.addEventListener('click', async () => {
            const code = couponInput.value.trim();
            if (! code) {
                setMsg('error', 'Please enter a coupon code.');
                return;
            }

            applyBtn.disabled = true;
            setMsg('info', 'Checking...');

            try {
                const res = await fetch(couponRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        code: code,
                        product_id: productId,
                    }),
                });

                const data = await res.json();

                if (! data.ok) {
                    appliedCoupon = null;
                    setMsg('error', data.message || 'Invalid coupon.');
                    couponBox.classList.remove('applied');
                    updateTotals();
                    return;
                }

                appliedCoupon = {
                    code: data.coupon.code,
                    discount: Number(data.discount),
                };

                couponBox.classList.add('applied');
                couponInput.disabled = true;
                applyBtn.textContent = 'Applied';
                applyBtn.disabled = true;

                setMsg('ok', data.message);
                updateTotals();

            } catch (e) {
                setMsg('error', 'Something went wrong. Try again.');
                applyBtn.disabled = false;
            }
        });

        couponInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyBtn.click();
            }
        });

        removeCoupon.addEventListener('click', () => {
            appliedCoupon = null;
            couponInput.value = '';
            couponInput.disabled = false;
            applyBtn.disabled = false;
            applyBtn.textContent = 'Apply';
            couponBox.classList.remove('applied');
            setMsg('', '');
            updateTotals();
        });

        function setMsg(type, text) {
            couponMsg.className = 'co-coupon-msg' + (type ? ' ' + type : '');
            couponMsg.textContent = text || '';
        }

        // ═══ Init ═══
        updateTotals();

        // If city pre-selected (from user profile), fetch charge on load
        if (citySelect?.value && currentOrderType === 'delivery') {
            fetchDeliveryCharge();
        }
    })();
    </script>
</x-app-layout>