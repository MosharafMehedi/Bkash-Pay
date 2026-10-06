@if ($brands->count() > 0)
    <section class="wh-section">
        <div class="wh-section-head">
            <h2 class="wh-section-title">{{ $settings['heading_brands'] ?? 'Top Brands' }}</h2>
        </div>

        <div class="wh-brands-grid">
            @foreach ($brands as $brand)
                <a href="{{ route('products.index', ['brand' => $brand]) }}"
                   class="wh-brand-card">
                    <div class="wh-brand-avatar">
                        {{ strtoupper(substr($brand, 0, 2)) }}
                    </div>
                    <div class="wh-brand-name">{{ $brand }}</div>
                </a>
            @endforeach
        </div>
    </section>

    @push('styles')
    <style>
        .wh-brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 0.75rem;
        }

        .wh-brand-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1rem 0.75rem;
            border-radius: 0.85rem;
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(255,255,255,0.08);
            text-decoration: none;
            color: inherit;
            transition: all 0.25s;
        }
        .wh-brand-card:hover {
            transform: translateY(-3px);
            border-color: rgba(167,139,250,0.4);
            background: rgba(167,139,250,0.06);
            box-shadow: 0 16px 32px -16px rgba(167,139,250,0.4);
        }

        .wh-brand-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: #a78bfa;
            background: linear-gradient(135deg, rgba(167,139,250,0.15), rgba(41,231,255,0.15));
            border: 1px solid rgba(167,139,250,0.3);
            margin-bottom: 0.6rem;
            letter-spacing: 0.02em;
            transition: transform 0.25s;
        }
        .wh-brand-card:hover .wh-brand-avatar {
            transform: scale(1.08) rotate(5deg);
        }

        .wh-brand-name {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 0.78rem;
            color: #f1f0fb;
            line-height: 1.25;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 560px) {
            .wh-brands-grid { grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
            .wh-brand-card { padding: 0.75rem 0.5rem; }
            .wh-brand-avatar { width: 42px; height: 42px; font-size: 0.78rem; }
            .wh-brand-name { font-size: 0.72rem; }
        }
    </style>
    @endpush
@endif