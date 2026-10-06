<x-app-layout>
    @section('title', 'Add Announcement')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-cyan-400 transition">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to announcements
            </a>
            <h1 class="text-2xl font-bold text-slate-100 mt-3">Add New Announcement</h1>
            <p class="text-sm text-slate-400 mt-1">Top offer bar message for homepage.</p>
        </div>

        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf
            @include('admin.announcements._form', ['announcement' => null])
        </form>
    </div>
</x-app-layout>