<x-app-layout>
    @section('title', 'Verify Your Cash Order')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .vf-page {
            font-family: 'Inter', sans-serif;
            --cyan: #29e7ff;
            --violet: #a78bfa;
            --green: #34d399;
            --text-hi: #f1f0fb;
            --text-mu: #9a94b8;
            --glass: rgba(255,255,255,0.045);
            --glass-border: rgba(255,255,255,0.09);
        }

        .vf-wrap { max-width: 480px; margin: 2rem auto; }

        .vf-card {
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            padding: 2rem;
            text-align: center;
        }

        .vf-icon {
            width: 64px; height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.15), rgba(167,139,250,0.15));
            border: 2px solid rgba(41,231,255,0.35);
            color: var(--cyan);
        }

        .vf-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-hi);
            margin-bottom: 0.5rem;
        }
        .vf-sub {
            font-size: 0.86rem;
            color: var(--text-mu);
            line-height: 1.55;
            margin-bottom: 1.5rem;
        }
        .vf-sub strong { color: var(--text-hi); }

        .vf-otp {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }
        .vf-otp input {
            width: 46px;
            height: 56px;
            text-align: center;
            font-family: 'Sora', monospace;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-hi);
            background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 0.7rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .vf-otp input:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(41,231,255,0.12);
        }
        .vf-otp input.filled {
            border-color: var(--cyan);
            background: rgba(41,231,255,0.06);
        }

        .vf-submit {
            width: 100%;
            padding: 0.9rem 1.25rem;
            border-radius: 0.75rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            border: none;
            cursor: pointer;
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
            transition: transform 0.15s, filter 0.15s;
            margin-bottom: 0.85rem;
        }
        .vf-submit:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .vf-help {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: var(--text-mu);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--glass-border);
        }
        .vf-resend {
            background: none;
            border: none;
            color: var(--cyan);
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
        }
        .vf-resend:hover { opacity: 0.85; }

        .vf-alert {
            padding: 0.7rem 0.9rem;
            border-radius: 0.65rem;
            font-size: 0.82rem;
            margin-bottom: 1rem;
            text-align: left;
        }
        .vf-alert.error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
        }
        .vf-alert.success {
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.3);
            color: #6ee7b7;
        }
        .vf-alert.info {
            background: rgba(41,231,255,0.08);
            border: 1px solid rgba(41,231,255,0.25);
            color: var(--cyan);
        }
    </style>

    <div class="vf-page vf-wrap">
        <div class="vf-card">

            <div class="vf-icon">
                <svg width="30" height="30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="vf-title">Check your email</h1>
            <p class="vf-sub">
                We sent a 6-digit code to <strong>{{ $order->customer_email }}</strong>.
                Enter it below to confirm your order.
            </p>

            @if (session('success'))
                <div class="vf-alert success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="vf-alert error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('cash.submitOtp', $order) }}" id="otpForm">
                @csrf

                <div class="vf-otp" id="otpInputs">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-idx="0" autofocus>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-idx="1">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-idx="2">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-idx="3">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-idx="4">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" data-idx="5">
                </div>

                <input type="hidden" name="otp" id="otpHidden">

                <button type="submit" class="vf-submit">
                    Verify &amp; Complete Order
                </button>
            </form>

            <div class="vf-help">
                <span>Order #{{ $order->order_number }}</span>
                <form method="POST" action="{{ route('cash.resendOtp', $order) }}">
                    @csrf
                    <button type="submit" class="vf-resend">Resend code</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const inputs  = document.querySelectorAll('#otpInputs input');
        const hidden  = document.getElementById('otpHidden');
        const form    = document.getElementById('otpForm');

        // Auto-advance on input
        inputs.forEach((input, idx) => {
            input.addEventListener('input', (e) => {
                const v = e.target.value.replace(/\D/g, '');
                e.target.value = v.slice(0, 1);
                e.target.classList.toggle('filled', !! e.target.value);

                if (v && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                }
                sync();
            });

            // Backspace → go back
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && ! e.target.value && idx > 0) {
                    inputs[idx - 1].focus();
                }
            });

            // Paste full code
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                const chars = paste.slice(0, 6).split('');
                chars.forEach((c, i) => {
                    if (inputs[i]) {
                        inputs[i].value = c;
                        inputs[i].classList.add('filled');
                    }
                });
                const next = Math.min(chars.length, inputs.length - 1);
                inputs[next].focus();
                sync();
            });
        });

        function sync() {
            const code = Array.from(inputs).map(i => i.value).join('');
            hidden.value = code;
        }

        // Auto submit when all 6 filled
        inputs.forEach((input) => {
            input.addEventListener('input', () => {
                const code = Array.from(inputs).map(i => i.value).join('');
                if (code.length === 6) {
                    setTimeout(() => form.submit(), 250);
                }
            });
        });
    </script>
</x-app-layout>