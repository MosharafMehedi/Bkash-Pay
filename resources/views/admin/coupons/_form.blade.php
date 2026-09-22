@php $c = $coupon ?? null; @endphp

<style>
    .fc-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .fc-title {
        font-size: 0.75rem; font-weight: 600; color: #29e7ff;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;
    }
    .fc-label { display: block; font-size: 0.8rem; font-weight: 500; color: #cbd5e1; margin-bottom: 0.4rem; }
    .fc-label .req { color: #f87171; }
    .fc-input, .fc-textarea, .fc-select {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem;
        padding: 0.6rem 0.85rem; font-size: 0.875rem; color: #f1f5f9;
        outline: none; transition: border-color 0.15s;
    }
    .fc-input:focus, .fc-textarea:focus, .fc-select:focus { border-color: rgba(41,231,255,0.6); }
    .fc-select option { background: #111827; }
    .fc-textarea { resize: vertical; min-height: 80px; }
    .fc-hint { font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; }
    .fc-err { font-size: 0.7rem; color: #f87171; margin-top: 0.3rem; }
    .fc-input.is-invalid { border-color: #f87171; }
    .fc-grid-2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    .fc-grid-3 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) {
        .fc-grid-2 { grid-template-columns: repeat(2, 1fr); }
        .fc-grid-3 { grid-template-columns: repeat(3, 1fr); }
    }
    .fc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.55rem;
        font-weight: 600; font-size: 0.875rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa);
        border: none; cursor: pointer;
    }
    .fc-btn:hover { opacity: 0.9; }
    .fc-btn-sec {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.55rem;
        font-weight: 500; font-size: 0.875rem; color: #cbd5e1;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
    }
</style>

@if ($errors->any())
    <div class="rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm px-4 py-3 mb-4">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- Basic --}}
<div class="fc-section">
    <div class="fc-title">Coupon Details</div>

    <div class="fc-grid-2" style="margin-bottom:1rem;">
        <div>
            <label class="fc-label">Code <span class="req">*</span></label>
            <input type="text" name="code" required
                   value="{{ old('code', $c->code ?? '') }}"
                   placeholder="e.g. SAVE10"
                   class="fc-input @error('code') is-invalid @enderror"
                   style="text-transform:uppercase; letter-spacing:0.05em; font-weight:600;">
            @error('code') <div class="fc-err">{{ $message }}</div> @enderror
            <div class="fc-hint">Uppercase letters — auto converted.</div>
        </div>
        <div>
            <label class="fc-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $c->name ?? '') }}"
                   placeholder="e.g. Summer Sale 2025"
                   class="fc-input">
        </div>
    </div>

    <div>
        <label class="fc-label">Description</label>
        <textarea name="description" rows="2" placeholder="What this coupon does..."
                  class="fc-textarea">{{ old('description', $c->description ?? '') }}</textarea>
    </div>
</div>

{{-- Discount --}}
<div class="fc-section">
    <div class="fc-title">Discount</div>

    <div class="fc-grid-3">
        <div>
            <label class="fc-label">Type <span class="req">*</span></label>
            <select name="type" required class="fc-select" id="couponType">
                <option value="fixed" {{ old('type', $c->type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed (৳)</option>
                <option value="percent" {{ old('type', $c->type ?? '') === 'percent' ? 'selected' : '' }}>Percent (%)</option>
            </select>
        </div>
        <div>
            <label class="fc-label">Value <span class="req">*</span></label>
            <input type="number" step="0.01" min="0" name="value" required
                   value="{{ old('value', $c->value ?? '') }}"
                   class="fc-input @error('value') is-invalid @enderror">
            @error('value') <div class="fc-err">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="fc-label">Max Discount (৳)</label>
            <input type="number" step="0.01" min="0" name="max_discount"
                   value="{{ old('max_discount', $c->max_discount ?? '') }}"
                   placeholder="Optional cap"
                   class="fc-input">
            <div class="fc-hint">Only for percent type.</div>
        </div>
    </div>

    <div style="margin-top:1rem;">
        <label class="fc-label">Minimum Order (৳)</label>
        <input type="number" step="0.01" min="0" name="min_order"
               value="{{ old('min_order', $c->min_order ?? 0) }}"
               class="fc-input">
    </div>
</div>

{{-- Usage --}}
<div class="fc-section">
    <div class="fc-title">Usage Limits</div>

    <div class="fc-grid-2">
        <div>
            <label class="fc-label">Total Usage Limit</label>
            <input type="number" min="1" name="usage_limit"
                   value="{{ old('usage_limit', $c->usage_limit ?? '') }}"
                   placeholder="Leave empty = unlimited"
                   class="fc-input">
        </div>
        <div>
            <label class="fc-label">Per-User Limit</label>
            <input type="number" min="1" name="per_user_limit"
                   value="{{ old('per_user_limit', $c->per_user_limit ?? 1) }}"
                   class="fc-input">
        </div>
    </div>
</div>

{{-- Schedule + Status --}}
<div class="fc-section">
    <div class="fc-title">Schedule &amp; Status</div>

    <div class="fc-grid-2" style="margin-bottom:1rem;">
        <div>
            <label class="fc-label">Starts At</label>
            <input type="datetime-local" name="starts_at"
                   value="{{ old('starts_at', $c && $c->starts_at ? $c->starts_at->format('Y-m-d\TH:i') : '') }}"
                   class="fc-input">
        </div>
        <div>
            <label class="fc-label">Expires At</label>
            <input type="datetime-local" name="expires_at"
                   value="{{ old('expires_at', $c && $c->expires_at ? $c->expires_at->format('Y-m-d\TH:i') : '') }}"
                   class="fc-input">
        </div>
    </div>

    <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $c->is_active ?? true) ? 'checked' : '' }}
               style="width:16px; height:16px; accent-color:#29e7ff;">
        <span style="font-size:0.875rem; color:#cbd5e1;">Active</span>
    </label>
</div>

<div style="display:flex; gap:0.5rem;">
    <button type="submit" class="fc-btn" style="flex:1;">
        {{ $c ? 'Update Coupon' : 'Create Coupon' }}
    </button>
    <a href="{{ route('admin.coupons.index') }}" class="fc-btn-sec">Cancel</a>
</div>