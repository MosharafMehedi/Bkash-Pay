<header class="app-header sticky top-0 z-20 h-16 flex items-center px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between w-full gap-4">

        <!-- Left: Mobile menu toggle + Title -->
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2 -ml-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <h1 class="text-lg font-semibold text-slate-100 truncate">
                {{ $title ?? 'Dashboard' }}
            </h1>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">

            <!-- Notification -->
            <button class="relative p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-cyan-400"></span>
            </button>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-lg hover:bg-white/5 transition">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center text-xs font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-44 rounded-xl py-1 z-50 shadow-xl"
                     style="background: rgba(17,24,39,0.95); border: 1px solid var(--glass-border);">
                    <a href="{{ route('profile.edit') ?? '#' }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Profile</a>
                    <form method="POST" action="{{ route('logout') ?? '#' }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-500/10">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>