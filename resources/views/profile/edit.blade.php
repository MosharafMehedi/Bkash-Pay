<x-app-layout>
    @section('title', 'My Profile')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600" rel="stylesheet">

    <style>
        .pf-page {
            font-family: 'Inter', sans-serif;
            --cyan:#29e7ff;
            --violet:#a78bfa;
            --pink:#ff5fb0;
            --green:#34d399;
            --text-hi:#f1f0fb;
            --text-mu:#9a94b8;
            --glass:rgba(255,255,255,0.045);
            --glass-border:rgba(255,255,255,0.09);
        }

        .pf-wrap { max-width: 1000px; margin: 0 auto; }

        /* ── Page Header ── */
        .pf-header {
            position: relative;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.5rem;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, rgba(41,231,255,0.06), rgba(167,139,250,0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            overflow: hidden;
        }
        .pf-header::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }
        .pf-header-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-hi);
        }
        .pf-header-sub {
            font-size: 0.82rem;
            color: var(--text-mu);
            margin-top: 0.3rem;
        }

        /* ── Grid ── */
        .pf-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1.25rem;
            align-items: start;
        }
        @media (max-width: 900px) { .pf-grid { grid-template-columns: 1fr; } }

        /* ── Panel ── */
        .pf-panel {
            border-radius: 1.1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            padding: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .pf-panel-title {
            font-family: 'Sora', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-hi);
            margin-bottom: 1.15rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .pf-panel-title::before {
            content: '';
            width: 4px; height: 18px;
            border-radius: 2px;
            background: linear-gradient(180deg, var(--cyan), var(--violet));
        }

        /* ── Avatar Section ── */
        .pf-avatar-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .pf-avatar-display {
            position: relative;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(41,231,255,0.15), rgba(167,139,250,0.15));
            border: 3px solid rgba(41,231,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 12px 32px -12px rgba(41,231,255,0.4);
            cursor: pointer;
            transition: all 0.25s;
        }
        .pf-avatar-display:hover {
            border-color: var(--cyan);
            box-shadow: 0 16px 40px -12px rgba(41,231,255,0.6);
        }
        .pf-avatar-display img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .pf-avatar-initials {
            font-family: 'Sora', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            color: var(--cyan);
            letter-spacing: 0.05em;
        }
        .pf-avatar-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            opacity: 0;
            transition: opacity 0.25s;
            color: #ffffff;
        }
        .pf-avatar-display:hover .pf-avatar-overlay { opacity: 1; }
        .pf-avatar-overlay svg { width: 28px; height: 28px; }
        .pf-avatar-overlay span {
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* ── Avatar buttons ── */
        .pf-avatar-name {
            font-family: 'Sora', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-hi);
            margin-bottom: 0.25rem;
        }
        .pf-avatar-email {
            font-size: 0.78rem;
            color: var(--text-mu);
            margin-bottom: 1rem;
        }
        .pf-avatar-role {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: rgba(167,139,250,0.15);
            color: var(--violet);
            border: 1px solid rgba(167,139,250,0.3);
            margin-bottom: 1rem;
        }

        .pf-avatar-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }

        .pf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.65rem 1rem;
            border-radius: 0.65rem;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }
        .pf-btn-primary {
            color: #06050c;
            background: linear-gradient(135deg, var(--cyan), var(--violet));
            box-shadow: 0 10px 24px -10px rgba(41,231,255,0.6);
        }
        .pf-btn-primary:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 14px 32px -10px rgba(41,231,255,0.8);
        }
        .pf-btn-outline {
            color: var(--text-mu);
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
        }
        .pf-btn-outline:hover {
            color: #f87171;
            border-color: rgba(239,68,68,0.4);
            background: rgba(239,68,68,0.08);
        }
        .pf-btn svg { width: 14px; height: 14px; }

        /* ── Form Fields ── */
        .pf-field { margin-bottom: 1rem; }
        .pf-field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.4rem;
        }
        .pf-field label .req { color: #f87171; }
        .pf-field input,
        .pf-field textarea {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 0.65rem;
            padding: 0.7rem 0.9rem;
            font-size: 0.85rem;
            color: var(--text-hi);
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            font-family: inherit;
        }
        .pf-field input:focus,
        .pf-field textarea:focus {
            border-color: rgba(41,231,255,0.55);
            background: rgba(41,231,255,0.04);
        }
        .pf-field input::placeholder,
        .pf-field textarea::placeholder { color: var(--text-mu); }
        .pf-field textarea { resize: vertical; min-height: 80px; }
        .pf-field-error {
            font-size: 0.72rem;
            color: #f87171;
            margin-top: 0.35rem;
        }
        .pf-field-hint {
            font-size: 0.7rem;
            color: var(--text-mu);
            margin-top: 0.35rem;
        }

        .pf-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 640px) { .pf-grid-2 { grid-template-columns: 1fr; } }

        /* ── Alerts ── */
        .pf-alert {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 0.7rem;
            font-size: 0.82rem;
            font-weight: 500;
        }
        .pf-alert.success {
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.3);
            color: #6ee7b7;
        }
        .pf-alert.error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
        }
        .pf-alert svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* ── Save Button ── */
        .pf-save-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--glass-border);
        }

        /* ── Hidden file input ── */
        .pf-file-input { display: none; }

        /* ── Sidebar Panel (Left) ── */
        .pf-side-panel {
            position: sticky;
            top: 5rem;
        }
        @media (max-width: 900px) { .pf-side-panel { position: static; } }
    </style>

    <div class="pf-page pf-wrap">

        {{-- Header --}}
        <div class="pf-header">
            <div class="pf-header-title">My Profile</div>
            <div class="pf-header-sub">Manage your personal information and account settings.</div>
        </div>

        @if (session('success'))
            <div class="pf-alert success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('status') === 'profile-updated')
            <div class="pf-alert success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Profile updated successfully.
            </div>
        @endif

        @if ($errors->any())
            <div class="pf-alert error">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Please fix the errors below.
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="pf-grid">

                {{-- ══ LEFT: Avatar + Account Info ══ --}}
                <div class="pf-side-panel">

                    {{-- Avatar Card --}}
                    <div class="pf-panel">
                        <div class="pf-avatar-wrap">

                            {{-- Avatar Display (clickable) --}}
                            <div class="pf-avatar-display" onclick="document.getElementById('avatarInput').click();">
                                @if ($user->hasAvatar())
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatarPreview">
                                @else
                                    <div class="pf-avatar-initials" id="avatarInitials">
                                        {{ $user->initials }}
                                    </div>
                                    <img src="" alt="Preview" id="avatarPreview" style="display:none;">
                                @endif

                                <div class="pf-avatar-overlay">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Change Photo</span>
                                </div>
                            </div>

                            {{-- Hidden file input --}}
                            <input type="file"
                                   name="avatar"
                                   id="avatarInput"
                                   class="pf-file-input"
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="previewAvatar(this)">

                            {{-- User info --}}
                            <div class="pf-avatar-name">{{ $user->name }}</div>
                            <div class="pf-avatar-email">{{ $user->email }}</div>

                            <span class="pf-avatar-role">
                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                {{ $user->getRoleNames()->first() ?? 'user' }}
                            </span>

                            {{-- Avatar actions --}}
                            <div class="pf-avatar-actions">
                                <button type="button"
                                        class="pf-btn pf-btn-primary"
                                        onclick="document.getElementById('avatarInput').click();">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Upload New Photo
                                </button>

                                @if ($user->hasAvatar())
                                    <button type="button"
                                            class="pf-btn pf-btn-outline"
                                            onclick="if(confirm('Remove your avatar?')) document.getElementById('removeAvatarForm').submit();">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Remove Photo
                                    </button>
                                @endif
                            </div>

                            <div class="pf-field-hint" style="margin-top: 0.75rem;">
                                JPG, PNG, or WebP · Max 2 MB
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ RIGHT: Personal Info ══ --}}
                <div>

                    {{-- Personal Info --}}
                    <div class="pf-panel">
                        <div class="pf-panel-title">Personal Information</div>

                        <div class="pf-field">
                            <label>Full Name <span class="req">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   required autocomplete="name" placeholder="Your full name">
                            @error('name') <div class="pf-field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="pf-field">
                            <label>Email Address <span class="req">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   required autocomplete="username" placeholder="you@example.com">
                            @error('email') <div class="pf-field-error">{{ $message }}</div> @enderror
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="pf-field-hint">
                                    Your email is unverified.
                                    <form method="POST" action="{{ route('verification.send') }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="color:var(--cyan); background:none; border:none; text-decoration:underline; cursor:pointer; font-size:0.7rem;">
                                            Resend verification
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <div class="pf-grid-2">
                            <div class="pf-field">
                                <label>Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                       placeholder="01XXXXXXXXX" autocomplete="tel">
                                @error('phone') <div class="pf-field-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="pf-field">
                                <label>City</label>
                                <input type="text" name="city" value="{{ old('city', $user->city) }}"
                                       placeholder="Your city" autocomplete="address-level2">
                                @error('city') <div class="pf-field-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="pf-field">
                            <label>Address</label>
                            <textarea name="address" rows="2"
                                      placeholder="House, road, area..."
                                      autocomplete="street-address">{{ old('address', $user->address) }}</textarea>
                            @error('address') <div class="pf-field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="pf-field">
                            <label>Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                                   placeholder="Optional" autocomplete="postal-code" style="max-width: 200px;">
                            @error('postal_code') <div class="pf-field-error">{{ $message }}</div> @enderror
                        </div>

                        {{-- Save bar --}}
                        <div class="pf-save-bar">
                            <button type="submit" class="pf-btn pf-btn-primary">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        {{-- Remove avatar form (outside main form) --}}
        <form method="POST" action="{{ route('profile.avatar.remove') }}" id="removeAvatarForm" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        function previewAvatar(input) {
            if (! input.files || ! input.files[0]) return;

            const file = input.files[0];

            // Size check
            if (file.size > 2 * 1024 * 1024) {
                alert('File is too large. Maximum 2 MB allowed.');
                input.value = '';
                return;
            }

            // Type check
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (! allowedTypes.includes(file.type)) {
                alert('Invalid file type. Only JPG, PNG, WEBP allowed.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('avatarPreview');
                const initials = document.getElementById('avatarInitials');

                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                if (initials) {
                    initials.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    </script>
</x-app-layout>