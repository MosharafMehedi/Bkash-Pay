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
            --sidebar-width: 260px;
            --header-height: 64px;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-start);
            background-image:
                radial-gradient(circle at 10% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 40%);
            background-attachment: fixed;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Fixed Header ── */
        .app-header {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: var(--header-height);
            z-index: 30;
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.3);
            transition: left 0.3s ease;
        }

        @media (min-width: 1024px) {
            .app-header { left: var(--sidebar-width); }
        }

        /* ── Fixed Sidebar ── */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            z-index: 40;
            display: flex;
            flex-direction: column;
            background: rgba(11, 15, 25, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            box-shadow: 4px 0 12px -4px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        @media (max-width: 1023px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.open { transform: translateX(0); }
        }

        /* ── Main Content ── */
        .app-main {
            padding-top: var(--header-height);
            min-height: 100vh;
            transition: padding-left 0.3s ease;
        }

        @media (min-width: 1024px) {
            .app-main { padding-left: var(--sidebar-width); }
        }

        /* ── Sidebar Links ── */
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
            text-decoration: none;
        }
        .sidebar-link:hover {
            background: rgba(6, 182, 212, 0.08);
            color: #e2e8f0;
        }
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.18), rgba(139, 92, 246, 0.12));
            color: #ffffff;
        }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0b0f19; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-cyan); }

        /* Sidebar menu scroll */
        .app-sidebar nav::-webkit-scrollbar { width: 6px; }
        .app-sidebar nav::-webkit-scrollbar-track { background: transparent; }
        .app-sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(41, 231, 255, 0.2);
            border-radius: 3px;
        }
        .app-sidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(41, 231, 255, 0.4);
        }

        /* ── Footer ── */
        .app-footer {
            background: rgba(11, 15, 25, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-top: 1px solid var(--glass-border);
        }

        /* ── Fix for anchor scroll (sidebar/header offset) ── */
        [id] {
            scroll-margin-top: calc(var(--header-height) + 1rem);
        }
    </style>
</head>
<body class="font-sans antialiased selection:bg-cyan-500 selection:text-white">

<div x-data="{ sidebarOpen: false }">

    {{-- Fixed Sidebar --}}
    @include('layouts.sidebar')

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition.opacity
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"></div>

    {{-- Fixed Header --}}
    @include('layouts.header')

    {{-- Main Content (offset by sidebar + header) --}}
    <main class="app-main">
        <div class="p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @include('layouts.footer')
    </main>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>