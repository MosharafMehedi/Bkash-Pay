<x-app-layout>
    @section('title', 'Homepage Settings')

    <style>
        .hs-page { --cyan:#29e7ff; --violet:#a78bfa; --pink:#ff5fb0; --green:#34d399;
            --text-hi:#f1f0fb; --text-mu:#9a94b8; --glass:rgba(255,255,255,0.045); --glass-border:rgba(255,255,255,0.09); }

        .hs-header {
            position: relative; padding: 1.5rem 1.75rem; margin-bottom: 1.25rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border); backdrop-filter: blur(18px); overflow: hidden;
        }
        .hs-header::before { content:""; position:absolute; left:0; top:0; bottom:0; width:3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet)); }
        .hs-header-title { font-family:'Sora',sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--text-hi); }

        .hs-header-top { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }

        .hs-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.15rem; border-radius: 0.7rem;
            font-weight: 600; font-size: 0.85rem; color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            text-decoration: none; border: none; cursor: pointer;
            box-shadow: 0 10px 28px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .hs-btn:hover { transform: translateY(-1px); filter: brightness(1.08); }
        .hs-btn-danger {
            background: rgba(239,68,68,0.15);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.3);
            box-shadow: none;
        }
        .hs-btn-danger:hover { background: rgba(239,68,68,0.25); }

        /* Section card */
        .hs-section {
            padding: 1.5rem; border-radius: 0.95rem;
            background: var(--glass); border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            margin-bottom: 1rem;
        }

        .hs-section-head {
            display: flex; align-items: center; gap: 0.85rem;
            padding-bottom: 0.9rem;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 1.25rem;
        }
        .hs-section-icon {
            width: 38px; height: 38px;
            border-radius: 0.6rem;
            display: flex; align-items: center; justify-content: center;
            background: rgba(41,231,255,0.12);
            border: 1px solid rgba(41,231,255,0.28);
            color: var(--cyan);
            flex-shrink: 0;
        }
        .hs-section-icon.violet { background: rgba(167,139,250,0.12); border-color: rgba(167,139,250,0.28); color: var(--violet); }
        .hs-section-icon.pink   { background: rgba(255,95,176,0.12);  border-color: rgba(255,95,176,0.28);  color: var(--pink); }
        .hs-section-icon.green  { background: rgba(52,211,153,0.12);  border-color: rgba(52,211,153,0.28);  color: var(--green); }

        .hs-section-title {
            font-family: 'Sora', sans-serif;
            font-size: 1rem; font-weight: 700; color: var(--text-hi);
        }
        .hs-section-sub {
            font-size: 0.72rem; color: var(--text-mu); margin-top: 0.1rem;
        }

        /* Toggle row */
        .hs-toggle-row {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 1rem;
            border-radius: 0.7rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }
        .hs-toggle-row:hover {
            border-color: rgba(41,231,255,0.25);
            background: rgba(41,231,255,0.03);
        }
        .hs-toggle-label {
            font-size: 0.9rem; font-weight: 600; color: var(--text-hi);
        }
        .hs-toggle-desc {
            font-size: 0.72rem; color: var(--text-mu); margin-top: 0.1rem;
        }

        /* iOS-style toggle */
        .hs-switch {
            position: relative;
            display: inline-block;
            width: 44px; height: 24px;
            flex-shrink: 0;
        }
        .hs-switch input { opacity: 0; width: 0; height: 0; }
        .hs-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(148,163,184,0.3);
            border-radius: 999px;
            transition: 0.3s;
        }
        .hs-slider::before {
            content: '';
            position: absolute;
            height: 18px; width: 18px;
            left: 3px; bottom: 3px;
            background: #fff;
            border-radius: 50%;
            transition: 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .hs-switch input:checked + .hs-slider {
            background: linear-gradient(135deg, #29e7ff, #a78bfa);
        }
        .hs-switch input:checked + .hs-slider::before {
            transform: translateX(20px);
        }

        /* Input fields */
        .hs-grid-2 {
            display: grid; grid-template-columns: 1fr; gap: 1rem;
        }
        @media (min-width: 640px) { .hs-grid-2 { grid-template-columns: 1fr 1fr; } }
        .hs-grid-3 {
            display: grid; grid-template-columns: 1fr; gap: 1rem;
        }
        @media (min-width: 768px) { .hs-grid-3 { grid-template-columns: repeat(3, 1fr); } }

        .hs-field { margin-bottom: 1rem; }
        .hs-label {
            display: block; font-size: 0.75rem; font-weight: 600;
            color: #cbd5e1; margin-bottom: 0.4rem;
        }
        .hs-input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.6rem;
            padding: 0.65rem 0.85rem;
            font-size: 0.875rem;
            color: var(--text-hi);
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            font-family: inherit;
        }
        .hs-input:focus {
            border-color: rgba(41,231,255,0.55);
            background: rgba(41,231,255,0.04);
        }
        .hs-input::placeholder { color: var(--text-mu); }
    </style>

    <div class="hs-page max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="hs-header">
            <div class="hs-header-top">
                <div>
                    <div style="font-size:0.68rem; letter-spacing:0.16em; text-transform:uppercase; color:var(--cyan); font-weight:600;">Homepage</div>
                    <div class="hs-header-title">Homepage Settings</div>
                    <p style="font-size:0.82rem; color:var(--text-mu); margin-top:0.3rem;">
                        Toggle sections and manage content
                    </p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.homepage.update') }}">
            @csrf @method('PUT')

            {{-- Sections --}}
            <div class="hs-section">
                <div class="hs-section-head">
                    <div class="hs-section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="hs-section-title">Homepage Sections</div>
                        <div class="hs-section-sub">Toggle sections on/off</div>
                    </div>
                </div>

                @foreach ($groups['sections'] ?? [] as $setting)
                    <div class="hs-toggle-row">
                        <div>
                            <div class="hs-toggle-label">{{ $setting['label'] }}</div>
                        </div>
                        <label class="hs-switch">
                            <input type="hidden" name="{{ $setting['key'] }}" value="0">
                            <input type="checkbox" name="{{ $setting['key'] }}" value="1"
                                   {{ ($settings[$setting['key']] ?? $setting['default']) ? 'checked' : '' }}>
                            <span class="hs-slider"></span>
                        </label>
                    </div>
                @endforeach
            </div>

            {{-- General --}}
            <div class="hs-section">
                <div class="hs-section-head">
                    <div class="hs-section-icon violet">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="hs-section-title">General Content</div>
                        <div class="hs-section-sub">Site title, tagline, hero</div>
                    </div>
                </div>

                <div class="hs-grid-2">
                    @foreach ($groups['general'] ?? [] as $setting)
                        <div class="hs-field">
                            <label class="hs-label">{{ $setting['label'] }}</label>
                            <input type="text" name="{{ $setting['key'] }}"
                                   value="{{ $settings[$setting['key']] ?? $setting['default'] }}"
                                   class="hs-input">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Headings --}}
            <div class="hs-section">
                <div class="hs-section-head">
                    <div class="hs-section-icon pink">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="hs-section-title">Section Headings</div>
                        <div class="hs-section-sub">Custom titles for each section</div>
                    </div>
                </div>

                <div class="hs-grid-2">
                    @foreach ($groups['headings'] ?? [] as $setting)
                        <div class="hs-field">
                            <label class="hs-label">{{ $setting['label'] }}</label>
                            <input type="text" name="{{ $setting['key'] }}"
                                   value="{{ $settings[$setting['key']] ?? $setting['default'] }}"
                                   class="hs-input">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Trust Badges --}}
            <div class="hs-section">
                <div class="hs-section-head">
                    <div class="hs-section-icon green">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="hs-section-title">Trust Badges</div>
                        <div class="hs-section-sub">4 badges — icon + title + subtitle</div>
                    </div>
                </div>

                @for ($i = 1; $i <= 4; $i++)
                    <div class="hs-grid-3" style="margin-bottom: 1rem;">
                        @foreach ($groups['trust'] ?? [] as $setting)
                            @if (str_starts_with($setting['key'], "trust_badge_{$i}_"))
                                <div class="hs-field">
                                    <label class="hs-label">{{ $setting['label'] }}</label>
                                    <input type="text" name="{{ $setting['key'] }}"
                                           value="{{ $settings[$setting['key']] ?? $setting['default'] }}"
                                           class="hs-input">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endfor
            </div>

            {{-- Social --}}
            <div class="hs-section">
                <div class="hs-section-head">
                    <div class="hs-section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <div>
                        <div class="hs-section-title">Social Links</div>
                        <div class="hs-section-sub">Social media URLs</div>
                    </div>
                </div>

                <div class="hs-grid-2">
                    @foreach ($groups['social'] ?? [] as $setting)
                        <div class="hs-field">
                            <label class="hs-label">{{ $setting['label'] }}</label>
                            <input type="text" name="{{ $setting['key'] }}"
                                   value="{{ $settings[$setting['key']] ?? $setting['default'] }}"
                                   class="hs-input"
                                   placeholder="https://...">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Contact --}}
            <div class="hs-section">
                <div class="hs-section-head">
                    <div class="hs-section-icon pink">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="hs-section-title">Contact & Footer</div>
                        <div class="hs-section-sub">Contact info + footer text</div>
                    </div>
                </div>

                <div class="hs-grid-2">
                    @foreach ($groups['contact'] ?? [] as $setting)
                        <div class="hs-field">
                            <label class="hs-label">{{ $setting['label'] }}</label>
                            <input type="text" name="{{ $setting['key'] }}"
                                   value="{{ $settings[$setting['key']] ?? $setting['default'] }}"
                                   class="hs-input">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
                <button type="submit" class="hs-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Settings
                </button>

                <button type="button" class="hs-btn hs-btn-danger"
                        onclick="if(confirm('Reset all settings to default?')) document.getElementById('resetForm').submit();">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset to Default
                </button>
            </div>
        </form>

        {{-- Reset Form --}}
        <form method="POST" action="{{ route('admin.homepage.reset') }}" id="resetForm" style="display:none;">
            @csrf
        </form>
    </div>
</x-app-layout>