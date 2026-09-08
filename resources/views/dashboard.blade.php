<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white tracking-wide">
            {{ __('mPay Store') }}
        </h2>
    </x-slot>

    <!-- Internal Styles for Product Cards -->
    <style>
        .store-bg {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(6, 182, 212, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
            min-height: calc(100vh - 65px);
        }

        /* Product Card Glassmorphism */
        .product-card {
            background: rgba(17, 24, 39, 0.65);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: rgba(6, 182, 212, 0.3);
            box-shadow: 0 15px 30px -10px rgba(6, 182, 212, 0.15), 0 0 15px rgba(139, 92, 246, 0.1);
        }

        /* Subtitle Badge */
        .subtitle-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            border-radius: 50px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(6, 182, 212, 0.1);
            color: #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        /* Buy Button Glow */
        .btn-buy-glow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: linear-gradient(135deg, #06b6d4, #8b5cf6);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.55rem 1.1rem;
            border-radius: 12px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.25);
        }

        .btn-buy-glow:hover {
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.45);
            opacity: 0.95;
        }

        .btn-buy-glow svg {
            transition: transform 0.2s ease;
        }

        .btn-buy-glow:hover svg {
            transform: translateX(3px);
        }
    </style>

    <div class="store-bg py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="mb-8 text-center sm:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Sandbox Checkout Mode
                </div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Pick Something to Buy</h1>
                <p class="text-gray-400 text-sm mt-1">Test payments securely — no real money moves.</p>
            </div>

            <!-- Products Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <div class="product-card p-6 flex flex-col justify-between">
                        <div>
                            <!-- Header / Subtitle Tag -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="subtitle-badge">
                                    {{ $product->subtitle }}
                                </span>
                                <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                            </div>

                            <!-- Title -->
                            <h3 class="font-bold text-white text-xl mb-2 group-hover:text-cyan-400 transition">
                                {{ $product->name }}
                            </h3>

                            <!-- Description -->
                            <p class="text-sm text-gray-400 leading-relaxed mb-6">
                                {{ $product->description }}
                            </p>
                        </div>

                        <!-- Footer / Price & Action -->
                        <div class="pt-4 border-t border-white/5 flex items-center justify-between mt-auto">
                            <div>
                                <div class="text-2xl font-black text-white tracking-tight">
                                    ৳{{ number_format($product->price_bdt, 0) }}
                                </div>
                                <div class="text-[0.72rem] text-slate-400 flex items-center gap-1 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-800 text-cyan-400 font-medium">${{ number_format($product->price_usd, 2) }}</span> 
                                    <span>via PayPal</span>
                                </div>
                            </div>

                            <!-- Buy Button -->
                            <a href="{{ route('checkout.show', $product) }}" class="btn-buy-glow group">
                                <span>Buy</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>