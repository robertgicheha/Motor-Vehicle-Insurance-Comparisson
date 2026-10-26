<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $unit ?? 'Quotations' }} Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #1a1a2e; margin: 0; padding: 0; }
        .mic-banner { text-align: center; padding: 6px 0 4px; }
        .mic-banner img { max-height: 36px; }
        .letterhead { display: flex; justify-content: space-between; align-items: stretch; min-height: 64px; border-bottom: 1.5px solid #dde3ed; }
        .lh-left { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; padding: 8px 26px; min-width: 220px; }
        .lh-left img { max-height: 34px; max-width: 180px; object-fit: contain; }
        .lh-left .lh-fallback-name { font-size: 18px; font-weight: 800; color: #1a1a2e; display: none; }
        .lh-left .lh-caption { margin-top: 4px; font-size: 10px; }
        .lh-left .lh-caption .lh-company-name { font-weight: 700; color: #1a1a2e; }
        .lh-right { flex: 1; display: flex; align-items: center; justify-content: flex-end; padding: 8px 26px; text-align: right; }
        .lh-right p { font-size: 10px; line-height: 1.4; color: #6b7280; margin: 0; }
        .pdf-body { padding: 10px 26px; }
        .report-title { font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; margin: 8px 0 2px; text-decoration: underline; }
        .meta-line { font-size: 9.5px; color: #6b7280; margin: 0 0 8px; line-height: 1.5; }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #1a1a2e; margin: 10px 0 4px; padding-bottom: 3px; border-bottom: 1.5px solid #dde3ed; }
        table { width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 9px; }
        th { background: #f0f4fa; padding: 4px 8px; text-align: left; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; border: 1px solid #dde3ed; }
        td { padding: 3.5px 8px; border: 1px solid #dde3ed; }
        .kpi-row { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin: 4px 0; }
        .kpi { background: #f0f7ff; border: 1px solid #dbeafe; border-radius: 5px; padding: 6px 10px; }
        .kpi-label { font-size: 7.5px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
        .kpi-value { font-size: 12.5px; font-weight: 800; color: #263B8B; }
        .total-row { background: #263B8B; color: #fff; font-weight: bold; }
        .bar-track { background: #eef2f7; height: 8px; width: 100%; }
        .bar-fill { background: #0066B3; height: 8px; }
        .insights { margin: 4px 0 0; padding-left: 15px; }
        .insights li { font-size: 9.5px; line-height: 1.5; margin-bottom: 2px; }
        .muted { color: #94a3b8; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; font-size: 8px; }
        .page-break { page-break-before: always; }
        .pdf-footer-text { padding: 8px 26px; background: #f8f9fb; border-top: 1px solid #dde3ed; font-size: 9px; color: #6b7280; line-height: 1.4; }
    </style>
</head>
<body>
    @include('pdf.partials.report-head', ['company' => $company ?? null])

    <div class="pdf-body">
        @php
            $__period = ($request->date_from ?: 'All time') . ' to ' . ($request->date_to ?: 'Present');
        @endphp
        <div class="report-title">{{ $unit }} Report</div>
        <p class="meta-line">
            Report Ref: MR-{{ strtoupper(substr($unit, 0, 3)) }}-{{ now()->format('Ymd-His') }}
            &nbsp;&middot;&nbsp; Period: {{ $__period }}
            &nbsp;&middot;&nbsp; Underwriter: {{ $company->name ?? 'All Underwriters' }}
            &nbsp;&middot;&nbsp; Generated: {{ now()->format('d F Y, h:i A') }} by {{ auth()->user()?->username ?? 'System' }}
        </p>

        @include('pdf.partials.report-analytics', ['analytics' => $analytics, 'unit' => $unit, 'showDownloads' => false])

        <div class="page-break"></div>
        <div class="section-title">Detailed Report &mdash; {{ $quotations->count() }} Records</div>
        <table>
            <thead><tr>
                <th>Quotation #</th><th>Username</th><th>Company</th><th>Registration</th><th>Vehicle</th>
                <th style="text-align:right">Valuation</th><th style="text-align:right">Premium</th><th style="text-align:right">Total</th>
                <th>Status</th><th>Date</th>
            </tr></thead>
            <tbody>
                @forelse($quotations as $q)
                    <tr>
                        <td class="mono">{{ $q->quotation_number }}</td>
                        <td>{{ $q->user?->username ?? 'N/A' }}</td>
                        <td>{{ $q->company?->name ?? 'N/A' }}</td>
                        <td>{{ $q->car_registration ?? '—' }}</td>
                        <td>{{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }})</td>
                        <td style="text-align:right">{{ number_format($q->car_valuation, 0) }}</td>
                        <td style="text-align:right">{{ number_format($q->premium, 2) }}</td>
                        <td style="text-align:right;font-weight:bold">{{ number_format($q->total_premium, 2) }}</td>
                        <td>{{ ucfirst($q->status) }}</td>
                        <td>{{ $q->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="muted" style="text-align:center">No records found for the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pdf-footer-text">
        KENYA | UGANDA | TANZANIA | This is a computer-generated report.<br>
        LOLC Microfinance Bank &middot; www.micglobalrisks.com
    </div>
</body>
</html>
