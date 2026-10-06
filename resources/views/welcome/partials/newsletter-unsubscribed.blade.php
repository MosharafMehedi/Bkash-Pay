<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Newsletter {{ $success ? 'Unsubscribed' : 'Error' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,600,700|inter:400,500,600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #0b0f19;
            background-image:
                radial-gradient(circle at 10% 20%, rgba(139,92,246,0.08), transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6,182,212,0.08), transparent 40%);
            color: #f1f0fb;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .card {
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            border-radius: 1.25rem;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.09);
            backdrop-filter: blur(18px);
            text-align: center;
            box-shadow: 0 25px 60px -25px rgba(0,0,0,0.6);
        }
        .icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        .icon.success { background: rgba(52,211,153,0.15); border: 2px solid rgba(52,211,153,0.4); color: #34d399; }
        .icon.error   { background: rgba(239,68,68,0.15); border: 2px solid rgba(239,68,68,0.4);  color: #f87171; }
        h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f0fb;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 0.9rem;
            color: #9a94b8;
            line-height: 1.55;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.7rem;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            color: #06050c;
            background: linear-gradient(135deg, #29e7ff, #a78bfa);
            text-decoration: none;
            box-shadow: 0 12px 30px -10px rgba(41,231,255,0.65);
            transition: transform 0.2s, filter 0.2s;
        }
        .btn:hover { transform: translateY(-1px); filter: brightness(1.08); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon {{ $success ? 'success' : 'error' }}">
            {{ $success ? '✓' : '!' }}
        </div>
        <h1>{{ $success ? 'Unsubscribed' : 'Error' }}</h1>
        <p>{{ $message }}</p>
        <a href="{{ route('welcome') }}" class="btn">
            Back to Home
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>
</body>
</html>