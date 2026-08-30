<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>bKash Sandbox Test Payment</title>
    <style>
        body { font-family: sans-serif; max-width: 420px; margin: 60px auto; }
        input[type=number] { padding: 8px; width: 100%; box-sizing: border-box; margin-bottom: 12px; }
        button { background: #e2136e; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; }
        .error { color: red; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h2>bKash Sandbox Test Payment</h2>

    @if (session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('bkash.pay') }}">
        @csrf
        <label>Amount (BDT)</label>
        <input type="number" name="amount" step="0.01" min="1" value="100" required>
        <button type="submit">Pay with bKash</button>
    </form>
</body>
</html>
