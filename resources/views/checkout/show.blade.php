<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout — {{ $product->name }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,600i|space-mono:400,700" rel="stylesheet" />

    <style>
        :root {
            --ink: #201d1a;
            --ink-muted: #6b6558;
            --paper: #f6f1e4;
            --bg: #15141c;
            --bg-soft: #1d1c26;
            --line: #c9c2ae;
            --stamp: #a5342a;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: #ece8e0;
            font-family: 'Space Mono', monospace;
            min-height: 100vh;
        }

        .page {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            min-height: 100vh;
        }

        @media (max-width: 900px) {
            .page { grid-template-columns: 1fr; }
        }

        .story {
            padding: 5rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background:
                radial-gradient(circle at 15% 20%, rgba(165, 52, 42, 0.12), transparent 45%),
                var(--bg);
        }

        @media (max-width: 900px) {
            .story { padding: 3.5rem 1.75rem 2rem; }
        }

        .back-link {
            color: #8f8a7c;
            text-decoration: none;
            font-size: 0.8rem;
            margin-bottom: 2rem;
            display: inline-block;
        }
        .back-link:hover { color: #ece8e0; }

        .eyebrow {
            font-size: 0.8rem;
            color: #8f8a7c;
            letter-spacing: 0.02em;
            margin-bottom: 1.5rem;
        }

        .story h1 {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.1;
            margin: 0 0 1.5rem;
            color: #f6f1e4;
            max-width: 14ch;
        }

        .story p.lede {
            font-family: 'Fraunces', serif;
            font-style: italic;
            font-weight: 400;
            font-size: 1.1rem;
            color: #b8b2a2;
            line-height: 1.55;
            max-width: 38ch;
            margin: 0;
        }

        .receipt-wrap {
            background: var(--bg-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2.5rem;
        }

        @media (max-width: 900px) {
            .receipt-wrap { padding: 0 1.5rem 4rem; }
        }

        .receipt {
            background: var(--paper);
            color: var(--ink);
            width: 100%;
            max-width: 25rem;
            padding: 2.25rem 2rem 2.5rem;
            position: relative;
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.55);
        }

        .receipt::before {
            content: "";
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background-image: radial-gradient(circle at 10px 10px, var(--bg-soft) 9px, transparent 9.5px);
            background-size: 20px 20px;
            background-repeat: repeat-x;
        }

        .receipt-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 1.25rem;
        }

        .receipt-head .label {
            font-size: 0.7rem;
            letter-spacing: 0.04em;
            color: var(--ink-muted);
        }

        .receipt-head .num {
            font-size: 0.7rem;
            color: var(--ink-muted);
        }

        .rule {
            border: none;
            border-top: 1px dashed var(--line);
            margin: 1.1rem 0;
        }

        .line-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.85rem;
            padding: 0.3rem 0;
            line-height: 1.5;
        }

        .line-item .desc { color: var(--ink-muted); }
        .line-item .val { text-align: right; }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 0.9rem;
        }

        .total-row .t-label {
            font-family: 'Fraunces', serif;
            font-size: 1rem;
        }

        .total-row .t-amount {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 1.6rem;
        }

        .total-row .t-sub {
            font-size: 0.7rem;
            color: var(--ink-muted);
            display: block;
            text-align: right;
        }

        .pay-title {
            font-size: 0.7rem;
            letter-spacing: 0.04em;
            color: var(--ink-muted);
            margin: 1.6rem 0 0.75rem;
        }

        .methods {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .method {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid #d8d0b8;
            padding: 0.7rem 0.85rem;
            cursor: pointer;
            background: #fdfbf3;
            transition: border-color 0.15s ease, background 0.15s ease;
        }

        .method:hover { border-color: var(--ink); }

        .method input[type="radio"] {
            accent-color: var(--stamp);
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        .method .swatch {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .swatch.bkash { background: #e2136e; }
        .swatch.paypal { background: #1546a0; }
        .swatch.sslcommerz { background: #1f7a4d; }

        .method .m-name {
            font-family: 'Fraunces', serif;
            font-size: 0.95rem;
            flex: 1;
        }

        .method .m-note {
            font-size: 0.68rem;
            color: var(--ink-muted);
        }

        .method.selected {
            border-color: var(--ink);
            background: #fff;
        }

        .submit-btn {
            width: 100%;
            margin-top: 1.5rem;
            padding: 0.95rem;
            background: var(--ink);
            color: var(--paper);
            border: none;
            font-family: 'Space Mono', monospace;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .submit-btn:hover { background: #3a352d; }

        .stamp {
            position: absolute;
            top: 1.6rem;
            right: -0.5rem;
            border: 2px solid var(--stamp);
            color: var(--stamp);
            font-family: 'Space Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 0.06em;
            padding: 0.25rem 0.5rem;
            transform: rotate(8deg);
            opacity: 0.85;
        }

        .foot-note {
            margin-top: 1.25rem;
            font-size: 0.68rem;
            color: var(--ink-muted);
            text-align: center;
        }

        .error-box {
            background: #fdecea;
            border: 1px solid #f3b5ac;
            color: #7a2a20;
            font-size: 0.78rem;
            padding: 0.6rem 0.8rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="page">

        <div class="story">
            <a href="{{ route('dashboard') }}" class="back-link">← Back to store</a>
            <div class="eyebrow">Checkout — {{ $product->subtitle }}</div>
            <h1>{{ $product->name }}</h1>
            <p class="lede">{{ $product->description }}</p>
        </div>

        <div class="receipt-wrap">
            <form class="receipt" id="checkout-form" method="POST" action="{{ route('bkash.pay') }}">
                @csrf
                <div class="stamp">SANDBOX</div>

                @if (session('error'))
                    <div class="error-box">{{ session('error') }}</div>
                @endif

                <div class="receipt-head">
                    <span class="label">ORDER RECEIPT</span>
                    <span class="num">{{ auth()->user()->email }}</span>
                </div>

                <div class="line-item">
                    <span class="desc">Item</span>
                    <span class="val">{{ $product->name }}</span>
                </div>
                <div class="line-item">
                    <span class="desc">Format</span>
                    <span class="val">{{ $product->subtitle }}</span>
                </div>
                <div class="line-item">
                    <span class="desc">Qty</span>
                    <span class="val">1</span>
                </div>

                <hr class="rule">

                <div class="total-row">
                    <span class="t-label">Total</span>
                    <span>
                        <span class="t-amount" id="amount-display">৳{{ number_format($product->price_bdt, 0) }}</span>
                        <span class="t-sub" id="amount-sub">BDT</span>
                    </span>
                </div>

                <div class="pay-title">Pay with</div>
                <div class="methods">
                    <label class="method selected">
                        <input type="radio" name="method" value="bkash" checked>
                        <span class="swatch bkash"></span>
                        <span class="m-name">bKash</span>
                        <span class="m-note">Mobile banking</span>
                    </label>

                    <label class="method">
                        <input type="radio" name="method" value="sslcommerz">
                        <span class="swatch sslcommerz"></span>
                        <span class="m-name">SSLCommerz</span>
                        <span class="m-note">Card &amp; bank</span>
                    </label>

                    <label class="method">
                        <input type="radio" name="method" value="paypal">
                        <span class="swatch paypal"></span>
                        <span class="m-name">PayPal</span>
                        <span class="m-note">Card / balance</span>
                    </label>
                </div>

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <button type="submit" class="submit-btn">Continue to payment →</button>

                <div class="foot-note">Test transaction · no real money moves · receipt goes to {{ auth()->user()->email }}</div>
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
        const amountSub = document.getElementById('amount-sub');
        const methods = document.querySelectorAll('.method');

        methods.forEach((el) => {
            el.addEventListener('click', () => {
                methods.forEach((m) => m.classList.remove('selected'));
                el.classList.add('selected');

                const radio = el.querySelector('input[type="radio"]');
                form.action = routes[radio.value];

                if (radio.value === 'paypal') {
                    amountDisplay.textContent = '$' + priceUsd.toFixed(2);
                    amountSub.textContent = 'USD';
                } else {
                    amountDisplay.textContent = '৳' + priceBdt.toFixed(0);
                    amountSub.textContent = 'BDT';
                }
            });
        });
    </script>

</body>
</html>
