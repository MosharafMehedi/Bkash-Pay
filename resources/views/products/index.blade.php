<x-app-layout>
    @section('title', 'Products')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .prod-page {
            font-family: 'Inter', sans-serif;
            --cyan: #29e7ff;
            --violet: #a78bfa;
            --pink: #ff5fb0;
            --text-hi: #f1f0fb;
            --text-mu: #9a94b8;
            --glass: rgba(255,255,255,0.045);
            --glass-border: rgba(255,255,255,0.09);
        }

        /* ── Hero banner ── */
        .prod-hero {
            position: relative;
            border-radius: 1.5rem;
            padding: 2rem 2rem 2.25rem;
            margin-bottom: 1.75rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.06));
            border: 1px solid var(--glass-border);
        }
        .prod-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, rgba(167,139,250,0.22), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(41,231,255,0.20), transparent 45%);
            pointer-events: none;
        }
        .prod-hero > * { position: relative; z-index: 1; }

        .prod-eyebrow {
            font-family: 'Sora', sans-serif;
            font-size: 0.72rem;
            letter-spacing: 0.16em;
            color: var(--cyan);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }
        .prod-title {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.85rem;
            color: var(--text-hi);
            margin-bottom: 0.4rem;
        }
        .prod-sub {
            color: var(--text-mu);
            font-size: 0.9rem;
            max-width: 480px;
        }

        /* ── Toolbar ── */
        .prod-toolbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.75rem;
        }
        .prod-search {
            flex: 1;
            min-width: 220px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.7rem 1rem;
            border-radius: 0.8rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(12px);
            transition: border-color 0.2s;
        }
        .prod-search:focus-within { border-color: rgba(41,231,255,0.5); }
        .prod-search input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text-hi);
            font-size: 0.9rem;
        }
        .prod-search input::placeholder { color: var(--text-mu); }

        .prod-filter {
            padding: 0.7rem 1rem;
            border-radius: 0.8rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            color: var(--text-hi);
            font-size: 0.85rem;
            cursor: pointer;
            outline: none;
        }
        .prod-filter option { background: #111827; }

        /* ── Grid ── */
        .prod-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        @media (max-width: 1024px) { .prod-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .prod-grid { grid-template-columns: 1fr; } }

        /* ── Card ── */
        .prod-card {
            position: relative;
            display: flex;
            flex-direction: column;
            border-radius: 1.25rem;
            padding: 1.5rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            transition: transform 0.35s cubic-bezier(.2,.8,.2,1), border-color 0.35s, box-shadow 0.35s;
            overflow: hidden;
        }
        .prod-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(41,231,255,0.5), rgba(167,139,250,0.4), rgba(255,95,176,0.35));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
                    mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.35s;
            pointer-events: none;
        }
        .prod-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 30px 60px -25px rgba(41,231,255,0.25);
        }
        .prod-card:hover::before { opacity: 1; }

        .prod-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .prod-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(41,231,255,0.18), rgba(167,139,250,0.18));
            border: 1px solid rgba(41,231,255,0.25);
            color: var(--cyan);
        }

        .prod-badge {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            background: rgba(41,231,255,0.12);
            color: var(--cyan);
            border: 1px solid rgba(41,231,255,0.25);
        }
        .prod-badge.stock-ok {
            background: rgba(52,211,153,0.12);
            color: #34d399;
            border-color: rgba(52,211,153,0.25);
        }
        .prod-badge.stock-low {
            background: rgba(255,95,176,0.12);
            color: var(--pink);
            border-color: rgba(255,95,176,0.25);
        }

        .prod-name {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--text-hi);
            margin-bottom: 0.35rem;
        }
        .prod-desc {
            font-size: 0.85rem;
            color: var(--text-mu);
            line-height: 1.55;
            flex: 1;
            margin-bottom: 1.1rem;
        }

        .prod-rating {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-bottom: 1rem;
            font-size: 0.78rem;
            color: var(--text-mu);
        }
        .prod-rating .stars { color: #fbbf24; letter-spacing: 0.05em; }

        .prod-price-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid var(--glass-border);
        }
        .prod-price {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.35rem;
            color: var(--text-hi);
            line-height: 1;
        }
        .prod-price-sub {
            font-size: 0.72rem;
            color: var(--text-mu);
            margin-top: 0.3rem;
            display: block;
        }

        .prod-buy {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.05rem;
            border-radius: 0.7rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            box-shadow: 0 8px 22px -8px rgba(41,231,255,0.55);
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
            white-space: nowrap;
        }
        .prod-buy:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 12px 28px -8px rgba(41,231,255,0.7);
        }
        .prod-buy:active { transform: translateY(0); }

        /* ── Empty state ── */
        .prod-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 1rem;
            border-radius: 1.25rem;
            background: var(--glass);
            border: 1px dashed var(--glass-border);
            color: var(--text-mu);
        }
        .prod-empty svg { margin: 0 auto 1rem; opacity: 0.5; }

        /* ── Entrance animation ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .prod-card { animation: fadeUp 0.5s ease both; }
        .prod-card:nth-child(1) { animation-delay: 0.02s; }
        .prod-card:nth-child(2) { animation-delay: 0.06s; }
        .prod-card:nth-child(3) { animation-delay: 0.10s; }
        .prod-card:nth-child(4) { animation-delay: 0.14s; }
        .prod-card:nth-child(5) { animation-delay: 0.18s; }
        .prod-card:nth-child(6) { animation-delay: 0.22s; }
    </style>

    <div class="prod-page max-w-6xl mx-auto">

        {{-- ── Hero banner ── --}}
        <div class="prod-hero">
            <div class="prod-eyebrow">Marketplace</div>
            <h1 class="prod-title">Pick something to buy</h1>
            <p class="prod-sub">Secure sandbox checkout — explore our catalog. No real money moves.</p>
        </div>

        {{-- ── Toolbar (search + filter) ── --}}
        <div class="prod-toolbar">
            <div class="prod-search">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="prodSearch" placeholder="Search products...">
            </div>
            <select class="prod-filter" id="prodSort">
                <option value="default">Sort: Featured</option>
                <option value="price-asc">Price: Low → High</option>
                <option value="price-desc">Price: High → Low</option>
                <option value="name-asc">Name: A → Z</option>
            </select>
        </div>

        {{-- ── Product grid ── --}}
        <div class="prod-grid" id="prodGrid">
            @forelse ($products as $product)
                <div class="prod-card"
                     data-name="{{ strtolower($product->name) }}"
                     data-price="{{ $product->price_bdt }}">

                    {{-- Top: icon + badge --}}
                    <div class="prod-card-top">
                        <div class="prod-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        @php
                            $stock = $product->stock ?? 10;
                            $badgeClass = $stock > 3 ? 'stock-ok' : 'stock-low';
                            $badgeText = $stock > 3 ? 'In stock' : 'Only ' . $stock . ' left';
                        @endphp
                        <span class="prod-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                    </div>

                    {{-- Body --}}
                    <div class="prod-name">{{ $product->name }}</div>
                    <p class="prod-desc">{{ $product->description }}</p>

                    {{-- Rating --}}
                    <div class="prod-rating">
                        <span class="stars">★★★★★</span>
                        <span>4.9 · 128 reviews</span>
                    </div>

                    {{-- Price + Buy --}}
                    <div class="prod-price-row">
                        <div>
                            <span class="prod-price">৳{{ number_format($product->price_bdt, 0) }}</span>
                            <span class="prod-price-sub">${{ number_format($product->price_usd, 2) }} via PayPal</span>
                        </div>
                        <a href="{{ route('checkout.show', $product) }}" class="prod-buy">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Buy
                        </a>
                    </div>
                </div>
            @empty
                <div class="prod-empty">
                    <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-base font-semibold text-slate-300 mb-1">No products available</p>
                    <p class="text-sm">Check back soon — new items are on the way.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── Search + Sort JS ── --}}
    <script>
        (function () {
            const grid = document.getElementById('prodGrid');
            const search = document.getElementById('prodSearch');
            const sort = document.getElementById('prodSort');

            function apply() {
                const q = search.value.trim().toLowerCase();
                const cards = Array.from(grid.querySelectorAll('.prod-card'));

                // Filter
                cards.forEach(c => {
                    const match = c.dataset.name.includes(q);
                    c.style.display = match ? '' : 'none';
                });

                // Sort
                const visible = cards.filter(c => c.style.display !== 'none');
                visible.sort((a, b) => {
                    const pa = parseFloat(a.dataset.price);
                    const pb = parseFloat(b.dataset.price);
                    switch (sort.value) {
                        case 'price-asc': return pa - pb;
                        case 'price-desc': return pb - pa;
                        case 'name-asc': return a.dataset.name.localeCompare(b.dataset.name);
                        default: return 0;
                    }
                });
                visible.forEach(c => grid.appendChild(c));
            }

            search.addEventListener('input', apply);
            sort.addEventListener('change', apply);
        })();
    </script>
</x-app-layout>