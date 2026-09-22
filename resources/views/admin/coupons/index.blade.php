<x-app-layout>
    @section('title', 'Manage Coupons')

    <style>
        .admin-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --text-hi:#f1f0fb; --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .admin-header {
            position: relative; display: flex; flex-direction: column; gap: 1.25rem;
            padding: 1.5rem 1.75rem; margin-bottom: 1.5rem; border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(167,139,250,0.07), rgba(41,231,255,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        @media (min-width: 640px) { .admin-header { flex-direction: row; align-items: center; justify-content: space-between; } }
        .admin-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--violet), var(--cyan)); }
        .admin-header-icon {
            width: 48px; height: 48px; border-radius: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, rgba(167,139,250,0.2), rgba(41,231,255,0.18));
            border: 1px solid rgba(167,139,250,0.3); color: var(--violet);
            box-shadow: 0 8px 22px -10px rgba(167,139,250,0.55);
        }
        .admin-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }
        .admin-header-pill {
            display: inline-flex; padding: 0.2rem 0.6rem; border-radius: 999px;
            background: rgba(167,139,250,0.12); border: 1px solid rgba(167,139,250,0.28);
            color: var(--violet); font-size: 0.72rem; font-weight: 600;
        }
        .btn-add {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem; font-weight: 600; font-size: 0.85rem;
            color: #06050c; background: linear-gradient(135deg, #29e7ff, #a78bfa);
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65); text-decoration: none;
        }
        .btn-add:hover { filter: brightness(1.08); }

        .admin-table-wrap {
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px); border-radius: 1rem; overflow: hidden;
        }
        .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .admin-table thead th {
            background: rgba(11,15,25,0.85); color: var(--text-mu);
            font-size: 0.7rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;
            padding: 0.9rem 1rem; text-align: left; border-bottom: 1px solid var(--glass-border);
        }
        .admin-table tbody td {
            padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #cbd5e1; font-size: 0.85rem; vertical-align: middle;
        }
        .admin-table tbody tr:hover { background: rgba(167,139,250,0.04); }

        .coupon-code {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-weight: 700; color: var(--violet); font-size: 0.85rem;
            padding: 0.35rem 0.7rem; border-radius: 0.5rem;
            background: rgba(167,139,250,0.1); border: 1px dashed rgba(167,139,250,0.4);
            letter-spacing: 0.06em;
        }
        .coupon-type {
            display: inline-flex; padding: 0.22rem 0.6rem; border-radius: 999px;
            font-size: 0.68rem; font-weight: 600; text-transform: uppercase;
            background: rgba(41,231,255,0.12); color: var(--cyan); border: 1px solid rgba(41,231,255,0.25);
        }
        .coupon-type.percent { background: rgba(167,139,250,0.12); color: var(--violet); border-color: rgba(167,139,250,0.3); }

        .status-btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;
            cursor: pointer; border: 1px solid transparent;
        }
        .status-btn::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
        .status-btn.active   { background: rgba(52,211,153,0.12); color:#34d399; border-color: rgba(52,211,153,0.28); }
        .status-btn.inactive { background: rgba(148,163,184,0.12); color:#94a3b8; border-color: rgba(148,163,184,0.22); }

        .action-btn {
            width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.55rem; color: var(--text-mu);
            transition: all 0.2s; border: 1px solid transparent; background: none; cursor: pointer;
        }
        .action-btn:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.08); }
        .action-btn.edit:hover   { color: var(--cyan); background: rgba(41,231,255,0.1); border-color: rgba(41,231,255,0.25); }
        .action-btn.delete:hover { color: #f87171; background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); }
    </style>

    <div class="admin-page max-w-7xl mx-auto">

        <div class="admin-header">
            <div class="flex items-center gap-4">
                <div class="admin-header-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--violet); font-weight:600;">Marketing</div>
                    <div class="admin-header-title">Coupons</div>
                    <div style="margin-top:0.35rem; font-size:0.8rem; color:var(--text-mu);">
                        <span class="admin-header-pill">{{ $coupons->total() }} coupons</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.coupons.create') }}" class="btn-add">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Add Coupon
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" class="glass rounded-xl p-4 mb-5 flex flex-wrap gap-3" style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 0.75rem;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search code or name..."
                   style="flex:1; min-width:220px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:0.7rem; padding:0.65rem 0.95rem; color:#f1f0fb; font-size:0.85rem; outline:none;">
            <select name="status" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:0.7rem; padding:0.65rem 0.95rem; color:#f1f0fb; font-size:0.85rem;">
                <option value="">All status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button style="padding:0.65rem 1.15rem; border-radius:0.7rem; font-size:0.85rem; font-weight:600; color:#29e7ff; background:rgba(41,231,255,0.12); border:1px solid rgba(41,231,255,0.32); cursor:pointer;">
                Filter
            </button>
        </form>

        <div class="admin-table-wrap">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Order</th>
                            <th>Used / Limit</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $coupon)
                            <tr>
                                <td>
                                    <span class="coupon-code">{{ $coupon->code }}</span>
                                    @if ($coupon->name)
                                        <div style="font-size:0.72rem; color:var(--text-mu); margin-top:0.3rem;">{{ $coupon->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="coupon-type {{ $coupon->type }}">
                                        {{ $coupon->type === 'percent' ? '%' : 'Fixed' }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color:#f1f0fb;">
                                        {{ $coupon->type === 'percent' ? $coupon->value.'%' : '৳'.number_format($coupon->value, 0) }}
                                    </strong>
                                </td>
                                <td>৳{{ number_format($coupon->min_order, 0) }}</td>
                                <td>
                                    <span style="color:#f1f0fb; font-weight:600;">{{ $coupon->used_count }}</span>
                                    <span style="color:var(--text-mu);"> / {{ $coupon->usage_limit ?? '∞' }}</span>
                                </td>
                                <td style="font-size:0.78rem; color:var(--text-mu);">
                                    @if ($coupon->expires_at)
                                        Ends {{ $coupon->expires_at->format('M d, Y') }}
                                    @else
                                        No expiry
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="status-btn {{ $coupon->is_active ? 'active' : 'inactive' }}">
                                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div style="display:flex; justify-content:flex-end; gap:0.25rem;">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="action-btn edit" title="Edit">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}"
                                              onsubmit="return confirm('Delete this coupon?');">
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
                                <td colspan="8" style="padding:4rem 1rem; text-align:center; color:var(--text-mu);">
                                    No coupons found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $coupons->links() }}</div>
    </div>
</x-app-layout>