<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Sandbox Test Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="max-w-2xl mx-auto py-12 px-4">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-700 text-white text-xl font-bold mb-3">
                PP
            </div>
            <h1 class="text-2xl font-bold text-gray-800">PayPal Sandbox Payment</h1>
            <p class="text-gray-500 text-sm mt-1">Test the Orders v2 checkout flow end to end</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">

            @if (session('error'))
                <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('paypal.pay') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount ({{ config('paypal.currency') }})</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">$</span>
                        <input
                            type="number" name="amount" step="0.01" min="1" value="10" required
                            class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                        >
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 transition text-white font-semibold py-3 rounded-xl shadow-sm">
                    Pay with PayPal
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Recent Transactions</h2>
            </div>

            @if ($transactions->isEmpty())
                <p class="text-sm text-gray-400 text-center py-8">No transactions yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-400 uppercase text-xs bg-gray-50">
                                <th class="px-6 py-3 font-medium">Invoice</th>
                                <th class="px-6 py-3 font-medium">Amount</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium">Capture ID</th>
                                <th class="px-6 py-3 font-medium">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($transactions as $trx)
                                <tr>
                                    <td class="px-6 py-3 text-gray-700">{{ $trx->invoice_number }}</td>
                                    <td class="px-6 py-3 text-gray-700">${{ number_format($trx->amount, 2) }}</td>
                                    <td class="px-6 py-3">
                                        @php
                                            $badge = match ($trx->status) {
                                                'success'   => 'bg-green-50 text-green-700 border-green-200',
                                                'failed'    => 'bg-red-50 text-red-700 border-red-200',
                                                'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                default     => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border {{ $badge }}">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">{{ $trx->capture_id ?? '—' }}</td>
                                    <td class="px-6 py-3 text-gray-400">{{ $trx->created_at->format('d M, h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

</body>
</html>
