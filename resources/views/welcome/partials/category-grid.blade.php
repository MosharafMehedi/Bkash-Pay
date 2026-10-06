@if ($categories->count() > 0)
    <section class="wh-section">
        <div class="wh-section-head">
            <h2 class="wh-section-title">{{ $settings['heading_categories'] ?? 'Shop by Category' }}</h2>
            <a href="{{ route('products.index') }}" class="wh-section-link">
                View All
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem;">
            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                   class="wh-cat-card">
                    <div class="wh-cat-icon">
                        @if ($category->icon)
                            <span style="font-size: 2rem;">{{ $category->icon }}</span>
                        @elseif ($category->hasImage())
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}">
                        @else
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #64748b;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="wh-cat-name">{{ $category->name }}</div>
                    <div class="wh-cat-count">{{ $category->products_count }} products</div>
                </a>
            @endforeach
        </div>
    </section>

    @push('styles')
    <style>
        .wh-cat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1.25rem 1rem;
            border-radius: 0.95rem;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.09);
            backdrop-filter: blur(14px);
            text-decoration: none;
            color: inherit;
            transition: all 0.25s;
        }
        .wh-cat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(41,231,255,0.4);
            background: rgba(41,231,255,0.05);
            box-shadow: 0 20px 40px -20px rgba(41,231,255,0.3);
        }
        .wh-cat-icon {
            width: 64px;
            height: 64px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(41,231,255,0.15), rgba(167,139,250,0.15));
            border: 1px solid rgba(41,231,255,0.28);
            margin-bottom: 0.75rem;
            overflow: hidden;
            transition: transform 0.25s;
        }
        .wh-cat-icon img { width: 100%; height: 100%; object-fit: cover; }
        .wh-cat-card:hover .wh-cat-icon { transform: scale(1.08) rotate(-3deg); }
        .wh-cat-name {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #f1f0fb;
            margin-bottom: 0.2rem;
        }
        .wh-cat-count {
            font-size: 0.68rem;
            color: #9a94b8;
        }
    </style>
    @endpush
@endif