<section class="wh-section" style="padding-bottom: 4rem;">
    <div class="wh-newsletter"
         x-data="newsletterForm()"
         style="position: relative; border-radius: 1.5rem; padding: 2.5rem 1.75rem; overflow: hidden; background: linear-gradient(135deg, rgba(41,231,255,0.08), rgba(167,139,250,0.06)); border: 1px solid rgba(41,231,255,0.25);">

        {{-- Glow --}}
        <div style="position: absolute; inset: 0; background: radial-gradient(circle at 20% 50%, rgba(41,231,255,0.15), transparent 50%), radial-gradient(circle at 80% 50%, rgba(167,139,250,0.12), transparent 50%); pointer-events: none;"></div>

        <div style="position: relative; z-index: 1; max-width: 620px; margin: 0 auto; text-align: center;">

            {{-- Icon --}}
            <div class="wh-newsletter-icon">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            {{-- Heading --}}
            <h2 style="font-family: 'Sora', sans-serif; font-size: 1.65rem; font-weight: 700; color: #f1f0fb; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                Subscribe to our Newsletter
            </h2>
            <p style="font-size: 0.9rem; color: #9a94b8; margin-bottom: 1.5rem; line-height: 1.5;">
                Get the latest updates, exclusive offers, and new arrivals straight to your inbox.
            </p>

            {{-- Form --}}
            <form @submit.prevent="subscribe()" class="wh-newsletter-form">
                <div class="wh-newsletter-input-wrap">
                    <svg class="wh-newsletter-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                    <input type="email"
                           x-model="email"
                           placeholder="Enter your email address"
                           required
                           :disabled="loading || success"
                           class="wh-newsletter-input">
                </div>

                <button type="submit"
                        :disabled="loading || success"
                        class="wh-newsletter-btn">
                    <template x-if="!loading && !success">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            Subscribe
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            <svg width="14" height="14" style="animation: spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Subscribing...
                        </span>
                    </template>
                    <template x-if="success">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            ✓ Subscribed
                        </span>
                    </template>
                </button>
            </form>

            {{-- Message --}}
            <div x-show="message" x-transition
                 :class="error ? 'wh-newsletter-msg error' : 'wh-newsletter-msg success'"
                 style="margin-top: 0.85rem; font-size: 0.8rem; font-weight: 600;"
                 x-text="message">
            </div>

            {{-- Privacy --}}
            <p style="font-size: 0.7rem; color: #64748b; margin-top: 1rem;">
                🔒 We respect your privacy. Unsubscribe anytime.
            </p>
        </div>
    </div>

    @push('styles')
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .wh-newsletter-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(41,231,255,0.2), rgba(167,139,250,0.2));
            border: 2px solid rgba(41,231,255,0.35);
            color: #29e7ff;
            margin: 0 auto 1.25rem;
            box-shadow: 0 12px 32px -12px rgba(41,231,255,0.5);
        }

        .wh-newsletter-form {
            display: flex;
            gap: 0.5rem;
            max-width: 520px;
            margin: 0 auto;
        }

        .wh-newsletter-input-wrap {
            position: relative;
            flex: 1;
        }

        .wh-newsletter-input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #64748b;
            pointer-events: none;
        }

        .wh-newsletter-input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            border-radius: 0.75rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            color: #f1f0fb;
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }
        .wh-newsletter-input::placeholder { color: #64748b; }
        .wh-newsletter-input:focus {
            border-color: rgba(41,231,255,0.5);
            background: rgba(41,231,255,0.05);
        }
        .wh-newsletter-input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .wh-newsletter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.5rem;
            border-radius: 0.75rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            color: #06050c;
            background: linear-gradient(135deg, #29e7ff, #a78bfa);
            border: none;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .wh-newsletter-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            filter: brightness(1.08);
        }
        .wh-newsletter-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .wh-newsletter-msg.success { color: #6ee7b7; }
        .wh-newsletter-msg.error   { color: #fca5a5; }

        @media (max-width: 560px) {
            .wh-newsletter-form {
                flex-direction: column;
            }
            .wh-newsletter-btn {
                width: 100%;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function newsletterForm() {
            return {
                email: '',
                loading: false,
                success: false,
                message: '',
                error: false,

                async subscribe() {
                    if (! this.email) return;

                    this.loading = true;
                    this.message = '';
                    this.error = false;

                    try {
                        const res = await fetch('{{ route("newsletter.subscribe") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ email: this.email }),
                        });

                        const data = await res.json();

                        if (data.ok) {
                            this.success = true;
                            this.message = data.message || 'Subscribed successfully!';
                            this.error = false;
                        } else {
                            this.message = data.message || 'Subscription failed.';
                            this.error = true;
                        }
                    } catch (e) {
                        this.message = 'Something went wrong. Please try again.';
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
    @endpush
</section>