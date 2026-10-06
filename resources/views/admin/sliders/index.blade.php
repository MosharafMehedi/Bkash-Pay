<x-app-layout>
    @section('title', 'Sliders')

    <style>
        .as-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .as-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .as-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .as-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        .as-header-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }

        .as-btn-add {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem;
            font-weight: 600; font-size: 0.85rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none;
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .as-btn-add:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .as-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;
            margin-top: 1.15rem; padding-top: 1.15rem;
            border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 640px) { .as-stats { grid-template-columns: repeat(2, 1fr); } }

        .as-stat {
            padding: 0.7rem 0.85rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .as-stat-label { font-size: 0.62rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.25rem; }
        .as-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--text-hi); }
        .as-stat.cyan .as-stat-value { color: var(--cyan); }
        .as-stat.green .as-stat-value { color: var(--green); }
        .as-stat.amber .as-stat-value { color: #fbbf24; }

        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        /* Cards */
        .as-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1rem;
        }

        .as-card {
            border-radius: 1rem;
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px);
            overflow: hidden;
            transition: all 0.25s;
        }
        .as-card:hover { border-color: rgba(41,231,255,0.3); transform: translateY(-2px); }

        .as-card-preview {
            width: 100%;
            aspect-ratio: 16 / 9;
            background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
            border-bottom: 1px solid var(--glass-border);
            display: flex; align-items: center; justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .as-card-preview img { width: 100%; height: 100%; object-fit: cover; }
        .as-card-preview .no-image {
            color: var(--text-mu); font-size: 0.78rem;
            display: flex; flex-direction: column; align-items: center; gap: 0.4rem;
        }

        .as-card-badge {
            position: absolute; top: 0.65rem; left: 0.65rem;
            padding: 0.22rem 0.6rem; border-radius: 999px;
            font-size: 0.62rem; font-weight: 700; letter-spacing: 0.03em;
            background: rgba(41,231,255,0.15);
            color: var(--cyan); border: 1px solid rgba(41,231,255,0.35);
            backdrop-filter: blur(10px);
        }
        .as-card-badge.inactive {
            background: rgba(148,163,184,0.15); color: #94a3b8; border-color: rgba(148,163,184,0.3);
        }
        .as-card-badge.scheduled {
            background: rgba(251,191,36,0.15); color: #fbbf24; border-color: rgba(251,191,36,0.3);
        }

        .as-card-sort {
            position: absolute; top: 0.65rem; right: 0.65rem;
            padding: 0.22rem 0.5rem; border-radius: 0.4rem;
            font-size: 0.62rem; font-weight: 700;
            background: rgba(0,0,0,0.5);
            color: #fff;
            backdrop-filter: blur(10px);
        }

        .as-card-body { padding: 1rem; }
        .as-card-title {
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 0.95rem;
            color: var(--text-hi); margin-bottom: 0.3rem;
            display: -webkit-box; -webkit-line-clamp: 1;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .as-card-sub {
            font-size: 0.78rem; color: var(--text-mu);
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
            margin-bottom: 0.7rem;
            min-height: 2.1rem;
        }

        .as-card-meta {
            display: flex; gap: 0.6rem; flex-wrap: wrap;
            font-size: 0.68rem; color: var(--text-mu);
            margin-bottom: 0.85rem;
        }
        .as-card-meta strong { color: var(--text-hi); }

        .as-card-actions {
            display: flex; gap: 0.4rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--glass-border);
        }

        .as-status {
            flex: 1;
            display: inline-flex; align-items: center; justify-content: center;
            gap: 0.35rem;
            padding: 0.45rem 0.7rem;
            border-radius: 0.5rem;
            font-size: 0.7rem; font-weight: 600;
            cursor: pointer; border: 1px solid transparent;
            background: none; transition: all 0.2s;
        }
        .as-status::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
        .as-status.active { background: rgba(52,211,153,0.12); color:#34d399; border-color: rgba(52,211,153,0.28); }
        .as-status.inactive { background: rgba(148,163,184,0.12); color:#94a3b8; border-color: rgba(148,163,184,0.22); }

        .as-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem; color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .as-icon-btn svg { width: 14px; height: 14px; }
        .as-icon-btn:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); background: rgba(41,231,255,0.08); }
        .as-icon-btn.danger:hover { color: #f87171; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.08); }

        /* Empty */
        .as-empty {
            text-align: center; padding: 4rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
            grid-column: 1 / -1;
        }
        .as-empty svg { width: 60px; height: 60px; margin: 0 auto 1rem; opacity: 0.4; }
        .as-empty-title { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.35rem; }
    </style>

    <div class="as-page max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="as-header">
            <div class="as-header-top">
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Homepage</div>
                    <div class="as-header-title">Hero Sliders</div>
                    <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                        Manage homepage banner sliders
                    </p>
                </div>

                <a href="{{ route('admin.sliders.create') }}" class="as-btn-add">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Slider
                </a>
            </div>

            {{-- Stats --}}
            <div class="as-stats">
                <div class="as-stat cyan">
                    <div class="as-stat-label">Total</div>
                    <div class="as-stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="as-stat green">
                    <div class="as-stat-label">Active</div>
                    <div class="as-stat-value">{{ $stats['active'] }}</div>
                </div>
                <div class="as-stat">
                    <div class="as-stat-label">Inactive</div>
                    <div class="as-stat-value" style="color:#94a3b8;">{{ $stats['inactive'] }}</div>
                </div>
                <div class="as-stat amber">
                    <div class="as-stat-label">Scheduled</div>
                    <div class="as-stat-value">{{ $stats['scheduled'] }}</div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="rounded-xl p-4 mb-5 flex flex-wrap gap-3" style="background:rgba(255,255,255,0.03); border:1px solid var(--glass-border);">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title..."
                   class="filter-input" style="flex:1; min-width:220px;">

            <select name="status" class="filter-select" style="min-width:150px;" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- Sliders Grid --}}
        <div class="as-grid">
            @forelse ($sliders as $slider)
                <div class="as-card">

                    {{-- Preview --}}
                    <div class="as-card-preview">
                        @if ($slider->hasBgImage())
                            <img src="{{ $slider->bg_image_url }}" alt="{{ $slider->title }}">
                        @elseif ($slider->hasImage())
                            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}">
                        @else
                            <div class="no-image">
                                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                No banner image
                            </div>
                        @endif

                        {{-- Status badge --}}
                        @if ($slider->isScheduled())
                            <span class="as-card-badge scheduled">⏰ Scheduled</span>
                        @elseif ($slider->is_active)
                            <span class="as-card-badge">● Active</span>
                        @else
                            <span class="as-card-badge inactive">● Inactive</span>
                        @endif

                        {{-- Sort order --}}
                        <span class="as-card-sort">#{{ $slider->sort_order }}</span>
                    </div>

                    {{-- Body --}}
                    <div class="as-card-body">
                        <div class="as-card-title">{{ $slider->title }}</div>
                        <div class="as-card-sub">{{ $slider->subtitle ?? 'No subtitle' }}</div>

                        <div class="as-card-meta">
                            <span>🔗 <strong>{{ ucfirst($slider->link_type) }}</strong></span>
                            @if ($slider->button_text)
                                <span>· Button: <strong>{{ $slider->button_text }}</strong></span>
                            @endif
                            <span>· <strong>{{ ucfirst($slider->text_position) }}</strong></span>
                        </div>

                        {{-- Actions --}}
                        <div class="as-card-actions">
                            <form method="POST" action="{{ route('admin.sliders.toggle', $slider) }}" style="flex:1;">
                                @csrf @method('PATCH')
                                <button type="submit" class="as-status {{ $slider->is_active ? 'active' : 'inactive' }}" style="width:100%;">
                                    {{ $slider->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>

                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="as-icon-btn" title="Edit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}"
                                  onsubmit="return confirm('Delete this slider?');" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="as-icon-btn danger" title="Delete">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="as-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div class="as-empty-title">No sliders yet</div>
                    <p>Create your first hero slider to display on homepage.</p>
                    <a href="{{ route('admin.sliders.create') }}" class="as-btn-add" style="margin-top:1rem; display:inline-flex;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Slider
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-5">{{ $sliders->links() }}</div>
    </div>
</x-app-layout>