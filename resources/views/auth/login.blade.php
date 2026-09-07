<x-guest-layout>
    <!-- Page Header Title -->
    <div class="auth-header">
        <h2 class="auth-title">Welcome Back</h2>
        <p class="auth-subtitle">Log in to manage your payments & account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email Address')" class="custom-label" />
            <x-text-input id="email" class="custom-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-pink-400" />
        </div>

        <!-- Password -->
        <div class="form-group mt-4">
            <x-input-label for="password" :value="__('Password')" class="custom-label" />

            <x-text-input id="password" class="custom-input"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-pink-400" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="form-actions-row">
            <label for="remember_me" class="remember-label">
                <input id="remember_me" type="checkbox" class="custom-checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="btn-submit">
                {{ __('Log in to Account') }}
            </button>
        </div>
    </form>
</x-guest-layout>