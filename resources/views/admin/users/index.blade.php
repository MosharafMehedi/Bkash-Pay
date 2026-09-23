<x-app-layout>
    @section('title', 'Manage Users')

    <style>
        .admin-page {
            --cyan: #29e7ff;
            --violet: #a78bfa;
            --pink: #ff5fb0;
            --text-hi: #f1f0fb;
            --text-mu: #9a94b8;
            --glass: rgba(255, 255, 255, 0.045);
            --glass-border: rgba(255, 255, 255, 0.09);
        }

        .admin-header {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41, 231, 255, 0.06), rgba(167, 139, 250, 0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }

        @media (min-width: 640px) {
            .admin-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .admin-header::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }

        .admin-header-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-hi);
        }

        .admin-header-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(41, 231, 255, 0.18), rgba(167, 139, 250, 0.18));
            border: 1px solid rgba(41, 231, 255, 0.28);
            color: var(--cyan);
            box-shadow: 0 8px 22px -10px rgba(41, 231, 255, 0.55);
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            border-radius: 0.7rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: #06050c;
            background: linear-gradient(135deg, #29e7ff, #a78bfa);
            box-shadow: 0 10px 28px -10px rgba(41, 231, 255, 0.65);
            text-decoration: none;
        }

        .btn-add:hover {
            filter: brightness(1.08);
        }

        .admin-table-wrap {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            border-radius: 1rem;
            overflow: hidden;
        }

        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .admin-table thead th {
            background: rgba(11, 15, 25, 0.85);
            color: var(--text-mu);
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 600;
            padding: 0.9rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .admin-table tbody td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #cbd5e1;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .admin-table tbody tr:hover {
            background: rgba(41, 231, 255, 0.035);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(41, 231, 255, 0.25), rgba(167, 139, 250, 0.25));
            border: 1px solid rgba(41, 231, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--cyan);
            font-size: 0.85rem;
        }

        .role-badge {
            display: inline-flex;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            background: rgba(41, 231, 255, 0.12);
            color: var(--cyan);
            border: 1px solid rgba(41, 231, 255, 0.28);
            margin: 0.1rem 0.15rem;
        }

        .role-badge.admin {
            background: rgba(167, 139, 250, 0.15);
            color: var(--violet);
            border-color: rgba(167, 139, 250, 0.35);
        }

        .role-badge.delivery_man {
            background: rgba(52, 211, 153, 0.12);
            color: #34d399;
            border-color: rgba(52, 211, 153, 0.3);
        }

        .role-badge.vendor {
            background: rgba(255, 95, 176, 0.12);
            color: var(--pink);
            border-color: rgba(255, 95, 176, 0.3);
        }

        .status-dot {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .status-dot::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-dot.active {
            color: #34d399;
        }

        .status-dot.inactive {
            color: #94a3b8;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.55rem;
            color: var(--text-mu);
            background: none;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .action-btn.edit:hover {
            color: var(--cyan);
            background: rgba(41, 231, 255, 0.1);
            border-color: rgba(41, 231, 255, 0.25);
        }

        .action-btn.delete:hover {
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.25);
        }

        .filter-input,
        .filter-select {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 0.7rem;
            padding: 0.6rem 0.9rem;
            font-size: 0.85rem;
            color: var(--text-hi);
            outline: none;
        }

        .filter-input::placeholder {
            color: var(--text-mu);
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: rgba(41, 231, 255, 0.5);
        }

        .filter-select option {
            background: #111827;
        }
    </style>

    <div class="admin-page max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="admin-header">
            <div class="flex items-center gap-4">
                <div class="admin-header-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <div
                        style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">
                        Access Control</div>
                    <div class="admin-header-title">Users</div>
                    <div style="margin-top:0.35rem; font-size:0.8rem; color:var(--text-mu);">
                        {{ $users->total() }} users registered
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.users.create') }}" class="btn-add">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </a>
        </div>

        @if (session('success'))
            <div
                class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="glass rounded-xl p-4 mb-5 flex flex-wrap gap-3"
            style="background:rgba(255,255,255,0.03); border:1px solid var(--glass-border); border-radius:0.75rem;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone..."
                class="filter-input" style="flex:1; min-width:220px;">
            <select name="role" class="filter-select" style="min-width:160px;">
                <option value="">All roles</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                        {{ $r->display_name ?? $r->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="filter-select" style="min-width:140px;">
                <option value="">All status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="filter-input"
                style="color:#29e7ff; background:rgba(41,231,255,0.12); border-color:rgba(41,231,255,0.32); cursor:pointer; font-weight:600;">
                Filter
            </button>
        </form>

        {{-- Table --}}
        <div class="admin-table-wrap">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:var(--text-hi);">{{ $user->name }}
                                            </div>
                                            <div style="font-size:0.72rem; color:var(--text-mu);">#{{ $user->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:0.82rem; color:#cbd5e1;">{{ $user->email }}</div>
                                    @if ($user->phone)
                                        <div style="font-size:0.72rem; color:var(--text-mu);">{{ $user->phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span
                                            class="role-badge {{ $role->name }}">{{ $role->display_name ?? $role->name }}</span>
                                    @empty
                                        <span style="color:var(--text-mu); font-size:0.75rem;">No role</span>
                                    @endforelse
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="status-dot {{ $user->status == 1 ? 'active' : 'inactive' }}"
                                            style="background:none; border:none; cursor:pointer;">
                                            {{ $user->status == 1 ? 'Active' : 'Deactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td style="font-size:0.78rem; color:var(--text-mu);">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="action-btn edit"
                                            title="Edit">
                                            <svg width="16" height="16" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Delete this user?');" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button class="action-btn delete" title="Delete">
                                                    <svg width="16" height="16" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:4rem 1rem; text-align:center; color:var(--text-mu);">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $users->links() }}</div>
    </div>
</x-app-layout>
