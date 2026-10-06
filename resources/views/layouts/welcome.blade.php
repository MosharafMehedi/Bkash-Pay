<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'mPay Gateway') }} — Modern Ecommerce</title>

    <meta name="description" content="{{ $metaDescription ?? 'Shop the latest products at mPay Gateway. Secure checkout, fast delivery, and unbeatable prices.' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600,700|plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-start: #0b0f19;
            --bg-end: #111827;
            --accent-cyan: #29e7ff;
            --accent-purple: #a78bfa;
            --accent-pink: #ff5fb0;
            --accent-green: #34d399;
            --glass-border: rgba(255,255,255,0.09);
            --glass: rgba(255,255,255,0.045);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }

        body {
            background-color: var(--bg-start);
            background-image:
                radial-gradient(circle at 10% 20%, rgba(139,92,246,0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6,182,212,0.08) 0%, transparent 40%);
            background-attachment: fixed;
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0b0f19; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-cyan); }

        /* ── Announcement Bar ── */
        .announce-bar {
            background: linear-gradient(90deg, #29e7ff, #a78bfa, #ff5fb0);
            color: #06050c;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            text-align: center;
            position: relative;
            z-index: 60;
        }
        .announce-bar.cyan    { background: linear-gradient(90deg, #29e7ff, #06b6d4); }
        .announce-bar.violet  { background: linear-gradient(90deg, #a78bfa, #8b5cf6); color: #fff; }
        .announce-bar.pink    { background: linear-gradient(90deg, #ff5fb0, #ec4899); color: #fff; }
        .announce-bar.green   { background: linear-gradient(90deg, #34d399, #10b981); }
        .announce-bar.amber   { background: linear-gradient(90deg, #fbbf24, #f59e0b); }

        .announce-bar-close {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: currentColor;
            cursor: pointer;
            opacity: 0.7;
            font-size: 1.2rem;
            line-height: 1;
            padding: 0.2rem;
        }
        .announce-bar-close:hover { opacity: 1; }

        /* ── Public Header ── */
        .wh-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(11,15,25,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            transition: all 0.3s;
        }

        /* ── Page wrapper ── */
        .wh-main {
            min-height: 60vh;
        }

        /* ── Generic sections ── */
        .wh-section {
            max-width: 1280px;
            margin: 0 auto;
            padding: 3rem 1.25rem;
        }
        @media (max-width: 640px) { .wh-section { padding: 2rem 1rem; } }

        .wh-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .wh-section-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f0fb;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .wh-section-title::before {
            content: '';
            width: 4px; height: 24px;
            border-radius: 2px;
            background: linear-gradient(180deg, var(--accent-cyan), var(--accent-purple));
        }
        .wh-section-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--accent-cyan);
            text-decoration: none;
            transition: transform 0.2s;
        }
        .wh-section-link:hover { transform: translateX(3px); }
    </style>

    @stack('styles')
</head>
<body class="antialiased">

    {{-- ═══ Announcement Bar ═══ --}}
    @include('welcome.partials.announcement-bar')

    {{-- ═══ Public Header ═══ --}}
    @include('welcome.partials.header-mega')

    {{-- ═══ Main Content ═══ --}}
    <main class="wh-main">
        {{ $slot }}
    </main>

    {{-- ═══ Footer ═══ --}}
    @include('welcome.partials.footer')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>