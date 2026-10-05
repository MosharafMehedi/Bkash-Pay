@php $c = $category ?? null; @endphp

<style>
    .fc-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .fc-title {
        font-size: 0.75rem; font-weight: 600; color: #29e7ff;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .fc-title::before {
        content: ''; width: 4px; height: 14px; border-radius: 2px;
        background: linear-gradient(180deg, #29e7ff, #a78bfa);
    }

    .fc-label {
        display: block; font-size: 0.8rem; font-weight: 500;
        color: #cbd5e1; margin-bottom: 0.4rem;
    }
    .fc-label .req { color: #f87171; }
    .fc-label .optional { color: #64748b; font-weight: 400; }

    .fc-input, .fc-textarea, .fc-select {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.6rem;
        padding: 0.65rem 0.9rem; font-size: 0.875rem; color: #f1f5f9;
        outline: none; transition: border-color 0.2s, background 0.2s;
        font-family: inherit;
    }
    .fc-input:focus, .fc-textarea:focus, .fc-select:focus {
        border-color: rgba(41,231,255,0.55);
        background: rgba(41,231,255,0.04);
    }
    .fc-input::placeholder, .fc-textarea::placeholder { color: #64748b; }
    .fc-textarea { resize: vertical; min-height: 80px; }
    .fc-select option { background: #111827; }
    .fc-input.is-invalid, .fc-textarea.is-invalid, .fc-select.is-invalid { border-color: #f87171; }

    .fc-hint { font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; }
    .fc-error { font-size: 0.7rem; color: #f87171; margin-top: 0.3rem; }

    .fc-grid-2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) { .fc-grid-2 { grid-template-columns: 1fr 1fr; } }

    /* Image uploader */
    .fc-image-wrap {
        display: flex; gap: 1rem; align-items: flex-start; flex-wrap: wrap;
    }
    .fc-image-preview {
        width: 120px; height: 120px;
        border-radius: 0.75rem; overflow: hidden;
        background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
        border: 1px solid rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; position: relative;
    }
    .fc-image-preview img { width: 100%; height: 100%; object-fit: cover; }
    .fc-image-preview .placeholder {
        font-size: 0.7rem; color: #64748b; text-align: center; padding: 0.5rem;
    }
    .fc-image-preview .icon-preview { font-size: 2rem; }
    .fc-image-controls { flex: 1; min-width: 220px; }

    .fc-file-input {
        display: block; width: 100%;
        font-size: 0.78rem; color: #94a3b8;
        padding: 0.5rem;
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        cursor: pointer;
    }
    .fc-file-input::file-selector-button {
        margin-right: 0.75rem;
        padding: 0.4rem 0.75rem;
        border-radius: 0.4rem;
        border: none;
        background: rgba(41,231,255,0.15);
        color: #29e7ff;
        font-weight: 600;
        font-size: 0.75rem;
        cursor: pointer;
    }
    .fc-file-input::file-selector-button:hover { background: rgba(41,231,255,0.25); }

    /* Buttons */
    .fc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.6rem;
        font-weight: 600; font-size: 0.875rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa);
        border: none; cursor: pointer;
        transition: transform 0.2s, filter 0.2s;
    }
    .fc-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }
    .fc-btn-sec {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.6rem;
        font-weight: 500; font-size: 0.875rem; color: #cbd5e1;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
    }
    .fc-btn-sec:hover { background: rgba(255,255,255,0.1); }

    /* Toggle */
    .fc-toggle-row {
        display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;
    }
    .fc-toggle {
        display: flex; align-items: center; gap: 0.6rem; cursor: pointer;
    }
    .fc-toggle input[type="checkbox"] {
        appearance: none; width: 36px; height: 20px;
        border-radius: 999px; background: rgba(148,163,184,0.3);
        position: relative; cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
        border: none;
        outline: none;
    }
    .fc-toggle input[type="checkbox"]::before {
        content: ''; position: absolute; top: 2px; left: 2px;
        width: 16px; height: 16px; border-radius: 50%;
        background: #fff;
        transition: transform 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .fc-toggle input[type="checkbox"]:checked {
        background: linear-gradient(135deg, #29e7ff, #a78bfa);
    }
    .fc-toggle input[type="checkbox"]:checked::before {
        transform: translateX(16px);
    }
    .fc-toggle-label { font-size: 0.85rem; color: #cbd5e1; font-weight: 500; }
</style>

@if ($errors->any())
    <div class="rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm px-4 py-3 mb-4">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- Basic Info --}}
<div class="fc-section">
    <div class="fc-title">Basic Information</div>

    <div class="fc-grid-2" style="margin-bottom: 1rem;">
        <div>
            <label class="fc-label">Category Name <span class="req">*</span></label>
            <input type="text" name="name" required
                   value="{{ old('name', $c->name ?? '') }}"
                   placeholder="e.g. Electronics"
                   class="fc-input @error('name') is-invalid @enderror"
                   oninput="generateSlug(this.value)">
            @error('name') <div class="fc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="fc-label">Slug</label>
            <input type="text" id="slugPreview" readonly
                   value="{{ old('slug', $c->slug ?? '') }}"
                   placeholder="auto-generated"
                   class="fc-input" style="font-family: monospace; background: rgba(255,255,255,0.02);">
            <div class="fc-hint">Auto-generated from name</div>
        </div>
    </div>

    <div style="margin-bottom: 1rem;">
        <label class="fc-label">Parent Category <span class="optional">(optional)</span></label>
        <select name="parent_id" class="fc-select @error('parent_id') is-invalid @enderror">
            <option value="">— None (Top-level category) —</option>
            @foreach ($parents as $parent)
                <option value="{{ $parent->id }}"
                        {{ old('parent_id', $c->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
        @error('parent_id') <div class="fc-error">{{ $message }}</div> @enderror
        <div class="fc-hint">Maximum 2 levels. Only parent categories can be selected.</div>
    </div>

    <div>
        <label class="fc-label">Description <span class="optional">(optional)</span></label>
        <textarea name="description" rows="3" class="fc-textarea @error('description') is-invalid @enderror"
                  placeholder="Brief description of this category...">{{ old('description', $c->description ?? '') }}</textarea>
        @error('description') <div class="fc-error">{{ $message }}</div> @enderror
    </div>
</div>

{{-- Media --}}
<div class="fc-section">
    <div class="fc-title">Media</div>

    <div class="fc-image-wrap">
        {{-- Preview --}}
        <div class="fc-image-preview" id="imagePreview">
            @if ($c && $c->hasImage())
                <img src="{{ $c->image_url }}" alt="{{ $c->name }}">
            @elseif ($c && $c->icon)
                <span class="icon-preview">{{ $c->icon }}</span>
            @else
                <div class="placeholder">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#64748b; margin: 0 auto 0.4rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    No image
                </div>
            @endif
        </div>

        {{-- Controls --}}
        <div class="fc-image-controls">
            <label class="fc-label">Upload Image</label>
            <input type="file" name="image" accept="image/*" class="fc-file-input" onchange="previewImage(this)">
            <div class="fc-hint">JPG, PNG, WEBP · Max 2 MB</div>

            @if ($c && $c->hasImage())
                <form method="POST" action="{{ route('admin.categories.remove-image', $c) }}" style="margin-top: 0.75rem;">
                    @csrf @method('DELETE')
                    <button type="submit" class="fc-btn-sec" style="padding: 0.5rem 0.9rem; font-size: 0.8rem; color: #f87171; border-color: rgba(239,68,68,0.3);"
                            onclick="return confirm('Remove this image?');">
                        Remove Image
                    </button>
                </form>
            @endif

            <div style="margin-top: 1rem;">
                <label class="fc-label">Icon <span class="optional">(emoji — optional)</span></label>
                <input type="text" name="icon" maxlength="10"
                       value="{{ old('icon', $c->icon ?? '') }}"
                       placeholder="e.g. 📱 🎧 👕"
                       class="fc-input" style="max-width: 120px; font-size: 1.2rem; text-align: center;">
                <div class="fc-hint">Single emoji shown if no image</div>
            </div>
        </div>
    </div>
</div>

{{-- Settings --}}
<div class="fc-section">
    <div class="fc-title">Settings</div>

    <div class="fc-toggle-row" style="margin-bottom: 1rem;">
        <label class="fc-toggle">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $c->is_active ?? true) ? 'checked' : '' }}>
            <span class="fc-toggle-label">Active</span>
        </label>

        <label class="fc-toggle">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1"
                   {{ old('is_featured', $c->is_featured ?? false) ? 'checked' : '' }}>
            <span class="fc-toggle-label">Featured</span>
        </label>
    </div>

    <div class="fc-grid-2">
        <div>
            <label class="fc-label">Sort Order</label>
            <input type="number" name="sort_order" min="0" max="9999"
                   value="{{ old('sort_order', $c->sort_order ?? 0) }}"
                   class="fc-input">
            <div class="fc-hint">Lower = appears first</div>
        </div>
    </div>
</div>

{{-- SEO --}}
<div class="fc-section">
    <div class="fc-title">SEO <span class="optional" style="text-transform:none; font-weight:400; font-size:0.7rem; color:#64748b;">(optional)</span></div>

    <div style="margin-bottom: 1rem;">
        <label class="fc-label">Meta Title</label>
        <input type="text" name="meta_title" maxlength="150"
               value="{{ old('meta_title', $c->meta_title ?? '') }}"
               placeholder="Page title for search engines"
               class="fc-input">
    </div>

    <div>
        <label class="fc-label">Meta Description</label>
        <textarea name="meta_description" rows="2" maxlength="300"
                  class="fc-textarea"
                  placeholder="Brief description for search engines...">{{ old('meta_description', $c->meta_description ?? '') }}</textarea>
    </div>
</div>

{{-- Actions --}}
<div style="display: flex; gap: 0.5rem;">
    <button type="submit" class="fc-btn" style="flex: 1;">
        {{ $c ? 'Update Category' : 'Create Category' }}
    </button>
    <a href="{{ route('admin.categories.index') }}" class="fc-btn-sec">Cancel</a>
</div>

<script>
    function generateSlug(value) {
        const slug = value.toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        const preview = document.getElementById('slugPreview');
        if (preview) preview.value = slug;
    }

    function previewImage(input) {
        if (! input.files || ! input.files[0]) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const wrap = document.getElementById('imagePreview');
            wrap.innerHTML = '<img src="' + e.target.result + '" alt="preview">';
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>