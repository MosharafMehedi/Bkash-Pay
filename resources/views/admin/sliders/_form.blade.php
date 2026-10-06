@php $s = $slider ?? null; @endphp

<style>
    .fs-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .fs-title {
        font-size: 0.75rem; font-weight: 600; color: #29e7ff;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .fs-title::before {
        content: ''; width: 4px; height: 14px; border-radius: 2px;
        background: linear-gradient(180deg, #29e7ff, #a78bfa);
    }

    .fs-label {
        display: block; font-size: 0.8rem; font-weight: 500;
        color: #cbd5e1; margin-bottom: 0.4rem;
    }
    .fs-label .req { color: #f87171; }
    .fs-label .optional { color: #64748b; font-weight: 400; }

    .fs-input, .fs-textarea, .fs-select {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.6rem;
        padding: 0.65rem 0.9rem; font-size: 0.875rem; color: #f1f5f9;
        outline: none; transition: border-color 0.2s, background 0.2s;
        font-family: inherit;
    }
    .fs-input:focus, .fs-textarea:focus, .fs-select:focus {
        border-color: rgba(41,231,255,0.55);
        background: rgba(41,231,255,0.04);
    }
    .fs-input::placeholder, .fs-textarea::placeholder { color: #64748b; }
    .fs-textarea { resize: vertical; min-height: 70px; }
    .fs-select { cursor: pointer; }
    .fs-select option { background: #111827; color: #f1f5f9; }
    .fs-input.is-invalid, .fs-textarea.is-invalid, .fs-select.is-invalid { border-color: #f87171; }

    .fs-hint { font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; }
    .fs-error { font-size: 0.7rem; color: #f87171; margin-top: 0.3rem; }

    .fs-grid-2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    .fs-grid-3 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) {
        .fs-grid-2 { grid-template-columns: 1fr 1fr; }
        .fs-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    }

    /* Image preview */
    .fs-img-wrap { display: flex; gap: 1rem; flex-wrap: wrap; }
    .fs-img-preview {
        width: 160px; height: 100px;
        border-radius: 0.6rem; overflow: hidden;
        background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.08));
        border: 1px solid rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .fs-img-preview img { width: 100%; height: 100%; object-fit: cover; }
    .fs-img-preview .placeholder { font-size: 0.7rem; color: #64748b; }

    .fs-file {
        display: block; width: 100%;
        padding: 0.6rem 0.85rem; border-radius: 0.5rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        font-size: 0.8rem; color: #94a3b8;
        cursor: pointer;
    }
    .fs-file::file-selector-button {
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
    .fs-file::file-selector-button:hover { background: rgba(41,231,255,0.25); }

    .fs-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.6rem;
        font-weight: 600; font-size: 0.875rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa);
        border: none; cursor: pointer;
        transition: transform 0.2s, filter 0.2s;
    }
    .fs-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }
    .fs-btn-sec {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.6rem;
        font-weight: 500; font-size: 0.875rem; color: #cbd5e1;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
    }
    .fs-btn-sec:hover { background: rgba(255,255,255,0.1); }
</style>

@if ($errors->any())
    <div class="rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm px-4 py-3 mb-4">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- Content --}}
<div class="fs-section">
    <div class="fs-title">Content</div>

    <div class="fs-grid-2" style="margin-bottom: 1rem;">
        <div>
            <label class="fs-label">Title <span class="req">*</span></label>
            <input type="text" name="title" required maxlength="150"
                   value="{{ old('title', $s->title ?? '') }}"
                   placeholder="e.g. Mega Sale 50% OFF"
                   class="fs-input @error('title') is-invalid @enderror">
            @error('title') <div class="fs-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="fs-label">Subtitle <span class="optional">(optional)</span></label>
            <input type="text" name="subtitle" maxlength="255"
                   value="{{ old('subtitle', $s->subtitle ?? '') }}"
                   placeholder="e.g. Limited time offer"
                   class="fs-input">
        </div>
    </div>

    <div class="fs-grid-3">
        <div>
            <label class="fs-label">Badge Text <span class="optional">(optional)</span></label>
            <input type="text" name="badge_text" maxlength="50"
                   value="{{ old('badge_text', $s->badge_text ?? '') }}"
                   placeholder="e.g. NEW, HOT"
                   class="fs-input">
        </div>

        <div>
            <label class="fs-label">Badge Color</label>
            <select name="badge_color" class="fs-select">
                <option value="">— None —</option>
                @foreach (['cyan' => 'Cyan', 'violet' => 'Violet', 'pink' => 'Pink', 'green' => 'Green', 'amber' => 'Amber'] as $key => $label)
                    <option value="{{ $key }}" {{ old('badge_color', $s->badge_color ?? '') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="fs-label">Text Position</label>
            <select name="text_position" class="fs-select" required>
                <option value="left" {{ old('text_position', $s->text_position ?? 'left') === 'left' ? 'selected' : '' }}>Left</option>
                <option value="center" {{ old('text_position', $s->text_position ?? '') === 'center' ? 'selected' : '' }}>Center</option>
                <option value="right" {{ old('text_position', $s->text_position ?? '') === 'right' ? 'selected' : '' }}>Right</option>
            </select>
        </div>
    </div>
</div>

{{-- Media --}}
<div class="fs-section">
    <div class="fs-title">Media</div>

    {{-- Background Image --}}
    <div style="margin-bottom: 1.25rem;">
        <label class="fs-label">Background Image <span class="optional">(recommended: 1920×800)</span></label>
        <div class="fs-img-wrap">
            <div class="fs-img-preview" id="bgPreview">
                @if ($s && $s->hasBgImage())
                    <img src="{{ $s->bg_image_url }}" alt="bg">
                @else
                    <span class="placeholder">No bg image</span>
                @endif
            </div>
            <div style="flex: 1; min-width: 220px;">
                <input type="file" name="bg_image" accept="image/*"
                       class="fs-file" onchange="previewImage(this, 'bgPreview')">
                <div class="fs-hint">Full-width background for slider</div>

                @if ($s && $s->hasBgImage())
                    <button type="button" class="fs-btn-sec" style="padding: 0.45rem 0.85rem; font-size: 0.75rem; color: #f87171; margin-top: 0.5rem;"
                            onclick="if(confirm('Remove bg image?')) document.getElementById('removeBgForm').submit();">
                        Remove bg image
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Product Image --}}
    <div>
        <label class="fs-label">Product / Feature Image <span class="optional">(optional)</span></label>
        <div class="fs-img-wrap">
            <div class="fs-img-preview" id="imgPreview">
                @if ($s && $s->hasImage())
                    <img src="{{ $s->image_url }}" alt="img">
                @else
                    <span class="placeholder">No image</span>
                @endif
            </div>
            <div style="flex: 1; min-width: 220px;">
                <input type="file" name="image" accept="image/*"
                       class="fs-file" onchange="previewImage(this, 'imgPreview')">
                <div class="fs-hint">Overlay image on right side</div>

                @if ($s && $s->hasImage())
                    <button type="button" class="fs-btn-sec" style="padding: 0.45rem 0.85rem; font-size: 0.75rem; color: #f87171; margin-top: 0.5rem;"
                            onclick="if(confirm('Remove image?')) document.getElementById('removeImgForm').submit();">
                        Remove image
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- CTA Link --}}
<div class="fs-section">
    <div class="fs-title">Call to Action</div>

    <div class="fs-grid-3">
        <div>
            <label class="fs-label">Button Text</label>
            <input type="text" name="button_text" maxlength="50"
                   value="{{ old('button_text', $s->button_text ?? '') }}"
                   placeholder="e.g. Shop Now"
                   class="fs-input">
        </div>

        <div>
            <label class="fs-label">Link Type <span class="req">*</span></label>
            <select name="link_type" class="fs-select" required id="linkTypeSelect" onchange="toggleLinkValue()">
                <option value="product" {{ old('link_type', $s->link_type ?? '') === 'product' ? 'selected' : '' }}>Product</option>
                <option value="category" {{ old('link_type', $s->link_type ?? '') === 'category' ? 'selected' : '' }}>Category</option>
                <option value="custom" {{ old('link_type', $s->link_type ?? '') === 'custom' ? 'selected' : '' }}>Custom Path</option>
                <option value="url" {{ old('link_type', $s->link_type ?? 'url') === 'url' ? 'selected' : '' }}>Full URL</option>
            </select>
        </div>

        <div>
            <label class="fs-label">Link Value <span class="req">*</span></label>

            {{-- Product select --}}
            <select name="link_value" class="fs-select" id="productSelect" style="display:none;">
                <option value="">— Select Product —</option>
                @foreach ($products as $product)
                    <option value="{{ $product->slug }}"
                            {{ old('link_value', $s->link_value ?? '') === $product->slug ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>

            {{-- Category select --}}
            <select name="link_value" class="fs-select" id="categorySelect" style="display:none;">
                <option value="">— Select Category —</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}"
                            {{ old('link_value', $s->link_value ?? '') === $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            {{-- Custom/URL text input --}}
            <input type="text" name="link_value" id="urlInput"
                   value="{{ old('link_value', $s->link_value ?? '') }}"
                   placeholder="/path or https://..."
                   class="fs-input">
        </div>
    </div>

    @error('link_value') <div class="fs-error">{{ $message }}</div> @enderror
</div>

{{-- Settings --}}
<div class="fs-section">
    <div class="fs-title">Settings</div>

    <div class="fs-grid-3">
        <div>
            <label class="fs-label">Sort Order</label>
            <input type="number" name="sort_order" min="0" max="9999"
                   value="{{ old('sort_order', $s->sort_order ?? 0) }}"
                   class="fs-input">
            <div class="fs-hint">Lower = shows first</div>
        </div>

        <div>
            <label class="fs-label">Start Date <span class="optional">(optional)</span></label>
            <input type="datetime-local" name="starts_at"
                   value="{{ old('starts_at', $s && $s->starts_at ? $s->starts_at->format('Y-m-d\TH:i') : '') }}"
                   class="fs-input">
        </div>

        <div>
            <label class="fs-label">End Date <span class="optional">(optional)</span></label>
            <input type="datetime-local" name="ends_at"
                   value="{{ old('ends_at', $s && $s->ends_at ? $s->ends_at->format('Y-m-d\TH:i') : '') }}"
                   class="fs-input">
        </div>
    </div>

    <label class="flex items-center gap-2.5 cursor-pointer" style="margin-top: 1rem;">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $s->is_active ?? true) ? 'checked' : '' }}
               style="width:16px; height:16px; accent-color:#29e7ff;">
        <span style="font-size:0.875rem; color:#cbd5e1;">Active (show on homepage)</span>
    </label>
