@php
    $config = $emailSetting?->letterheadConfig() ?? [
        'company' => config('app.name'),
        'color' => '#263B8B',
        'address' => 'LOLC Microfinance Bank, P.O. Box 10000 - 00100 Nairobi, Kenya',
        'website' => 'www.micglobalrisks.com',
    ];
    $logo = $emailSetting?->logoPath();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { margin: 0; padding: 0; background: #eef2f7; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; }
        .letterhead { background: {{ $config['color'] }}; padding: 22px 30px; }
        .letterhead img { max-height: 44px; max-width: 180px; display: block; }
        .letterhead .lh-brand { color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: .02em; }
        .letterhead .lh-address { color: rgba(255,255,255,.9); font-size: 11px; line-height: 1.6; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,.25); }
        .body-content { padding: 30px; color: #475569; font-size: 13px; line-height: 1.7; }
        .body-content p { margin: 0 0 12px; }
        .body-content h1, .body-content h2, .body-content h3 { color: #1a1a2e; }
        .body-content a { color: {{ $config['color'] }}; }
        .body-content .btn { display: inline-block; background: {{ $config['color'] }}; color: #ffffff !important; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; }
        .body-content table { width: 100%; border-collapse: collapse; margin: 14px 0; }
        .body-content table td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .body-content .premium-box { background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px; text-align: center; margin: 14px 0; }
        .body-content .premium-box .amount { font-size: 26px; font-weight: 800; color: {{ $config['color'] }}; }
        .body-content .premium-box .label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .footer { background: #f8fafc; padding: 18px 30px; text-align: center; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 11px; line-height: 1.7; }
        .footer img { max-height: 34px; max-width: 140px; display: block; margin: 0 auto 8px; }
        .footer .footer-brand { color: #334155; font-size: 12px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="letterhead">
            @if($logo && ($emailSetting?->letterhead_logo_header ?? true))
                <img src="{{ $message->embed($logo) }}" alt="{{ $config['company'] }}">
            @else
                <div class="lh-brand">{{ $config['company'] }}</div>
            @endif
            <div class="lh-address">{{ $config['address'] }}<br>{{ $config['website'] }}</div>
        </div>

        <div class="body-content">
            {!! $content !!}
        </div>

        <div class="footer">
            @if($logo && ($emailSetting?->letterhead_logo_footer ?? true))
                <img src="{{ $message->embed($logo) }}" alt="{{ $config['company'] }}">
            @endif
            <div class="footer-brand">{{ $config['company'] }}</div>
            {{ $config['website'] }} | This email was sent from the Motor Vehicle Insurance Portal.
        </div>
    </div>
</body>
</html>
