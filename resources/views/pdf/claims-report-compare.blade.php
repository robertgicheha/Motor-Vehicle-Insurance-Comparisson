<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Claims Report Comparison</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 0; }
        .pdf-body { padding: 20px 28px; }
        .pdf-body h2 { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin: 0 0 4px; }
        .pdf-date { font-size: 10px; color: #6b7280; margin-bottom: 14px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #0066B3; margin: 16px 0 8px; padding-bottom: 6px; border-bottom: 1.5px solid #e8f2fa; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 9px; }
        table th { background: #f0f4fa; padding: 6px 8px; text-align: left; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; border: 1px solid #dde3ed; }
        table td { padding: 5px 8px; border: 1px solid #dde3ed; }
        .kpi { background: #f0f7ff; border: 1px solid #dbeafe; border-radius: 6px; padding: 8px 12px; }
        .kpi-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
        .kpi-value { font-size: 14px; font-weight: 800; color: #004f8c; }
        .page-break { page-break-before: always; }
        .compare-badge { display: inline-block; background: #004f8c; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; }
        .pdf-footer-text { padding: 14px 28px; background: #f8f9fb; border-top: 1px solid #dde3ed; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="pdf-body">
        @php $__micLogoPath = public_path('images/MIC-Global-Risks_logo_no_background.png'); @endphp
        @if(file_exists($__micLogoPath))
            <div style="text-align:center;padding-bottom:8px"><img src="{{ $__micLogoPath }}" style="max-height:34px"></div>
        @endif
        <h2 style="text-align:center">CLAIMS REPORT COMPARISON</h2>
        <p class="pdf-date" style="text-align:center">Generated {{ now()->format('d F Y, h:i A') }} by {{ auth()->user()->name }} &middot; {{ count($sections) }} report(s) compared</p>

        {{-- Side-by-side summary across all sections first --}}
        <div class="section-title">SIDE-BY-SIDE SUMMARY</div>
        <table>
            <thead><tr>
                <th>Report</th><th style="text-align:right">Total Claims</th>
                <th style="text-align:right">Total Settlement</th><th style="text-align:right">Avg Settlement</th>
                <th style="text-align:right">Police Notified</th>
            </tr></thead>
            <tbody>
                @foreach($sections as $s)
                    <tr>
                        <td><span class="compare-badge">{{ $s['label'] }}</span></td>
                        <td style="text-align:right">{{ number_format($s['summary']['total_claims']) }}</td>
                        <td style="text-align:right">{{ number_format($s['summary']['total_settlement'], 2) }}</td>
                        <td style="text-align:right">{{ number_format($s['summary']['average_settlement'], 2) }}</td>
                        <td style="text-align:right">{{ number_format($s['summary']['police_notified_count']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach($sections as $s)
        <div class="pdf-body page-break">
            <p class="compare-badge">{{ $s['label'] }}</p>
            <p class="pdf-date">{{ $s['company']->name ?? 'All Underwriters' }}</p>

            <div class="section-title">BY UNDERWRITER</div>
            <table>
                <thead><tr><th>Underwriter</th><th style="text-align:right">Count</th><th style="text-align:right">Total Settlement</th></tr></thead>
                <tbody>
                    @forelse($s['byCompany'] as $name => $item)
                        <tr><td>{{ $name }}</td><td style="text-align:right">{{ $item['count'] }}</td><td style="text-align:right">{{ number_format($item['total_settlement'], 2) }}</td></tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#94a3b8">No data</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="section-title">BY TYPE</div>
            <table>
                <thead><tr><th>Claim Type</th><th style="text-align:right">Count</th></tr></thead>
                <tbody>
                    @forelse($s['byClaimType'] as $name => $item)
                        <tr><td>{{ $name }}</td><td style="text-align:right">{{ $item['count'] }}</td></tr>
                    @empty
                        <tr><td colspan="2" style="text-align:center;color:#94a3b8">No data</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="section-title">DETAILED RECORDS &mdash; {{ $s['claims']->count() }}</div>
            <table>
                <thead><tr><th>Claim #</th><th>Customer</th><th>Registration</th><th>Type</th><th>Status</th><th style="text-align:right">Settlement</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($s['claims'] as $c)
                        <tr>
                            <td>{{ $c->claim_number }}</td>
                            <td>{{ $c->onBehalfOfUser?->name ?? $c->customer_name }}</td>
                            <td>{{ $c->registration_number }}</td>
                            <td>{{ App\Models\Claim::claimTypes()[$c->claim_type] ?? $c->claim_type }}</td>
                            <td>{{ ucfirst($c->status) }}</td>
                            <td style="text-align:right">{{ number_format($c->settlement_amount ?? 0, 2) }}</td>
                            <td>{{ $c->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;color:#94a3b8">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="pdf-footer-text">
        MIC Global Risks Insurance (Kenya) Limited &middot; www.micglobalrisks.com &middot; This is a computer-generated report.
    </div>
</body>
</html>
