<x-app-layout>
    @section('title', 'Permissions')

    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-100">All Permissions</h1>
            <p class="text-sm text-slate-400 mt-1">Reference list — assign via roles.</p>
        </div>

        @foreach ($permissions as $group => $items)
            <div class="rounded-xl p-5 mb-4" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-2 mb-3">
                    <div class="text-sm font-bold text-slate-100 capitalize">{{ str_replace('-', ' ', $group) }}</div>
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(41,231,255,0.12); color:#29e7ff;">{{ $items->count() }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($items as $perm)
                        <span class="px-3 py-1.5 rounded-lg font-mono text-xs" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); color:#cbd5e1;">
                            {{ $perm->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>