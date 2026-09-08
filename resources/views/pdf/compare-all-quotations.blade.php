<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Compare All Insurers</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #1a1a2e; margin: 0; padding: 0; }
        .mic-banner { text-align: center; padding: 6px 0 4px; }
        .mic-banner img { max-height: 36px; }
        .letterhead {
            display: flex; justify-content: space-between; align-items: stretch; min-height: 64px; border-bottom: 1.5px solid #dde3ed;
        }
        .lh-left {
            display: flex; flex-direction: column; align-items: flex-start; justify-content: center; padding: 8px 26px; min-width: 220px;
        }
        .lh-left img { max-height: 34px; max-width: 180px; object-fit: contain; }
        .lh-left .lh-fallback-name { font-size: 18px; font-weight: 800; color: #1a1a2e; letter-spacing: .01em; display: none; }
        .lh-left .lh-caption { margin-top: 4px; font-size: 10px; }
        .lh-left .lh-caption span { display: block; color: #6b7280; line-height: 1.4; }
        .lh-left .lh-caption .lh-company-name { font-weight: 700; color: #1a1a2e; }
        .lh-right { flex: 1; display: flex; align-items: center; justify-content: flex-end; padding: 8px 26px; text-align: right; }
        .lh-right p { font-size: 10px; line-height: 1.4; color: #6b7280; margin: 0; }
        .pdf-body { padding: 10px 26px; }
        .pdf-body h2 { font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; margin: 0; text-decoration: underline; display: inline-block; }
        .re-row { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 6px; }
        .date-ref-row { width: 100%; border-collapse: collapse; margin: 4px 0 5px; font-size: 10.5px; }
        .date-ref-row td { border: none; padding: 0; }
        .pdf-body p { font-size: 10.5px; line-height: 1.35; margin: 0 0 5px; }
        .pdf-body table { width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 10px; }
        .pdf-body table th { background: #f0f4fa; padding: 3px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; border: 1px solid #dde3ed; }
        .pdf-body table td { padding: 3px 8px; border: 1px solid #dde3ed; }
        .pdf-footer-text {
            padding: 8px 26px; background: #f8f9fb; border-top: 1px solid #dde3ed;
            font-size: 9px; color: #6b7280; line-height: 1.4;
        }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #1a1a2e; margin: 8px 0 3px; padding-bottom: 3px; border-bottom: 1.5px solid #dde3ed; }
        .total-row { background: #004f8c; color: white; font-weight: bold; }
        .total-row td { padding: 5px 8px; font-size: 11px; }
        .company-logo { max-height: 40px; max-width: 140px; }
        .page-break { page-break-before: always; }
        .grid-2col td { width: 25%; }
        .compare-cover { padding: 30px 26px; text-align: center; }
        .compare-cover h1 { font-size: 20px; margin: 10px 0 4px; color: #1a1a2e; }
        .compare-cover p { font-size: 11px; color: #6b7280; }
        .compare-cover table { width: 100%; border-collapse: collapse; margin: 18px 0; font-size: 10.5px; }
        .compare-cover th { background: #004f8c; color: #fff; padding: 6px 8px; text-align: left; }
        .compare-cover td { padding: 6px 8px; border: 1px solid #dde3ed; }
    </style>
</head>
<body>
    {{-- COVER / SUMMARY PAGE --}}
    <div class="compare-cover">
        @php $__micLogoPath = public_path('images/MIC-Global-Risks_logo_no_background.png'); @endphp
        @if(file_exists($__micLogoPath))
            <img src="{{ $__micLogoPath }}" alt="MIC Global Risks" style="max-height:48px">
        @endif
        <h1>Compare All Insurers</h1>
        <p>Side-by-side premium comparison across every underwriter that covers this vehicle — generated {{ now()->format('d F Y, h:i A') }}.</p>
        <table>
            <thead>
                <tr><th>Insurer</th><th style="text-align:right">Rate</th><th style="text-align:right">Total Premium (KES)</th></tr>
            </thead>
            <tbody>
                @foreach($quotations as $q)
                <tr>
                    <td>{{ $q->company->name }}</td>
                    <td style="text-align:right">{{ \App\Models\Rate::fmtPercent($q->breakdown['premium_rate'] ?? 0) }}%</td>
                    <td style="text-align:right;font-weight:bold">{{ number_format($q->breakdown['total_premium'] ?? $q->total_premium, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach($quotations as $quotation)
        <div class="page-break"></div>
        @include('pdf.partials.quotation-body', ['quotation' => $quotation])
    @endforeach
</body>
</html>
