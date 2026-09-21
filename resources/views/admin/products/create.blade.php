<x-app-layout>
    @section('title', 'Add Product')

    <style>
        .form-page { --cyan: #29e7ff; --violet: #a78bfa; --text-hi: #f1f0fb; --text-mu: #9a94b8; --glass-border: rgba(255,255,255,0.09); }

        /* ── Page header ── */
        .form-header {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }
        .form-header::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }
        .form-header::after {
            content: "";
            position: absolute;
            right: -40px; top: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(41,231,255,0.15), transparent 70%);
            pointer-events: none;
        }
        .form-header-icon {
            position: relative;
            z-index: 1;
            width: 48px; height: 48px;
            border-radius: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, rgba(41,231,255,0.18), rgba(167,139,250,0.18));
            border: 1px solid rgba(41,231,255,0.28);
            color: var(--cyan);
            box-shadow: 0 8px 22px -10px rgba(41,231,255,0.55);
            flex-shrink: 0;
        }
        .form-header-body { position: relative; z-index: 1; flex: 1; min-width: 0; }
        .form-eyebrow {
            font-size: 0.68rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--cyan);
            font-weight: 600;
            margin-bottom: 0.15rem;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-hi);
            line-height: 1.15;
        }
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
            color: var(--cyan);
            background: rgba(41,231,255,0.08);
            border-color: rgba(41,231,255,0.28);
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
                          d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div class="form-header-body">
                <div class="form-eyebrow">Create</div>
                <h1 class="form-title">Add New Product</h1>
                <p class="form-sub">Fill in the details below to publish a new item to your catalog.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="form-back">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
</x-app-layout>