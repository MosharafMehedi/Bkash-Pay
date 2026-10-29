@php
    $p = $permission ?? null;
    $isEdit = $p !== null;
    // Pre-fill group/action if editing
    if ($isEdit) {
        [$groupPart, $actionPart] = array_pad(explode('.', $p->name, 2), 2, '');
    }
@endphp

<style>
    .pf-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .pf-title {
        font-size: 0.75rem; font-weight: 600; color: #29e7ff;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;
    }
    .pf-label { display: block; font-size: 0.8rem; font-weight: 500; color: #cbd5e1; margin-bottom: 0.4rem; }
    .pf-label .req { color: #f87171; }
    .pf-input, .pf-textarea, .pf-select {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem;
        padding: 0.6rem 0.85rem; font-size: 0.875rem; color: #f1f5f9;
        outline: none; font-family: inherit;
    }
    .pf-input:focus, .pf-textarea:focus, .pf-select:focus { border-color: rgba(41,231,255,0.6); }
    .pf-input.is-invalid { border-color: #f87171; }
    .pf-textarea { resize: vertical; min-height: 70px; }
    .pf-select option { background: #111827; }
    .pf-hint { font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; }
    .pf-err { font-size: 0.7rem; color: #f87171; margin-top: 0.3rem; }
    .pf-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) { .pf-grid { grid-template-columns: 1fr 1fr; } }
    .pf-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.55rem;
        font-weight: 600; font-size: 0.875rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa); border: none; cursor: pointer;
    }
    .pf-btn-sec {
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

<div class="pf-section">
    <div class="pf-title">Permission Details</div>

    @if ($isEdit)
        <div style="margin-bottom: 1rem;">
            <label class="pf-label">Full Name (locked)</label>
            <input type="text" value="{{ $p->name }}" disabled
                   class="pf-input" style="font-family: monospace; opacity: 0.7; cursor: not-allowed;">
            <div class="pf-hint">Permission name is immutable — routes reference it.</div>
        </div>
    @else
        <div class="pf-grid" style="margin-bottom: 1rem;">
            <div>
                <label class="pf-label">Group (module) <span class="req">*</span></label>
                <input type="text" name="group" required
                       value="{{ old('group') }}"
                       placeholder="e.g. product"
                       class="pf-input @error('group') is-invalid @enderror"
                       style="font-family: monospace;"
                       list="groupsList">
                <datalist id="groupsList">
                    @foreach ($groups as $g)
                        <option value="{{ $g }}">
                    @endforeach
                </datalist>
                <div class="pf-hint">Lowercase — e.g. <code>product</code>, <code>user</code></div>
                @error('group') <div class="pf-err">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="pf-label">Action <span class="req">*</span></label>
                <input type="text" name="name" required
                       value="{{ old('name') }}"
                       placeholder="e.g. view"
                       class="pf-input @error('name') is-invalid @enderror"
                       style="font-family: monospace;">
                <div class="pf-hint">e.g. <code>view</code>, <code>create</code>, <code>edit</code>, <code>delete</code></div>
                @error('name') <div class="pf-err">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="padding: 0.7rem 0.9rem; border-radius: 0.55rem; background: rgba(41,231,255,0.06); border: 1px solid rgba(41,231,255,0.25); font-size: 0.75rem; color: #cbd5e1; margin-bottom: 1rem;">
            ℹ️ Full permission name will be: <code style="color: #29e7ff;" id="fullNamePreview">product.view</code>
        </div>
    @endif

    <div style="margin-bottom: 1rem;">
        <label class="pf-label">Display Name <span class="req">*</span></label>
        <input type="text" name="display_name" required
               value="{{ old('display_name', $p->display_name ?? '') }}"
               placeholder="e.g. View Products"
               class="pf-input @error('display_name') is-invalid @enderror">
        @error('display_name') <div class="pf-err">{{ $message }}</div> @enderror
    </div>

    <div style="margin-bottom: 1rem;">
        <label class="pf-label">Description</label>
        <textarea name="description" rows="2"
                  placeholder="What this permission allows..."
                  class="pf-textarea">{{ old('description', $p->description ?? '') }}</textarea>
    </div>

    <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $p->is_active ?? true) ? 'checked' : '' }}
               style="width:16px; height:16px; accent-color:#29e7ff;">
        <span style="font-size:0.875rem; color:#cbd5e1;">Active</span>
    </label>
</div>

<div style="display:flex; gap:0.5rem;">
    <button type="submit" class="pf-btn" style="flex:1;">
        {{ $isEdit ? 'Update Permission' : 'Create Permission' }}
    </button>
    <a href="{{ route('admin.permissions.index') }}" class="pf-btn-sec">Cancel</a>
</div>

@if (! $isEdit)
    @push('scripts')
    <script>
        function updatePreview() {
            const group = document.querySelector('input[name="group"]').value.trim();
            const action = document.querySelector('input[name="name"]').value.trim();
            const preview = document.getElementById('fullNamePreview');

            if (preview) {
                preview.textContent = (group || 'group') + '.' + (action || 'action');
            }
        }

        document.querySelector('input[name="group"]').addEventListener('input', updatePreview);
        document.querySelector('input[name="name"]').addEventListener('input', updatePreview);
        updatePreview();
    </script>
    @endpush
@endif