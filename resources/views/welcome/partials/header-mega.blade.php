@php
    use App\Models\Category;
    use App\Models\CartItem;

    $headerCategories = Category::active()
        ->whereNull('parent_id')
        ->ordered()
        ->with(['children' => fn ($q) => $q->active()->ordered()])
        ->take(8)
        ->get();

    $cartCount = auth()->check()
        ? (int) CartItem::where('user_id', auth()->id())->sum('quantity')
        : 0;
@endphp

<header class="wh-header" x-data="{ mobileOpen: false, searchOpen: false }">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 1.25rem;">

        {{-- Top Row: Logo + Search + Actions --}}
        <div style="display: flex; align-items: center; gap: 1rem; height: 68px;">

            {{-- Mobile menu button --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden"
                    style="background: none; border: none; color: #94a3b8; padding: 0.5rem; cursor: pointer;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Logo --}}
            <a href="{{ route('welcome') }}"
               style="display: flex; align-items: center; gap: 0.6rem; text-decoration: none; flex-shrink: 0;">
                <div style="width: 38px; height: 38px; border-radius: 0.7rem; background: linear-gradient(135deg, #29e7ff, #a78bfa); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" fill="none" stroke="#06050c" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span style="font-family: 'Sora', sans-serif; font-size: 1.15rem; font-weight: 700; background: linear-gradient(90deg, #29e7ff, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    {{ $settings['site_title'] ?? 'mPay Gateway' }}
                </span>
            </a>

            {{-- Search (Desktop) --}}
            <div class="hidden md:flex" style="flex: 1; max-width: 480px; margin: 0 auto;">
                <form action="{{ route('products.index') }}" method="GET"
                      style="width: 100%; position: relative;">
                    <input type="text" name="q"
                           placeholder="Search products..."
                           style="width: 100%; padding: 0.65rem 1rem 0.65rem 2.5rem; border-radius: 0.7rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #f1f0fb; font-size: 0.875rem; outline: none; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='rgba(41,231,255,0.5)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.09)'">
                    <svg style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #64748b;"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </form>
            </div>

            {{-- Right Actions --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: auto;">

                {{-- Mobile search --}}
                <button @click="searchOpen = !searchOpen" class="md:hidden"
                        style="background: none; border: none; color: #94a3b8; padding: 0.5rem; cursor: pointer;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                {{-- Cart --}}
                <a href="{{ auth()->check() ? route('cart.index') : route('login') }}"
                   style="position: relative; padding: 0.5rem; color: #94a3b8; text-decoration: none; transition: color 0.2s;"
                   onmouseover="this.style.color='#fff'"
                   onmouseout="this.style.color='#94a3b8'">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @if ($cartCount > 0)
                        <span style="position: absolute; top: 0; right: 0; min-width: 18px; height: 18px; padding: 0 5px; border-radius: 999px; background: linear-gradient(135deg, #29e7ff, #a78bfa); color: #06050c; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center;">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    {{-- Profile dropdown --}}
                    <div x-data="{ open: false }" style="position: relative;">
                        <button @click="open = !open"
                                style="display: flex; align-items: center; gap: 0.5rem; background: none; border: none; cursor: pointer; padding: 0.3rem;">
                            @if (auth()->user()->hasAvatar())
                                <img src="{{ auth()->user()->avatar_url }}"
                                     style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(41,231,255,0.3);">
                            @else
                                <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #29e7ff, #a78bfa); display: flex; align-items: center; justify-content: center; color: #06050c; font-weight: 700; font-size: 0.85rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                             style="position: absolute; right: 0; top: 100%; margin-top: 0.5rem; width: 200px; background: rgba(17,24,39,0.98); border: 1px solid var(--glass-border); border-radius: 0.75rem; padding: 0.4rem; z-index: 100; backdrop-filter: blur(20px);">
                            <div style="padding: 0.6rem 0.85rem; border-bottom: 1px solid var(--glass-border); margin-bottom: 0.3rem;">
                                <div style="font-size: 0.85rem; font-weight: 600; color: #f1f0fb;">{{ auth()->user()->name }}</div>
                                <div style="font-size: 0.7rem; color: #94a3b8;">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.55rem 0.85rem; border-radius: 0.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.82rem; transition: all 0.2s;"
                               onmouseover="this.style.background='rgba(41,231,255,0.08)'; this.style.color='#29e7ff'"
                               onmouseout="this.style.background='none'; this.style.color='#cbd5e1'">
                                Dashboard
                            </a>
                            <a href="{{ route('my-orders.index') }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.55rem 0.85rem; border-radius: 0.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.82rem; transition: all 0.2s;"
                               onmouseover="this.style.background='rgba(41,231,255,0.08)'; this.style.color='#29e7ff'"
                               onmouseout="this.style.background='none'; this.style.color='#cbd5e1'">
                                My Orders
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" style="width: 100%; text-align: left; padding: 0.55rem 0.85rem; border-radius: 0.5rem; color: #f87171; background: none; border: none; cursor: pointer; font-size: 0.82rem; transition: all 0.2s;"
                                        onmouseover="this.style.background='rgba(239,68,68,0.08)'"
                                        onmouseout="this.style.background='none'">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Login / Register --}}
                    <a href="{{ route('login') }}"
                       style="padding: 0.55rem 1.1rem; border-radius: 0.6rem; font-size: 0.82rem; font-weight: 600; color: #e2e8f0; background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); text-decoration: none; transition: all 0.2s; white-space: nowrap;"
                       onmouseover="this.style.borderColor='rgba(41,231,255,0.5)'"
                       onmouseout="this.style.borderColor='rgba(255,255,255,0.09)'">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="hidden sm:inline-flex"
                       style="padding: 0.55rem 1.1rem; border-radius: 0.6rem; font-size: 0.82rem; font-weight: 700; color: #06050c; background: linear-gradient(135deg, #29e7ff, #a78bfa); text-decoration: none; transition: all 0.2s; white-space: nowrap; box-shadow: 0 4px 14px -4px rgba(41,231,255,0.6);">
                        Register
                    </a>
                @endauth
            </div>
        </div>

        {{-- Mobile Search (toggle) --}}
        <div x-show="searchOpen" x-transition
             class="md:hidden"
             style="padding-bottom: 0.85rem;">
            <form action="{{ route('products.index') }}" method="GET" style="position: relative;">
                <input type="text" name="q"
                       placeholder="Search products..."
                       style="width: 100%; padding: 0.7rem 1rem 0.7rem 2.5rem; border-radius: 0.7rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #f1f0fb; font-size: 0.875rem; outline: none;">
                <svg style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #64748b;"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>
        </div>

        {{-- Category Menu (Desktop) --}}
        <nav class="hidden lg:flex" style="gap: 0.3rem; padding-bottom: 0.5rem; border-top: 1px solid rgba(255,255,255,0.04); padding-top: 0.5rem;">
            <a href="{{ route('products.index') }}"
               style="padding: 0.5rem 0.9rem; border-radius: 0.5rem; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; text-decoration: none; transition: all 0.2s;"
               onmouseover="this.style.background='rgba(41,231,255,0.08)'; this.style.color='#29e7ff'"
               onmouseout="this.style.background='none'; this.style.color='#cbd5e1'">
                All Products
            </a>

            @foreach ($headerCategories as $cat)
                <div style="position: relative;" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                       style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 0.9rem; border-radius: 0.5rem; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; text-decoration: none; transition: all 0.2s;"
                       onmouseover="this.style.background='rgba(41,231,255,0.08)'; this.style.color='#29e7ff'"
                       onmouseout="this.style.background='none'; this.style.color='#cbd5e1'">
                        @if ($cat->icon)
                            <span>{{ $cat->icon }}</span>
                        @endif
                        {{ $cat->name }}
                        @if ($cat->children->count() > 0)
                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        @endif
                    </a>

                    @if ($cat->children->count() > 0)
                        <div x-show="open" x-transition.opacity
                             style="position: absolute; top: 100%; left: 0; min-width: 220px; background: rgba(17,24,39,0.98); border: 1px solid var(--glass-border); border-radius: 0.75rem; padding: 0.5rem; margin-top: 0.25rem; box-shadow: 0 20px 40px -12px rgba(0,0,0,0.5); backdrop-filter: blur(20px); z-index: 100;">
                            @foreach ($cat->children as $child)
                                <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                   style="display: flex; align-items: center; gap: 0.5rem; padding: 0.55rem 0.85rem; border-radius: 0.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.8rem; transition: all 0.2s;"
                                   onmouseover="this.style.background='rgba(41,231,255,0.08)'; this.style.color='#29e7ff'"
                                   onmouseout="this.style.background='none'; this.style.color='#cbd5e1'">
                                    @if ($child->icon)
                                        <span>{{ $child->icon }}</span>
                                    @endif
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <a href="{{ route('products.index', ['sort' => 'newest']) }}"
               style="padding: 0.5rem 0.9rem; border-radius: 0.5rem; font-size: 0.82rem; font-weight: 600; color: #ff5fb0; text-decoration: none; transition: all 0.2s;"
               onmouseover="this.style.background='rgba(255,95,176,0.1)'"
               onmouseout="this.style.background='none'">
                🔥 Hot Deals
            </a>
        </nav>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-transition
             class="lg:hidden"
             style="padding-bottom: 1rem; border-top: 1px solid rgba(255,255,255,0.04); margin-top: 0.5rem;">
            <div style="padding-top: 0.75rem;">
                <a href="{{ route('products.index') }}"
                   style="display: block; padding: 0.65rem 0.9rem; border-radius: 0.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                    All Products
                </a>

                @foreach ($headerCategories as $cat)
                    <div>
                        <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                           style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 0.9rem; border-radius: 0.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                            @if ($cat->icon) <span>{{ $cat->icon }}</span> @endif
                            {{ $cat->name }}
                        </a>

                        @if ($cat->children->count() > 0)
                            <div style="padding-left: 1.5rem;">
                                @foreach ($cat->children as $child)
                                    <a href="{{ route('products.index', ['category' => $child->slug]) }}"
                                       style="display: block; padding: 0.5rem 0.9rem; color: #94a3b8; text-decoration: none; font-size: 0.8rem;">
                                        ↳ {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</header>