<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sticker {{ $sticker->sticker_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 0; }
        .letterhead {
            display: flex; justify-content: space-between; align-items: stretch; min-height: 90px;
        }
        .lh-left {
            display: flex; align-items: center; padding: 20px 28px; min-width: 220px;
        }
        .lh-left img { max-height: 44px; max-width: 160px; object-fit: contain; }
        .lh-left .lh-name { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: .01em; display: none; }
        .lh-right { flex: 1; display: flex; align-items: center; justify-content: flex-end; padding: 18px 28px; text-align: right; }
        .lh-right p { font-size: 12px; line-height: 1.75; color: rgba(255,255,255,.92); margin: 0; }
        .pdf-body { padding: 28px 36px; }
        .pdf-body h2 { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
        .pdf-body .pdf-date { font-size: 12px; color: #6b7280; margin-bottom: 20px; }
        .pdf-body p { font-size: 13px; line-height: 1.7; margin-bottom: 16px; }
        .pdf-body table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px; }
        .pdf-body table th { background: #f0f4fa; padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; border: 1px solid #dde3ed; }
        .pdf-body table td { padding: 9px 12px; border: 1px solid #dde3ed; }
        .pdf-footer-text {
            padding: 20px 36px; background: #f8f9fb; border-top: 1px solid #dde3ed;
            font-size: 11px; color: #6b7280; line-height: 1.8;
        }
        .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #0066B3; margin: 24px 0 10px; padding-bottom: 8px; border-bottom: 1.5px solid #e8f2fa; }
        .total-row { background: #263B8B; color: white; font-weight: bold; }
        .total-row td { padding: 10px 12px; font-size: 14px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    @php
        $company = $sticker->company;
        $slug = strtoupper($company->slug ?? '');
        $companyMeta = [
            'GA' => [
                'color' => '#005baa',
                'address' => 'GA Insurance Ltd, GA Building, Hospital Road, Upper Hill, P.O. Box 42166 - 00100 Nairobi',
                'website' => 'www.gainsurance.co.ke',
                'img' => asset('images/GA Insurance.png'),
            ],
            'BRITAM' => [
                'color' => '#0071bc',
                'address' => 'Britam General Insurance Company (Kenya) Limited, Britam Centre, Junction of Mara and Ragati Road, Upper Hill, P.O. BOX 30375 - 0100 Nairobi',
                'website' => 'www.britam.com',
                'img' => asset('images/britam_logo.png'),
            ],
            'SANLAM' => [
                'color' => '#1d3c6e',
                'address' => 'Sanlam Allianz Insurance Company Limited, Sanlam Tower, Hospital Road, Upper Hill, P.O. Box 30522 - 00100 Nairobi',
                'website' => 'www.sanlam.co.ke',
                'img' => asset('images/Sanlam-Allianz.png'),
            ],
        ];
        $meta = $companyMeta[$slug] ?? ['color' => '#0066B3', 'address' => '', 'website' => 'www.micglobalrisks.com', 'img' => $company->logoUrl()];
        $now = now()->format('d F Y, h:i A');
    @endphp

    {{-- LETTERHEAD BANNER --}}
    <div class="letterhead" style="background:{{ $meta['color'] }}">
        <div class="lh-left">
            @php $__logoPath = $company->logoPath() ?? ''; @endphp
            @if($__logoPath && file_exists($__logoPath))
                <img src="{{ $__logoPath }}" alt="{{ $company->name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            @else
                <img src="{{ $meta['img'] ?? '' }}" alt="{{ $company->name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            @endif
            <span class="lh-name">{{ $company->name }}</span>
        </div>
        <div class="lh-right">
            <p>{{ $meta['address'] }}<br>Website: {{ $meta['website'] }}</p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="pdf-body">
        <p style="margin-top:4px">{{ $now }}</p>
        <p>Dear Sir/Madam,</p>
        <h2>MOTOR INSURANCE STICKER</h2>
        <p class="pdf-date">
            Sticker ID: <strong>{{ $sticker->sticker_number }}</strong>
            @if($sticker->underwriter_sticker_no)
                &nbsp;&middot;&nbsp; Sticker Number: <strong>{{ $sticker->underwriter_sticker_no }}</strong>
            @endif
            &nbsp;&middot;&nbsp; <span class="badge">{{ strtoupper($sticker->cover_type ?? '') }}</span>
        </p>
        <p>This document confirms that the vehicle described below is covered under a {{ $sticker->cover_type }} motor insurance policy with {{ $company->name }}.</p>

        <div class="section-title">CLIENT DETAILS</div>
        <table>
            <thead>
                <tr><th>Client Type</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $sticker->client_type === 'corporate' ? 'Corporate' : 'Individual' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">VEHICLE DETAILS</div>
        <table>
            <thead>
                <tr><th>Registration No</th><th>Chassis No</th><th>Engine No</th><th>Make</th><th>Model</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $sticker->vehicle_registration ?? 'N/A' }}</td>
                    <td>{{ $sticker->chassis_number ?? 'N/A' }}</td>
                    <td>{{ $sticker->engine_number ?? 'N/A' }}</td>
                    <td>{{ $sticker->car_make }}</td>
                    <td>{{ $sticker->car_model }}</td>
                </tr>
            </tbody>
        </table>
        <table>
            <thead>
                <tr><th>Year of Manufacture</th><th>Color</th><th style="text-align:right">Valuation (KES)</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $sticker->car_year }}</td>
                    <td>{{ $sticker->color ?? 'N/A' }}</td>
                    <td style="text-align:right">{{ number_format($sticker->car_valuation, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">PREMIUM DETAILS</div>
        <table>
            <thead><tr><th>Description</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount (KES)</th></tr></thead>
            <tbody>
                <tr><td>Vehicle Valuation</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($sticker->car_valuation, 2) }}</td></tr>
                <tr><td>Premium</td><td style="text-align:right">{{ $sticker->premium_rate ? number_format($sticker->premium_rate, 2) . '%' : '—' }}</td><td style="text-align:right">{{ number_format($sticker->premium, 2) }}</td></tr>
            </tbody>
            <tfoot>
                <tr class="total-row"><td>Total Premium</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($sticker->premium, 2) }}</td></tr>
            </tfoot>
        </table>

        @if($sticker->breakdown && count($sticker->breakdown) > 0)
        <div class="section-title">PREMIUM BREAKDOWN</div>
        <table>
            <thead><tr><th>Component</th><th style="text-align:right">Amount (KES)</th></tr></thead>
            <tbody>
                @foreach($sticker->breakdown as $key => $value)
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                        <td style="text-align:right">{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <p style="font-size:10px;color:#64748b;margin-top:15px">
            This sticker must be displayed on the vehicle windscreen at all times. Subject to standard policy terms and conditions.
        </p>
        <p style="font-size:10px;color:#64748b">
            Generated: {{ $sticker->generated_at?->format('d M Y h:i A') ?? $sticker->created_at->format('d M Y h:i A') }}
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="pdf-footer-text">
        KENYA | UGANDA | TANZANIA<br>
        Sticker ID {{ $sticker->sticker_number }} &mdash; LOLC Microfinance Bank | This is a computer-generated document.
    </div>
</body>
</html>
