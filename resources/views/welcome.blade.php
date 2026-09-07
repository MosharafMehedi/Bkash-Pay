<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modern Payment Gateway Experience</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    <style>
        :root {
            --bg-start: #0b0f19;
            --bg-end: #111827;
            --accent-cyan: #06b6d4;
            --accent-pink: #ec4899;
            --accent-purple: #8b5cf6;
            --glass-card: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-start);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.08) 0%, transparent 50%);
            min-height: 100vh;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ---------- Top Header Navbar ---------- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 4rem;
            background: rgba(11, 15, 25, 0.7);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
        }

        .brand {
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            letter-spacing: -0.02em;
        }

        .brand-logo-glow {
            width: 12px;
            height: 12px;
            background: var(--accent-cyan);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--accent-cyan);
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-auth {
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            padding: 0.65rem 1.6rem;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-login {
            color: #e2e8f0;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.03);
        }

        .btn-login:hover {
            border-color: var(--accent-cyan);
            color: #fff;
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.25);
            transform: translateY(-2px);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            color: #fff;
            border: none;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.35);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(6, 182, 212, 0.5);
        }

        /* ---------- Main Visual Display ---------- */
        .viewport-main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 6rem 2rem 2rem;
        }

        .main-stage {
            position: relative;
            width: 100%;
            max-width: 1050px;
            height: 540px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Left Phone Area */
        .phone-mockup {
            width: 290px;
            height: 480px;
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(25px);
            border: 2px solid rgba(255, 255, 255, 0.12);
            border-radius: 36px;
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(6, 182, 212, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .cart-graphic {
            width: 100px;
            height: 100px;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px rgba(6, 182, 212, 0.15);
        }

        .cart-graphic svg {
            width: 48px;
            height: 48px;
            fill: var(--accent-cyan);
        }

        .gift-pills {
            display: flex;
            gap: 0.6rem;
            margin-top: 1.25rem;
        }

        .pill-item {
            font-size: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            color: #cbd5e1;
        }

        /* Middle Connecting Lines (SVG) */
        .svg-connector {
            position: absolute;
            left: 280px;
            width: 160px;
            height: 360px;
            pointer-events: none;
            z-index: 1;
        }

        /* Payment Gateway Method Stack */
        .method-stack {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
            z-index: 2;
            margin-left: 9.5rem;
        }

        .payment-box {
            width: 220px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 0.9rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
        }

        .payment-box:hover {
            transform: translateX(8px);
            border-color: var(--accent-cyan);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 25px rgba(6, 182, 212, 0.3);
        }

        .lock-icon-dot {
            position: absolute;
            left: -32px;
            width: 22px;
            height: 22px;
            background: #111827;
            border: 1px solid var(--accent-cyan);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .lock-icon-dot svg {
            width: 10px;
            height: 10px;
            fill: var(--accent-cyan);
        }

        .box-title {
            font-weight: 700;
            font-size: 1rem;
        }

        /* Right Security Shield & Promo Badges */
        .shield-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
        }

        .shield-box {
            width: 180px;
            height: 210px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            border: 1px solid rgba(6, 182, 212, 0.25);
            border-radius: 28px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 40px rgba(6, 182, 212, 0.15);
        }

        .shield-box svg {
            width: 64px;
            height: 64px;
            stroke: var(--accent-cyan);
            filter: drop-shadow(0 0 10px var(--accent-cyan));
        }

        .floating-circles {
            display: flex;
            gap: 1rem;
        }

        .circle-badge {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(10px);
            font-weight: 700;
            color: var(--accent-pink);
            transition: transform 0.3s ease;
        }

        .circle-badge:hover {
            transform: translateY(-4px);
            border-color: var(--accent-pink);
            box-shadow: 0 0 15px rgba(236, 72, 153, 0.4);
        }

        @media (max-width: 900px) {
            .navbar { padding: 1rem 1.5rem; }
            .main-stage { flex-direction: column; height: auto; gap: 2.5rem; }
            .svg-connector { display: none; }
            .method-stack { margin-left: 0; }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <a href="/" class="brand">
            <div class="brand-logo-glow"></div>
            mPay Gateway
        </a>
        <div class="auth-buttons">
            <a href="{{ route('login') }}" class="btn-auth btn-login">Login</a>
            <a href="{{ route('register') }}" class="btn-auth btn-register">Register</a>
        </div>
    </header>

    <!-- Visual Landing Content -->
    <div class="viewport-main">
        <div class="main-stage">

            <!-- 1. Left Phone & Shopping Assets (Image 2 Concept) -->
            <div class="phone-mockup">
                <div class="cart-graphic">
                    <svg viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                </div>
                <h3 style="font-size: 1.05rem; font-weight:700;">Express Checkout</h3>
                <p style="font-size:0.75rem; color:#94a3b8; margin-top:0.3rem;">Instant Payment Processing</p>

                <div class="gift-pills">
                    <span class="pill-item">📦 Gifts</span>
                    <span class="pill-item">🛍️ Orders</span>
                </div>
            </div>

            <!-- SVG Circuit Connector (Image 1 Concept) -->
            <svg class="svg-connector">
                <path d="M 0 50 Q 80 50 150 20" stroke="#06b6d4" stroke-width="2" fill="none" stroke-dasharray="4" opacity="0.6"/>
                <path d="M 0 140 L 150 110" stroke="#06b6d4" stroke-width="2" fill="none" stroke-dasharray="4" opacity="0.6"/>
                <path d="M 0 230 L 150 200" stroke="#06b6d4" stroke-width="2" fill="none" stroke-dasharray="4" opacity="0.6"/>
                <path d="M 0 310 Q 80 310 150 290" stroke="#06b6d4" stroke-width="2" fill="none" stroke-dasharray="4" opacity="0.6"/>
            </svg>

            <!-- 2. Middle Gateway Cards (bKash, Nagad, Visa, Bank) -->
            <div class="method-stack">
                <div class="payment-box">
                    <div class="lock-icon-dot"><svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg></div>
                    <span class="box-title" style="color:#ec4899;">bKash</span>
                    <span style="font-size:0.7rem; color:#94a3b8;">Mobile Pay</span>
                </div>

                <div class="payment-box">
                    <div class="lock-icon-dot"><svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg></div>
                    <span class="box-title" style="color:#06b6d4;">Card / Visa</span>
                    <span style="font-size:0.7rem; color:#94a3b8;">Instant Debit</span>
                </div>

                <div class="payment-box">
                    <div class="lock-icon-dot"><svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg></div>
                    <span class="box-title" style="color:#f97316;">Nagad</span>
                    <span style="font-size:0.7rem; color:#94a3b8;">MFS Banking</span>
                </div>

                <div class="payment-box">
                    <div class="lock-icon-dot"><svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg></div>
                    <span class="box-title" style="color:#a855f7;">Bank Transfer</span>
                    <span style="font-size:0.7rem; color:#94a3b8;">Direct Wire</span>
                </div>
            </div>

            <!-- 3. Right Security Shield & Promo Badges -->
            <div class="shield-wrapper">
                <div class="shield-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="rgba(6, 182, 212, 0.1)"/>
                        <path d="M12 8v4M12 16h.01" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span style="font-size: 0.8rem; font-weight:700; margin-top:0.8rem;">256-Bit SSL</span>
                    <span style="font-size:0.65rem; color:#94a3b8;">Encrypted</span>
                </div>

                <div class="floating-circles">
                    <div class="circle-badge">%</div>
                    <div class="circle-badge">★</div>
                    <div class="circle-badge">🔒</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>