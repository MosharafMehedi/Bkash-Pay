@php
    $r = $role ?? null;
    $selected = $rolePermissions ?? old('permissions', []);
    $isAdmin = $r && $r->name === 'admin';
@endphp

<style>
    .rf-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .rf-title {
        font-size: 0.75rem; font-weight: 600; color: #29e7ff;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;
    }
    .rf-label { display: block; font-size: 0.8rem; font-weight: 500; color: #cbd5e1; margin-bottom: 0.4rem; }
    .rf-label .req { color: #f87171; }
    .rf-input, .rf-textarea {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem;
        padding: 0.6rem 0.85rem; font-size: 0.875rem; color: #f1f5f9; outline: none;
    }
    .rf-input:focus, .rf-textarea:focus { border-color: rgba(41,231,255,0.6); }
    .rf-textarea { resize: vertical; min-height: 60px; }

    /* Permission group card */
    .perm-group {
        padding: 1rem 1.15rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.07);
        margin-bottom: 0.75rem;
    }
    .perm-group-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 0.85rem; padding-bottom: 0.6rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .perm-group-title {
        display: flex; align-items: center; gap: 0.6rem;
        font-size: 0.85rem; font-weight: 700; color: #f1f0fb; text-transform: capitalize;
    }
    .perm-group-count {
        font-size: 0.68rem; padding: 0.15rem 0.5rem; border-radius: 999px;
        background: rgba(41,231,255,0.12); color: #29e7ff; border: 1px solid rgba(41,231,255,0.25);
    }
    .perm-toggle-all {
        font-size: 0.7rem; color: #29e7ff; background: none; border: none;
        cursor: pointer; text-decoration: underline; padding: 0;
    }

    .perm-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem;
    }
    .perm-item {
        display: flex; align-items: center; gap: 0.55rem;
        padding: 0.5rem 0.75rem; border-radius: 0.5rem;
        background: rgba(255,255,255,0.025); border: 1.5px solid rgba(255,255,255,0.06);
        cursor: pointer; transition: all 0.15s;
    }
    .perm-item:hover { border-color: rgba(41,231,255,0.35); background: rgba(41,231,255,0.03); }
    .perm-item.selected { border-color: #29e7ff; background: rgba(41,231,255,0.07); }
    .perm-item input { accent-color: #29e7ff; width: 15px; height: 15px; cursor: pointer; }
    .perm-item-name {
        font-family: monospace; font-size: 0.75rem; color: #cbd5e1;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .perm-item.selected .perm-item-name { color: #f1f0fb; font-weight: 600; }

    .rf-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.75rem 1.5rem; border-radius: 0.55rem;
        font-weight: 600; font-size: 0.9rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa); border: none; cursor: pointer;
    }
    .rf-btn:hover { opacity: 0.9; }
    .rf-btn-sec {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.75rem 1.5rem; border-radius: 0.55rem;
        font-weight: 500; font-size: 0.9rem; color: #cbd5e1;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
    }
</style>

@if ($errors->any())
    <div class="rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm px-4 py-3 mb-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- Basic Info --}}
<div class="rf-section">
    <div class="rf-title">Role Information</div>

    <div style="display:grid; grid-template-columns:1fr; gap:1rem; margin-bottom:1rem;" class="sm:grid-cols-2">
        <div>
            <label class="rf-label">Role Name (slug) <span class="req">*</span></label>
            <input type="text" name="name" required
                   value="{{ old('name', $r->name ?? '') }}"
                   placeholder="e.g. manager"
                   {{ $isAdmin ? 'disabled' : '' }}
                   class="rf-input" style="font-family:monospace; text-transform:lowercase;">
            @if ($isAdmin)
                <p style="font-size:0.7rem; color:#fbbf24; margin-top:0.3rem;">System role name cannot be changed.</p>
            @else
                <p style="font-size:0.7rem; color:#64748b; margin-top:0.3rem;">Lowercase, underscores — e.g. <code>delivery_manager</code></p>
            @endif
        </div>
        <div>
            <label class="rf-label">Display Name <span class="req">*</span></label>
            <input type="text" name="display_name" required
                   value="{{ old('display_name', $r->display_name ?? '') }}"
                   placeholder="e.g. Delivery Manager"
                   class="rf-input">
        </div>
    </div>

    <div>
        <label class="rf-label">Description</label>
        <textarea name="description" class="rf-textarea"
                  placeholder="What this role can do...">{{ old('description', $r->description ?? '') }}</textarea>
    </div>
</div>

{{-- Permissions --}}
<div class="rf-section">
    <div class="rf-title">Permissions</div>

    @if ($isAdmin)
        <div style="padding:0.75rem 0.95rem; border-radius:0.6rem; margin-bottom:1rem; background:rgba(167,139,250,0.1); border:1px solid rgba(167,139,250,0.3); color:#c4b5fd; font-size:0.82rem;">
            ⚠️ Super Admin bypasses all permissions. Any selection below is only for reference.
        </div>
    @endif

    <div style="display:flex; gap:0.5rem; margin-bottom:1rem; flex-wrap:wrap;">
        <button type="button" onclick="selectAllPerms(true)" style="font-size:0.75rem; padding:0.4rem 0.75rem; border-radius:0.5rem; background:rgba(41,231,255,0.12); color:#29e7ff; border:1px solid rgba(41,231,255,0.3); cursor:pointer;">
            Select All
        </button>
        <button type="button" onclick="selectAllPerms(false)" style="font-size:0.75rem; padding:0.4rem 0.75rem; border-radius:0.5rem; background:rgba(255,255,255,0.05); color:#cbd5e1; border:1px solid rgba(255,255,255,0.1); cursor:pointer;">
            Clear All
        </button>
    </div>

    @foreach ($permissions as $group => $items)
        <div class="perm-group" data-group="{{ $group }}">
            <div class="perm-group-header">
                <div class="perm-group-title">
                    {{ str_replace('-', ' ', $group) }}
                    <span class="perm-group-count">{{ $items->count() }}</span>
                </div>
                <button type="button" class="perm-toggle-all" onclick="toggleGroup('{{ $group }}')">
                    Toggle all
                </button>
            </div>

            <div class="perm-grid">
                @foreach ($items as $permission)
                    @php $isChecked = in_array($permission->name, $selected); @endphp
                    <label class="perm-item {{ $isChecked ? 'selected' : '' }}"
                           x-data="{ on: {{ $isChecked ? 'true' : 'false' }} }"
                           :class="{ 'selected': on }">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->name }}"
                               {{ $isChecked ? 'checked' : '' }}
                               @change="on = $event.target.checked">
                        <span class="perm-item-name" title="{{ $permission->name }}">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<div style="display:flex; gap:0.5rem;">
    <button type="submit" class="rf-btn" style="flex:1;">
        {{ $r ? 'Update Role' : 'Create Role' }}
    </button>
    <a href="{{ route('admin.roles.index') }}" class="rf-btn-sec">Cancel</a>
</div>

<script>
    function selectAllPerms(select) {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
            cb.checked = select;
            cb.dispatchEvent(new Event('change'));
        });
    }

    function toggleGroup(group) {
        const container = document.querySelector(`[data-group="${group}"]`);
        const checkboxes = container.querySelectorAll('input[name="permissions[]"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(cb => {
            cb.checked = ! allChecked;
            cb.dispatchEvent(new Event('change'));
        });
    }
</script>