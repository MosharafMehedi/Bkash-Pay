<x-app-layout>
    @section('title', 'Checkout')

    @php
        $usdRate = max((float) config('app.usd_rate', 122), 0.0001);
        $subtotalUsd = round(((float) $subtotal) / $usdRate, 2);
    @endphp

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600|jetbrains-mono:400,700" rel="stylesheet">

    <style>
        .co-page {
            font-family: 'Inter', sans-serif;
            --cyan: #29e7ff;
            --violet: #a78bfa;
            --pink: #ff5fb0;
            --green: #34d399;
            --red: #f87171;
            --text-hi: #f1f0fb;
            --text-md: #cbd5e1;
            --text-mu: #9a94b8;
            --glass: rgba(255,255,255,0.045);
            --glass-border: rgba(255,255,255,0.09);
            color: var(--text-hi);
        }
        .co-wrap { max-width: 1180px; margin: 0 auto; }

        /* Steps */
        .co-steps {
            display: flex; align-items: center; justify-content: center; flex-wrap: wrap;
            gap: 0.6rem; margin-bottom: 1.75rem; font-size: 0.8rem; font-weight: 500; color: var(--text-mu);
        }
        .co-step { display: flex; align-items: center; gap: 0.45rem; }
        .co-step-num {
            width: 24px; height: 24px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.72rem; font-weight: 700;
            background: rgba(255,255,255,0.08); color: var(--text-mu);
        }
        .co-step.done .co-step-num { background: var(--green); color: #06050c; }
        .co-step.active { color: var(--text-hi); font-weight: 600; }
        .co-step.active .co-step-num { background: linear-gradient(135deg, var(--cyan), var(--violet)); color: #06050c; }
        .co-step-line { width: 36px; height: 2px; background: var(--glass-border); }

        .co-back {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: var(--text-mu); font-size: 0.85rem; font-weight: 500;
            text-decoration: none; margin-bottom: 1.25rem; transition: color .2s;
        }
        .co-back:hover { color: var(--cyan); }

        .co-grid { display: grid; grid-template-columns: 1fr 400px; gap: 1.5rem; align-items: start; }
        @media (max-width: 1024px) { .co-grid { grid-template-columns: 1fr; } }
        .co-aside { position: sticky; top: 1rem; }
        @media (max-width: 1024px) { .co-aside { position: static; } }

        .co-panel {
            border-radius: 1.1rem; background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); padding: 1.5rem; margin-bottom: 1.25rem;
        }
        .co-panel-title {
            font-family: 'Sora', sans-serif; font-size: 0.95rem; font-weight: 700;
            color: var(--text-hi); margin-bottom: 1.1rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .co-panel-title .count {
            font-family: 'Inter', sans-serif; font-size: 0.7rem; font-weight: 600;
            background: rgba(41,231,255,0.12); color: var(--cyan);
            padding: 0.12rem 0.55rem; border-radius: 999px;
        }
        .co-section-head { margin-bottom: 1.1rem; }
        .co-section-head h2 { font-family: 'Sora', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--text-hi); margin-bottom: 0.15rem; }
        .co-section-head p { font-size: 0.8rem; color: var(--text-mu); }

        /* Order type */
        .co-type-toggle { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem; }
        .co-type-option {
            display: flex; align-items: center; gap: 0.65rem; padding: 0.8rem;
            border-radius: 0.75rem; border: 1.5px solid var(--glass-border);
            background: rgba(255,255,255,0.02); cursor: pointer; transition: all .2s;
        }
        .co-type-option:hover { border-color: rgba(41,231,255,0.35); }
        .co-type-option.active {
            border-color: var(--cyan); background: rgba(41,231,255,0.06);
            box-shadow: 0 0 0 3px rgba(41,231,255,0.08);
        }
        .co-type-option input { display: none; }
        .co-type-icon {
            width: 36px; height: 36px; border-radius: 0.55rem;
            background: rgba(41,231,255,0.12); border: 1px solid rgba(41,231,255,0.25);
            display: flex; align-items: center; justify-content: center; color: var(--cyan); flex-shrink: 0;
        }
        .co-type-title { font-size: 0.82rem; font-weight: 600; color: var(--text-hi); line-height: 1.15; }
        .co-type-sub { font-size: 0.68rem; color: var(--text-mu); margin-top: 0.1rem; }

        /* Fields */
        .co-fields-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 1rem; }
        .co-fields-grid .full { grid-column: 1 / -1; }
        @media (max-width: 640px) { .co-fields-grid { grid-template-columns: 1fr; } }
        .co-field { margin-bottom: 1rem; }
        .co-field label { display: block; font-size: 0.74rem; font-weight: 600; color: var(--text-md); margin-bottom: 0.35rem; }
        .co-field label .req { color: var(--red); }
        .co-field input, .co-field select, .co-field textarea {
            width: 100%; background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09); border-radius: 0.6rem;
            padding: 0.6rem 0.8rem; font-size: 0.85rem; color: var(--text-hi);
            outline: none; font-family: inherit; transition: border-color .2s;
        }
        .co-field input::placeholder, .co-field textarea::placeholder { color: var(--text-mu); }
        .co-field input:focus, .co-field select:focus, .co-field textarea:focus { border-color: rgba(41,231,255,0.5); }
        .co-field select option { background: #111827; }
        .co-field textarea { resize: vertical; }

        /* Pickup */
        .co-pickup-box {
            display: flex; gap: 0.75rem; padding: 0.9rem; border-radius: 0.7rem;
            background: rgba(52,211,153,0.06); border: 1px solid rgba(52,211,153,0.28); margin-bottom: 0.7rem;
        }
        .co-pickup-icon { font-size: 1.5rem; flex-shrink: 0; }
        .co-pickup-title { font-weight: 700; color: #6ee7b7; font-size: 0.85rem; margin-bottom: 0.3rem; }
        .co-pickup-address { font-size: 0.78rem; color: var(--text-hi); margin-bottom: 0.15rem; }
        .co-pickup-hours { font-size: 0.68rem; color: var(--text-mu); }
        .co-pickup-note { font-size: 0.72rem; color: var(--text-mu); line-height: 1.5; }

        /* Account row */
        .co-user {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.65rem 0.85rem; border-radius: 0.65rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
            font-size: 0.78rem; color: var(--text-mu); margin-bottom: 1rem;
        }
        .co-user .email { color: var(--text-hi); font-weight: 600; }

        /* Payment method cards */
        .co-methods { display: flex; flex-direction: column; gap: 0.55rem; }
        .co-method {
            position: relative; display: flex; align-items: center;
            padding: 0.75rem 0.9rem; border-radius: 0.75rem;
            border: 1.5px solid var(--glass-border); background: rgba(255,255,255,0.02);
            cursor: pointer; transition: border-color .2s, background .2s, transform .2s;
        }
        .co-method:hover { border-color: rgba(41,231,255,0.35); transform: translateY(-1px); }
        .co-method.selected {
            border-color: var(--cyan); background: rgba(41,231,255,0.06);
            box-shadow: 0 0 0 3px rgba(41,231,255,0.08);
        }
        .co-method input[type="radio"] {
            appearance: none; -webkit-appearance: none;
            width: 16px; height: 16px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.25);
            margin-right: 0.75rem; position: relative; cursor: pointer; flex-shrink: 0;
        }
        .co-method input[type="radio"]:checked { border-color: var(--cyan); }
        .co-method input[type="radio"]:checked::before {
            content: ''; position: absolute; inset: 3px; border-radius: 50%;
            background: var(--cyan); box-shadow: 0 0 8px var(--cyan);
        }
        .co-method input[type="radio"]:focus-visible { outline: 2px solid var(--cyan); outline-offset: 3px; }
        .co-logo {
            width: 34px; height: 34px; border-radius: 0.55rem;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.62rem; color: #fff;
            margin-right: 0.75rem; flex-shrink: 0; letter-spacing: 0.02em;
        }
        .logo-bkash      { background: linear-gradient(135deg, #e2136e, #ff5fb0); }
        .logo-sslcommerz { background: linear-gradient(135deg, #0072bc, #29e7ff); color: #06050c; }
        .logo-paypal     { background: linear-gradient(135deg, #003087, #1546a0); }
        .logo-cash       { background: linear-gradient(135deg, #10b981, #34d399); color: #06050c; }
        .co-method-info { flex: 1; min-width: 0; }
        .co-method-title { font-size: 0.82rem; font-weight: 600; color: var(--text-hi); }
        .co-method-desc { font-size: 0.68rem; color: var(--text-mu); margin-top: 0.1rem; }

        /* Summary items */
        .co-items { display: flex; flex-direction: column; max-height: 320px; overflow-y: auto; margin: 0 -0.25rem; padding: 0 0.25rem; }
        .co-item { display: flex; gap: 0.85rem; padding: 0.8rem 0; border-bottom: 1px solid rgba(255,255,255,0.06); align-items: center; }
        .co-item:last-child { border-bottom: none; }
        .co-item-img {
            width: 56px; height: 56px; border-radius: 0.55rem; overflow: hidden; flex-shrink: 0;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center;
        }
        .co-item-img img { width: 100%; height: 100%; object-fit: cover; }
        .co-item-img svg { color: #64748b; width: 22px; height: 22px; }
        .co-item-info { flex: 1; min-width: 0; }
        .co-item-name {
            font-size: 0.85rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.15rem;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .co-item-meta { font-size: 0.72rem; color: var(--text-mu); }
        .co-item-price { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.88rem; color: var(--text-hi); white-space: nowrap; }

        /* Coupon */
        .co-coupon { margin: 1rem 0; padding-top: 1rem; border-top: 1px solid var(--glass-border); }
        .co-coupon-label { display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; color: var(--text-mu); margin-bottom: 0.45rem; }
        .co-coupon-label svg { width: 13px; height: 13px; color: var(--cyan); }
        .co-coupon-row { display: flex; gap: 0.5rem; }
        .co-coupon-row input {
            flex: 1; min-width: 0; padding: 0.6rem 0.8rem; border-radius: 0.6rem;
            background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); color: var(--text-hi);
            font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; letter-spacing: 0.06em;
            text-transform: uppercase; outline: none; transition: border-color .2s;
        }
        .co-coupon-row input::placeholder { color: var(--text-mu); letter-spacing: 0; text-transform: none; font-family: 'Inter', sans-serif; }
        .co-coupon-row input:focus { border-color: rgba(41,231,255,0.5); }
        .co-coupon-row input:disabled { opacity: 0.7; cursor: not-allowed; }
        .co-coupon-row button {
            padding: 0.6rem 1rem; border-radius: 0.6rem; font-size: 0.78rem; font-weight: 600;
            color: #06050c; background: linear-gradient(135deg, var(--cyan), var(--violet));
            border: none; cursor: pointer; transition: filter .15s; white-space: nowrap;
        }
        .co-coupon-row button:hover:not(:disabled) { filter: brightness(1.08); }
        .co-coupon-row button:disabled { opacity: 0.6; cursor: wait; }
        .co-coupon-msg { margin-top: 0.4rem; font-size: 0.72rem; min-height: 1em; }
        .co-coupon-msg.ok { color: #6ee7b7; }
        .co-coupon-msg.error { color: #fca5a5; }
        .co-coupon-msg.info { color: var(--cyan); }
        .co-coupon.applied .co-coupon-row input { border-color: rgba(52,211,153,0.5); background: rgba(52,211,153,0.05); }

        /* Prices */
        .co-price-box { padding-top: 0.25rem; }
        .co-price-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: var(--text-mu); padding: 0.4rem 0; }
        .co-price-row.total {
            border-top: 1px dashed var(--glass-border); margin-top: 0.5rem; padding-top: 0.85rem;
            font-weight: 600; color: var(--text-hi); font-size: 0.92rem;
        }
        .co-price-row.total .co-amount { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.3rem; color: var(--cyan); }
        .co-price-row.discount { color: #6ee7b7; }
        .co-price-row.discount .co-discount { font-weight: 700; color: #6ee7b7; }
        .co-coupon-tag { color: var(--cyan); font-weight: 600; font-size: 0.72rem; margin-left: 0.35rem; }
        .co-remove-coupon { background: none; border: none; color: var(--red); font-size: 0.7rem; cursor: pointer; margin-left: 0.5rem; text-decoration: underline; padding: 0; }

        /* Submit + trust */
        .co-submit {
            width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.9rem 1.1rem; margin-top: 1.1rem; border-radius: 0.75rem;
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.9rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet)); border: none; cursor: pointer;
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
            transition: transform .2s, filter .2s, box-shadow .2s;
        }
        .co-submit:hover:not(:disabled) { transform: translateY(-1px); filter: brightness(1.08); box-shadow: 0 16px 38px -10px rgba(41,231,255,0.85); }
        .co-submit:active:not(:disabled) { transform: translateY(0); }
        .co-submit:disabled { opacity: 0.65; cursor: wait; }
        .co-submit svg { width: 15px; height: 15px; }

        .co-trust {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;
            margin-top: 1.1rem; padding-top: 1.1rem; border-top: 1px solid var(--glass-border);
        }
        .co-trust-item { text-align: center; font-size: 0.66rem; color: var(--text-mu); line-height: 1.3; }
        .co-trust-item span { display: block; font-size: 1.1rem; margin-bottom: 0.2rem; }
        .co-secure { margin-top: 0.85rem; text-align: center; font-size: 0.7rem; color: var(--text-mu); }

        .co-alert { padding: 0.7rem 0.9rem; margin-bottom: 1rem; border-radius: 0.65rem; font-size: 0.78rem; }
        .co-alert.error   { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
        .co-alert.success { background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.3); color: #6ee7b7; }
    </style>

    <div class="co-page">
        <div class="co-wrap">

            {{-- Steps --}}
            <div class="co-steps">
                <div class="co-step done"><span class="co-step-num">✓</span> Cart</div>
                <div class="co-step-line"></div>
                <div class="co-step active"><span class="co-step-num">2</span> Checkout</div>
                <div class="co-step-line"></div>
                <div class="co-step"><span class="co-step-num">3</span> Payment</div>
            </div>

            <a href="{{ route('cart.index') }}" class="co-back">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Cart
            </a>

            @if (session('success'))
                <div class="co-alert success">✓ {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="co-alert error">{{ session('error') }}</div>
            @endif

            <form id="checkout-form" method="POST" action="{{ route('bkash.pay') }}">
                @csrf

                <div class="co-grid">

                    {{-- ══ LEFT: Delivery + Payment ══ --}}
                    <div>
                        {{-- Delivery --}}
                        <div class="co-panel">
                            <div class="co-section-head">
                                <h2>Delivery Information</h2>
                                <p>Where should we send your order?</p>
                            </div>

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

                            <div id="deliveryFields">
                                <div class="co-fields-grid">
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
                                        <label>Postal Code</label>
                                        <input type="text" name="delivery_postal"
                                               value="{{ old('delivery_postal', auth()->user()->postal_code) }}"
                                               placeholder="Optional">
                                    </div>

                                    <div class="co-field full">
                                        <label>Full Address <span class="req">*</span></label>
                                        <textarea name="delivery_address" rows="2"
                                                  placeholder="House, road, area...">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                                    </div>

                                    <div class="co-field full" style="margin-bottom:0;">
                                        <label>Delivery Note</label>
                                        <textarea name="delivery_note" rows="2"
                                                  placeholder="Special instructions (optional)">{{ old('delivery_note') }}</textarea>
                                    </div>
                                </div>
                            </div>

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

                        {{-- Payment --}}
                        <div class="co-panel">
                            <div class="co-section-head">
                                <h2>Payment Method</h2>
                                <p>Select how you'd like to pay.</p>
                            </div>

                            <div class="co-user">
                                <span>Account</span>
                                <span class="email">{{ auth()->user()->email }}</span>
                            </div>

                            <div class="co-methods">
                                <label class="co-method selected">
                                    <input type="radio" name="method" value="bkash" checked>
                                    <div class="co-logo logo-bkash">bKash</div>
                                    <div class="co-method-info">
                                        <div class="co-method-title">bKash Online</div>
                                        <div class="co-method-desc">Mobile Wallet · BDT</div>
                                    </div>
                                </label>

                                <label class="co-method">
                                    <input type="radio" name="method" value="sslcommerz">
                                    <div class="co-logo logo-sslcommerz">SSL</div>
                                    <div class="co-method-info">
                                        <div class="co-method-title">SSLCommerz</div>
                                        <div class="co-method-desc">Cards, Net Banking, Wallets</div>
                                    </div>
                                </label>

                                <label class="co-method">
                                    <input type="radio" name="method" value="paypal">
                                    <div class="co-logo logo-paypal">PayPal</div>
                                    <div class="co-method-info">
                                        <div class="co-method-title">PayPal / Card</div>
                                        <div class="co-method-desc">International · USD</div>
                                    </div>
                                </label>

                                <label class="co-method">
                                    <input type="radio" name="method" value="cash">
                                    <div class="co-logo logo-cash">CASH</div>
                                    <div class="co-method-info">
                                        <div class="co-method-title">Cash on Delivery</div>
                                        <div class="co-method-desc">Pay when you receive</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- ══ RIGHT: Order summary (sticky) ══ --}}
                    <aside class="co-aside">
                        <div class="co-panel">
                            <div class="co-panel-title">
                                Order Summary
                                <span class="count">{{ $itemCount }} {{ \Illuminate\Support\Str::plural('item', $itemCount) }}</span>
                            </div>

                            <div class="co-items">
                                @foreach ($items as $item)
                                    @php $product = $item->product; @endphp
                                    <div class="co-item">
                                        <div class="co-item-img">
                                            @if ($product && $product->image)
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                            @else
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="co-item-info">
                                            <div class="co-item-name">{{ $product->name ?? 'Product Unavailable' }}</div>
                                            <div class="co-item-meta">৳{{ number_format($item->unit_price, 0) }} × {{ $item->quantity }}</div>
                                        </div>
                                        <div class="co-item-price">৳{{ number_format($item->line_total, 0) }}</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Coupon --}}
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

                            {{-- Prices --}}
                            <div class="co-price-box">
                                <div class="co-price-row">
                                    <span>Subtotal</span>
                                    <span id="subtotal-val">৳{{ number_format($subtotal, 0) }}</span>
                                </div>

                                <div class="co-price-row discount" id="discountRow" style="display:none;">
                                    <span>
                                        Discount
                                        <span class="co-coupon-tag" id="couponCodeLabel"></span>
                                        <button type="button" class="co-remove-coupon" id="removeCoupon">remove</button>
                                    </span>
                                    <span class="co-discount" id="discountAmount">−৳0</span>
                                </div>

                                <div class="co-price-row" id="deliveryChargeRow" style="display:none;">
                                    <span>Delivery Charge</span>
                                    <span id="deliveryChargeAmount">৳0</span>
                                </div>

                                <div class="co-price-row total">
                                    <span>Total</span>
                                    <span class="co-amount" id="amount-display">৳{{ number_format($subtotal, 0) }}</span>
                                </div>
                            </div>

                            <input type="hidden" name="coupon_code" id="couponHidden" value="">
                            <input type="hidden" name="discount_amount" id="discountHidden" value="0">
                            <input type="hidden" name="delivery_charge" id="deliveryChargeHidden" value="0">

                            <button type="submit" class="co-submit" id="submitBtn">
                                <span>Pay</span>
                                <span id="btn-amount">৳{{ number_format($subtotal, 0) }}</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>

                            <div class="co-trust">
                                <div class="co-trust-item"><span>🔒</span>Secure<br>Payment</div>
                                <div class="co-trust-item"><span>🚚</span>Fast<br>Delivery</div>
                                <div class="co-trust-item"><span>↩️</span>Easy<br>Support</div>
                            </div>
                            <div class="co-secure">Your payment information is encrypted and safe.</div>
                        </div>
                    </aside>

                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        const routes = {
            bkash:      "{{ route('bkash.pay') }}",
            paypal:     "{{ route('paypal.pay') }}",
            sslcommerz: "{{ route('sslcommerz.pay') }}",
            cash:       "{{ route('cash.pay') }}",
        };
        const couponRoute         = @json(route('coupon.apply'));
        const deliveryChargeRoute = @json(route('checkout.deliveryCharge'));
        const csrfToken           = @json(csrf_token());

        const SUBTOTAL_BDT = @json((float) $subtotal);
        const USD_RATE     = @json((float) $usdRate);   // 1 USD = X BDT

        const form           = document.getElementById('checkout-form');
        const submitBtn      = document.getElementById('submitBtn');
        const amountDisplay  = document.getElementById('amount-display');
        const subtotalVal    = document.getElementById('subtotal-val');
        const btnAmount      = document.getElementById('btn-amount');
        const methodRadios   = document.querySelectorAll('input[name="method"]');

        const couponBox      = document.getElementById('couponBox');
        const couponInput    = document.getElementById('couponInput');
        const applyBtn       = document.getElementById('applyCoupon');
        const couponMsg      = document.getElementById('couponMsg');
        const discountRow    = document.getElementById('discountRow');
        const discountAmt    = document.getElementById('discountAmount');
        const couponLbl      = document.getElementById('couponCodeLabel');
        const couponHidden   = document.getElementById('couponHidden');
        const discountHidden = document.getElementById('discountHidden');
        const removeCoupon   = document.getElementById('removeCoupon');

        const deliveryChargeRow    = document.getElementById('deliveryChargeRow');
        const deliveryChargeAmt    = document.getElementById('deliveryChargeAmount');
        const deliveryChargeHidden = document.getElementById('deliveryChargeHidden');

        const deliveryFields = document.getElementById('deliveryFields');
        const pickupInfo     = document.getElementById('pickupInfo');
        const citySelect     = document.getElementById('citySelect');

        let appliedCoupon         = null;      // { code, discount (BDT) }
        let currentMethod         = 'bkash';
        let currentDeliveryCharge = 0;         // always BDT
        let currentOrderType      = 'delivery';
        let chargeRequestId       = 0;

        const fmtBdt = (n) => '৳' + Math.round(Number(n)).toLocaleString('en-US');
        const fmtUsd = (n) => '$' + Number(n).toFixed(2);

        function updateTotals() {
            const isUsd = currentMethod === 'paypal';
            const conv  = (n) => isUsd ? n / USD_RATE : n;
            const fmt   = isUsd ? fmtUsd : fmtBdt;

            const discountBdt = appliedCoupon ? Math.min(appliedCoupon.discount, SUBTOTAL_BDT) : 0;
            const deliveryBdt = currentOrderType === 'pickup' ? 0 : currentDeliveryCharge;
            const totalBdt    = Math.max(SUBTOTAL_BDT - discountBdt, 0) + deliveryBdt;

            subtotalVal.textContent   = fmt(conv(SUBTOTAL_BDT));
            amountDisplay.textContent = fmt(conv(totalBdt));
            btnAmount.textContent     = fmt(conv(totalBdt));

            // Discount row
            if (discountBdt > 0) {
                discountRow.style.display = '';
                discountAmt.textContent   = '−' + fmt(conv(discountBdt));
                couponLbl.textContent     = '(' + appliedCoupon.code + ')';
                couponHidden.value        = appliedCoupon.code;
                discountHidden.value      = discountBdt;
            } else {
                discountRow.style.display = 'none';
                couponHidden.value        = '';
                discountHidden.value      = '0';
            }

            // Delivery row
            if (deliveryBdt > 0) {
                deliveryChargeRow.style.display = '';
                deliveryChargeAmt.textContent   = fmt(conv(deliveryBdt));
            } else if (currentOrderType === 'delivery' && citySelect && citySelect.value) {
                deliveryChargeRow.style.display = '';
                deliveryChargeAmt.innerHTML     = '<span style="color:#6ee7b7;font-weight:700;">FREE</span>';
            } else {
                deliveryChargeRow.style.display = 'none';
            }

            deliveryChargeHidden.value = deliveryBdt; // BDT, for the server
        }

        // ── Payment method ──
        function applyMethod(radio) {
            document.querySelectorAll('.co-method').forEach((el) => el.classList.remove('selected'));
            radio.closest('.co-method').classList.add('selected');
            currentMethod = radio.value;
            form.action   = routes[radio.value];
            updateTotals();
        }
        methodRadios.forEach((r) => r.addEventListener('change', () => applyMethod(r)));

        // ── Order type ──
        function setRequired(on) {
            ['delivery_name', 'delivery_phone', 'delivery_city', 'delivery_address'].forEach((n) => {
                const el = form.elements[n];
                if (el) el.required = on;
            });
        }

        function applyOrderType(value) {
            currentOrderType = value;
            document.querySelectorAll('.co-type-option').forEach((el) => {
                el.classList.toggle('active', el.querySelector('input').value === value);
            });

            if (value === 'pickup') {
                deliveryFields.style.display = 'none';
                pickupInfo.style.display     = '';
                setRequired(false);
                currentDeliveryCharge = 0;
                updateTotals();
            } else {
                deliveryFields.style.display = '';
                pickupInfo.style.display     = 'none';
                setRequired(true);
                fetchDeliveryCharge();
            }
        }

        document.querySelectorAll('input[name="order_type"]').forEach((radio) => {
            radio.addEventListener('change', () => applyOrderType(radio.value));
        });

        // ── Delivery charge (always calculated in BDT) ──
        function fetchDeliveryCharge() {
            const city = citySelect ? citySelect.value : '';
            if (!city) {
                currentDeliveryCharge = 0;
                updateTotals();
                return;
            }

            const myId = ++chargeRequestId;

            fetch(deliveryChargeRoute + '?city=' + encodeURIComponent(city) + '&amount=' + SUBTOTAL_BDT, {
                headers: { 'Accept': 'application/json' }
            })
            .then((r) => r.json())
            .then((data) => {
                if (myId !== chargeRequestId) return; // stale response
                currentDeliveryCharge = Number(data.charge) || 0;
                updateTotals();
            })
            .catch(() => {
                if (myId !== chargeRequestId) return;
                currentDeliveryCharge = 0;
                updateTotals();
            });
        }

        if (citySelect) citySelect.addEventListener('change', fetchDeliveryCharge);

        // ── Coupon ──
        function setMsg(type, text) {
            couponMsg.className   = 'co-coupon-msg' + (type ? ' ' + type : '');
            couponMsg.textContent = text || '';
        }

        applyBtn.addEventListener('click', async () => {
            const code = couponInput.value.trim();
            if (!code) {
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
                    body: JSON.stringify({ code: code }),
                });

                const data = await res.json();

                if (!data.ok) {
                    appliedCoupon = null;
                    setMsg('error', data.message || 'Invalid coupon.');
                    couponBox.classList.remove('applied');
                    applyBtn.disabled = false;
                    updateTotals();
                    return;
                }

                appliedCoupon = { code: data.coupon.code, discount: Number(data.discount) };

                couponBox.classList.add('applied');
                couponInput.disabled = true;
                applyBtn.textContent = 'Applied';
                applyBtn.disabled    = true;

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
            couponInput.value    = '';
            couponInput.disabled = false;
            applyBtn.disabled    = false;
            applyBtn.textContent = 'Apply';
            couponBox.classList.remove('applied');
            setMsg('', '');
            updateTotals();
        });

        // ── Prevent double submit ──
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
        });
        window.addEventListener('pageshow', () => {
            submitBtn.disabled = false; // back button / bfcache
        });

        // ── Init ──
        const checkedMethod = document.querySelector('input[name="method"]:checked');
        if (checkedMethod) applyMethod(checkedMethod);

        const checkedType = document.querySelector('input[name="order_type"]:checked');
        applyOrderType(checkedType ? checkedType.value : 'delivery');
    })();
    </script>
</x-app-layout>