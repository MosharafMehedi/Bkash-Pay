@php $c = $charge ?? null; @endphp

<style>
    .dc-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .dc-title { font-size: 0.75rem; font-weight: 600; color: #29e7ff; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem; }
    .dc-label { display: block; font-size: 0.8rem; font-weight: 500; color: #cbd5e1; margin-bottom: 0.4rem; }
    .dc-label .req { color: #f87171; }
    .dc-input {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem;
        padding: 0.6rem 0.85rem; font-size: 0.875rem; color: #f1f5f9; outline: none;
    }
    .dc-input:focus { border-color: rgba(41,231,255,0.6); }
    .dc-hint { font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; }
    .dc-err { font-size: 0.7rem; color: #f87171; margin-top: 0.3rem; }
    .dc-input.is-invalid { border-color: #f87171; }
    .dc-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) { .dc-grid { grid-template-columns: repeat(2, 1fr); } }
    .dc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.55rem;
        font-weight: 600; font-size: 0.875rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa); border: none; cursor: pointer;
    }
    .dc-btn:hover { opacity: 0.9; }
    .dc-btn-sec {
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

<div class="dc-section">
    <div class="dc-title">City Details</div>

    <div class="dc-grid">
        <div>
            <label class="dc-label">City Name <span class="req">*</span></label>
            <input type="text" name="city" required
                   value="{{ old('city', $c->city ?? '') }}"
                   placeholder="e.g. Dhaka"
                   class="dc-input @error('city') is-invalid @enderror">
            @error('city') <div class="dc-err">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="dc-label">Delivery Charge (৳) <span class="req">*</span></label>
            <input type="number" step="0.01" min="0" name="charge" required
                   value="{{ old('charge', $c->charge ?? '') }}"
                   class="dc-input @error('charge') is-invalid @enderror">
            @error('charge') <div class="dc-err">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="dc-section">
    <div class="dc-title">Optional Settings</div>

    <div class="dc-grid">
        <div>
            <label class="dc-label">Free Above (৳)</label>
            <input type="number" step="0.01" min="0" name="free_above"
                   value="{{ old('free_above', $c->free_above ?? '') }}"
                   placeholder="Leave empty for no free threshold"
                   class="dc-input">
            <div class="dc-hint">Order amount er upor free hobe. Jemon 1000 dile 1000৳+ order free.</div>
        </div>

        <div>
            <label class="dc-label">Estimated Days</label>
            <input type="number" min="0" max="30" name="estimated_days"
                   value="{{ old('estimated_days', $c->estimated_days ?? '') }}"
                   placeholder="e.g. 2"
                   class="dc-input">
            <div class="dc-hint">Approximate delivery time.</div>
        </div>
    </div>
</div>

<div class="dc-section">
    <div class="dc-title">Status</div>
    <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $c->is_active ?? true) ? 'checked' : '' }}
               style="width:16px; height:16px; accent-color:#29e7ff;">
        <span style="font-size:0.875rem; color:#cbd5e1;">Active (show in checkout)</span>
    </label>
</div>

<div style="display:flex; gap:0.5rem;">
    <button type="submit" class="dc-btn" style="flex:1;">
        {{ $c ? 'Update Charge' : 'Create Charge' }}
    </button>
    <a href="{{ route('admin.delivery-charges.index') }}" class="dc-btn-sec">Cancel</a>
</div>