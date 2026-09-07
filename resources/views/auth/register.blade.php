<x-guest-layout>
    <!-- Compact Header -->
    <div class="auth-header mb-3">
        <h2 class="auth-title">Create Account</h2>
        <p class="auth-subtitle">Get started with your payment gateway</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <x-input-label for="name" :value="__('Full Name')" class="custom-label" />
            <x-text-input id="name" class="custom-input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-pink-400" />
        </div>

        <!-- Email Address -->
        <div class="form-group mt-2.5">
            <x-input-label for="email" :value="__('Email Address')" class="custom-label" />
            <x-text-input id="email" class="custom-input" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-pink-400" />
        </div>

        <!-- Password -->
        <div class="form-group mt-2.5">
            <x-input-label for="password" :value="__('Password')" class="custom-label" />
            <x-text-input id="password" class="custom-input" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-pink-400" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group mt-2.5">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="custom-label" />
            <x-text-input id="password_confirmation" class="custom-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-pink-400" />
        </div>

        <!-- Links & Submit Button -->
        <div class="flex items-center justify-between mt-4">
            <a class="forgot-link" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <button type="submit" class="btn-submit-sm">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</x-guest-layout>