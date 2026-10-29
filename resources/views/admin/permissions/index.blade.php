<x-app-layout>
    @section('title', 'Permissions')

    <style>
        .pm-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .pm-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .pm-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .pm-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }
        .pm-header-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }

        .pm-btn-add {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem;
            font-weight: 600; font-size: 0.85rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none;
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .pm-btn-add:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .pm-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.65rem;
            margin-top: 1.15rem; padding-top: 1.15rem;
            border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 640px) { .pm-stats { grid-template-columns: repeat(2, 1fr); } }
        .pm-stat {
            padding: 0.75rem 0.9rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .pm-stat-label { font-size: 0.65rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.25rem; }
        .pm-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.2rem; color: var(--text-hi); }
        .pm-stat.cyan .pm-stat-value { color: var(--cyan); }
        .pm-stat.green .pm-stat-value { color: var(--green); }
        .pm-stat.violet .pm-stat-value { color: var(--violet); }

        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        .pm-group {
            border-radius: 1rem; background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px); padding: 1.25rem; margin-bottom: 1rem;
        }
        .pm-group-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; margin-bottom: 1rem; padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--glass-border);
        }
        .pm-group-title {
            font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1rem;
            color: var(--text-hi); text-transform: capitalize;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .pm-group-count {
            font-size: 0.7rem; padding: 0.15rem 0.55rem; border-radius: 999px;
            background: rgba(41,231,255,0.12); color: var(--cyan);
            border: 1px solid rgba(41,231,255,0.28);
        }
        .pm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.65rem; }

        .pm-card {
            display: flex; align-items: flex-start; gap: 0.75rem;
            padding: 0.85rem 1rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);
            transition: all 0.2s;
        }
        .pm-card:hover { border-color: rgba(41,231,255,0.3); background: rgba(41,231,255,0.03); }
        .pm-card.inactive { opacity: 0.6; }

        .pm-icon {
            width: 36px; height: 36px; border-radius: 0.6rem;
            display: flex; align-items: center; justify-content: center;
            background: rgba(41,231,255,0.12); border: 1px solid rgba(41,231,255,0.28);
            color: var(--cyan); flex-shrink: 0;
        }
        .pm-icon.inactive {
            background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.22);
            color: #94a3b8;
        }

        .pm-info { flex: 1; min-width: 0; }
        .pm-name {
            font-family: monospace; font-weight: 600; font-size: 0.82rem;
            color: var(--text-hi); margin-bottom: 0.2rem;
            word-break: break-all;
        }
        .pm-display { font-size: 0.75rem; color: var(--text-mu); margin-bottom: 0.2rem; }
        .pm-desc { font-size: 0.68rem; color: var(--text-mu); line-height: 1.4; }
        .pm-badge {
            display: inline-flex; align-items: center; gap: 0.25rem;
            font-size: 0.62rem; font-weight: 700; text-transform: uppercase;
            padding: 0.15rem 0.45rem; border-radius: 999px;
        }
        .pm-badge.active { background: rgba(52,211,153,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.28); }
        .pm-badge.inactive { background: rgba(148,163,184,0.1); color: #94a3b8; border: 1px solid rgba(148,163,184,0.22); }

        .pm-actions { display: flex; gap: 0.35rem; flex-shrink: 0; }

        .pm-icon-btn {
            width: 30px; height: 30px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.45rem; color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .pm-icon-btn svg { width: 14px; height: 14px; }
        .pm-icon-btn:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); background: rgba(41,231,255,0.08); }
        .pm-icon-btn.danger:hover { color: #f87171; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.08); }
    </style>

    <div class="pm-page max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="pm-header">
            <div class="pm-header-top">
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Access Control</div>
                    <div class="pm-header-title">Permissions</div>
                    <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                        Manage all permissions — routes are guarded automatically
                    </p>
                </div>

                @can('permission.create')
                    <a href="{{ route('admin.permissions.create') }}" class="pm-btn-add">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Permission
                    </a>
                @endcan
            </div>

            <div class="pm-stats">
                <div class="pm-stat cyan">
                    <div class="pm-stat-label">Total</div>
                    <div class="pm-stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="pm-stat green">
                    <div class="pm-stat-label">Active</div>
                    <div class="pm-stat-value">{{ $stats['active'] }}</div>
                </div>
                <div class="pm-stat">
                    <div class="pm-stat-label">Inactive</div>
                    <div class="pm-stat-value" style="color:#94a3b8;">{{ $stats['inactive'] }}</div>
                </div>
                <div class="pm-stat violet">
                    <div class="pm-stat-label">Groups</div>
                    <div class="pm-stat-value">{{ $stats['groups'] }}</div>
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search permission name or display..."
                   class="filter-input" style="flex:1; min-width:220px;">

            <select name="group" class="filter-select" style="min-width:150px;" onchange="this.form.submit()">
                <option value="">All groups</option>
                @foreach ($groups as $g)
                    <option value="{{ $g }}" {{ request('group') === $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- Groups --}}
        @forelse ($permissions as $group => $items)
            <div class="pm-group">
                <div class="pm-group-header">
                    <div class="pm-group-title">
                        {{ str_replace('-', ' ', $group) }}
                        <span class="pm-group-count">{{ $items->count() }}</span>
                    </div>
                </div>

                <div class="pm-grid">
                    @foreach ($items as $permission)
                        <div class="pm-card {{ $permission->is_active ? '' : 'inactive' }}">
                            <div class="pm-icon {{ $permission->is_active ? '' : 'inactive' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>

                            <div class="pm-info">
                                <div class="pm-name">{{ $permission->name }}</div>
                                <div class="pm-display">{{ $permission->display_name }}</div>
                                @if ($permission->description)
                                    <div class="pm-desc">{{ $permission->description }}</div>
                                @endif
                            </div>

                            <div class="pm-actions">
                                @can('permission.edit')
                                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="pm-icon-btn" title="Edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endcan

                                @can('permission.delete')
                                    <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}"
                                          onsubmit="return confirm('Delete this permission?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="pm-icon-btn danger" title="Delete">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="pm-group" style="text-align:center; padding: 4rem 1rem;">
                <p style="color:var(--text-mu);">No permissions found.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>