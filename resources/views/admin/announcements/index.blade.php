<x-app-layout>
    @section('title', 'Announcements')

    <style>
        .an-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .an-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .an-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .an-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }
        .an-header-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }

        .an-btn-add {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem;
            font-weight: 600; font-size: 0.85rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none;
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .an-btn-add:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .an-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;
            margin-top: 1.15rem; padding-top: 1.15rem;
            border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 640px) { .an-stats { grid-template-columns: repeat(2, 1fr); } }

        .an-stat {
            padding: 0.7rem 0.85rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .an-stat-label { font-size: 0.62rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.25rem; }
        .an-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--text-hi); }
        .an-stat.cyan .an-stat-value { color: var(--cyan); }
        .an-stat.green .an-stat-value { color: var(--green); }
        .an-stat.amber .an-stat-value { color: #fbbf24; }

        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        /* List */
        .an-list { display: flex; flex-direction: column; gap: 0.65rem; }

        .an-row {
            display: flex; gap: 1rem; align-items: center;
            padding: 1rem 1.15rem; border-radius: 0.85rem;
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px);
            transition: all 0.2s;
        }
        .an-row:hover { border-color: rgba(41,231,255,0.3); }

        .an-icon {
            width: 46px; height: 46px; border-radius: 0.65rem;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, rgba(41,231,255,0.15), rgba(167,139,250,0.15));
            border: 1px solid rgba(41,231,255,0.3);
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .an-info { flex: 1; min-width: 0; }
        .an-info-text {
            font-size: 0.9rem; font-weight: 600; color: var(--text-hi);
            margin-bottom: 0.25rem;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .an-info-meta {
            font-size: 0.7rem; color: var(--text-mu);
            display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;
        }
        .an-info-meta strong { color: var(--text-hi); }

        .an-chip {
            display: inline-flex; align-items: center; gap: 0.25rem;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.62rem; font-weight: 700;
            background: rgba(255,255,255,0.06);
            color: var(--text-mu);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .an-chip.cyan { background: rgba(41,231,255,0.12); color: var(--cyan); border-color: rgba(41,231,255,0.28); }
        .an-chip.pink { background: rgba(255,95,176,0.12); color: var(--pink); border-color: rgba(255,95,176,0.28); }
        .an-chip.violet { background: rgba(167,139,250,0.12); color: var(--violet); border-color: rgba(167,139,250,0.28); }
        .an-chip.green { background: rgba(52,211,153,0.12); color: var(--green); border-color: rgba(52,211,153,0.28); }
        .an-chip.amber { background: rgba(251,191,36,0.12); color: #fbbf24; border-color: rgba(251,191,36,0.28); }

        .an-color-dot {
            width: 20px; height: 20px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.2);
            display: inline-block;
        }

        .an-status {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.68rem; font-weight: 600;
            cursor: pointer; border: 1px solid transparent;
            background: none; transition: all 0.2s;
        }
        .an-status::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
        .an-status.active { background: rgba(52,211,153,0.12); color:#34d399; border-color: rgba(52,211,153,0.28); }
        .an-status.inactive { background: rgba(148,163,184,0.12); color:#94a3b8; border-color: rgba(148,163,184,0.22); }

        .an-actions { display: flex; gap: 0.35rem; flex-shrink: 0; }

        .an-icon-btn {
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem; color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .an-icon-btn svg { width: 14px; height: 14px; }
        .an-icon-btn:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); background: rgba(41,231,255,0.08); }
        .an-icon-btn.danger:hover { color: #f87171; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.08); }

        /* Empty */
        .an-empty {
            text-align: center; padding: 4rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
        }
        .an-empty svg { width: 60px; height: 60px; margin: 0 auto 1rem; opacity: 0.4; }
        .an-empty-title { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.35rem; }
    </style>

    <div class="an-page max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="an-header">
            <div class="an-header-top">
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Homepage</div>
                    <div class="an-header-title">Announcements</div>
                    <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                        Top offer bar messages — display on homepage
                    </p>
                </div>

                <a href="{{ route('admin.announcements.create') }}" class="an-btn-add">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Announcement
                </a>
            </div>

            {{-- Stats --}}
            <div class="an-stats">
                <div class="an-stat cyan">
                    <div class="an-stat-label">Total</div>
                    <div class="an-stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="an-stat green">
                    <div class="an-stat-label">Active</div>
                    <div class="an-stat-value">{{ $stats['active'] }}</div>
                </div>
                <div class="an-stat">
                    <div class="an-stat-label">Inactive</div>
                    <div class="an-stat-value" style="color:#94a3b8;">{{ $stats['inactive'] }}</div>
                </div>
                <div class="an-stat amber">
                    <div class="an-stat-label">Scheduled</div>
                    <div class="an-stat-value">{{ $stats['scheduled'] }}</div>
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search text..."
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

        {{-- List --}}
        @if ($announcements->count())
            <div class="an-list">
                @foreach ($announcements as $announcement)
                    <div class="an-row">

                        {{-- Icon --}}
                        <div class="an-icon">
                            {{ $announcement->icon ?? '📢' }}
                        </div>

                        {{-- Info --}}
                        <div class="an-info">
                            <div class="an-info-text">{{ $announcement->text }}</div>
                            <div class="an-info-meta">
                                @if ($announcement->link)
                                    <span class="an-chip cyan">🔗 Link</span>
                                @endif
                                @if ($announcement->is_dismissible)
                                    <span class="an-chip violet">✕ Dismissible</span>
                                @endif
                                @if ($announcement->bg_color)
                                    @php
                                        $colorMap = [
                                            'cyan' => 'linear-gradient(135deg, #29e7ff, #06b6d4)',
                                            'violet' => 'linear-gradient(135deg, #a78bfa, #8b5cf6)',
                                            'pink' => 'linear-gradient(135deg, #ff5fb0, #ec4899)',
                                            'green' => 'linear-gradient(135deg, #34d399, #10b981)',
                                            'amber' => 'linear-gradient(135deg, #fbbf24, #f59e0b)',
                                        ];
                                        $bgStyle = $colorMap[$announcement->bg_color] ?? $announcement->bg_color;
                                    @endphp
                                    <span class="an-chip amber">
                                        <span class="an-color-dot" style="background:{{ $bgStyle }}; width:12px; height:12px;"></span>
                                        {{ ucfirst($announcement->bg_color) }}
                                    </span>
                                @endif
                                @if ($announcement->starts_at || $announcement->ends_at)
                                    <span class="an-chip amber">
                                        ⏰
                                        @if ($announcement->starts_at) From {{ $announcement->starts_at->format('M d') }} @endif
                                        @if ($announcement->ends_at) → {{ $announcement->ends_at->format('M d') }} @endif
                                    </span>
                                @endif
                                <span>· Sort: <strong>{{ $announcement->sort_order }}</strong></span>
                            </div>
                        </div>

                        {{-- Status toggle --}}
                        <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="an-status {{ $announcement->is_active ? 'active' : 'inactive' }}">
                                {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>

                        {{-- Actions --}}
                        <div class="an-actions">
                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="an-icon-btn" title="Edit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}"
                                  onsubmit="return confirm('Delete this announcement?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="an-icon-btn danger" title="Delete">
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

            <div class="mt-5">{{ $announcements->links() }}</div>
        @else
            <div class="an-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <div class="an-empty-title">No announcements yet</div>
                <p>Add your first announcement to display on homepage.</p>
                <a href="{{ route('admin.announcements.create') }}" class="an-btn-add" style="margin-top:1rem; display:inline-flex;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Announcement
                </a>
            </div>
        @endif
    </div>
</x-app-layout>