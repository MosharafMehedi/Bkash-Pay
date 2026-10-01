<header class="app-header fixed top-0 right-0 left-0 lg:left-[260px] z-30 h-16 flex items-center px-4 sm:px-6 lg:px-8 transition-all duration-300"
        :class="sidebarOpen ? 'lg:left-[260px]' : ''">
    <div class="flex items-center justify-between w-full gap-4">

        <!-- Left: Mobile menu toggle + Title -->
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- <h1 class="text-lg font-semibold text-slate-100 truncate">
                {{ $title ?? 'Dashboard' }}
            </h1> --}}
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-1.5 sm:gap-2">

            {{-- Search (Desktop) --}}
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 focus-within:border-cyan-500/50 transition w-64">
                <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search..." class="bg-transparent text-sm text-slate-200 placeholder-slate-500 focus:outline-none w-full">
                <kbd class="hidden lg:inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold text-slate-500 bg-white/5 border border-white/10">
                    ⌘K
                </kbd>
            </div>

            {{-- Search (Mobile) --}}
            <button class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition" title="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>

            {{-- Cart icon --}}
            <a href="{{ route('cart.index') }}"
                class="relative p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition"
                title="Cart">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>

                @php
                    $cartCount = auth()->check()
                        ? (int) \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity')
                        : 0;
                @endphp

                <span id="cartBadge"
                    class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold text-[#06050c] flex items-center justify-center"
                    style="background: linear-gradient(135deg, #29e7ff, #a78bfa); {{ $cartCount > 0 ? '' : 'display: none;' }}">
                    <span id="cartCount">{{ $cartCount }}</span>
                </span>
            </a>

            {{-- Notification --}}
            <button class="relative p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
            </button>

            {{-- Profile Dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 p-1 pr-2 rounded-lg hover:bg-white/5 transition">
                    @if (auth()->user()->hasAvatar())
                        <img src="{{ auth()->user()->avatar_url }}"
                            alt="{{ auth()->user()->name }}"
                            class="w-8 h-8 rounded-full object-cover border-2 border-cyan-500/30">
                    @else
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center text-xs font-bold text-white">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-semibold text-slate-200 leading-tight">
                            {{ Str::limit(auth()->user()->name ?? 'Guest', 15) }}
                        </div>
                        <div class="text-[10px] text-slate-500 leading-tight">
                            {{ auth()->user()->getRoleNames()->first() ?? 'user' }}
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-2 w-56 rounded-xl py-1.5 z-50 shadow-2xl"
                    style="background: rgba(17,24,39,0.98); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(15px);">

                    {{-- User info --}}
                    <div class="px-4 py-3 border-b border-white/5">
                        <div class="text-sm font-semibold text-slate-100">{{ auth()->user()->name ?? 'Guest' }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</div>
                    </div>

                    <a href="{{ route('profile.edit') ?? '#' }}"
                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        My Profile
                    </a>

                    @role('user')
                        <a href="{{ route('my-orders.index') }}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            My Orders
                        </a>
                    @endrole

                    <div class="border-t border-white/5 my-1"></div>

                    <form method="POST" action="{{ route('logout') ?? '#' }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>