<x-app-layout>
    @section('title', 'Newsletter Subscribers')

    <style>
        .nl-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .nl-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .nl-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .nl-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }
        .nl-header-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }

        .nl-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem;
            font-weight: 600; font-size: 0.85rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none; border: none; cursor: pointer;
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .nl-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }

        .nl-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;
            margin-top: 1.15rem; padding-top: 1.15rem;
            border-top: 1px dashed var(--glass-border);
        }
        @media (max-width: 640px) { .nl-stats { grid-template-columns: repeat(2, 1fr); } }

        .nl-stat {
            padding: 0.7rem 0.85rem; border-radius: 0.7rem;
            background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
        }
        .nl-stat-label { font-size: 0.62rem; color: var(--text-mu); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-bottom: 0.25rem; }
        .nl-stat-value { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--text-hi); }
        .nl-stat.cyan .nl-stat-value { color: var(--cyan); }
        .nl-stat.green .nl-stat-value { color: var(--green); }
        .nl-stat.pink .nl-stat-value { color: var(--pink); }

        .filter-input, .filter-select {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem; padding: 0.6rem 0.9rem; font-size: 0.82rem; color: var(--text-hi);
            outline: none;
        }
        .filter-input::placeholder { color: var(--text-mu); }
        .filter-input:focus, .filter-select:focus { border-color: rgba(41,231,255,0.5); }
        .filter-select option { background: #111827; }

        /* Table */
        .nl-table-wrap {
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); border-radius: 1rem; overflow: hidden;
        }
        .nl-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .nl-table thead th {
            background: rgba(11,15,25,0.85); color: var(--text-mu);
            font-size: 0.68rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;
            padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);
        }
        .nl-table tbody td {
            padding: 0.85rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1; font-size: 0.85rem; vertical-align: middle;
        }
        .nl-table tbody tr:hover { background: rgba(41,231,255,0.035); }

        .nl-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(41,231,255,0.25), rgba(167,139,250,0.25));
            border: 1px solid rgba(41,231,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--cyan); font-weight: 700; font-size: 0.85rem;
            flex-shrink: 0;
        }

        .nl-status {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.28rem 0.65rem; border-radius: 999px;
            font-size: 0.68rem; font-weight: 600;
        }
        .nl-status::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
        .nl-status.active { background: rgba(52,211,153,0.12); color:#34d399; border: 1px solid rgba(52,211,153,0.28); }
        .nl-status.inactive { background: rgba(148,163,184,0.12); color:#94a3b8; border: 1px solid rgba(148,163,184,0.22); }

        .nl-icon-btn {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem; color: var(--text-mu);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .nl-icon-btn svg { width: 14px; height: 14px; }
        .nl-icon-btn.danger:hover { color: #f87171; border-color: rgba(239,68,68,0.4); background: rgba(239,68,68,0.08); }

        .nl-empty {
            text-align: center; padding: 4rem 1.5rem;
            border-radius: 1.1rem; background: var(--glass);
            border: 1px dashed var(--glass-border); color: var(--text-mu);
        }
        .nl-empty svg { width: 60px; height: 60px; margin: 0 auto 1rem; opacity: 0.4; }
        .nl-empty-title { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 600; color: var(--text-hi); margin-bottom: 0.35rem; }
    </style>

    <div class="nl-page max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="nl-header">
            <div class="nl-header-top">
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Marketing</div>
                    <div class="nl-header-title">Newsletter Subscribers</div>
                    <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                        Manage your email list
                    </p>
                </div>

                <a href="{{ route('admin.newsletter.export') }}" class="nl-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </a>
            </div>

            {{-- Stats --}}
            <div class="nl-stats">
                <div class="nl-stat cyan">
                    <div class="nl-stat-label">Total</div>
                    <div class="nl-stat-value">{{ $stats['total'] }}</div>
                </div>
                <div class="nl-stat green">
                    <div class="nl-stat-label">Active</div>
                    <div class="nl-stat-value">{{ $stats['active'] }}</div>
                </div>
                <div class="nl-stat">
                    <div class="nl-stat-label">Unsubscribed</div>
                    <div class="nl-stat-value" style="color:#94a3b8;">{{ $stats['unsubscribed'] }}</div>
                </div>
                <div class="nl-stat pink">
                    <div class="nl-stat-label">This Week</div>
                    <div class="nl-stat-value">+{{ $stats['this_week'] }}</div>
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search email..."
                   class="filter-input" style="flex:1; min-width:220px;">

            <select name="status" class="filter-select" style="min-width:150px;" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Unsubscribed</option>
            </select>

            <button type="submit" class="filter-input" style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- Table --}}
        @if ($subscribers->count())
            <div class="nl-table-wrap">
                <div style="overflow-x:auto;">
                    <table class="nl-table">
                        <thead>
                            <tr>
                                <th>Subscriber</th>
                                <th>Subscribed</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscribers as $subscriber)
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:0.75rem;">
                                            <div class="nl-avatar">
                                                {{ strtoupper(substr($subscriber->email, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:600; color:var(--text-hi); font-size:0.85rem;">
                                                    {{ $subscriber->email }}
                                                </div>
                                                @if ($subscriber->ip_address)
                                                    <div style="font-size:0.68rem; color:var(--text-mu); font-family:monospace;">
                                                        {{ $subscriber->ip_address }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:0.82rem; color:var(--text-hi);">
                                            {{ $subscriber->subscribed_at?->format('M d, Y') ?? '—' }}
                                        </div>
                                        <div style="font-size:0.7rem; color:var(--text-mu);">
                                            {{ $subscriber->subscribed_at?->diffForHumans() ?? '' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($subscriber->is_active)
                                            <span class="nl-status active">Active</span>
                                        @else
                                            <span class="nl-status inactive">Unsubscribed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex; justify-content:flex-end;">
                                            <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}"
                                                  onsubmit="return confirm('Remove this subscriber?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="nl-icon-btn danger" title="Remove">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $subscribers->links() }}</div>
        @else
            <div class="nl-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <div class="nl-empty-title">No subscribers yet</div>
                <p>Subscribers will appear here once they sign up.</p>
            </div>
        @endif
    </div>
</x-app-layout>