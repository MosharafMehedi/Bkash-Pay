@php $p = $product ?? null; @endphp

<style>
    .form-shell { --cyan: #29e7ff; }

    .fsection {
        padding: 1.25rem 1.5rem;
        border-radius: 0.75rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        margin: 3px 0px;
    }

    .fsection-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--cyan);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 1rem;
    }

    .ffield label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #cbd5e1;
        margin-bottom: 0.4rem;
    }
    .ffield label .req { color: #f87171; }

    .finput, .ftextarea {
        width: 100%;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.5rem;
        padding: 0.6rem 0.85rem;
        font-size: 0.875rem;
        color: #f1f5f9;
        outline: none;
        transition: border-color 0.15s;
    }
    .finput::placeholder, .ftextarea::placeholder { color: #64748b; }
    .finput:focus, .ftextarea:focus { border-color: rgba(41,231,255,0.6); }
    .ftextarea { resize: vertical; min-height: 90px; }

    .finput.is-invalid, .ftextarea.is-invalid { border-color: #f87171; }

    .fhint { font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; }
    .ferror { font-size: 0.7rem; color: #f87171; margin-top: 0.3rem; }

    .fgrid-2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    .fgrid-3 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    @media (min-width: 640px) {
        .fgrid-2 { grid-template-columns: repeat(2, 1fr); }
        .fgrid-3 { grid-template-columns: repeat(3, 1fr); }
    }

    /* Custom file input */
    .ffile {
        display: block;
        width: 100%;
        padding: 0.6rem 0.85rem;
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.1);
        font-size: 0.8rem;
        color: #94a3b8;
        cursor: pointer;
    }
    .ffile::file-selector-button {
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
    .ffile::file-selector-button:hover { background: rgba(41,231,255,0.25); }

    /* Image preview — fixed compact size */
    .fimg-preview {
        width: 120px;
        height: 120px;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.03);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 0.7rem;
        margin-bottom: 0.75rem;
        flex-shrink: 0;
    }
    .fimg-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Buttons */
    .fbtn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.65rem 1.25rem;
        border-radius: 0.55rem;
        font-weight: 600;
        font-size: 0.875rem;
        color: #06050c;
        background: linear-gradient(135deg, #29e7ff, #a78bfa);
        border: none;
        cursor: pointer;
        transition: opacity 0.15s;
    }
    .fbtn-primary:hover { opacity: 0.9; }
    .fbtn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.65rem 1.25rem;
        border-radius: 0.55rem;
        font-weight: 500;
        font-size: 0.875rem;
        color: #cbd5e1;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
        transition: background 0.15s;
    }
    .fbtn-secondary:hover { background: rgba(255,255,255,0.1); }
</style>

<div class="form-shell grid gap-5 lg:grid-cols-3">

    {{-- LEFT --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Errors --}}
        @if ($errors->any())
            <div class="rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm px-4 py-3">
                <div class="font-semibold mb-1">Please fix the following:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Basic Info --}}
        <div class="fsection">
            <div class="fsection-title">Basic Information</div>

            <div class="space-y-4">
                <div class="ffield">
                    <label>Product Name <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}" required
                           placeholder="e.g. Premium Wireless Headphones"
                           class="finput @error('name') is-invalid @enderror">
                    @error('name') <div class="ferror">{{ $message }}</div> @enderror
                </div>

                <div class="ffield">
                    <label>Subtitle</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $p->subtitle ?? '') }}"
                           placeholder="Short tagline"
                           class="finput">
                </div>

                <div class="ffield">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Product details..."
                              class="ftextarea">{{ old('description', $p->description ?? '') }}</textarea>
                </div>

                <div class="fgrid-3">
                    <div class="ffield">
                        <label>Category</label>
                        <input type="text" name="category" value="{{ old('category', $p->category ?? '') }}"
                               placeholder="Audio" class="finput">
                    </div>
                    <div class="ffield">
                        <label>Brand</label>
                        <input type="text" name="brand" value="{{ old('brand', $p->brand ?? '') }}"
                               placeholder="Sony" class="finput">
                    </div>
                    <div class="ffield">
                        <label>SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $p->sku ?? '') }}"
                               placeholder="Auto" class="finput">
                    </div>
                </div>

                <div class="ffield">
                    <label>Tags</label>
                    <input type="text" name="tags"
                           value="{{ old('tags', $p && $p->tags ? implode(', ', $p->tags) : '') }}"
                           placeholder="new, hot, featured"
                           class="finput">
                    <div class="fhint">Separate with commas.</div>
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="fsection">
            <div class="fsection-title">Pricing</div>

            <div class="fgrid-3">
                <div class="ffield">
                    <label>Price BDT <span class="req">*</span></label>
                    <input type="number" step="0.01" name="price_bdt" required
                           value="{{ old('price_bdt', $p->price_bdt ?? '') }}"
                           placeholder="0.00"
                           class="finput @error('price_bdt') is-invalid @enderror">
                </div>
                <div class="ffield">
                    <label>Price USD <span class="req">*</span></label>
                    <input type="number" step="0.01" name="price_usd" required
                           value="{{ old('price_usd', $p->price_usd ?? '') }}"
                           placeholder="0.00"
                           class="finput @error('price_usd') is-invalid @enderror">
                </div>
                <div class="ffield">
                    <label>Discount price</label>
                    <input type="number" step="0.01" name="discount_price"
                           value="{{ old('discount_price', $p->discount_price ?? '') }}"
                           placeholder="Optional"
                           class="finput">
                </div>
            </div>
        </div>

        {{-- Inventory --}}
        <div class="fsection">
            <div class="fsection-title">Inventory</div>

            <div class="fgrid-2">
                <div class="ffield">
                    <label>Quantity <span class="req">*</span></label>
                    <input type="number" name="quantity" required min="0"
                           value="{{ old('quantity', $p->quantity ?? 0) }}"
                           class="finput">
                    <div class="fhint">Total units in storage.</div>
                </div>
                <div class="ffield">
                    <label>Available Stock <span class="req">*</span></label>
                    <input type="number" name="stock" required min="0"
                           value="{{ old('stock', $p->stock ?? 0) }}"
                           class="finput">
                    <div class="fhint">Units available for sale.</div>
                </div>
            </div>
        </div>

        {{-- Rating --}}
        <div class="fsection">
            <div class="fsection-title">Rating & Reviews</div>

            <div class="fgrid-2">
                <div class="ffield">
                    <label>Rating (0 – 5)</label>
                    <input type="number" step="0.01" min="0" max="5" name="rating"
                           value="{{ old('rating', $p->rating ?? 0) }}"
                           class="finput">
                </div>
                <div class="ffield">
                    <label>Review count</label>
                    <input type="number" min="0" name="review_count"
                           value="{{ old('review_count', $p->review_count ?? 0) }}"
                           class="finput">
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="fsection">
            <div class="fsection-title">SEO</div>

            <div class="space-y-4">
                <div class="ffield">
                    <label>Meta title</label>
                    <input type="text" name="meta_title"
                           value="{{ old('meta_title', $p->meta_title ?? '') }}"
                           placeholder="Product name — your brand"
                           class="finput">
                </div>
                <div class="ffield">
                    <label>Meta description</label>
                    <textarea name="meta_description" rows="2"
                              placeholder="Brief summary for search engines..."
                              class="ftextarea">{{ old('meta_description', $p->meta_description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="space-y-5">

        {{-- Status --}}
        <div class="fsection">
            <div class="fsection-title">Status</div>

            <div class="space-y-3">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $p->is_active ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 accent-cyan-500">
                    <span class="text-sm text-slate-300">Active</span>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1"
                           {{ old('is_featured', $p->is_featured ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 accent-cyan-500">
                    <span class="text-sm text-slate-300">Featured</span>
                </label>

                <div class="ffield">
                    <label>Publish date</label>
                    <input type="datetime-local" name="published_at"
                           value="{{ old('published_at', $p && $p->published_at ? $p->published_at->format('Y-m-d\TH:i') : '') }}"
                           class="finput">
                </div>
            </div>
        </div>

        {{-- Main Image --}}
        <div class="fsection">
            <div class="fsection-title">Main Image</div>

            <div class="fimg-preview" id="mainPreview">
                @if ($p && $p->image)
                    <img src="{{ Storage::url($p->image) }}" alt="preview">
                @else
                    <span>No image</span>
                @endif
            </div>

            <input type="file" id="mainImage" name="image" accept="image/*"
                   class="ffile" onchange="previewMain(this)">
            <div class="fhint">Square (1:1) · PNG/JPG · max 2 MB</div>
        </div>

        {{-- Gallery --}}
        <div class="fsection">
            <div class="fsection-title">Gallery</div>

            @if ($p && $p->gallery)
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach ($p->gallery as $img)
                        <div class="fimg-preview" style="margin-bottom:0;">
                            <img src="{{ Storage::url($img) }}" alt="">
                        </div>
                    @endforeach
                </div>
            @endif

            <input type="file" name="gallery[]" accept="image/*" multiple class="ffile">
            <div class="fhint">Multiple images allowed.</div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <button type="submit" class="fbtn-primary flex-1">
                {{ $p ? 'Update Product' : 'Create Product' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="fbtn-secondary">
                Cancel
            </a>
        </div>
    </div>
</div>

<script>
    function previewMain(input) {
        if (! input.files || ! input.files[0]) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('mainPreview').innerHTML =
                '<img src="' + e.target.result + '" alt="preview">';
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>