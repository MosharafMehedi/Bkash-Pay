@if ($deals->count() > 0)
    @php
        // Find the earliest ending deal
        $firstEndingDeal = $deals
            ->filter(fn ($d) => $d->deal_ends_at)
            ->sortBy('deal_ends_at')
            ->first();

        $endTime = $firstEndingDeal?->deal_ends_at;
    @endphp

    <section class="wh-deals-section"
             @if ($endTime)
                 x-data="dealCountdown('{{ $endTime->toIso8601String() }}')"
                 x-init="start()"
             @endif
             style="position: relative; max-width: 1280px; margin: 3rem auto; padding: 0 1.25rem;">

        {{-- Deals Wrapper --}}
        <div style="position: relative; border-radius: 1.5rem; padding: 2rem 1.75rem; overflow: hidden; background: linear-gradient(135deg, rgba(255,95,176,0.08), rgba(41,231,255,0.06)); border: 1px solid rgba(255,95,176,0.25);">

            {{-- Glow bg --}}
            <div style="position: absolute; inset: 0; background: radial-gradient(circle at 20% 30%, rgba(255,95,176,0.15), transparent 50%), radial-gradient(circle at 80% 70%, rgba(41,231,255,0.12), transparent 50%); pointer-events: none;"></div>

            {{-- Header --}}
            <div class="wh-section-head" style="position: relative; z-index: 1; margin-bottom: 1.75rem; align-items: flex-end;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.35rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; background: linear-gradient(135deg, #ff5fb0, #f43f5e); color: #fff; box-shadow: 0 6px 20px -8px rgba(244,63,94,0.7);">
                            ⚡ Limited Time
                        </span>
                    </div>
                    <h2 class="wh-section-title" style="font-size: 1.65rem; {{ $endTime ? 'margin-bottom: 0;' : '' }}">
                        {{ $settings['heading_deals'] ?? 'Deals of the Day' }}
                    </h2>

                    @if ($endTime)
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; font-size: 0.82rem; color: #9a94b8;">
                            <span>Ends in</span>

                            {{-- Countdown --}}
                            <div style="display: flex; gap: 0.35rem; align-items: center;">
                                <div class="wh-countdown-unit">
                                    <span class="wh-countdown-num" x-text="pad(hours)">00</span>
                                    <span class="wh-countdown-lbl">H</span>
                                </div>
                                <span style="color: #ff5fb0; font-weight: 700;">:</span>
                                <div class="wh-countdown-unit">
                                    <span class="wh-countdown-num" x-text="pad(minutes)">00</span>
                                    <span class="wh-countdown-lbl">M</span>
                                </div>
                                <span style="color: #ff5fb0; font-weight: 700;">:</span>
                                <div class="wh-countdown-unit">
                                    <span class="wh-countdown-num" x-text="pad(seconds)">00</span>
                                    <span class="wh-countdown-lbl">S</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <a href="{{ route('products.index') }}" class="wh-section-link" style="color: #ff5fb0;">
                    View All Deals
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            {{-- Deals grid --}}
            <div class="wh-products-grid" style="position: relative; z-index: 1;">
                @foreach ($deals as $product)
                    @include('welcome.partials._product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        .wh-countdown-unit {
            display: inline-flex;
            align-items: baseline;
            gap: 0.15rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.4rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,95,176,0.25);
        }
        .wh-countdown-num {
            font-family: 'Sora', monospace;
            font-weight: 700;
            font-size: 0.85rem;
            color: #f1f0fb;
            letter-spacing: 0.02em;
        }
        .wh-countdown-lbl {
            font-size: 0.6rem;
            color: #9a94b8;
            text-transform: uppercase;
            font-weight: 600;
        }

        @media (max-width: 560px) {
            .wh-countdown-unit { padding: 0.2rem 0.4rem; }
            .wh-countdown-num { font-size: 0.78rem; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function dealCountdown(endTime) {
            return {
                hours: 0,
                minutes: 0,
                seconds: 0,
                interval: null,
                end: null,

                start() {
                    this.end = new Date(endTime).getTime();

                    this.tick();
                    this.interval = setInterval(() => this.tick(), 1000);
                },

                tick() {
                    const now = new Date().getTime();
                    const distance = this.end - now;

                    if (distance <= 0) {
                        this.hours = this.minutes = this.seconds = 0;
                        if (this.interval) clearInterval(this.interval);
                        return;
                    }

                    this.hours = Math.floor(distance / (1000 * 60 * 60));
                    this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                },

                pad(n) {
                    return String(n).padStart(2, '0');
                }
            };
        }
    </script>
    @endpush
@endif