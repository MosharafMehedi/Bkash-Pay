@php $a = $announcement ?? null; @endphp

<style>
    .fa-section {
        padding: 1.25rem 1.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
        margin-bottom: 1rem;
    }
    .fa-title {
        font-size: 0.75rem; font-weight: 600; color: #29e7ff;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .fa-title::before {
        content: ''; width: 4px; height: 14px; border-radius: 2px;
        background: linear-gradient(180deg, #29e7ff, #a78bfa);
    }

    .fa-label {
        display: block; font-size: 0.8rem; font-weight: 500;
        color: #cbd5e1; margin-bottom: 0.4rem;
    }
    .fa-label .req { color: #f87171; }
    .fa-label .optional { color: #64748b; font-weight: 400; }

    .fa-input, .fa-textarea, .fa-select {
        width: 100%; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 0.6rem;
        padding: 0.65rem 0.9rem; font-size: 0.875rem; color: #f1f5f9;
        outline: none; transition: border-color 0.2s;
        font-family: inherit;
    }
    .fa-input:focus, .fa-textarea:focus, .fa-select:focus {
        border-color: rgba(41,231,255,0.55);
        background: rgba(41,231,255,0.04);
    }
    .fa-input::placeholder, .fa-textarea::placeholder { color: #64748b; }
    .fa-textarea { resize: vertical; min-height: 70px; }
    .fa-select { cursor: pointer; }
    .fa-select option { background: #111827; color: #f1f5f9; }
    .fa-input.is-invalid { border-color: #f87171; }

    .fa-hint { font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; }
    .fa-error { font-size: 0.7rem; color: #f87171; margin-top: 0.3rem; }

    .fa-grid-2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) { .fa-grid-2 { grid-template-columns: 1fr 1fr; } }

    /* Preview */
    .fa-preview {
        padding: 0.85rem 1.25rem;
        border-radius: 0.6rem;
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        font-size: 0.85rem; font-weight: 600;
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .fa-preview-bar {
        background: linear-gradient(135deg, #29e7ff, #a78bfa);
    }
    .fa-preview-bar.cyan { background: linear-gradient(135deg, #29e7ff, #06b6d4); }
    .fa-preview-bar.violet { background: linear-gradient(135deg, #a78bfa, #8b5cf6); }
    .fa-preview-bar.pink { background: linear-gradient(135deg, #ff5fb0, #ec4899); }
    .fa-preview-bar.green { background: linear-gradient(135deg, #34d399, #10b981); }
    .fa-preview-bar.amber { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #06050c; }

    /* Emoji picker row */
    .fa-emoji-row {
        display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.5rem;
    }
    .fa-emoji-btn {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.15s;
    }
    .fa-emoji-btn:hover {
        background: rgba(41,231,255,0.1);
        border-color: rgba(41,231,255,0.4);
    }

    .fa-btn {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.6rem;
        font-weight: 600; font-size: 0.875rem; color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa);
        border: none; cursor: pointer;
        transition: transform 0.2s, filter 0.2s;
    }
    .fa-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }
    .fa-btn-sec {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.7rem 1.25rem; border-radius: 0.6rem;
        font-weight: 500; font-size: 0.875rem; color: #cbd5e1;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
    }
    .fa-btn-sec:hover { background: rgba(255,255,255,0.1); }
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
<div class="fa-section">
    <div class="fa-title">Content</div>

    <div style="margin-bottom: 1rem;">
        <label class="fa-label">Text <span class="req">*</span></label>
        <input type="text" name="text" required maxlength="255"
               value="{{ old('text', $a->text ?? '') }}"
               placeholder="e.g. Free shipping on orders ৳500+"
               class="fa-input @error('text') is-invalid @enderror"
               oninput="updatePreview()">
        @error('text') <div class="fa-error">{{ $message }}</div> @enderror
    </div>

    {{-- Live preview --}}
    <div style="margin-bottom: 1rem;">
        <label class="fa-label" style="font-size: 0.7rem; color: #64748b;">Live Preview</label>
        <div class="fa-preview fa-preview-bar" id="previewBar">
            <span id="previewIcon"></span>
            <span id="previewText">{{ old('text', $a->text ?? 'Your announcement text here') }}</span>
        </div>
    </div>

    <div class="fa-grid-2">
        <div>
            <label class="fa-label">Icon <span class="optional">(emoji)</span></label>
            <input type="text" name="icon" maxlength="10"
                   value="{{ old('icon', $a->icon ?? '') }}"
                   placeholder="🎉"
                   class="fa-input" id="iconInput" oninput="updatePreview()">

            <div class="fa-emoji-row">
                @foreach (['🎉', '🔥', '⚡', '💎', '🎁', '📢', '🚚', '💰', '⏰', '⭐'] as $emoji)
                    <button type="button" class="fa-emoji-btn" onclick="setEmoji('{{ $emoji }}')">
                        {{ $emoji }}
                    </button>
                @endforeach
            </div>
        </div>

        <div>
            <label class="fa-label">Background Color</label>
            <select name="bg_color" class="fa-select" id="bgColorSelect" onchange="updatePreview()">
                <option value="">— Default Gradient —</option>
                <option value="cyan" {{ old('bg_color', $a->bg_color ?? '') === 'cyan' ? 'selected' : '' }}>Cyan</option>
                <option value="violet" {{ old('bg_color', $a->bg_color ?? '') === 'violet' ? 'selected' : '' }}>Violet</option>
                <option value="pink" {{ old('bg_color', $a->bg_color ?? '') === 'pink' ? 'selected' : '' }}>Pink</option>
                <option value="green" {{ old('bg_color', $a->bg_color ?? '') === 'green' ? 'selected' : '' }}>Green</option>
                <option value="amber" {{ old('bg_color', $a->bg_color ?? '') === 'amber' ? 'selected' : '' }}>Amber</option>
            </select>
        </div>
    </div>
</div>

{{-- Link --}}
<div class="fa-section">
    <div class="fa-title">Link <span class="optional" style="font-size:0.7rem; font-weight:400; text-transform:none; color:#64748b;">(optional)</span></div>

    <label class="fa-label">URL</label>
    <input type="text" name="link" maxlength="500"
           value="{{ old('link', $a->link ?? '') }}"
           placeholder="https://example.com/sale"
           class="fa-input">
    <div class="fa-hint">Click on announcement will open this link. Leave empty for no link.</div>
</div>

{{-- Settings --}}
<div class="fa-section">
    <div class="fa-title">Settings</div>

    <div class="fa-grid-2" style="margin-bottom: 1rem;">
        <div>
            <label class="fa-label">Sort Order</label>
            <input type="number" name="sort_order" min="0" max="9999"
                   value="{{ old('sort_order', $a->sort_order ?? 0) }}"
                   class="fa-input">
            <div class="fa-hint">Lower = shows first</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.6rem; padding-top: 1.5rem;">
            <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $a->is_active ?? true) ? 'checked' : '' }}
                       style="width:16px; height:16px; accent-color:#29e7ff;">
                <span style="font-size:0.875rem; color:#cbd5e1;">Active</span>
            </label>

            <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer;">
                <input type="hidden" name="is_dismissible" value="0">
                <input type="checkbox" name="is_dismissible" value="1"
                       {{ old('is_dismissible', $a->is_dismissible ?? true) ? 'checked' : '' }}
                       style="width:16px; height:16px; accent-color:#29e7ff;">
                <span style="font-size:0.875rem; color:#cbd5e1;">Dismissible</span>
            </label>
        </div>
    </div>

    <div class="fa-grid-2">
        <div>
            <label class="fa-label">Start Date <span class="optional">(optional)</span></label>
            <input type="datetime-local" name="starts_at"
                   value="{{ old('starts_at', $a && $a->starts_at ? $a->starts_at->format('Y-m-d\TH:i') : '') }}"
                   class="fa-input">
        </div>
        <div>
            <label class="fa-label">End Date <span class="optional">(optional)</span></label>
            <input type="datetime-local" name="ends_at"
                   value="{{ old('ends_at', $a && $a->ends_at ? $a->ends_at->format('Y-m-d\TH:i') : '') }}"
                   class="fa-input">
        </div>
    </div>
</div>

{{-- Actions --}}
<div style="display: flex; gap: 0.5rem;">
    <button type="submit" class="fa-btn" style="flex: 1;">
        {{ $a ? 'Update Announcement' : 'Create Announcement' }}
    </button>
    <a href="{{ route('admin.announcements.index') }}" class="fa-btn-sec">Cancel</a>
</div>

<script>
    function setEmoji(emoji) {
        document.getElementById('iconInput').value = emoji;
        updatePreview();
    }

    function updatePreview() {
        const text = document.querySelector('input[name="text"]').value || 'Your announcement text here';
        const icon = document.getElementById('iconInput').value || '';
        const bgColor = document.getElementById('bgColorSelect').value;

        document.getElementById('previewText').textContent = text;
        document.getElementById('previewIcon').textContent = icon;

        const bar = document.getElementById('previewBar');
        bar.className = 'fa-preview fa-preview-bar';
        if (bgColor) {
            bar.classList.add(bgColor);
        }
    }

    // Init
    updatePreview();
</script>