<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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

        <!-- Global Theme Styles -->
        <style>
            :root {
                --bg-start: #0b0f19;
                --bg-end: #111827;
                --accent-cyan: #06b6d4;
                --accent-purple: #8b5cf6;
                --glass-border: rgba(255, 255, 255, 0.08);
            }

            body {
                background-color: var(--bg-start);
                background-image: 
                    radial-gradient(circle at 10% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 40%);
                color: #f8fafc;
                font-family: 'Plus Jakarta Sans', sans-serif;
                min-height: 100vh;
            }

            /* Custom Header Glassmorphism */
            .app-header {
                background: rgba(17, 24, 39, 0.7);
                backdrop-filter: blur(15px);
                border-bottom: 1px solid var(--glass-border);
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #0b0f19;
            }
            ::-webkit-scrollbar-thumb {
                background: #1e293b;
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: var(--accent-cyan);
            }
        </style>
    </head>
    <body class="font-sans antialiased selection:bg-cyan-500 selection:text-white">
        <div class="min-h-screen flex flex-col bg-[#0b0f19]">
            
            <!-- Navigation Bar -->
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="app-header sticky top-0 z-10 shadow-sm">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
            
        </div>
    </body>
</html>