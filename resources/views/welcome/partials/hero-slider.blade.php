@if ($sliders->count() > 0)
    <section class="wh-hero"
             x-data="heroSlider({{ $sliders->count() }})"
             x-init="init()"
             style="position: relative; max-width: 1280px; margin: 1.5rem auto 0; padding: 0 1.25rem;">

        <div class="wh-hero-wrap"
             style="position: relative; border-radius: 1.5rem; overflow: hidden; height: 420px; border: 1px solid rgba(255,255,255,0.09); background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.06));">
            @foreach ($sliders as $index => $slider)
                @php
                    $pos = $slider->text_position ?? 'left';
                    $badgeColors = [
                        'cyan'   => ['bg' => 'rgba(41,231,255,0.15)',  'color' => '#29e7ff', 'border' => 'rgba(41,231,255,0.4)'],
                        'violet' => ['bg' => 'rgba(167,139,250,0.15)', 'color' => '#a78bfa', 'border' => 'rgba(167,139,250,0.4)'],
                        'pink'   => ['bg' => 'rgba(255,95,176,0.15)',  'color' => '#ff5fb0', 'border' => 'rgba(255,95,176,0.4)'],
                        'green'  => ['bg' => 'rgba(52,211,153,0.15)',  'color' => '#34d399', 'border' => 'rgba(52,211,153,0.4)'],
                        'amber'  => ['bg' => 'rgba(251,191,36,0.15)',  'color' => '#fbbf24', 'border' => 'rgba(251,191,36,0.4)'],
                    ];
                    $badge = $badgeColors[$slider->badge_color] ?? $badgeColors['cyan'];
                @endphp

                <div class="wh-slide"
                     x-show="active === {{ $index }}"
                     x-transition:enter="slide-enter"
                     x-transition:enter-start="slide-enter-start"
                     x-transition:enter-end="slide-enter-end"
                     x-transition:leave="slide-leave"
                     x-transition:leave-start="slide-leave-start"
                     x-transition:leave-end="slide-leave-end"
                     style="position: absolute; inset: 0; display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 2rem;">

                    {{-- Background Image --}}
                    @if ($slider->hasBgImage())
                        <div style="position: absolute; inset: 0; z-index: 0;">
                            <img src="{{ $slider->bg_image_url }}"
                                 alt="{{ $slider->title }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="position: absolute; inset: 0; background: linear-gradient({{ $pos === 'right' ? '90deg, rgba(11,15,25,0.95) 0%, rgba(11,15,25,0.7) 50%, rgba(11,15,25,0.3) 100%' : ($pos === 'center' ? '135deg, rgba(11,15,25,0.85), rgba(11,15,25,0.5)' : '270deg, rgba(11,15,25,0.95) 0%, rgba(11,15,25,0.7) 50%, rgba(11,15,25,0.3) 100%') }};"></div>
                        </div>
                    @endif

                    {{-- Text content --}}
                    <div class="wh-hero-text" style="position: relative; z-index: 2; padding: 2.5rem; {{ $pos === 'right' ? 'grid-column: 2; text-align: right;' : ($pos === 'center' ? 'grid-column: 1 / -1; text-align: center; max-width: 700px; margin: 0 auto;' : '') }}">

                        @if ($slider->badge_text)
                            <span style="display: inline-block; padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; border: 1px solid {{ $badge['border'] }}; margin-bottom: 1rem;">
                                {{ $slider->badge_text }}
                            </span>
                        @endif

                        <h2 style="font-family: 'Sora', sans-serif; font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 700; color: #f1f0fb; line-height: 1.15; margin-bottom: 0.85rem; letter-spacing: -0.02em;">
                            {{ $slider->title }}
                        </h2>

                        @if ($slider->subtitle)
                            <p style="font-size: 1rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 1.5rem; max-width: 460px; {{ $pos === 'right' ? 'margin-left: auto;' : ($pos === 'center' ? 'margin-left: auto; margin-right: auto;' : '') }}">
                                {{ $slider->subtitle }}
                            </p>
                        @endif

                        @if ($slider->button_text)
                            <a href="{{ $slider->cta_url }}"
                               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.85rem 1.75rem; border-radius: 0.75rem; font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.9rem; color: #06050c; background: linear-gradient(135deg, #29e7ff, #a78bfa); text-decoration: none; box-shadow: 0 14px 34px -10px rgba(41,231,255,0.75); transition: transform 0.2s, filter 0.2s;"
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.filter='brightness(1.08)'"
                               onmouseout="this.style.transform='translateY(0)'; this.style.filter='brightness(1)'">
                                {{ $slider->button_text }}
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        @endif
                    </div>

                    {{-- Product image (right side) --}}
                    @if ($slider->hasImage())
                        <div class="wh-hero-image" style="position: relative; z-index: 2; {{ $pos === 'right' ? 'grid-column: 1;' : '' }} {{ $pos === 'center' ? 'display: none;' : '' }}">
                            <img src="{{ $slider->image_url }}"
                                 alt="{{ $slider->title }}"
                                 style="width: 100%; max-width: 400px; height: auto; object-fit: contain; margin: 0 auto; display: block; filter: drop-shadow(0 30px 40px rgba(0,0,0,0.5)); animation: float 4s ease-in-out infinite;">
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Prev / Next --}}
            @if ($sliders->count() > 1)
                <button @click="prev()"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); z-index: 10; width: 42px; height: 42px; border-radius: 50%; background: rgba(11,15,25,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15); color: #f1f0fb; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(41,231,255,0.2)'; this.style.borderColor='rgba(41,231,255,0.5)'"
                        onmouseout="this.style.background='rgba(11,15,25,0.7)'; this.style.borderColor='rgba(255,255,255,0.15)'">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <button @click="next()"
                        style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); z-index: 10; width: 42px; height: 42px; border-radius: 50%; background: rgba(11,15,25,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15); color: #f1f0fb; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(41,231,255,0.2)'; this.style.borderColor='rgba(41,231,255,0.5)'"
                        onmouseout="this.style.background='rgba(11,15,25,0.7)'; this.style.borderColor='rgba(255,255,255,0.15)'">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endif

            {{-- Dots --}}
            @if ($sliders->count() > 1)
                <div style="position: absolute; bottom: 1.25rem; left: 50%; transform: translateX(-50%); z-index: 10; display: flex; gap: 0.4rem;">
                    @foreach ($sliders as $index => $s)
                        <button @click="goTo({{ $index }})"
                                :style="active === {{ $index }} ? 'width: 24px; background: linear-gradient(90deg, #29e7ff, #a78bfa);' : 'width: 8px; background: rgba(255,255,255,0.4);'"
                                style="height: 8px; border-radius: 999px; border: none; cursor: pointer; transition: all 0.3s; padding: 0;">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        function heroSlider(total) {
            return {
                active: 0,
                total: total,
                interval: null,

                init() {
                    if (this.total > 1) {
                        this.startAuto();
                    }
                },

                startAuto() {
                    this.interval = setInterval(() => this.next(), 5000);
                },

                stopAuto() {
                    if (this.interval) clearInterval(this.interval);
                },

                next() {
                    this.active = (this.active + 1) % this.total;
                },

                prev() {
                    this.active = (this.active - 1 + this.total) % this.total;
                },

                goTo(i) {
                    this.active = i;
                    this.stopAuto();
                    this.startAuto();
                }
            };
        }
    </script>
    @endpush

    @push('styles')
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .slide-enter { transition: all 0.7s ease; }
        .slide-enter-start { opacity: 0; transform: translateX(30px); }
        .slide-enter-end { opacity: 1; transform: translateX(0); }
        .slide-leave { transition: all 0.7s ease; }
        .slide-leave-start { opacity: 1; transform: translateX(0); }
        .slide-leave-end { opacity: 0; transform: translateX(-30px); }

        @media (max-width: 900px) {
            .wh-hero-wrap { height: auto !important; min-height: 400px; }
            .wh-slide { grid-template-columns: 1fr !important; }
            .wh-hero-image { display: none !important; }
            .wh-hero-text { grid-column: 1 !important; text-align: left !important; padding: 2rem 1.5rem !important; }
        }
    </style>
    @endpush
@endif