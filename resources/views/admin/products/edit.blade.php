<x-app-layout>
    @section('title', 'Edit Product')

    <style>
        .form-page { --cyan: #29e7ff; --violet: #a78bfa; --pink: #ff5fb0; --text-hi: #f1f0fb; --text-mu: #9a94b8; --glass-border: rgba(255,255,255,0.09); }

        .form-header {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(167,139,250,0.07), rgba(255,95,176,0.04));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }
        .form-header::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--violet), var(--pink));
        }
        .form-header::after {
            content: "";
            position: absolute;
            right: -40px; top: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(167,139,250,0.18), transparent 70%);
            pointer-events: none;
        }
        .form-header-icon {
            position: relative;
            z-index: 1;
            width: 48px; height: 48px;
            border-radius: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, rgba(167,139,250,0.18), rgba(255,95,176,0.14));
            border: 1px solid rgba(167,139,250,0.3);
            color: var(--violet);
            box-shadow: 0 8px 22px -10px rgba(167,139,250,0.55);
            flex-shrink: 0;
        }
        .form-header-body { position: relative; z-index: 1; flex: 1; min-width: 0; }
        .form-eyebrow {
            font-size: 0.68rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--violet);
            font-weight: 600;
            margin-bottom: 0.15rem;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-hi);
            line-height: 1.15;
        }
        .form-title span { color: var(--violet); }
        .form-sub {
            margin-top: 0.35rem;
            font-size: 0.8rem;
            color: var(--text-mu);
        }
        .form-back {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.55rem 0.95rem;
            border-radius: 0.7rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-mu);
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            transition: all 0.2s;
            text-decoration: none;
        }
        .form-back:hover {
            color: var(--violet);
            background: rgba(167,139,250,0.08);
            border-color: rgba(167,139,250,0.28);
        }

        @media (max-width: 640px) {
            .form-header { flex-direction: column; align-items: flex-start; padding: 1.25rem; }
            .form-back { width: 100%; justify-content: center; }
            .form-title { font-size: 1.3rem; }
        }
    </style>

    <div class="form-page max-w-5xl mx-auto">

        {{-- ── Page header ── --}}
        <div class="form-header">
            <div class="form-header-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="form-header-body">
                <div class="form-eyebrow">Edit</div>
                <h1 class="form-title">Update <span>{{ $product->name }}</span></h1>
                <p class="form-sub">Change the fields you need — everything else stays the same.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="form-back">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.products._form', ['product' => $product])
        </form>
    </div>
</x-app-layout>