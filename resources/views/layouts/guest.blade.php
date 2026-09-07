<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'mPay Gateway') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg-start: #0b0f19;
                --bg-end: #111827;
                --accent-cyan: #06b6d4;
                --accent-pink: #ec4899;
                --accent-purple: #8b5cf6;
                --glass-card: rgba(255, 255, 255, 0.04);
                --glass-border: rgba(255, 255, 255, 0.12);
            }

            body {
                background-color: var(--bg-start);
                background-image: 
                    radial-gradient(circle at 10% 20%, rgba(139, 92, 246, 0.18) 0%, transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.18) 0%, transparent 40%),
                    radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.08) 0%, transparent 50%);
                min-height: 100vh;
                color: #ffffff;
                font-family: 'Plus Jakarta Sans', sans-serif;
                margin: 0;
            }

            /* Container & Layout Styling */
            .auth-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 2rem 1rem;
                position: relative;
            }

            /* Logo Styling */
            .brand-logo-wrapper {
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.6rem;
                text-decoration: none;
                font-size: 1.4rem;
                font-weight: 800;
                color: #ffffff;
            }

            .brand-logo-glow {
                width: 14px;
                height: 14px;
                background: var(--accent-cyan);
                border-radius: 50%;
                box-shadow: 0 0 15px var(--accent-cyan);
            }

            /* Glassmorphism Card Box */
            .auth-card {
                width: 100%;
                max-width: 440px;
                background: rgba(17, 24, 39, 0.65);
                backdrop-filter: blur(25px);
                border: 1px solid var(--glass-border);
                border-radius: 28px;
                padding: 2.25rem 2rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(6, 182, 212, 0.1);
            }

            .auth-header {
                text-align: center;
                margin-bottom: 1.75rem;
            }

            .auth-title {
                font-size: 1.5rem;
                font-weight: 800;
                color: #ffffff;
                letter-spacing: -0.02em;
            }

            .auth-subtitle {
                font-size: 0.85rem;
                color: #94a3b8;
                margin-top: 0.3rem;
            }

            /* Form Elements */
            .custom-label {
                color: #cbd5e1 !important;
                font-size: 0.85rem !important;
                font-weight: 600 !important;
                margin-bottom: 0.4rem;
            }

            .custom-input {
                width: 100%;
                background: rgba(255, 255, 255, 0.05) !important;
                border: 1px solid var(--glass-border) !important;
                border-radius: 12px !important;
                padding: 0.75rem 1rem !important;
                color: #ffffff !important;
                font-size: 0.9rem !important;
                transition: all 0.3s ease !important;
                outline: none !important;
            }

            .custom-input:focus {
                border-color: var(--accent-cyan) !important;
                box-shadow: 0 0 15px rgba(6, 182, 212, 0.3) !important;
                background: rgba(255, 255, 255, 0.08) !important;
            }

            .custom-input::placeholder {
                color: #64748b;
            }

            /* Actions Row */
            .form-actions-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 1.25rem;
            }

            .remember-label {
                display: inline-flex;
                align-items: center;
                font-size: 0.825rem;
                color: #94a3b8;
                cursor: pointer;
            }

            .custom-checkbox {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid var(--glass-border);
                border-radius: 4px;
                color: var(--accent-cyan);
                margin-right: 0.5rem;
            }

            .forgot-link {
                font-size: 0.825rem;
                color: var(--accent-cyan);
                text-decoration: none;
                transition: color 0.2s;
            }

            .forgot-link:hover {
                color: #38bdf8;
                text-decoration: underline;
            }

            /* Submit Button */
            .btn-submit {
                width: 100%;
                background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
                color: #ffffff;
                font-weight: 700;
                font-size: 0.95rem;
                padding: 0.85rem;
                border-radius: 50px;
                border: none;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(139, 92, 246, 0.35);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .btn-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 25px rgba(6, 182, 212, 0.5);
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="auth-container">
            <!-- Brand Logo -->
            <a href="/" class="brand-logo-wrapper">
                <div class="brand-logo-glow"></div>
                mPay Gateway
            </a>

            <!-- Glassmorphism Card Layout -->
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>