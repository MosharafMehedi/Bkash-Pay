<x-guest-layout>
    <!-- Dedicated Styles for Reset Password Page -->
    <style>
        /* Header Glow Badge */
        .rp-badge {
            width: 2.5rem;
            height: 2.5rem;
            margin: 0 auto 0.5rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.25);
            color: var(--accent-cyan, #06b6d4);
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.15);
        }

        /* Input Wrapper with Icons */
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

        /* Submit Button */
        .btn-reset-glow {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--accent-cyan, #06b6d4), var(--accent-purple, #8b5cf6));
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.65rem 1.25rem;
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

        /* Back Link Footer */
        .back-login-wrapper {
            text-align: center;
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .back-login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
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
    <div class="auth-header mb-3 text-center">
        <div class="rp-badge">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h2 class="auth-title">Set New Password</h2>
        <p class="auth-subtitle">Please enter your email and new password to reset.</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email Address')" class="custom-label" />
            <div class="input-icon-wrapper">
                <x-text-input id="email" class="custom-input custom-input-with-icon" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="name@example.com" />
                <svg class="w-4 h-4 input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-pink-400" />
        </div>

        <!-- New Password -->
        <div class="form-group mt-2.5">
            <x-input-label for="password" :value="__('New Password')" class="custom-label" />
            <div class="input-icon-wrapper">
                <x-text-input id="password" class="custom-input custom-input-with-icon" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                <svg class="w-4 h-4 input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-pink-400" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group mt-2.5">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="custom-label" />
            <div class="input-icon-wrapper">
                <x-text-input id="password_confirmation" class="custom-input custom-input-with-icon" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                <svg class="w-4 h-4 input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-pink-400" />
        </div>

        <!-- Submit Button -->
        <div class="mt-4">
            <button type="submit" class="btn-reset-glow">
                <span>{{ __('Reset Password') }}</span>
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