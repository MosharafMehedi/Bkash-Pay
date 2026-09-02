<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Full-Stack Laravel Handbook — Checkout</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,600i|space-mono:400,700" rel="stylesheet" />

    <style>
        :root {
            --ink: #201d1a;
            --ink-muted: #6b6558;
            --paper: #f6f1e4;
            --paper-shadow: #e9e2cf;
            --bg: #15141c;
            --bg-soft: #1d1c26;
            --line: #c9c2ae;
            --stamp: #a5342a;
            --sage: #46654c;
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

        /* ---------- Left: story panel ---------- */
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

        .eyebrow {
            font-size: 0.8rem;
            color: #8f8a7c;
            letter-spacing: 0.02em;
            margin-bottom: 1.5rem;
        }

        .story h1 {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: clamp(2.2rem, 4.2vw, 3.4rem);
            line-height: 1.08;
            margin: 0 0 1.5rem;
            color: #f6f1e4;
            max-width: 12ch;
        }

        .story p.lede {
            font-family: 'Fraunces', serif;
            font-style: italic;
            font-weight: 400;
            font-size: 1.15rem;
            color: #b8b2a2;
            line-height: 1.55;
            max-width: 38ch;
            margin: 0 0 2.5rem;
        }

        .contents {
            border-top: 1px solid #33323e;
            padding-top: 1.75rem;
            max-width: 30rem;
        }

        .contents-title {
            font-size: 0.75rem;
            color: #8f8a7c;
            margin-bottom: 1rem;
        }

        .contents ol {
            list-style: none;
            margin: 0;
            padding: 0;
            counter-reset: chapter;
        }

        .contents li {
            counter-increment: chapter;
            display: flex;
            gap: 1rem;
            padding: 0.55rem 0;
            font-size: 0.85rem;
            color: #cfc9ba;
            border-bottom: 1px dashed #2c2b36;
        }

        .contents li:last-child { border-bottom: none; }

        .contents li::before {
            content: counter(chapter, decimal-leading-zero);
            color: #6b6a78;
            flex-shrink: 0;
        }

        /* ---------- Right: receipt panel ---------- */
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

        /* perforated top edge */
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

        /* payment method selector */
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
    </style>
</head>
<body>

    <div class="page">

        {{-- Left: product story --}}
        <div class="story">
            <div class="eyebrow">mBuild Tech Labs — sandbox edition</div>
            <h1>Full-Stack Laravel Handbook</h1>
            <p class="lede">A practical field guide to shipping real Laravel applications — from authentication to background jobs to taking payment.</p>

            <div class="contents">
                <div class="contents-title">Table of Contents</div>
                <ol>
                    <li>Authentication &amp; roles</li>
                    <li>Queues, jobs, and scheduling</li>
                    <li>Accepting payments in Bangladesh</li>
                    <li>Shipping and monitoring in production</li>
                </ol>
            </div>
        </div>

        {{-- Right: receipt / checkout --}}
        <div class="receipt-wrap">
            <form class="receipt" id="checkout-form" method="POST" action="{{ route('bkash.pay') }}">
                @csrf
                <div class="stamp">SANDBOX</div>

                <div class="receipt-head">
                    <span class="label">ORDER RECEIPT</span>
                    <span class="num">No. LHB-0042</span>
                </div>

                <div class="line-item">
                    <span class="desc">Item</span>
                    <span class="val">Full-Stack Laravel Handbook</span>
                </div>
                <div class="line-item">
                    <span class="desc">Format</span>
                    <span class="val">PDF + EPUB</span>
                </div>
                <div class="line-item">
                    <span class="desc">Qty</span>
                    <span class="val">1</span>
                </div>

                <hr class="rule">

                <div class="total-row">
                    <span class="t-label">Total</span>
                    <span>
                        <span class="t-amount" id="amount-display">৳990</span>
                        <span class="t-sub" id="amount-sub">BDT</span>
                    </span>
                </div>

                <div class="pay-title">Pay with</div>
                <div class="methods">
                    <label class="method selected" data-currency="BDT" data-amount="990">
                        <input type="radio" name="method" value="bkash" checked>
                        <span class="swatch bkash"></span>
                        <span class="m-name">bKash</span>
                        <span class="m-note">Mobile banking</span>
                    </label>

                    <label class="method" data-currency="BDT" data-amount="990">
                        <input type="radio" name="method" value="sslcommerz">
                        <span class="swatch sslcommerz"></span>
                        <span class="m-name">SSLCommerz</span>
                        <span class="m-note">Card &amp; bank</span>
                    </label>

                    <label class="method" data-currency="USD" data-amount="9">
                        <input type="radio" name="method" value="paypal">
                        <span class="swatch paypal"></span>
                        <span class="m-name">PayPal</span>
                        <span class="m-note">Card / balance</span>
                    </label>
                </div>

                <input type="hidden" name="amount" id="amount-field" value="990">

                <button type="submit" class="submit-btn">Continue to payment →</button>

                <div class="foot-note">Test transaction · no real money moves</div>
            </form>
        </div>

    </div>

    <script>
        const routes = {
            bkash: "{{ route('bkash.pay') }}",
            paypal: "{{ route('paypal.pay') }}",
            sslcommerz: "{{ route('sslcommerz.pay') }}",
        };

        const form = document.getElementById('checkout-form');
        const amountField = document.getElementById('amount-field');
        const amountDisplay = document.getElementById('amount-display');
        const amountSub = document.getElementById('amount-sub');
        const methods = document.querySelectorAll('.method');

        methods.forEach((el) => {
            el.addEventListener('click', () => {
                methods.forEach((m) => m.classList.remove('selected'));
                el.classList.add('selected');

                const radio = el.querySelector('input[type="radio"]');
                const amount = el.dataset.amount;
                const currency = el.dataset.currency;

                form.action = routes[radio.value];
                amountField.value = amount;
                amountDisplay.textContent = (currency === 'USD' ? '$' : '৳') + amount;
                amountSub.textContent = currency;
            });
        });
    </script>

</body>
</html>
