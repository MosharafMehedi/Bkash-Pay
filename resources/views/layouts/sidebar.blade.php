<aside class="app-sidebar w-[260px] lg:sticky lg:top-0 h-screen flex flex-col z-40" :class="sidebarOpen ? 'open' : ''">

    <!-- Logo — role-based home -->
    @php
        $homeRoute = 'dashboard';
        if (auth()->check()) {
            if (auth()->user()->hasRole('admin')) {
                $homeRoute = 'admin.orders.index';
            } elseif (auth()->user()->hasRole('delivery_man') || auth()->user()->hasRole('vendor')) {
                $homeRoute = 'delivery.dashboard';
            }
        }
    @endphp

    <div class="flex items-center justify-between h-16 px-5 border-b border-white/5">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-lg font-bold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">
                {{ config('app.name', 'mPay') }}
            </span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        {{-- ═══════════ MAIN (user / admin only) ═══════════ --}}
        @unlessrole('delivery_man|vendor')
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Main</p>

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Products — customer can view --}}
            @can('product.view')
                @unlessrole('admin')
                    <a href="{{ route('products.index') }}"
                        class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Products</span>
                    </a>
                @endunlessrole
            @endcan

            {{-- My Orders — customer --}}
            @role('user')
                <a href="{{ route('my-orders.index') }}"
                    class="sidebar-link {{ request()->routeIs('my-orders.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>My Orders</span>
                </a>
            @endrole
        @endunlessrole

        {{-- ═══════════ ADMIN SECTION ═══════════ --}}
        @role('admin')
            <p class="px-3 pt-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Admin</p>

            @can('product.view')
                <a href="{{ route('admin.products.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Manage Products</span>
                </a>
            @endcan

            @can('coupon.view')
                <a href="{{ route('admin.coupons.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span>Coupons</span>
                </a>
            @endcan

            @can('order.view-all')
                <a href="{{ route('admin.orders.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Orders</span>
                </a>
            @endcan

            @can('delivery-charge.view')
                <a href="{{ route('admin.delivery-charges.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.delivery-charges.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <span>Delivery Charges</span>
                </a>
            @endcan
        @endrole

        {{-- ═══════════ ACCESS CONTROL ═══════════ --}}
        @role('admin')
            <p class="px-3 pt-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Access Control</p>

            @can('user.view')
                <a href="{{ route('admin.users.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Users</span>
                </a>
            @endcan

            @can('role.view')
                <a href="{{ route('admin.roles.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Roles</span>
                </a>
            @endcan

            @can('permission.view')
                <a href="{{ route('admin.permissions.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Permissions</span>
                </a>
            @endcan
        @endrole

        {{-- ═══════════ DELIVERY (delivery_man / vendor) ═══════════ --}}
        @hasanyrole('delivery_man|vendor')
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Delivery</p>

            <a href="{{ route('delivery.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('delivery.orders.index') }}"
                class="sidebar-link {{ request()->routeIs('delivery.orders.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span>My Deliveries</span>
            </a>
        @endhasanyrole

        {{-- ═══════════ SETTINGS ═══════════ --}}
        <p class="px-3 pt-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Settings</p>

        <a href="{{ route('profile.edit') }}"
            class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>Profile</span>
        </a>
    </nav>

    <!-- User Card -->
    <div class="p-4 border-t border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center text-sm font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-200 truncate">{{ auth()->user()->name ?? 'Guest' }}</p>
                <p class="text-xs text-slate-500 truncate">
                    @if (auth()->check())
                        {{ auth()->user()->getRoleNames()->first() ?? auth()->user()->email }}
                    @else
                        guest@mpay.com
                    @endif
                </p>
            </div>
        </div>
    </div>
</aside>