<x-app-layout>
    @section('title', 'Edit Slider')

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.sliders.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-cyan-400 transition">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to sliders
            </a>
            <h1 class="text-2xl font-bold text-slate-100 mt-3">Edit — {{ $slider->title }}</h1>
            <p class="text-sm text-slate-400 mt-1">Update slider details.</p>
        </div>

        <form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.sliders._form', ['slider' => $slider])
        </form>
    </div>
</x-app-layout>