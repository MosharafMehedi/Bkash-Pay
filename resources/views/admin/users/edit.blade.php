<x-app-layout>
    @section('title', 'Edit User')

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-cyan-400 transition">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to users
            </a>
            <h1 class="text-2xl font-bold text-slate-100 mt-3">Edit User — {{ $user->name }}</h1>
            <p class="text-sm text-slate-400 mt-1">Update user info and roles.</p>
        </div>

        @if ($user->id === auth()->id())
            <div class="mb-4 px-4 py-3 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 text-sm">
                ⚠️ You're editing your own account. Roles won't be changed for self.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            @include('admin.users._form', ['user' => $user])
        </form>
    </div>
</x-app-layout>