<x-welcome-layout>

    {{-- ═══ HERO SLIDER ═══ --}}
    @if ($settings['show_slider'] && $sliders->count() > 0)
        @include('welcome.partials.hero-slider')
    @endif

    {{-- ═══ TRUST BADGES ═══ --}}
    @if ($settings['show_trust_badges'])
        <section style="max-width: 1280px; margin: 0 auto; padding: 2rem 1.25rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                @foreach (range(1, 4) as $i)
                    <div style="display: flex; align-items: center; gap: 0.85rem; padding: 1rem 1.15rem; border-radius: 0.85rem; background: var(--glass); border: 1px solid var(--glass-border); backdrop-filter: blur(14px);">
                        <span style="font-size: 1.75rem;">{{ $settings["trust_badge_{$i}_icon"] ?? '✨' }}</span>
                        <div>
                            <div style="font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.85rem; color: #f1f0fb;">
                                {{ $settings["trust_badge_{$i}_title"] ?? '' }}
                            </div>
                            <div style="font-size: 0.7rem; color: #94a3b8;">
                                {{ $settings["trust_badge_{$i}_sub"] ?? '' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══ CATEGORIES ═══ --}}
    @if ($settings['show_categories'] && $categories->count() > 0)
        @include('welcome.partials.category-grid')
    @endif

    {{-- ═══ FEATURED PRODUCTS ═══ --}}
    @if ($settings['show_featured_products'] && $featuredProducts->count() > 0)
        @include('welcome.partials.featured-products')
    @endif

    {{-- ═══ DEALS OF THE DAY ═══ --}}
    @if ($settings['show_deals'] && $deals->count() > 0)
        @include('welcome.partials.deals')
    @endif

    {{-- ═══ NEW ARRIVALS ═══ --}}
    @if ($settings['show_new_arrivals'] && $newArrivals->count() > 0)
        @include('welcome.partials.new-arrivals')
    @endif

    {{-- ═══ BEST SELLERS ═══ --}}
    @if ($settings['show_best_sellers'] && $bestSellers->count() > 0)
        @include('welcome.partials.best-sellers')
    @endif

    {{-- ═══ BRANDS ═══ --}}
    @if ($settings['show_brands'] && $brands->count() > 0)
        @include('welcome.partials.brands')
    @endif

    {{-- ═══ TESTIMONIALS ═══ --}}
    @if ($settings['show_testimonials'] && $testimonials->count() > 0)
        @include('welcome.partials.testimonials')
    @endif

    {{-- ═══ NEWSLETTER ═══ --}}
    @if ($settings['show_newsletter'])
        @include('welcome.partials.newsletter')
    @endif

</x-welcome-layout>