</div>

{{-- Actions --}}
<div style="display: flex; gap: 0.5rem;">
    <button type="submit" class="fs-btn" style="flex: 1;">
        {{ $s ? 'Update Slider' : 'Create Slider' }}
    </button>
    <a href="{{ route('admin.sliders.index') }}" class="fs-btn-sec">Cancel</a>
</div>

{{-- Hidden remove forms (only if edit) --}}
@if ($s)
    <form method="POST" action="{{ route('admin.sliders.remove-image', ['slider' => $s, 'field' => 'image']) }}"
          id="removeImgForm" style="display:none;">
        @csrf @method('DELETE')
    </form>

    <form method="POST" action="{{ route('admin.sliders.remove-image', ['slider' => $s, 'field' => 'bg_image']) }}"
          id="removeBgForm" style="display:none;">
        @csrf @method('DELETE')
    </form>
@endif

<script>
    function toggleLinkValue() {
        const type = document.getElementById('linkTypeSelect').value;
        const productSelect = document.getElementById('productSelect');
        const categorySelect = document.getElementById('categorySelect');
        const urlInput = document.getElementById('urlInput');

        // Hide all first
        productSelect.style.display = 'none';
        categorySelect.style.display = 'none';
        urlInput.style.display = 'none';

        // Disable all inputs (so only active one submits)
        productSelect.disabled = true;
        categorySelect.disabled = true;
        urlInput.disabled = true;

        if (type === 'product') {
            productSelect.style.display = '';
            productSelect.disabled = false;
        } else if (type === 'category') {
            categorySelect.style.display = '';
            categorySelect.disabled = false;
        } else {
            urlInput.style.display = '';
            urlInput.disabled = false;
        }
    }

    function previewImage(input, previewId) {
        if (! input.files || ! input.files[0]) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById(previewId).innerHTML =
                '<img src="' + e.target.result + '" alt="preview">';
        };
        reader.readAsDataURL(input.files[0]);
    }

    // Init on page load
    toggleLinkValue();
</script>