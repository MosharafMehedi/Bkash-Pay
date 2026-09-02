<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="max-w-md w-full">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">

            @if ($success)
                <div class="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 text-3xl">
                    ✓
                </div>
                <h1 class="text-xl font-bold text-gray-800 mb-1">Payment Successful</h1>
            @else
                <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 text-3xl">
                    ✕
                </div>
                <h1 class="text-xl font-bold text-gray-800 mb-1">Payment Failed</h1>
            @endif

            <p class="text-gray-500 text-sm mb-6">{{ $message }}</p>

            @if ($transaction)
                <div class="text-left bg-gray-50 rounded-xl p-4 space-y-2 mb-6 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Invoice</span>
                        <span class="text-gray-700 font-medium">{{ $transaction->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Amount</span>
                        <span class="text-gray-700 font-medium">৳{{ number_format($transaction->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Bank Trx ID</span>
                        <span class="text-gray-700 font-medium">{{ $transaction->bank_tran_id ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Card/Bank Type</span>
                        <span class="text-gray-700 font-medium">{{ $transaction->card_type ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status</span>
                        <span class="text-gray-700 font-medium">{{ ucfirst($transaction->status) }}</span>
                    </div>
                </div>
            @endif

            <a href="{{ route('sslcommerz.index') }}"
                class="block w-full bg-emerald-700 hover:bg-emerald-800 transition text-white font-semibold py-3 rounded-xl">
                Back to Home
            </a>
        </div>

        <details class="mt-4">
            <summary class="text-xs text-gray-400 cursor-pointer">Raw response (debug)</summary>
            <pre class="text-xs bg-gray-900 text-gray-100 rounded-lg p-4 mt-2 overflow-x-auto">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
        </details>
    </div>

</body>
</html>
