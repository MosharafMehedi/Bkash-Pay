@php
    $hasDiscount = !empty($product->discount_price) && $product->discount_price < $product->price_bdt;
    $isDeal = $product->isDealActive();
    $finalPrice = $isDeal
        ? (float) $product->deal_price
        : ($hasDiscount ? (float) $product->discount_price : (float) $product->price_bdt);
    $oldPrice = $isDeal
        ? (float) ($hasDiscount ? $product->discount_price : $product->price_bdt)
        : ($hasDiscount ? (float) $product->price_bdt : null);
    $discountPct = $oldPrice && $oldPrice > 0
        ? round((($oldPrice - $finalPrice) / $oldPrice) * 100)
        : 0;

    $rating = (float) ($product->rating ?? 0);
    $rounded = (int) round($rating);
    $stars = str_repeat('★', $rounded) . str_repeat('☆', 5 - $rounded);
    $stock = (int) $product->stock;
@endphp

<a href="{{ route('products.show', $product) }}"
   class="wpc-card">

    {{-- Ribbons --}}
    <div class="wpc-ribbons">
        @if ($isDeal)
            <span class="wpc-ribbon deal">⚡ DEAL</span>
        @endif
        @if ($discountPct > 0 && ! $isDeal)
            <span class="wpc-ribbon discount">-{{ $discountPct }}%</span>
        @endif
        @if ($product->is_featured)
            <span class="wpc-ribbon featured">⭐</span>
        @endif
    </div>

    {{-- Image --}}
    <div class="wpc-media">
        @if ($product->image)
            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #64748b;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        @endif

        @if ($stock <= 0)
            <div class="wpc-out">Out of Stock</div>
        @endif
    </div>

    {{-- Body --}}
    <div class="wpc-body">

        {{-- Category --}}
        @if ($product->category)
            <div class="wpc-cat">{{ $product->category->name }}</div>
        @endif

        {{-- Name --}}
        <div class="wpc-name">{{ $product->name }}</div>

        {{-- Rating --}}
        <div class="wpc-rating">
            <span class="wpc-stars">{{ $stars }}</span>
            <span class="wpc-num">{{ number_format($rating, 1) }}</span>
            <span class="wpc-count">({{ $product->review_count ?? 0 }})</span>
        </div>

        {{-- Price --}}
        <div class="wpc-price-row">
            <div>
                <span class="wpc-price">৳{{ number_format($finalPrice, 0) }}</span>
                @if ($oldPrice)
                    <span class="wpc-price-old">৳{{ number_format($oldPrice, 0) }}</span>
                @endif
            </div>
        </div>

        {{-- Button --}}
        <div class="wpc-btn">
            @if ($stock > 0)
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Product
            @else
                Sold Out
            @endif
        </div>
    </div>
</a>

@once
    @push('styles')
    <style>
        /* ── Welcome Product Card ── */
        .wpc-card {
            position: relative;
            display: flex;
            flex-direction: column;
            border-radius: 1rem;
            padding: 0.85rem;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.09);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            transition: transform 0.3s cubic-bezier(.2,.8,.2,1), border-color 0.3s, box-shadow 0.3s;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }
        .wpc-card::before {
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
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .wpc-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 48px -22px rgba(41,231,255,0.28);
        }
        .wpc-card:hover::before { opacity: 1; }

        /* Ribbons */
        .wpc-ribbons {
            position: absolute;
            top: 0.65rem;
            left: 0.65rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            z-index: 3;
        }
        .wpc-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            backdrop-filter: blur(10px);
            width: fit-content;
        }
        .wpc-ribbon.deal {
            background: linear-gradient(135deg, #ff5fb0, #f43f5e);
            color: #fff;
            box-shadow: 0 6px 16px -6px rgba(244,63,94,0.6);
        }
        .wpc-ribbon.discount {
            background: linear-gradient(135deg, #29e7ff, #a78bfa);
            color: #06050c;
            box-shadow: 0 6px 16px -6px rgba(41,231,255,0.6);
        }
        .wpc-ribbon.featured {
            background: rgba(251,191,36,0.9);
            color: #06050c;
        }

        /* Media */
        .wpc-media {
            position: relative;
            width: 100%;
            aspect-ratio: 1/1;
            max-height: 200px;
            border-radius: 0.75rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.85rem;
        }
        .wpc-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.45s cubic-bezier(.2,.8,.2,1);
        }
        .wpc-card:hover .wpc-media img { transform: scale(1.06); }

        .wpc-out {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.65);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Body */
        .wpc-body { display: flex; flex-direction: column; flex: 1; }

        .wpc-cat {
            font-size: 0.62rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #9a94b8;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wpc-name {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: #f1f0fb;
            line-height: 1.3;
            margin-bottom: 0.35rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.35rem;
        }

        .wpc-rating {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 0.6rem;
            font-size: 0.7rem;
            color: #9a94b8;
        }
        .wpc-stars { color: #fbbf24; letter-spacing: 0.02em; font-size: 0.7rem; }
        .wpc-num { color: #f1f0fb; font-weight: 600; }
        .wpc-count { font-size: 0.65rem; }

        .wpc-price-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.5rem;
            padding-top: 0.7rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin-top: auto;
            margin-bottom: 0.65rem;
        }
        .wpc-price {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: #f1f0fb;
            line-height: 1;
        }
        .wpc-price-old {
            font-size: 0.7rem;
            color: #9a94b8;
            text-decoration: line-through;
            margin-left: 0.35rem;
        }

        .wpc-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.55rem 0.85rem;
            border-radius: 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #06050c;
            background: linear-gradient(135deg, #29e7ff, #a78bfa);
            box-shadow: 0 6px 18px -8px rgba(41,231,255,0.55);
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
            white-space: nowrap;
        }
        .wpc-card:hover .wpc-btn {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 10px 22px -8px rgba(41,231,255,0.7);
        }
    </style>
    @endpush
@endonce