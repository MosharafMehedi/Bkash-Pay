<x-app-layout>
    @section('title', 'Dashboard')

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

    <div class="max-w-6xl mx-auto">

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