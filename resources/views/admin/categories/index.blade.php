<x-app-layout>
    @section('title', 'Categories')

    <style>
        .ac-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .ac-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .ac-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .ac-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        .ac-header-top {
            display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;
        }

        .ac-btn-add {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem;
            font-weight: 600; font-size: 0.85rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none;
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .ac-btn-add:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .ac-stats {
            display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.65rem;
            margin-top: 1.15rem; padding-top: 1.15rem;
            border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 900px) { .ac-stats { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 500px) { .ac-stats { grid-template-columns: repeat(2, 1fr); } }

        .ac-stat {
            padding: 0.65rem 0.85rem; border-radius: 0.65rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .ac-stat-label { font-size: 0.62rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.25rem; }
        .ac-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.2rem; color: var(--text-hi); }
        .ac-stat.cyan .ac-stat-value { color: var(--cyan); }
        .ac-stat.green .ac-stat-value { color: var(--green); }
        .ac-stat.violet .ac-stat-value { color: var(--violet); }
        .ac-stat.amber .ac-stat-value { color: #fbbf24; }

        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        /* Cards list */
        .ac-list { display: flex; flex-direction: column; gap: 0.65rem; }

        .ac-row {
            display: flex; gap: 1rem; align-items: center;
            padding: 1rem 1.15rem; border-radius: 0.85rem;
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px);
            transition: all 0.2s;
        }
        .ac-row:hover { border-color: rgba(41,231,255,0.3); }
        .ac-row.child { margin-left: 2rem; background: rgba(255,255,255,0.02); border-left: 3px solid rgba(41,231,255,0.3); }
        @media (max-width: 640px) { .ac-row.child { margin-left: 0.75rem; } }

        .ac-img {
            width: 52px; height: 52px; border-radius: 0.65rem;
            overflow: hidden; flex-shrink: 0;
            background: linear-gradient(135deg, rgba(41,231,255,0.1), rgba(167,139,250,0.1));
            border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
        }
        .ac-img img { width: 100%; height: 100%; object-fit: cover; }
        .ac-img svg { color: #64748b; }
        .ac-img .icon { font-size: 1.5rem; }

        .ac-info { flex: 1; min-width: 0; }
        .ac-info-name {
            display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
            font-size: 0.92rem; font-weight: 600; color: var(--text-hi);
            margin-bottom: 0.2rem;
        }
        .ac-info-slug {
            font-family: monospace; font-size: 0.7rem; color: var(--text-mu);
        }
        .ac-info-meta {
            font-size: 0.72rem; color: var(--text-mu);
            display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.25rem;
        }
        .ac-info-meta strong { color: var(--text-hi); }

        .ac-badge {
            display: inline-flex; align-items: center; gap: 0.25rem;
            padding: 0.15rem 0.5rem; border-radius: 999px;
            font-size: 0.6rem; font-weight: 700; letter-spacing: 0.03em;
        }
        .ac-badge.featured { background: rgba(255,95,176,0.12); color: var(--pink); border: 1px solid rgba(255,95,176,0.28); }
        .ac-badge.child    { background: rgba(167,139,250,0.12); color: var(--violet); border: 1px solid rgba(167,139,250,0.28); }

        .ac-actions { display: flex; gap: 0.35rem; flex-shrink: 0; }

        .ac-status {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.3rem 0.7rem; border-radius: 999px;
            font-size: 0.68rem; font-weight: 600;
            cursor: pointer; border: 1px solid transparent;
            background: none;
        }
        .ac-status::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
        .ac-status.active { background: rgba(52,211,153,0.12); color:#34d399; border-color: rgba(52,211,153,0.28); }
        .ac-status.inactive { background: rgba(148,163,184,0.12); color:#94a3b8; border-color: rgba(148,163,184,0.22); }

        .ac-icon-btn {
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem; color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .ac-icon-btn svg { width: 14px; height: 14px; }
        .ac-icon-btn:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); background: rgba(41,231,255,0.08); }
        .ac-icon-btn.danger:hover { color: #f87171; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.08); }
        .ac-icon-btn.star:hover,
        .ac-icon-btn.star.active { color: #fbbf24; border-color: rgba(251,191,36,0.4); background: rgba(251,191,36,0.08); }

        /* Empty */
        .ac-empty {
            text-align: center; padding: 4rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
        }
        .ac-empty svg { width: 60px; height: 60px; margin: 0 auto 1rem; opacity: 0.4; }
        .ac-empty-title { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.35rem; }
    </style>

    <div class="ac-page max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="ac-header">
            <div class="ac-header-top">
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Catalog</div>
                    <div class="ac-header-title">Categories</div>
                    <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                        Organize your products into categories
                    </p>
                </div>

                <a href="{{ route('admin.categories.create') }}" class="ac-btn-add">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Category
                </a>
            </div>

            {{-- Stats --}}
            <div class="ac-stats">
                <div class="ac-stat cyan">
                    <div class="ac-stat-label">Total</div>
                    <div class="ac-stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="ac-stat green">
                    <div class="ac-stat-label">Active</div>
                    <div class="ac-stat-value">{{ $stats['active'] }}</div>
                </div>
                <div class="ac-stat violet">
                    <div class="ac-stat-label">Parents</div>
                    <div class="ac-stat-value">{{ $stats['parents'] }}</div>
                </div>
                <div class="ac-stat amber">
                    <div class="ac-stat-label">Children</div>
                    <div class="ac-stat-value">{{ $stats['children'] }}</div>
                </div>
                <div class="ac-stat" style="--cyan:#ff5fb0;">
                    <div class="ac-stat-label">Featured</div>
                    <div class="ac-stat-value" style="color:var(--pink);">{{ $stats['featured'] }}</div>
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or slug..."
                   class="filter-input" style="flex:1; min-width:220px;">

            <select name="status" class="filter-select" style="min-width:150px;" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="active"   {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- Categories List --}}
        @if ($categories->count())
            <div class="ac-list">
                @foreach ($categories as $category)
                    <div class="ac-row {{ $category->parent_id ? 'child' : '' }}">

                        {{-- Image/Icon --}}
                        <div class="ac-img">
                            @if ($category->hasImage())
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}">
                            @elseif ($category->icon)
                                <span class="icon">{{ $category->icon }}</span>
                            @else
                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="ac-info">
                            <div class="ac-info-name">
                                {{ $category->name }}
                                @if ($category->is_featured)
                                    <span class="ac-badge featured">⭐ Featured</span>
                                @endif
                                @if ($category->parent_id)
                                    <span class="ac-badge child">↳ Child</span>
                                @endif
                            </div>
                            <div class="ac-info-slug">/{{ $category->slug }}</div>
                            <div class="ac-info-meta">
                                <span>📦 <strong>{{ $category->products_count ?? 0 }}</strong> products</span>
                                @if (! $category->parent_id)
                                    <span>· 👥 <strong>{{ $category->children_count ?? 0 }}</strong> subcategories</span>
                                @endif
                                @if ($category->parent)
                                    <span>· Parent: <strong>{{ $category->parent->name }}</strong></span>
                                @endif
                                <span>· Sort: <strong>{{ $category->sort_order }}</strong></span>
                            </div>
                        </div>

                        {{-- Status toggle --}}
                        <form method="POST" action="{{ route('admin.categories.toggle', $category) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="ac-status {{ $category->is_active ? 'active' : 'inactive' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>

                        {{-- Actions --}}
                        <div class="ac-actions">
                            {{-- Toggle featured --}}
                            <form method="POST" action="{{ route('admin.categories.toggle-featured', $category) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="ac-icon-btn star {{ $category->is_featured ? 'active' : '' }}"
                                        title="{{ $category->is_featured ? 'Unfeature' : 'Feature' }}">
                                    <svg fill="{{ $category->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </button>
                            </form>

                            {{-- Edit --}}
                            <a href="{{ route('admin.categories.edit', $category) }}" class="ac-icon-btn" title="Edit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                  onsubmit="return confirm('Delete this category? Products will become uncategorized.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ac-icon-btn danger" title="Delete">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="ac-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <div class="ac-empty-title">No categories yet</div>
                <p>Create your first category to organize products.</p>
                <a href="{{ route('admin.categories.create') }}" class="ac-btn-add" style="margin-top:1rem; display:inline-flex;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Category
                </a>
            </div>
        @endif
    </div>
</x-app-layout>