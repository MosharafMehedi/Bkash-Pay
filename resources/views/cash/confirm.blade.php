<x-app-layout>
    @section('title', 'Confirm Cash Order')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .cf-page {
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

        .cf-wrap { max-width: 520px; margin: 2rem auto; }

        .cf-card {
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            padding: 2rem;
            text-align: center;
        }

        .cf-icon {
            width: 72px; height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            background: linear-gradient(135deg, rgba(251,191,36,0.15), rgba(245,158,11,0.1));
            border: 2px solid rgba(251,191,36,0.35);
            color: #fbbf24;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(251,191,36,0.4); }
            50%      { box-shadow: 0 0 0 14px rgba(251,191,36,0); }
        }

        .cf-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-hi);
            margin-bottom: 0.5rem;
        }
        .cf-sub {
            font-size: 0.88rem;
            color: var(--text-mu);
            line-height: 1.55;
            margin-bottom: 1.5rem;
        }

        .cf-details {
            border-radius: 0.85rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 1rem 1.1rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .cf-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            padding: 0.4rem 0;
            color: var(--text-mu);
        }
        .cf-row strong { color: var(--text-hi); font-weight: 600; }
        .cf-row.total {
            border-top: 1px dashed var(--glass-border);
            margin-top: 0.4rem;
            padding-top: 0.75rem;
            color: var(--text-hi);
            font-weight: 600;
            font-size: 0.9rem;
        }
        .cf-row.total .amount {
            font-family: 'Sora', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--cyan);
        }

        .cf-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.7rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            background: rgba(251,191,36,0.12);
            color: #fbbf24;
            border: 1px solid rgba(251,191,36,0.28);
            margin-bottom: 1rem;
        }
        .cf-badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse-dot 1.5s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.4; }
        }

        .cf-actions {
            display: flex;
            gap: 0.65rem;
            margin-top: 0.25rem;
        }
        .cf-btn {
            flex: 1;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            border: none;
            cursor: pointer;
            transition: transform 0.15s, filter 0.15s, background 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }
        .cf-btn-confirm {
            color: #06050c;
            background: linear-gradient(135deg, var(--green), var(--cyan));
            box-shadow: 0 12px 30px -10px rgba(52,211,153,0.65);
        }
        .cf-btn-confirm:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
        }
        .cf-btn-cancel {
            color: #cbd5e1;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .cf-btn-cancel:hover { background: rgba(239,68,68,0.12); color: #f87171; border-color: rgba(239,68,68,0.3); }
    </style>

    <div class="cf-page cf-wrap">
        <div class="cf-card">

            <span class="cf-badge">Pending</span>

            <div class="cf-icon">
                <svg width="34" height="34" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="cf-title">Confirm your Cash order</h1>
            <p class="cf-sub">
                Please review the order below. Click <strong>Confirm</strong> to receive a
                verification code by email — or <strong>Cancel</strong> to abort.
            </p>

            <div class="cf-details">
                <div class="cf-row">
                    <span>Order #</span>
                    <strong>{{ $order->order_number }}</strong>
                </div>
                <div class="cf-row">
                    <span>Product</span>
                    <strong>{{ $order->product->name }}</strong>
                </div>
                <div class="cf-row">
                    <span>Email</span>
                    <strong>{{ $order->customer_email }}</strong>
                </div>
                <div class="cf-row total">
                    <span>Total Due</span>
                    <span class="amount">৳{{ number_format($order->amount, 0) }}</span>
                </div>
            </div>

            <div class="cf-actions">
                <form method="POST" action="{{ route('cash.sendOtp', $order) }}" style="flex:1;">
                    @csrf
                    <button type="submit" class="cf-btn cf-btn-confirm" style="width:100%;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirm
                    </button>
                </form>

                <form method="POST" action="{{ route('cash.cancel', $order) }}" style="flex:1;">
                    @csrf
                    <button type="submit" class="cf-btn cf-btn-cancel" style="width:100%;">
                        Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>