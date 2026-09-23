<x-app-layout>
    @section('title', 'Edit Role')

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-cyan-400 transition">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to roles
            </a>
            <h1 class="text-2xl font-bold text-slate-100 mt-3">Edit — {{ $role->display_name ?? $role->name }}</h1>
            <p class="text-sm text-slate-400 mt-1">Update role and permissions.</p>
        </div>

        @if ($role->name === 'admin')
            <div class="mb-4 px-4 py-3 rounded-lg bg-violet-500/10 border border-violet-500/30 text-violet-300 text-sm">
                ⚠️ <strong>Super Admin</strong> — this role bypasses all permission checks. Permissions selected below are for display only.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @csrf @method('PUT')
            @include('admin.roles._form', ['role' => $role, 'rolePermissions' => $rolePermissions])
        </form>
    </div>
</x-app-layout>