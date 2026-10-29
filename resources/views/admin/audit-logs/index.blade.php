<x-app-layout>
    @section('title', 'Audit Logs')

    <style>
        .al-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --amber:#fbbf24; --red:#f87171; --text-hi:#f1f0fb; --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .al-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .al-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .al-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        .al-stats {
            display: grid; grid-template-columns: repeat(6, 1fr); gap: 0.6rem;
            margin-top: 1.15rem; padding-top: 1.15rem;
            border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 900px) { .al-stats { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 500px) { .al-stats { grid-template-columns: repeat(2, 1fr); } }

        .al-stat {
            padding: 0.7rem 0.85rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .al-stat-label { font-size: 0.62rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.25rem; }
        .al-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.2rem; color: var(--text-hi); }
        .al-stat.cyan .al-stat-value { color: var(--cyan); }
        .al-stat.green .al-stat-value { color: var(--green); }
        .al-stat.amber .al-stat-value { color: var(--amber); }
        .al-stat.red .al-stat-value { color: var(--red); }
        .al-stat.violet .al-stat-value { color: var(--violet); }

        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        .al-table-wrap {
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); border-radius: 1rem; overflow: hidden;
        }
        .al-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .al-table thead th {
            background: rgba(11,15,25,0.85); color: var(--text-mu);
            font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;
            padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);
        }
        .al-table tbody td {
            padding: 0.85rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1; font-size: 0.8rem; vertical-align: middle;
        }
        .al-table tbody tr:hover { background: rgba(41,231,255,0.035); }

        .al-user {
            display: flex; align-items: center; gap: 0.6rem;
        }
        .al-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, rgba(41,231,255,0.25), rgba(167,139,250,0.25));
            border: 1px solid rgba(41,231,255,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; color: var(--cyan); flex-shrink: 0;
        }

        .al-event-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.25rem 0.65rem; border-radius: 999px;
            font-size: 0.68rem; font-weight: 700; white-space: nowrap;
        }
        .al-event-badge::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor;
        }

        .al-desc {
            max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        .al-ip {
            font-family: monospace; font-size: 0.72rem; color: var(--text-mu);
        }

        .al-action-btn {
            width: 30px; height: 30px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.45rem; color: var(--text-mu);
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
            cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .al-action-btn:hover { color: var(--cyan); border-color: rgba(41,231,255,0.4); background: rgba(41,231,255,0.08); }

        .al-empty {
            text-align: center; padding: 4rem 1.5rem;
            background: var(--glass); border: 1px dashed var(--glass-border); border-radius: 1rem;
            color: var(--text-mu);
        }
    </style>

    <div class="al-page max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="al-header">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Security</div>
                    <div class="al-header-title">Audit Logs</div>
                    <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                        Track all system activity — who did what and when
                    </p>
                </div>

                @can('audit-log.delete')
                    <form method="POST" action="{{ route('admin.audit-logs.purge') }}"
                          onsubmit="return confirm('Purge old logs?');">
                        @csrf
                        <div style="display:flex; gap:0.5rem; align-items:center;">
                            <input type="number" name="days" value="30" min="1" max="365" required
                                   class="filter-input" style="width:80px; text-align:center;" title="Days to keep">
                            <button type="submit" class="filter-input" style="color:#f87171; background:rgba(239,68,68,0.1); border-color:rgba(239,68,68,0.3); cursor:pointer; font-weight:600; white-space:nowrap;">
                                Purge Old
                            </button>
                        </div>
                    </form>
                @endcan
            </div>

            <div class="al-stats">
                <div class="al-stat cyan">
                    <div class="al-stat-label">Total</div>
                    <div class="al-stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="al-stat green">
                    <div class="al-stat-label">Today</div>
                    <div class="al-stat-value">{{ $stats['today'] }}</div>
                </div>
                <div class="al-stat violet">
                    <div class="al-stat-label">This Week</div>
                    <div class="al-stat-value">{{ $stats['week'] }}</div>
                </div>
                <div class="al-stat green">
                    <div class="al-stat-label">Created</div>
                    <div class="al-stat-value">{{ $stats['created'] }}</div>
                </div>
                <div class="al-stat amber">
                    <div class="al-stat-label">Updated</div>
                    <div class="al-stat-value">{{ $stats['updated'] }}</div>
                </div>
                <div class="al-stat red">
                    <div class="al-stat-label">Deleted</div>
                    <div class="al-stat-value">{{ $stats['deleted'] }}</div>
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search description..."
                   class="filter-input" style="flex:1; min-width:200px;">

            <select name="event" class="filter-select" style="min-width:130px;">
                <option value="">All events</option>
                <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                <option value="login"   {{ request('event') === 'login'   ? 'selected' : '' }}>Login</option>
                <option value="logout"  {{ request('event') === 'logout'  ? 'selected' : '' }}>Logout</option>
                <option value="custom"  {{ request('event') === 'custom'  ? 'selected' : '' }}>Custom</option>
            </select>

            <select name="user_id" class="filter-select" style="min-width:150px;">
                <option value="">All users</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <select name="model_type" class="filter-select" style="min-width:140px;">
                <option value="">All models</option>
                @foreach ($modelTypes as $mt)
                    <option value="{{ $mt['value'] }}" {{ request('model_type') === $mt['value'] ? 'selected' : '' }}>{{ $mt['label'] }}</option>
                @endforeach
            </select>

            <input type="date" name="from" value="{{ request('from') }}" class="filter-input" style="min-width:140px;">
            <input type="date" name="to" value="{{ request('to') }}" class="filter-input" style="min-width:140px;">

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>

            @if (request()->hasAny(['q', 'event', 'user_id', 'model_type', 'from', 'to']))
                <a href="{{ route('admin.audit-logs.index') }}" class="filter-input" style="color:#f87171; text-decoration:none; display:inline-flex; align-items:center;">
                    Clear
                </a>
            @endif
        </form>

        {{-- Table --}}
        @if ($logs->count())
            <div class="al-table-wrap">
                <div style="overflow-x:auto;">
                    <table class="al-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Event</th>
                                <th>Model</th>
                                <th>Description</th>
                                <th>IP</th>
                                <th>Time</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                @php $badge = $log->eventBadge(); @endphp
                                <tr>
                                    <td>
                                        @if ($log->user)
                                            <div class="al-user">
                                                <div class="al-avatar">{{ strtoupper(substr($log->user->name, 0, 1)) }}</div>
                                                <div>
                                                    <div style="font-weight:600; color:var(--text-hi); font-size:0.8rem;">{{ $log->user->name }}</div>
                                                    <div style="font-size:0.68rem; color:var(--text-mu);">{{ $log->user->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span style="color:var(--text-mu); font-size:0.75rem;">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="al-event-badge"
                                              style="color:{{ $badge['color'] }}; background:{{ $badge['color'] }}1a; border:1px solid {{ $badge['color'] }}44;">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($log->auditable_type)
                                            <span style="font-size:0.72rem; color:var(--text-hi);">{{ $log->model_short_name }}</span>
                                            @if ($log->auditable_id)
                                                <span style="font-size:0.68rem; color:var(--text-mu);">#{{ $log->auditable_id }}</span>
                                            @endif
                                        @else
                                            <span style="color:var(--text-mu);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="al-desc" title="{{ $log->description }}">{{ $log->description }}</div>
                                    </td>
                                    <td><span class="al-ip">{{ $log->ip_address ?? '—' }}</span></td>
                                    <td style="font-size:0.72rem; color:var(--text-mu);">
                                        {{ $log->created_at->format('M d, H:i') }}
                                        <div style="font-size:0.62rem;">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div style="display:flex; justify-content:flex-end; gap:0.25rem;">
                                            <a href="{{ route('admin.audit-logs.show', $log) }}" class="al-action-btn" title="View">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>

                                            @can('audit-log.delete')
                                                <form method="POST" action="{{ route('admin.audit-logs.destroy', $log) }}"
                                                      onsubmit="return confirm('Delete this log?');" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="al-action-btn" style="color:#f87171;" title="Delete">
                                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $logs->links() }}</div>
        @else
            <div class="al-empty">
                <p style="font-size:1rem; font-weight:600; color:var(--text-hi); margin-bottom:0.3rem;">No audit logs</p>
                <p>Activity will appear here once users start interacting.</p>
            </div>
        @endif
    </div>
</x-app-layout>