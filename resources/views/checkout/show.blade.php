<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout — {{ $product->name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --border-color: #e2e8f0;
            --border-active: #2563eb;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-subtle: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-floating: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            -webkit-font-smoothing: antialiased;
        }

        .checkout-container {
            width: 100%;
            max-width: 1040px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-floating);
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            overflow: hidden;
        }

        @media (max-width: 868px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        /* Left Side: Summary & Details */
        .summary-section {
            background: #f8fafc;
            padding: 3rem 2.5rem;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (max-width: 868px) {
            .summary-section {
                border-right: none;
                border-bottom: 1px solid var(--border-color);
                padding: 2rem 1.5rem;
            }
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: color 0.15s ease;
        }

        .back-link:hover {
            color: var(--text-main);
        }

        .product-badge {
            display: inline-block;
            background: #e0e7ff;
            color: #3730a3;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .product-title {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            line-height: 1.25;
            margin-bottom: 0.75rem;
        }

        .product-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .price-breakdown {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 2rem;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .price-row:last-child {
            margin-bottom: 0;
            padding-top: 0.75rem;
            border-top: 1px dashed var(--border-color);
            font-weight: 600;
            color: var(--text-main);
        }

        .total-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--brand-primary);
        }

        .trust-badges {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 500;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Right Side: Payment Methods */
        .payment-section {
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (max-width: 868px) {
            .payment-section {
                padding: 2rem 1.5rem;
            }
        }

        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .section-header p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .user-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f1f5f9;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .user-chip span.email {
            font-weight: 600;
            color: var(--text-main);
        }

        .methods-grid {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .method-card {
            position: relative;
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
        }

        .method-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .method-card.selected {
            border-color: var(--border-active);
            background: #f0f6ff;
            box-shadow: 0 0 0 1px var(--border-active);
        }

        .method-radio {
            margin-right: 0.875rem;
            accent-color: var(--brand-primary);
            width: 18px;
            height: 18px;
        }

        .method-logo {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            color: #fff;
            margin-right: 0.875rem;
            flex-shrink: 0;
        }

        .logo-bkash { background: #e2136e; }
        .logo-sslcommerz { background: #0072bc; }
        .logo-paypal { background: #003087; }

        .method-info {
            flex: 1;
        }

        .method-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .method-desc {
            font-size: 0.775rem;
            color: var(--text-muted);
        }

        .sandbox-tag {
            background: #fef3c7;
            color: #92400e;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--brand-primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.1s ease;
            box-shadow: 0 4px 6px -1px rgb(37 99 235 / 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            background: var(--brand-primary-hover);
        }

        .submit-btn:active {
            transform: scale(0.99);
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            font-size: 0.85rem;
            padding: 0.875rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.25rem;
        }

        .security-footer {
            margin-top: 1.25rem;
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <div class="checkout-container">

        <!-- Left: Product Summary -->
        <div class="summary-section">
            <div>
                <a href="{{ route('dashboard') }}" class="back-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Store
                </a>

                <span class="product-badge">{{ $product->subtitle }}</span>
                <h1 class="product-title">{{ $product->name }}</h1>
                <p class="product-desc">{{ $product->description }}</p>

                <div class="price-breakdown">
                    <div class="price-row">
                        <span>Subtotal</span>
                        <span id="subtotal-val">৳{{ number_format($product->price_bdt, 0) }}</span>
                    </div>
                    <div class="price-row">
                        <span>Tax / Fees</span>
                        <span>৳0.00</span>
                    </div>
                    <div class="price-row">
                        <span>Total Due</span>
                        <span class="total-amount" id="amount-display">৳{{ number_format($product->price_bdt, 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="trust-badges">
                <div class="trust-item">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Encrypted Payment
                </div>
                <div class="trust-item">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Instant Access
                </div>
            </div>
        </div>

        <!-- Right: Payment Form -->
        <div class="payment-section">
            <form id="checkout-form" method="POST" action="{{ route('bkash.pay') }}">
                @csrf

                <div class="section-header">
                    <h2>Payment Method</h2>
                    <p>Select your preferred option to complete purchase.</p>
                </div>

                @if (session('error'))
                    <div class="error-box">{{ session('error') }}</div>
                @endif

                <div class="user-chip">
                    <span>Account</span>
                    <span class="email">{{ auth()->user()->email }}</span>
                </div>

                <div class="methods-grid">
                    <!-- bKash -->
                    <label class="method-card selected">
                        <input type="radio" class="method-radio" name="method" value="bkash" checked>
                        <div class="method-logo logo-bkash">bKash</div>
                        <div class="method-info">
                            <div class="method-title">bKash Online</div>
                            <div class="method-desc">Mobile Wallet / BDT</div>
                        </div>
                    </label>

                    <!-- SSLCommerz -->
                    <label class="method-card">
                        <input type="radio" class="method-radio" name="method" value="sslcommerz">
                        <div class="method-logo logo-sslcommerz">SSL</div>
                        <div class="method-info">
                            <div class="method-title">SSLCommerz Gateway</div>
                            <div class="method-desc">Cards, Net Banking, Wallets</div>
                        </div>
                    </label>

                    <!-- PayPal -->
                    <label class="method-card">
                        <input type="radio" class="method-radio" name="method" value="paypal">
                        <div class="method-logo logo-paypal">PayPal</div>
                        <div class="method-info">
                            <div class="method-title">PayPal / Card</div>
                            <div class="method-desc">International / USD</div>
                        </div>
                    </label>
                </div>

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <button type="submit" class="submit-btn" id="submit-btn">
                    <span>Pay</span>
                    <span id="btn-amount">৳{{ number_format($product->price_bdt, 0) }}</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>

                <div class="security-footer">
                    🔒 Guaranteed safe & secure checkout environment.
                </div>
            </form>
        </div>

    </div>

    <script>
        const routes = {
            bkash: "{{ route('bkash.pay') }}",
            paypal: "{{ route('paypal.pay') }}",
            sslcommerz: "{{ route('sslcommerz.pay') }}",
        };

        const priceBdt = {{ (float) $product->price_bdt }};
        const priceUsd = {{ (float) $product->price_usd }};

        const form = document.getElementById('checkout-form');
        const amountDisplay = document.getElementById('amount-display');
        const subtotalVal = document.getElementById('subtotal-val');
        const btnAmount = document.getElementById('btn-amount');
        const cards = document.querySelectorAll('.method-card');

        cards.forEach((card) => {
            card.addEventListener('click', () => {
                // Handle active state toggle
                cards.forEach((c) => c.classList.remove('selected'));
                card.classList.add('selected');

                const radio = card.querySelector('input[type="radio"]');
                radio.checked = true;
                form.action = routes[radio.value];

                // Update amounts and currency dynamically
                if (radio.value === 'paypal') {
                    const formattedUsd = '$' + priceUsd.toFixed(2);
                    amountDisplay.textContent = formattedUsd;
                    subtotalVal.textContent = formattedUsd;
                    btnAmount.textContent = formattedUsd;
                } else {
                    const formattedBdt = '৳' + priceBdt.toFixed(0);
                    amountDisplay.textContent = formattedBdt;
                    subtotalVal.textContent = formattedBdt;
                    btnAmount.textContent = formattedBdt;
                }
            });
        });
    </script>


</body>
</html>