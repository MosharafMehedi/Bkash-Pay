<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'mPay Gateway') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-start: #0b0f19;
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

        .app-header,
        .app-footer,
        .app-sidebar {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
        .app-header { border-bottom: 1px solid var(--glass-border); }
        .app-footer { border-top: 1px solid var(--glass-border); }
        .app-sidebar { border-right: 1px solid var(--glass-border); }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1rem;
            border-radius: 0.6rem;
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background: rgba(6, 182, 212, 0.08);
            color: #e2e8f0;
        }
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.18), rgba(139, 92, 246, 0.12));
            color: #ffffff;
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0b0f19; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-cyan); }

        @media (max-width: 1023px) {
            .app-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                position: fixed;
                top: 0; bottom: 0; left: 0;
                z-index: 50;
                width: 260px;
            }
            .app-sidebar.open { transform: translateX(0); }
        }
    </style>
</head>
<body class="font-sans antialiased selection:bg-cyan-500 selection:text-white">

<div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"></div>

    {{-- Main Wrapper --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
        @include('layouts.header')

        {{-- Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        @include('layouts.footer')
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>