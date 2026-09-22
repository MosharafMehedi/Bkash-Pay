<x-app-layout>
    @section('title', 'Checkout — ' . $product->name)

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

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

        .co-wrap { max-width: 1100px; margin: 0 auto; }

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

        .co-grid {
            display: grid;
            grid-template-columns: 1fr 1.05fr;
            gap: 1.25rem;
        }
        @media (max-width: 900px) {
            .co-grid { grid-template-columns: 1fr; }
        }

        .co-panel {
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            padding: 1.75rem;
        }

        /* LEFT — summary */
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
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-hi);
            line-height: 1.25;
            margin-bottom: 0.5rem;
        }
        .co-desc {
            color: var(--text-mu);
            font-size: 0.88rem;
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .co-price-box {
            border-radius: 0.85rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 1rem 1.1rem;
            margin-bottom: 1.25rem;
        }
        .co-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-mu);
            padding: 0.35rem 0;
        }
        .co-price-row.total {
            border-top: 1px dashed var(--glass-border);
            margin-top: 0.4rem;
            padding-top: 0.7rem;
            font-weight: 600;
            color: var(--text-hi);
            font-size: 0.9rem;
        }
        .co-price-row.total .co-amount {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--cyan);
        }

        .co-trust {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.72rem;
            color: var(--text-mu);
            font-weight: 500;
        }
        .co-trust-item { display: inline-flex; align-items: center; gap: 0.35rem; }
        .co-trust-item svg { width: 14px; height: 14px; color: var(--cyan); }

        /* RIGHT — payment */
        .co-section-head { margin-bottom: 1.25rem; }
        .co-section-head h2 {
            font-family: 'Sora', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-hi);
            margin-bottom: 0.2rem;
        }
        .co-section-head p {
            font-size: 0.82rem;
            color: var(--text-mu);
        }

        .co-user {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.7rem 0.9rem;
            border-radius: 0.7rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            font-size: 0.82rem;
            color: var(--text-mu);
            margin-bottom: 1.15rem;
        }
        .co-user .email { color: var(--text-hi); font-weight: 600; }

        .co-methods {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 1.15rem;
        }
        .co-method {
            position: relative;
            display: flex;
            align-items: center;
            padding: 0.85rem 1rem;
            border-radius: 0.85rem;
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
            width: 18px; height: 18px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.25);
            margin-right: 0.85rem;
            position: relative;
            cursor: pointer;
            flex-shrink: 0;
            transition: border-color 0.2s;
        }
        .co-method input[type="radio"]:checked {
            border-color: var(--cyan);
        }
        .co-method input[type="radio"]:checked::before {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 8px var(--cyan);
        }

        .co-logo {
            width: 38px; height: 38px;
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.68rem;
            color: #fff;
            margin-right: 0.85rem;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }
        .logo-bkash      { background: linear-gradient(135deg, #e2136e, #ff5fb0); }
        .logo-sslcommerz { background: linear-gradient(135deg, #0072bc, #29e7ff); color: #06050c; }
        .logo-paypal     { background: linear-gradient(135deg, #003087, #1546a0); }
        .logo-cash       { background: linear-gradient(135deg, #10b981, #34d399); color: #06050c; }

        .co-method-info { flex: 1; min-width: 0; }
        .co-method-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-hi);
        }
        .co-method-desc {
            font-size: 0.72rem;
            color: var(--text-mu);
            margin-top: 0.1rem;
        }

        .co-submit {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            padding: 0.9rem 1.25rem;
            border-radius: 0.8rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
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
        .co-submit svg { width: 16px; height: 16px; }

        .co-secure {
            margin-top: 0.9rem;
            text-align: center;
            font-size: 0.72rem;
            color: var(--text-mu);
        }

        .co-error {
            padding: 0.75rem 0.95rem;
            margin-bottom: 1.15rem;
            border-radius: 0.7rem;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            font-size: 0.82rem;
        }

        .co-success {
            padding: 0.75rem 0.95rem;
            margin-bottom: 1.15rem;
            border-radius: 0.7rem;
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.3);
            color: #6ee7b7;
            font-size: 0.82rem;
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

        <div class="co-grid">

            {{-- LEFT: Product summary --}}
            <div class="co-panel">
                <div class="co-summary-media">
                    @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                    @else
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            {{-- RIGHT: Payment method --}}
            <div class="co-panel">
                <form id="checkout-form" method="POST" action="{{ route('bkash.pay') }}">
                    @csrf

                    <div class="co-section-head">
                        <h2>Payment Method</h2>
                        <p>Select how you'd like to pay.</p>
                    </div>

                    <div class="co-user">
                        <span>Account</span>
                        <span class="email">{{ auth()->user()->email }}</span>
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
                                <div class="co-method-desc">Pay when you receive · BDT</div>
                            </div>
                        </label>
                    </div>

                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <button type="submit" class="co-submit">
                        <span>Pay</span>
                        <span id="btn-amount">৳{{ number_format($product->final_price_bdt, 0) }}</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>

                    <div class="co-secure">🔒 Guaranteed safe &amp; secure checkout</div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const routes = {
            bkash:      "{{ route('bkash.pay') }}",
            paypal:     "{{ route('paypal.pay') }}",
            sslcommerz: "{{ route('sslcommerz.pay') }}",
            cash:       "{{ route('cash.pay') }}",
        };

        const priceBdt = {{ (float) $product->final_price_bdt }};
        const priceUsd = {{ (float) $product->final_price_usd }};

        const form          = document.getElementById('checkout-form');
        const amountDisplay = document.getElementById('amount-display');
        const subtotalVal   = document.getElementById('subtotal-val');
        const btnAmount     = document.getElementById('btn-amount');
        const methods       = document.querySelectorAll('.co-method');

        methods.forEach((m) => {
            m.addEventListener('click', () => {
                methods.forEach((x) => x.classList.remove('selected'));
                m.classList.add('selected');

                const radio = m.querySelector('input[type="radio"]');
                radio.checked = true;
                form.action = routes[radio.value];

                if (radio.value === 'paypal') {
                    const v = '$' + priceUsd.toFixed(2);
                    amountDisplay.textContent = v;
                    subtotalVal.textContent = v;
                    btnAmount.textContent = v;
                } else {
                    const v = '৳' + priceBdt.toFixed(0);
                    amountDisplay.textContent = v;
                    subtotalVal.textContent = v;
                    btnAmount.textContent = v;
                }
            });
        });
    </script>
</x-app-layout>