<x-guest-layout>
    <!-- Internal Dedicated Styles for Forgot Password Page -->
    <style>
        /* Header Glow Badge */
        .fp-badge {
            width: 2.75rem;
            height: 2.75rem;
            margin: 0 auto 0.75rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.25);
            color: var(--accent-cyan, #06b6d4);
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.15);
        }

        /* Input Container with Icon */
        .input-icon-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.85rem;
            color: #64748b;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .custom-input-with-icon {
            padding-left: 2.5rem !important;
        }

        .custom-input-with-icon:focus + .input-icon,
        .input-icon-wrapper:focus-within .input-icon {
            color: var(--accent-cyan, #06b6d4);
        }

        /* Action Button */
        .btn-reset-glow {
            width: 100%;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--accent-cyan, #06b6d4), var(--accent-purple, #8b5cf6));
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.7rem 1.25rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.3);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-reset-glow:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 25px rgba(139, 92, 246, 0.5);
            opacity: 0.95;
        }

        .btn-reset-glow:active {
            transform: translateY(0);
        }

        /* Back to Login Footer */
        .back-login-wrapper {
            text-align: center;
            margin-top: 1.25rem;
            padding-top: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .back-login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.825rem;
            font-weight: 600;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-login-btn:hover {
            color: var(--accent-cyan, #06b6d4);
        }

        .back-login-btn svg {
            transition: transform 0.2s ease;
        }

        .back-login-btn:hover svg {
            transform: translateX(-3px);
        }
    </style>

    <!-- Header Section -->
    <div class="auth-header mb-4 text-center">
        <div class="fp-badge">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        <h2 class="auth-title">Forgot Password?</h2>
        <p class="auth-subtitle">No worries, enter your email and we'll send reset instructions.</p>
    </div>

    <!-- Session Status Alert -->
    <x-auth-session-status class="mb-3 p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-400 font-medium text-center" :status="session('status')" />

    <!-- Form -->
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Input -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email Address')" class="custom-label" />
            <div class="input-icon-wrapper">
                <x-text-input id="email" class="custom-input custom-input-with-icon" type="email" name="email" :value="old('email')" required autofocus placeholder="name@example.com" />
                <svg class="w-4 h-4 input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-pink-400" />
        </div>

        <!-- Submit Button -->
        <div class="mt-5">
            <button type="submit" class="btn-reset-glow">
                <span>{{ __('Send Reset Link') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </div>

        <!-- Back to Login Link -->
        <div class="back-login-wrapper">
            <a class="back-login-btn" href="{{ route('login') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Back to log in') }}</span>
            </a>
        </div>
    </form>
</x-guest-layout>