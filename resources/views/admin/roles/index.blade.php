<x-app-layout>
    @section('title', 'Manage Roles')

    <style>
        .admin-page { --cyan:#29e7ff; --violet:#a78bfa; --text-hi:#f1f0fb; --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }
        .admin-header {
            position: relative; display: flex; flex-direction: column; gap: 1.25rem;
            padding: 1.5rem 1.75rem; margin-bottom: 1.5rem; border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(167,139,250,0.07), rgba(41,231,255,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px);
        }
        @media (min-width: 640px) { .admin-header { flex-direction: row; align-items: center; justify-content: space-between; } }
        .admin-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--violet), var(--cyan)); }
        .admin-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }
        .admin-header-icon {
            width: 48px; height: 48px; border-radius: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, rgba(167,139,250,0.2), rgba(41,231,255,0.18));
            border: 1px solid rgba(167,139,250,0.3); color: var(--violet);
        }
        .btn-add {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem; font-weight: 600; font-size: 0.85rem;
            color: #06050c; background: linear-gradient(135deg, #a78bfa, #29e7ff);
            text-decoration: none;
        }
        .btn-add:hover { filter: brightness(1.08); }

        .role-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
        .role-card {
            padding: 1.25rem; border-radius: 0.9rem;
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(14px); position: relative;
        }
        .role-card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem; }
        .role-name { font-family:'Sora',sans-serif; font-weight: 700; color: var(--text-hi); font-size: 1rem; }
        .role-slug { font-family: monospace; font-size: 0.7rem; color: var(--text-mu); margin-top: 0.15rem; }
        .role-desc { font-size: 0.82rem; color: var(--text-mu); margin-bottom: 1rem; min-height: 2.4rem; line-height: 1.5; }
        .role-stats { display: flex; gap: 1rem; font-size: 0.72rem; color: var(--text-mu); padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.06); }
        .role-stats strong { color: var(--text-hi); }
        .sys-badge {
            position: absolute; top: 0.75rem; right: 0.75rem;
            padding: 0.15rem 0.5rem; border-radius: 999px;
            font-size: 0.6rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;
            background: rgba(167,139,250,0.15); color: var(--violet); border: 1px solid rgba(167,139,250,0.35);
        }
        .role-actions { display: flex; gap: 0.4rem; margin-top: 0.75rem; }
        .r-btn {
            flex: 1; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600;
            text-align: center; text-decoration: none; border: 1px solid transparent; cursor: pointer;
        }
        .r-btn-edit { color: var(--cyan); background: rgba(41,231,255,0.1); border-color: rgba(41,231,255,0.25); }
        .r-btn-edit:hover { background: rgba(41,231,255,0.2); }
        .r-btn-del { color: #f87171; background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.2); }
        .r-btn-del:hover { background: rgba(239,68,68,0.15); }
    </style>

    <div class="admin-page max-w-7xl mx-auto">

        <div class="admin-header">
            <div class="flex items-center gap-4">
                <div class="admin-header-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--violet); font-weight:600;">Access Control</div>
                    <div class="admin-header-title">Roles & Permissions</div>
                    <div style="margin-top:0.35rem; font-size:0.8rem; color:var(--text-mu);">
                        {{ $roles->count() }} roles · Manage access dynamically
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.roles.create') }}" class="btn-add">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Create Role
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="role-grid">
            @foreach ($roles as $role)
                <div class="role-card">
                    @if ($role->is_system)
                        <span class="sys-badge">System</span>
                    @endif

                    <div class="role-card-header">
                        <div>
                            <div class="role-name">{{ $role->display_name ?? $role->name }}</div>
                            <div class="role-slug">{{ $role->name }}</div>
                        </div>
                    </div>

                    <div class="role-desc">{{ $role->description ?? 'No description.' }}</div>

                    <div class="role-stats">
                        <span>👥 <strong>{{ $role->users_count }}</strong> users</span>
                        <span>🔐 <strong>{{ $role->permissions_count }}</strong> permissions</span>
                    </div>

                    <div class="role-actions">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="r-btn r-btn-edit">Edit</a>
                        @if (! $role->is_system)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                  onsubmit="return confirm('Delete this role?');" style="flex:1;">
                                @csrf @method('DELETE')
                                <button class="r-btn r-btn-del" style="width:100%;">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>