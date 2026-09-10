<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store') }}
        </h2>
    </x-slot>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .neon-zone {
            --bg-deep: #06050c;
            --glass: rgba(255, 255, 255, 0.045);
            --glass-border: rgba(255, 255, 255, 0.09);
            --cyan: #29e7ff;
            --violet: #a78bfa;
            --pink: #ff5fb0;
            --text-hi: #f1f0fb;
            --text-mu: #9a94b8;

            font-family: 'Inter', sans-serif;
            background: var(--bg-deep);
            border-radius: 1.5rem;
            padding: 2.25rem 2rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 2.5rem;
            box-shadow: 0 40px 80px -30px rgba(0, 0, 0, 0.6);
        }

        .neon-zone::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 12% 8%, rgba(167, 139, 250, 0.28), transparent 40%),
                radial-gradient(circle at 88% 15%, rgba(41, 231, 255, 0.22), transparent 42%),
                radial-gradient(circle at 50% 100%, rgba(255, 95, 176, 0.14), transparent 45%);
            pointer-events: none;
        }

        .neon-zone::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: radial-gradient(ellipse at center, black 0%, transparent 75%);
            pointer-events: none;
        }

        .neon-zone > * { position: relative; z-index: 1; }

        .neon-eyebrow {
            font-family: 'Sora', sans-serif;
            font-size: 0.72rem;
            letter-spacing: 0.14em;
            color: var(--cyan);
            margin-bottom: 0.4rem;
        }

        .neon-heading {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--text-hi);
            margin-bottom: 1.75rem;
        }

        .glass-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-radius: 1.1rem;
            padding: 1.4rem 1.5rem;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .stat-grid { grid-template-columns: 1fr; }
        }

        .stat-label {
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            color: var(--text-mu);
            text-transform: uppercase;
            margin-bottom: 0.6rem;
        }

        .stat-value {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.9rem;
            color: var(--text-hi);
        }

        .stat-value .unit {
            font-size: 1rem;
            color: var(--text-mu);
            font-weight: 400;
            margin-left: 0.25rem;
        }

        .stat-card.cyan { box-shadow: inset 0 0 0 1px rgba(41, 231, 255, 0.18); }
        .stat-card.violet { box-shadow: inset 0 0 0 1px rgba(167, 139, 250, 0.18); }
        .stat-card.pink { box-shadow: inset 0 0 0 1px rgba(255, 95, 176, 0.18); }

        .stat-card.cyan .stat-value { color: var(--cyan); }
        .stat-card.violet .stat-value { color: var(--violet); }
        .stat-card.pink .stat-value { color: var(--pink); }

        .chart-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 1.25rem;
        }

        @media (max-width: 900px) {
            .chart-grid { grid-template-columns: 1fr; }
        }

        .chart-card-title {
            font-family: 'Sora', sans-serif;
            font-size: 0.95rem;
            color: var(--text-hi);
            margin-bottom: 1rem;
        }

        .success-badge {
            display: inline-flex;
            align-items: baseline;
            gap: 0.4rem;
            margin-top: 0.6rem;
            font-size: 0.72rem;
            color: var(--text-mu);
        }

        .success-badge b {
            color: var(--cyan);
            font-size: 0.95rem;
            font-family: 'Sora', sans-serif;
        }
    </style>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Analytics zone --}}
            <div class="neon-zone">
                <div class="neon-eyebrow">OVERVIEW</div>
                <div class="neon-heading">Payment analytics</div>

                <div class="stat-grid">
                    <div class="glass-card stat-card cyan">
                        <div class="stat-label">Today's payment</div>
                        <div class="stat-value">৳{{ number_format($todayTotal, 0) }}<span class="unit">BDT</span></div>
                    </div>
                    <div class="glass-card stat-card violet">
                        <div class="stat-label">Last month's payment</div>
                        <div class="stat-value">৳{{ number_format($lastMonthTotal, 0) }}<span class="unit">BDT</span></div>
                    </div>
                    <div class="glass-card stat-card pink">
                        <div class="stat-label">Total payment</div>
                        <div class="stat-value">৳{{ number_format($grandTotal, 0) }}<span class="unit">BDT</span></div>
                        <div class="success-badge"><b>{{ $successRate }}%</b> success rate ({{ $totalAttempts }} attempts)</div>
                    </div>
                </div>

                <div class="chart-grid">
                    <div class="glass-card">
                        <div class="chart-card-title">Last 10 days</div>
                        <canvas id="dailyChart" height="160"></canvas>
                    </div>
                    <div class="glass-card">
                        <div class="chart-card-title">By gateway</div>
                        <canvas id="gatewayChart" height="160"></canvas>
                    </div>
                </div>
            </div>

            {{-- Product grid --}}
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Pick something to buy</h1>
                <p class="text-gray-500 text-sm mt-1">Sandbox checkout — no real money moves.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
                        <div class="text-xs uppercase tracking-wide text-gray-400 mb-2">{{ $product->subtitle }}</div>
                        <h3 class="font-semibold text-gray-800 text-lg mb-2">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 flex-1 mb-4">{{ $product->description }}</p>

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-lg font-bold text-gray-800">৳{{ number_format($product->price_bdt, 0) }}</span>
                                <span class="text-xs text-gray-400 block">${{ number_format($product->price_usd, 2) }} via PayPal</span>
                            </div>
                            <a href="{{ route('checkout.show', $product) }}"
                                class="bg-gray-900 hover:bg-gray-700 transition text-white text-sm font-semibold px-4 py-2 rounded-lg">
                                Buy
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        const dailyLabels = {!! json_encode($last10Days->pluck('label')) !!};
        const dailyAmounts = {!! json_encode($last10Days->pluck('amount')) !!};

        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    data: dailyAmounts,
                    backgroundColor: 'rgba(41, 231, 255, 0.55)',
                    hoverBackgroundColor: 'rgba(41, 231, 255, 0.85)',
                    borderRadius: 6,
                    maxBarThickness: 28,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#9a94b8', font: { family: 'Inter' } }, grid: { display: false } },
                    y: { ticks: { color: '#9a94b8', font: { family: 'Inter' } }, grid: { color: 'rgba(255,255,255,0.06)' }, beginAtZero: true },
                },
            },
        });

        const gatewayLabels = {!! json_encode(array_keys($gatewayTotals)) !!};
        const gatewayAmounts = {!! json_encode(array_values($gatewayTotals)) !!};

        new Chart(document.getElementById('gatewayChart'), {
            type: 'doughnut',
            data: {
                labels: gatewayLabels,
                datasets: [{
                    data: gatewayAmounts,
                    backgroundColor: ['#e2136e', '#1546a0', '#29e7ff'],
                    borderColor: '#06050c',
                    borderWidth: 3,
                }],
            },
            options: {
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#9a94b8', font: { family: 'Inter' }, boxWidth: 10, padding: 14 } },
                },
            },
        });
    </script>
</x-app-layout>