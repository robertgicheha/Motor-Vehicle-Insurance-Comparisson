{{--
    Shared error page layout.
    Expects: $code, $title, $message, $icon (html), $trace (array|null)
--}}
@php
    $showTrace = config('app.debug', false);
    if (!$showTrace && function_exists('auth') && auth()->check()) {
        try {
            $showTrace = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
        } catch (\Throwable $e) {
            $showTrace = false;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} · {{ $title }} · Motor Vehicle Insurance Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef2f7;
            --card: #ffffff;
            --ink: #1a2540;
            --muted: #64748b;
            --line: #e2e8f0;
            --accent: #1756c8;
            --accent-soft: #e8effc;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0f172a;
                --card: #1e293b;
                --ink: #f1f5f9;
                --muted: #94a3b8;
                --line: #334155;
                --accent: #60a5fa;
                --accent-soft: #17264a;
            }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background:
                radial-gradient(900px 500px at 10% -10%, rgba(23,86,200,.18), transparent 60%),
                radial-gradient(700px 500px at 110% 110%, rgba(0,95,158,.16), transparent 55%),
                var(--bg);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: 0 24px 70px -28px rgba(15, 23, 42, .45);
            max-width: 640px;
            width: 100%;
            padding: 44px 40px 36px;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, var(--accent), #0f7a65, #b45309, #be123c);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent);
            background: var(--accent-soft);
            padding: 6px 12px;
            border-radius: 999px;
            margin-bottom: 22px;
        }
        .code-row { display: flex; align-items: center; gap: 20px; margin-bottom: 8px; }
        .code {
            font-size: 84px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.03em;
            background: linear-gradient(135deg, var(--accent), #0f7a65);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .icon { color: var(--accent); flex-shrink: 0; }
        h1 { font-size: 22px; font-weight: 800; margin-bottom: 10px; letter-spacing: -.01em; }
        .message { color: var(--muted); font-size: 14px; line-height: 1.7; margin-bottom: 26px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 24px; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
            border: 1px solid transparent;
        }
        .btn:active { transform: translateY(1px); }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 10px 22px -12px var(--accent); }
        .btn-primary:hover { filter: brightness(1.08); }
        .btn-ghost { background: transparent; border-color: var(--line); color: var(--ink); }
        .btn-ghost:hover { background: var(--accent-soft); border-color: var(--accent); }
        .support { font-size: 12px; color: var(--muted); text-align: center; }
        .support a { color: var(--accent); font-weight: 600; text-decoration: none; }
        .support a:hover { text-decoration: underline; }
        details.details {
            margin-top: 6px;
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: 0;
            background: var(--bg);
        }
        details.details summary {
            cursor: pointer;
            list-style: none;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 700;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 8px;
            user-select: none;
        }
        details.details summary::-webkit-details-marker { display: none; }
        details.details[open] summary { border-bottom: 1px dashed var(--line); }
        .details-body { padding: 14px 16px; font-family: 'JetBrains Mono', monospace; font-size: 12px; line-height: 1.7; overflow-x: auto; }
        .details-body .k { color: var(--muted); text-transform: uppercase; font-size: 10px; letter-spacing: .1em; font-weight: 600; margin-bottom: 4px; }
        .details-body pre { white-space: pre-wrap; word-break: break-word; color: var(--ink); }
        .details-body code { background: var(--accent-soft); padding: 2px 6px; border-radius: 6px; }
        .chip { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
        .chip-local { background: #dcfce7; color: #166534; }
        .chip-prod { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            Something went wrong
        </span>

        <div class="code-row">
            <div class="code">{{ $code }}</div>
            <div class="icon">{!! $icon !!}</div>
        </div>

        <h1>{{ $title }}</h1>
        <p class="message">{{ $message }}</p>

        <div class="actions">
            <a class="btn btn-primary" href="{{ url('/') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"/></svg>
                Back to Home
            </a>
            <a class="btn btn-ghost" href="#" onclick="history.length > 1 ? history.back() : (location.href='{{ url('/') }}'); return false;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Go Back
            </a>
        </div>

        @if($showTrace && !empty($trace))
            <details class="details">
                <summary>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l-4 3 4 3m8-6l4 3-4 3M12 5l-2 14"/></svg>
                    Error details &amp; fix location
                </summary>
                <div class="details-body">
                    <div class="k">Error</div>
                    <pre>{{ $trace['error'] }}</pre>
                    <div class="k" style="margin-top:12px;">Location</div>
                    <pre><code>{{ $trace['file'] }}</code> : <b>{{ $trace['line'] }}</b></pre>
                    @if(!empty($trace['exception']))
                        <div class="k" style="margin-top:12px;">Stack trace</div>
                        <pre>{{ $trace['exception'] }}</pre>
                    @endif
                </div>
            </details>
        @endif

        <div class="support" style="margin-top:22px;">
            Still stuck?
            <a href="mailto:{{ config('mail.from.address') }}?subject={{ $code }} error on Motor Vehicle Insurance Portal">Contact Support</a>
            &middot; <span class="chip {{ config('app.env') === 'local' ? 'chip-local' : 'chip-prod' }}">{{ strtoupper(config('app.env')) }}</span>
        </div>
    </div>
</body>
</html>
