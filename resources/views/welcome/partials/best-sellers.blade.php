@if ($bestSellers->count() > 0)
    <section class="wh-section">
        <div class="wh-section-head">
            <h2 class="wh-section-title">{{ $settings['heading_best_sellers'] ?? 'Best Sellers' }}</h2>
            <a href="{{ route('products.index', ['sort' => 'best-sellers']) }}" class="wh-section-link">
                View All
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <div class="wh-products-grid">
            @foreach ($bestSellers as $product)
                @include('welcome.partials._product-card', ['product' => $product])
            @endforeach
        </div>
    </section>
@endif