<x-app-layout>
    @section('title', 'Delivery Charges')

    <style>
        .admin-page { --cyan:#29e7ff; --violet:#a78bfa; --text-hi:#f1f0fb; --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .admin-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px);
            display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;
        }
        .admin-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .admin-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        .btn-add {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem; font-weight: 600; font-size: 0.85rem;
            color: #06050c; background: linear-gradient(135deg, var(--cyan), var(--violet));
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65); text-decoration: none;
        }
        .btn-add:hover { filter: brightness(1.08); }

        .admin-table-wrap { background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); border-radius: 1rem; overflow: hidden; }
        .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .admin-table thead th {
            background: rgba(11,15,25,0.85); color: var(--text-mu);
            font-size: 0.7rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;
            padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);
        }
        .admin-table tbody td {
            padding: 0.9rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1; font-size: 0.85rem; vertical-align: middle;
        }
        .admin-table tbody tr:hover { background: rgba(41,231,255,0.035); }

        .city-name { font-weight: 700; color: var(--text-hi); font-size: 0.9rem; }
        .charge-amt { font-family: 'Sora', sans-serif; font-weight: 700; color: var(--cyan); font-size: 0.95rem; }
        .free-note { font-size: 0.72rem; color: #6ee7b7; margin-top: 0.15rem; }

        .status-btn {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.3rem 0.7rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;
            cursor: pointer; border: 1px solid transparent; background: none;
        }
        .status-btn::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
        .status-btn.active { background: rgba(52,211,153,0.12); color:#34d399; border-color: rgba(52,211,153,0.28); }
        .status-btn.inactive { background: rgba(148,163,184,0.12); color:#94a3b8; border-color: rgba(148,163,184,0.22); }

        .action-btn {
            width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.55rem; color: var(--text-mu); background: none; cursor: pointer;
            transition: all 0.2s; border: 1px solid transparent;
        }
        .action-btn:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.08); }
        .action-btn.edit:hover { color: var(--cyan); background: rgba(41,231,255,0.1); border-color: rgba(41,231,255,0.25); }
        .action-btn.delete:hover { color: #f87171; background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); }
    </style>

    <div class="admin-page max-w-5xl mx-auto">

        <div class="admin-header">
            <div>
                <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Delivery</div>
                <div class="admin-header-title">Delivery Charges</div>
                <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                    City-wise delivery charge management
                </p>
            </div>
            <a href="{{ route('admin.delivery-charges.create') }}" class="btn-add">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Add City
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
        @endif

        <form method="GET" class="rounded-xl p-4 mb-5 flex flex-wrap gap-3" style="background:rgba(255,255,255,0.03); border:1px solid var(--glass-border);">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search city..."
                   style="flex:1; min-width:200px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.09); border-radius:0.65rem; padding:0.6rem 0.9rem; color:#f1f0fb; font-size:0.85rem; outline:none;">
            <select name="status" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.09); border-radius:0.65rem; padding:0.6rem 0.9rem; color:#f1f0fb; font-size:0.85rem;">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button style="padding:0.6rem 1rem; border-radius:0.65rem; font-weight:600; font-size:0.85rem; color:#29e7ff; background:rgba(41,231,255,0.12); border:1px solid rgba(41,231,255,0.32); cursor:pointer;">
                Filter
            </button>
        </form>

        <div class="admin-table-wrap">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Charge</th>
                            <th>Free Above</th>
                            <th>Est. Days</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($charges as $charge)
                            <tr>
                                <td><span class="city-name">{{ $charge->city }}</span></td>
                                <td><span class="charge-amt">৳{{ number_format($charge->charge, 0) }}</span></td>
                                <td>
                                    @if ($charge->free_above)
                                        <span style="color:var(--text-hi);">৳{{ number_format($charge->free_above, 0) }}</span>
                                        <div class="free-note">Free above this</div>
                                    @else
                                        <span style="color:var(--text-mu);">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($charge->estimated_days)
                                        <span style="color:var(--text-hi);">{{ $charge->estimated_days }} day{{ $charge->estimated_days > 1 ? 's' : '' }}</span>
                                    @else
                                        <span style="color:var(--text-mu);">—</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.delivery-charges.toggle', $charge) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="status-btn {{ $charge->is_active ? 'active' : 'inactive' }}">
                                            {{ $charge->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div style="display:flex; justify-content:flex-end; gap:0.25rem;">
                                        <a href="{{ route('admin.delivery-charges.edit', $charge) }}" class="action-btn edit" title="Edit">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.delivery-charges.destroy', $charge) }}"
                                              onsubmit="return confirm('Delete this city?');" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="action-btn delete" title="Delete">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:4rem 1rem; text-align:center; color:var(--text-mu);">
                                    No delivery charges configured. Add your first city!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $charges->links() }}</div>
    </div>
</x-app-layout>