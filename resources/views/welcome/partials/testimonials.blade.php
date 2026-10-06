@if ($testimonials->count() > 0)
    <section class="wh-section">
        <div class="wh-section-head">
            <h2 class="wh-section-title">{{ $settings['heading_testimonials'] ?? 'What our customers say' }}</h2>
        </div>

        <div class="wh-testimonials-grid">
            @foreach ($testimonials as $review)
                @php
                    $user = $review->user;
                    $product = $review->product;
                @endphp

                <div class="wh-testimonial-card">

                    {{-- Quote mark --}}
                    <div class="wh-testimonial-quote">"</div>

                    {{-- Stars --}}
                    <div style="display: flex; gap: 0.15rem; margin-bottom: 0.85rem; font-size: 0.9rem; color: #fbbf24; letter-spacing: 0.05em;">
                        {{ str_repeat('★', $review->rating) }}
                    </div>

                    {{-- Comment --}}
                    <p class="wh-testimonial-text">
                        {{ \Illuminate\Support\Str::limit($review->comment, 180) }}
                    </p>

                    {{-- Product --}}
                    @if ($product)
                        <a href="{{ route('products.show', $product) }}"
                           class="wh-testimonial-product">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="wh-testimonial-product-placeholder">
                                    📦
                                </div>
                            @endif
                            <span>{{ \Illuminate\Support\Str::limit($product->name, 30) }}</span>
                        </a>
                    @endif

                    {{-- User --}}
                    <div class="wh-testimonial-user">
                        <div class="wh-testimonial-avatar">
                            @if ($user && $user->hasAvatar())
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                            @else
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <div class="wh-testimonial-name">{{ $user->name ?? 'Anonymous' }}</div>
                            <div class="wh-testimonial-date">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                        @if ($review->is_verified)
                            <span class="wh-verified-badge">✓ Verified</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @push('styles')
    <style>
        .wh-testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .wh-testimonial-card {
            position: relative;
            padding: 1.5rem 1.35rem;
            border-radius: 1rem;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.09);
            backdrop-filter: blur(14px);
            overflow: hidden;
            transition: all 0.25s;
        }
        .wh-testimonial-card:hover {
            transform: translateY(-3px);
            border-color: rgba(41,231,255,0.3);
            box-shadow: 0 20px 40px -20px rgba(41,231,255,0.3);
        }

        .wh-testimonial-quote {
            position: absolute;
            top: -10px;
            right: 1rem;
            font-family: 'Sora', sans-serif;
            font-size: 5rem;
            font-weight: 700;
            color: rgba(41,231,255,0.1);
            line-height: 1;
            pointer-events: none;
        }

        .wh-testimonial-text {
            font-size: 0.85rem;
            color: #cbd5e1;
            line-height: 1.6;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            min-height: 4rem;
        }

        .wh-testimonial-product {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            background: rgba(41,231,255,0.06);
            border: 1px solid rgba(41,231,255,0.15);
            text-decoration: none;
            color: #cbd5e1;
            font-size: 0.72rem;
            font-weight: 500;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }
        .wh-testimonial-product:hover {
            background: rgba(41,231,255,0.12);
            color: #29e7ff;
        }
        .wh-testimonial-product img {
            width: 24px;
            height: 24px;
            border-radius: 0.35rem;
            object-fit: cover;
        }
        .wh-testimonial-product-placeholder {
            width: 24px;
            height: 24px;
            border-radius: 0.35rem;
            background: rgba(41,231,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        .wh-testimonial-user {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .wh-testimonial-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(41,231,255,0.25), rgba(167,139,250,0.25));
            border: 1px solid rgba(41,231,255,0.3);
            color: #29e7ff;
            font-weight: 700;
            font-size: 0.85rem;
            overflow: hidden;
            flex-shrink: 0;
        }
        .wh-testimonial-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .wh-testimonial-name {
            font-weight: 600;
            font-size: 0.82rem;
            color: #f1f0fb;
        }
        .wh-testimonial-date {
            font-size: 0.68rem;
            color: #9a94b8;
        }

        .wh-verified-badge {
            margin-left: auto;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.6rem;
            font-weight: 700;
            background: rgba(52,211,153,0.12);
            color: #34d399;
            border: 1px solid rgba(52,211,153,0.28);
        }

        @media (max-width: 560px) {
            .wh-testimonials-grid { grid-template-columns: 1fr; }
        }
    </style>
    @endpush
@endif