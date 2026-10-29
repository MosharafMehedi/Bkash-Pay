<x-app-layout>
    @section('title', 'Edit Permission')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-cyan-400 transition">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to permissions
            </a>
            <h1 class="text-2xl font-bold text-slate-100 mt-3">Edit — {{ $permission->name }}</h1>
            <p class="text-sm text-slate-400 mt-1">Permission name is immutable (routes reference it).</p>
        </div>

        <div class="mb-4 px-4 py-3 rounded-lg bg-violet-500/10 border border-violet-500/30 text-violet-300 text-sm">
            ⚠️ <strong>Permission name cannot be changed</strong> — routes and controllers reference it.
            Only metadata (display name, description, status) can be edited.
        </div>

        <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
            @csrf @method('PUT')
            @include('admin.permissions._form', ['permission' => $permission])
        </form>
    </div>
</x-app-layout>