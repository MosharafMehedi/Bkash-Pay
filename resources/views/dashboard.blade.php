<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Pick something to buy</h1>
                <p class="text-gray-500 text-sm mt-1">Sandbox checkout — no real money moves.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
                        <div class="text-xs uppercase tracking-wide text-gray-400 mb-2">{{ $product->subtitle }}</div>
                        <h3 class="font-semibold text-gray-800 text-lg mb-2">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 flex-1 mb-4">{{ $product->description }}</p>

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-lg font-bold text-gray-800">৳{{ number_format($product->price_bdt, 0) }}</span>
                                <span class="text-xs text-gray-400 block">${{ number_format($product->price_usd, 2) }} via PayPal</span>
                            </div>
                            <a href="{{ route('checkout.show', $product) }}"
                                class="bg-gray-900 hover:bg-gray-700 transition text-white text-sm font-semibold px-4 py-2 rounded-lg">
                                Buy
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
