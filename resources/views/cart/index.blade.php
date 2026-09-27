<x-app-layout>
    @section('title', 'My Cart')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .ct-page {
            font-family: 'Inter', sans-serif;
            --cyan:#29e7ff;
            --violet:#a78bfa;
            --pink:#ff5fb0;
            --green:#34d399;
            --text-hi:#f1f0fb;
            --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045);
            --glass-border:rgba(255,255,255,0.09);
        }

        .ct-wrap { max-width: 1200px; margin: 0 auto; }

        /* Header */
        .ct-header {
            position: relative;
            padding: 1.5rem 1.75rem;
            border-radius: 1.1rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }
        .ct-header::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }
        .ct-header-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem; font-weight: 700; color: var(--text-hi);
        }
        .ct-header-sub {
            font-size: 0.82rem; color: var(--text-mu); margin-top: 0.25rem;
        }
        .ct-header-sub strong { color: var(--cyan); }

        /* Layout */
        .ct-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.25rem;
            align-items: start;
        }
        @media (max-width: 1024px) { .ct-grid { grid-template-columns: 1fr; } }

        /* Items list */
        .ct-items {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .ct-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-radius: 0.95rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px);
            transition: all 0.25s;
            align-items: center;
            position: relative;
        }
        .ct-item:hover {
            border-color: rgba(41,231,255,0.3);
            box-shadow: 0 10px 28px -16px rgba(41,231,255,0.3);
        }
        .ct-item.removing {
            opacity: 0.4;
            transform: scale(0.98);
            pointer-events: none;
        }

        /* Image */
        .ct-item-img {
            width: 90px;
            height: 90px;
            border-radius: 0.7rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .ct-item-img img { width: 100%; height: 100%; object-fit: cover; }
        .ct-item-img svg { color: #64748b; }

        /* Info */
        .ct-item-info {
            flex: 1;
            min-width: 0;
        }
        .ct-item-cat {
            font-size: 0.65rem; font-weight: 600; letter-spacing: 0.08em;
            text-transform: uppercase; color: var(--text-mu);
            margin-bottom: 0.2rem;
        }
        .ct-item-name {
            font-family: 'Sora', sans-serif;
            font-size: 0.95rem; font-weight: 600; color: var(--text-hi);
            line-height: 1.3; margin-bottom: 0.35rem;
            text-decoration: none;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .ct-item-name:hover { color: var(--cyan); }
        .ct-item-price {
            font-size: 0.78rem; color: var(--text-mu);
        }
        .ct-item-price strong {
            color: var(--cyan); font-weight: 700; font-size: 0.9rem;
        }
        .ct-item-stock {
            font-size: 0.68rem;
            margin-top: 0.25rem;
            display: inline-flex; align-items: center; gap: 0.25rem;
        }
        .ct-item-stock::before {
            content: ''; width: 5px; height: 5px; border-radius: 50%;
            background: currentColor; box-shadow: 0 0 6px currentColor;
        }
        .ct-item-stock.ok  { color: #34d399; }
        .ct-item-stock.low { color: var(--pink); }
        .ct-item-stock.out { color: #f87171; }

        /* Quantity stepper */
        .ct-qty {
            display: flex; align-items: center;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            border-radius: 0.6rem;
            overflow: hidden;
            flex-shrink: 0;
        }
        .ct-qty button {
            width: 32px; height: 38px;
            background: none; border: none;
            color: var(--text-hi); font-size: 1rem; font-weight: 700;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .ct-qty button:hover:not(:disabled) { background: rgba(41,231,255,0.1); color: var(--cyan); }
        .ct-qty button:disabled { opacity: 0.3; cursor: not-allowed; }
        .ct-qty input {
            width: 40px; height: 38px;
            text-align: center; background: none; border: none;
            color: var(--text-hi); font-family: 'Sora', sans-serif;
            font-size: 0.9rem; font-weight: 700; outline: none;
        }

        /* Item total + remove */
        .ct-item-total {
            font-family: 'Sora', sans-serif;
            font-weight: 700; font-size: 1.05rem;
            color: var(--text-hi);
            text-align: right;
            min-width: 90px;
            flex-shrink: 0;
        }
        .ct-item-remove {
            position: absolute;
            top: 0.5rem; right: 0.5rem;
            width: 26px; height: 26px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 0.4rem;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            cursor: pointer;
            transition: all 0.2s;
            opacity: 0.7;
        }
        .ct-item-remove:hover {
            background: rgba(239,68,68,0.2);
            border-color: rgba(239,68,68,0.4);
            opacity: 1;
            transform: scale(1.05);
        }
        .ct-item-remove svg { width: 13px; height: 13px; }

        /* Mobile adjustments */
        @media (max-width: 640px) {
            .ct-item { flex-wrap: wrap; }
            .ct-item-img { width: 70px; height: 70px; }
            .ct-qty { order: 3; flex: 1; justify-content: center; }
            .ct-item-total { order: 4; flex: 0 0 auto; }
        }

        /* Summary sidebar */
        .ct-summary {
            position: sticky; top: 6rem;
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            padding: 1.5rem;
        }
        @media (max-width: 1024px) { .ct-summary { position: static; } }

        .ct-summary-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.05rem; font-weight: 700; color: var(--text-hi);
            margin-bottom: 1.1rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .ct-summary-row {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.85rem; color: var(--text-mu);
            padding: 0.4rem 0;
        }
        .ct-summary-row strong { color: var(--text-hi); font-weight: 600; }

        .ct-summary-divider {
            border-top: 1px dashed var(--glass-border);
            margin: 0.85rem 0;
        }

        .ct-summary-total {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 0.85rem;
            border-top: 1px solid var(--glass-border);
            margin-top: 0.4rem;
        }
        .ct-summary-total-label {
            font-size: 0.9rem; font-weight: 700; color: var(--text-hi);
        }
        .ct-summary-total-amt {
            font-family: 'Sora', sans-serif;
            font-weight: 700; font-size: 1.3rem; color: var(--cyan);
        }

        /* Checkout button */
        .ct-checkout {
            width: 100%;
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.9rem 1.25rem;
            border-radius: 0.75rem;
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.92rem;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            border: none; cursor: pointer;
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s, box-shadow 0.2s;
            text-decoration: none;
            margin-top: 1rem;
        }
        .ct-checkout:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 16px 38px -10px rgba(41,231,255,0.85);
        }
        .ct-checkout svg { width: 16px; height: 16px; }

        /* Continue shopping */
        .ct-continue {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;
            width: 100%;
            padding: 0.7rem 1rem;
            border-radius: 0.7rem;
            font-size: 0.82rem; font-weight: 600;
            color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            text-decoration: none;
            transition: all 0.2s;
            margin-top: 0.6rem;
        }
        .ct-continue:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); }

        /* Clear button */
        .ct-clear {
            display: inline-flex; align-items: center; gap: 0.35rem;
            font-size: 0.75rem; font-weight: 600;
            color: #f87171;
            background: none; border: none;
            cursor: pointer;
            text-decoration: underline;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            transition: opacity 0.2s;
        }
        .ct-clear:hover { opacity: 0.8; }
        .ct-clear svg { width: 13px; height: 13px; }

        /* Empty state */
        .ct-empty {
            text-align: center;
            padding: 5rem 1.5rem;
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px dashed var(--glass-border);
        }
        .ct-empty-icon {
            width: 88px; height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 2px solid rgba(41,231,255,0.25);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            color: var(--cyan);
        }
        .ct-empty-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.25rem; font-weight: 700; color: var(--text-hi);
            margin-bottom: 0.5rem;
        }
        .ct-empty-sub {
            font-size: 0.88rem; color: var(--text-mu);
            max-width: 400px; margin: 0 auto 1.5rem;
        }
        .ct-empty-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.9rem;
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none;
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .ct-empty-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }

        /* ── Toast ── */
        @keyframes slideIn {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to   { transform: translateX(120%); opacity: 0; }
        }

        /* ── Loading spinner ── */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        .spinner {
            display: inline-block;
            width: 14px; height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
    </style>

    <div class="ct-page ct-wrap">

        @if ($items->count() > 0)

            {{-- Header --}}
            <div class="ct-header">
                <div class="ct-header-title">My Cart</div>
                <div class="ct-header-sub">
                    <strong>{{ $itemCount }}</strong> items
                    ({{ $uniqueCount }} {{ \Illuminate\Support\Str::plural('product', $uniqueCount) }})
                </div>
            </div>

            {{-- Grid --}}
            <div class="ct-grid">

                {{-- ══ LEFT: Items ══ --}}
                <div>
                    <div class="ct-items" id="cartItems">
                        @foreach ($items as $item)
                            @php
                                $product = $item->product;
                                $stock   = $product ? (int) $product->stock : 0;
                                $stockClass = $stock <= 0 ? 'out' : ($stock <= 3 ? 'low' : 'ok');
                                $stockText  = $stock <= 0 ? 'Out of stock' : ($stock <= 3 ? "Only {$stock} left" : 'In stock');
                                $maxQty = min(5, max($stock, 1));
                            @endphp

                            <div class="ct-item"
                                 data-item-id="{{ $item->id }}"
                                 data-price="{{ $item->unit_price }}"
                                 data-quantity="{{ $item->quantity }}">

                                {{-- Remove --}}
                                <button class="ct-item-remove" data-item-id="{{ $item->id }}" title="Remove">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                {{-- Image --}}
                                <a href="{{ route('products.show', $product) }}" class="ct-item-img">
                                    @if ($product && $product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    @endif
                                </a>

                                {{-- Info --}}
                                <div class="ct-item-info">
                                    <div class="ct-item-cat">{{ $product->category ?? 'Product' }}</div>
                                    <a href="{{ route('products.show', $product) }}" class="ct-item-name">
                                        {{ $product->name ?? 'Product Unavailable' }}
                                    </a>
                                    <div class="ct-item-price">
                                        ৳{{ number_format($item->unit_price, 0) }} × <span class="item-qty-display">{{ $item->quantity }}</span>
                                        = <strong class="item-line-total">৳{{ number_format($item->line_total, 0) }}</strong>
                                    </div>
                                    <div class="ct-item-stock {{ $stockClass }}">{{ $stockText }}</div>
                                </div>

                                {{-- Quantity --}}
                                <div class="ct-qty">
                                    <button type="button" class="qty-minus"
                                            data-item-id="{{ $item->id }}"
                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>−</button>
                                    <input type="number"
                                           value="{{ $item->quantity }}"
                                           min="1"
                                           max="{{ $maxQty }}"
                                           readonly
                                           data-item-id="{{ $item->id }}">
                                    <button type="button" class="qty-plus"
                                            data-item-id="{{ $item->id }}"
                                            {{ $item->quantity >= $maxQty ? 'disabled' : '' }}>+</button>
                                </div>

                                {{-- Line total --}}
                                <div class="ct-item-total item-total-display">৳{{ number_format($item->line_total, 0) }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Clear cart --}}
                    <div style="text-align: right; margin-top: 0.75rem;">
                        <button class="ct-clear" id="clearCartBtn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Clear entire cart
                        </button>
                    </div>
                </div>

                {{-- ══ RIGHT: Summary ══ --}}
                <aside class="ct-summary">
                    <div class="ct-summary-title">Order Summary</div>

                    <div class="ct-summary-row">
                        <span>Subtotal</span>
                        <strong id="subtotalDisplay">৳{{ number_format($subtotal, 0) }}</strong>
                    </div>

                    <div class="ct-summary-row">
                        <span>Items</span>
                        <strong id="itemCountDisplay">{{ $itemCount }}</strong>
                    </div>

                    <div class="ct-summary-divider"></div>

                    <div style="font-size:0.75rem; color:var(--text-mu); line-height:1.55; margin-bottom:0.75rem;">
                        ℹ️ Delivery charge & coupon will apply at checkout.
                    </div>

                    <div class="ct-summary-total">
                        <span class="ct-summary-total-label">Total</span>
                        <span class="ct-summary-total-amt" id="totalDisplay">৳{{ number_format($subtotal, 0) }}</span>
                    </div>

                    <a href="{{ route('checkout.show') }}" class="ct-checkout" id="checkoutBtn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        Proceed to Checkout
                    </a>

                    <a href="{{ route('products.index') }}" class="ct-continue">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 19l-7-7 7-7"/>
                        </svg>
                        Continue Shopping
                    </a>
                </aside>
            </div>

        @else
            {{-- ══ Empty State ══ --}}
            <div class="ct-empty">
                <div class="ct-empty-icon">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ct-empty-title">Your cart is empty</div>
                <p class="ct-empty-sub">
                    Looks like you haven't added anything yet. Start shopping to fill it up!
                </p>
                <a href="{{ route('products.index') }}" class="ct-empty-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Browse Products
                </a>
            </div>
        @endif
    </div>

    <script>
    (function () {
        const csrfToken = '{{ csrf_token() }}';
        const cartItems = document.getElementById('cartItems');

        const routes = {
            update: "{{ route('cart.update', ['cartItem' => '__ID__']) }}",
            remove: "{{ route('cart.remove', ['cartItem' => '__ID__']) }}",
            clear:  "{{ route('cart.clear') }}",
        };

        function url(name, id) {
            return routes[name].replace('__ID__', id);
        }

        // ── Format helpers ──
        const fmtBdt = (n) => '৳' + Number(n).toFixed(0);

        // ── Toast ──
        function showToast(message, type = 'success') {
            let container = document.getElementById('cartToastContainer');
            if (! container) {
                container = document.createElement('div');
                container.id = 'cartToastContainer';
                container.style.cssText = 'position:fixed;top:1.5rem;right:1.5rem;z-index:9999;display:flex;flex-direction:column;gap:0.5rem;';
                document.body.appendChild(container);
            }

            const bg     = type === 'error' ? 'rgba(239,68,68,0.15)' : 'rgba(52,211,153,0.15)';
            const border = type === 'error' ? 'rgba(239,68,68,0.4)'  : 'rgba(52,211,153,0.4)';
            const color  = type === 'error' ? '#fca5a5'              : '#6ee7b7';

            const toast = document.createElement('div');
            toast.style.cssText = `
                padding: 0.85rem 1.15rem; border-radius: 0.7rem;
                background: ${bg}; border: 1px solid ${border};
                color: ${color}; font-size: 0.85rem; font-weight: 600;
                backdrop-filter: blur(15px);
                box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
                animation: slideIn 0.3s ease; max-width: 320px;
            `;
            toast.textContent = message;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ── Update header badge ──
        function updateCartBadge(count) {
            const badge   = document.getElementById('cartBadge');
            const countEl = document.getElementById('cartCount');
            if (! badge) return;

            if (count > 0) {
                badge.style.display = 'flex';
                if (countEl) countEl.textContent = count;
            } else {
                badge.style.display = 'none';
            }
        }

        // ── Update summary ──
        function updateSummary(subtotal, count) {
            const subtotalEl = document.getElementById('subtotalDisplay');
            const totalEl    = document.getElementById('totalDisplay');
            const countEl    = document.getElementById('itemCountDisplay');

            if (subtotalEl) subtotalEl.textContent = fmtBdt(subtotal);
            if (totalEl)    totalEl.textContent    = fmtBdt(subtotal);
            if (countEl)    countEl.textContent    = count;

            updateCartBadge(count);
        }

        // ── Update quantity ──
        async function updateQty(itemId, newQty) {
            const itemEl = document.querySelector(`[data-item-id="${itemId}"].ct-item`);
            if (! itemEl) return;

            try {
                const res = await fetch(url('update', itemId), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ quantity: newQty }),
                });

                const data = await res.json();

                if (! data.ok) {
                    showToast(data.message || 'Could not update quantity.', 'error');
                    return;
                }

                // Update UI
                const qtyInput = itemEl.querySelector('input[type="number"]');
                const qtyDisplay = itemEl.querySelector('.item-qty-display');
                const lineTotal = itemEl.querySelector('.item-line-total');
                const itemTotal = itemEl.querySelector('.item-total-display');

                if (qtyInput) qtyInput.value = newQty;
                if (qtyDisplay) qtyDisplay.textContent = newQty;
                if (lineTotal) lineTotal.textContent = fmtBdt(data.item_total);
                if (itemTotal) itemTotal.textContent = fmtBdt(data.item_total);

                // Stepper buttons state
                const minusBtn = itemEl.querySelector('.qty-minus');
                const plusBtn  = itemEl.querySelector('.qty-plus');
                const maxQty   = parseInt(qtyInput?.max) || 5;

                if (minusBtn) minusBtn.disabled = newQty <= 1;
                if (plusBtn)  plusBtn.disabled  = newQty >= maxQty;

                updateSummary(data.subtotal, data.cart_count);
            } catch (e) {
                showToast('Something went wrong. Try again.', 'error');
            }
        }

        // ── Remove item ──
        async function removeItem(itemId) {
            const itemEl = document.querySelector(`[data-item-id="${itemId}"].ct-item`);
            if (! itemEl) return;

            if (! confirm('Remove this item from cart?')) return;

            itemEl.classList.add('removing');

            try {
                const res = await fetch(url('remove', itemId), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const data = await res.json();

                if (! data.ok) {
                    showToast(data.message || 'Could not remove item.', 'error');
                    itemEl.classList.remove('removing');
                    return;
                }

                // Animate out
                itemEl.style.transition = 'all 0.3s ease';
                itemEl.style.transform = 'translateX(-30px)';
                itemEl.style.opacity = '0';

                setTimeout(() => {
                    itemEl.remove();

                    // If cart empty → reload
                    if (document.querySelectorAll('.ct-item').length === 0) {
                        window.location.reload();
                    }
                }, 300);

                updateSummary(data.subtotal, data.cart_count);
                showToast('Item removed.', 'success');
            } catch (e) {
                itemEl.classList.remove('removing');
                showToast('Something went wrong. Try again.', 'error');
            }
        }

        // ── Clear cart ──
        async function clearCart() {
            if (! confirm('Clear entire cart?')) return;

            try {
                const res = await fetch(routes.clear, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const data = await res.json();

                if (! data.ok) {
                    showToast(data.message || 'Could not clear cart.', 'error');
                    return;
                }

                showToast('Cart cleared.', 'success');
                setTimeout(() => window.location.reload(), 400);
            } catch (e) {
                showToast('Something went wrong. Try again.', 'error');
            }
        }

        // ── Event delegation ──
        cartItems?.addEventListener('click', (e) => {
            // Remove button
            const removeBtn = e.target.closest('.ct-item-remove');
            if (removeBtn) {
                e.preventDefault();
                const itemId = removeBtn.dataset.itemId;
                removeItem(itemId);
                return;
            }

            // Qty minus
            const minusBtn = e.target.closest('.qty-minus');
            if (minusBtn && ! minusBtn.disabled) {
                const itemId = minusBtn.dataset.itemId;
                const itemEl = minusBtn.closest('.ct-item');
                const input = itemEl.querySelector('input[type="number"]');
                const newQty = Math.max(1, (parseInt(input.value) || 1) - 1);
                updateQty(itemId, newQty);
                return;
            }

            // Qty plus
            const plusBtn = e.target.closest('.qty-plus');
            if (plusBtn && ! plusBtn.disabled) {
                const itemId = plusBtn.dataset.itemId;
                const itemEl = plusBtn.closest('.ct-item');
                const input = itemEl.querySelector('input[type="number"]');
                const maxQty = parseInt(input.max) || 5;
                const newQty = Math.min(maxQty, (parseInt(input.value) || 1) + 1);
                updateQty(itemId, newQty);
                return;
            }
        });

        // ── Clear cart button ──
        document.getElementById('clearCartBtn')?.addEventListener('click', clearCart);
    })();
    </script>
</x-app-layout>