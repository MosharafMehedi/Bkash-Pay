<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Result</title>
    <style>
        body { font-family: sans-serif; max-width: 500px; margin: 60px auto; }
        .success { color: green; }
        .failure { color: red; }
        pre { background: #f4f4f4; padding: 12px; overflow-x: auto; }
    </style>
</head>
<body>
    <h2 class="{{ $success ? 'success' : 'failure' }}">
        {{ $message }}
    </h2>

    <h4>Raw response (for debugging):</h4>
    <pre>{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>

    <a href="{{ route('bkash.index') }}">Try another payment</a>
</body>
</html>
