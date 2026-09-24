@php $u = $user ?? null; $isSelf = $u && $u->id === auth()->id(); @endphp

<style>
    .uf-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .uf-title {
        font-size: 0.75rem; font-weight: 600; color: #29e7ff;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;
    }
    .uf-label { display: block; font-size: 0.8rem; font-weight: 500; color: #cbd5e1; margin-bottom: 0.4rem; }
    .uf-label .req { color: #f87171; }
    .uf-input, .uf-textarea {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem;
        padding: 0.6rem 0.85rem; font-size: 0.875rem; color: #f1f5f9;
        outline: none;
    }
    .uf-input:focus, .uf-textarea:focus { border-color: rgba(41,231,255,0.6); }
    .uf-textarea { resize: vertical; min-height: 70px; }
    .uf-grid-2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) { .uf-grid-2 { grid-template-columns: repeat(2, 1fr); } }

    .uf-role-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0.6rem;
    }
    .uf-role-card {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.7rem 0.9rem; border-radius: 0.6rem;
        background: rgba(255,255,255,0.03); border: 1.5px solid rgba(255,255,255,0.08);
        cursor: pointer; transition: all 0.2s;
    }
    .uf-role-card:hover { border-color: rgba(41,231,255,0.4); }
    .uf-role-card input { accent-color: #29e7ff; width: 16px; height: 16px; }
    .uf-role-card.selected { border-color: #29e7ff; background: rgba(41,231,255,0.06); }
    .uf-role-name { font-size: 0.82rem; font-weight: 600; color: #f1f0fb; }

    .uf-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.55rem;
        font-weight: 600; font-size: 0.875rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa); border: none; cursor: pointer;
    }
    .uf-btn:hover { opacity: 0.9; }
    .uf-btn-sec {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.55rem;
        font-weight: 500; font-size: 0.875rem; color: #cbd5e1;
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
<div class="uf-section">
    <div class="uf-title">Basic Information</div>

    <div class="uf-grid-2" style="margin-bottom:1rem;">
        <div>
            <label class="uf-label">Name <span class="req">*</span></label>
            <input type="text" name="name" required value="{{ old('name', $u->name ?? '') }}" class="uf-input">
        </div>
        <div>
            <label class="uf-label">Email <span class="req">*</span></label>
            <input type="email" name="email" required value="{{ old('email', $u->email ?? '') }}" class="uf-input">
        </div>
    </div>

    <div class="uf-grid-2">
        <div>
            <label class="uf-label">Password {{ $u ? '' : '*' }}</label>
            <input type="password" name="password" {{ $u ? '' : 'required' }} class="uf-input"
                   placeholder="{{ $u ? 'Leave empty to keep current' : 'Minimum 8 characters' }}">
        </div>
        <div>
            <label class="uf-label">Confirm Password {{ $u ? '' : '*' }}</label>
            <input type="password" name="password_confirmation" {{ $u ? '' : 'required' }} class="uf-input">
        </div>
    </div>
</div>

{{-- Contact --}}
<div class="uf-section">
    <div class="uf-title">Contact Information</div>

    <div class="uf-grid-2" style="margin-bottom:1rem;">
        <div>
            <label class="uf-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $u->phone ?? '') }}" class="uf-input">
        </div>
        <div>
            <label class="uf-label">City</label>
            <input type="text" name="city" value="{{ old('city', $u->city ?? '') }}" class="uf-input">
        </div>
    </div>

    <div style="margin-bottom:1rem;">
        <label class="uf-label">Address</label>
        <textarea name="address" class="uf-textarea">{{ old('address', $u->address ?? '') }}</textarea>
    </div>

    <div>
        <label class="uf-label">Postal Code</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $u->postal_code ?? '') }}" class="uf-input" style="max-width:200px;">
    </div>
</div>

{{-- Roles --}}
<div class="uf-section">
    <div class="uf-title">Roles {{ $isSelf ? '(locked for self)' : '*' }}</div>

    @if ($isSelf)
        <p style="font-size:0.78rem; color:#fbbf24; margin-bottom:0.75rem;">
            ⚠️ You cannot change your own roles.
        </p>
    @endif

    <div class="uf-role-grid">
        @foreach ($roles as $role)
            @php
                $checked = $u
                    ? $u->hasRole($role->name)
                    : old('roles') && in_array($role->name, old('roles', []));
            @endphp
            <label class="uf-role-card {{ $checked ? 'selected' : '' }}"
                   x-data="{ on: {{ $checked ? 'true' : 'false' }} }"
                   :class="{ 'selected': on }">
                <input type="checkbox"
                       name="roles[]"
                       value="{{ $role->name }}"
                       {{ $checked ? 'checked' : '' }}
                       {{ $isSelf ? 'disabled' : '' }}
                       @change="on = $event.target.checked">
                <div>
                    <div class="uf-role-name">{{ $role->display_name ?? $role->name }}</div>
                    <div style="font-size:0.68rem; color:var(--text-mu);">
                        {{ $role->permissions->count() }} permissions
                    </div>
                </div>
            </label>
        @endforeach
    </div>
</div>
{{-- Status --}}
<div class="uf-section">
    <div class="uf-title">Status</div>

    <label class="uf-label">Account Status</label>

    <select name="status" class="uf-input" style="max-width:250px;">
        <option value="1"
            {{ old('status', $u->status ?? 1) == 1 ? 'selected' : '' }}>
            Active
        </option>

        <option value="2"
            {{ old('status', $u->status ?? 1) == 2 ? 'selected' : '' }}>
            Deactive
        </option>
    </select>
</div>

<div style="display:flex; gap:0.5rem;">
    <button type="submit" class="uf-btn" style="flex:1;">
        {{ $u ? 'Update User' : 'Create User' }}
    </button>
    <a href="{{ route('admin.users.index') }}" class="uf-btn-sec">Cancel</a>
</div>