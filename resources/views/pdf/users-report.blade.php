<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Performance Report</title>
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
        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 7.5px; font-weight: 700; }
        .badge-on { background: #dcfce7; color: #15803d; }
        .badge-off { background: #fee2e2; color: #b91c1c; }
        .pdf-footer-text { padding: 8px 26px; background: #f8f9fb; border-top: 1px solid #dde3ed; font-size: 9px; color: #6b7280; line-height: 1.4; }
    </style>
</head>
<body>
    @include('pdf.partials.report-head', ['company' => $company ?? null])

    <div class="pdf-body">
        @php
            $s = $analytics['summary'];
            $__period = ($request->date_from ?: 'All time') . ' to ' . ($request->date_to ?: 'Present');
            $kpis = [
                ['Total Users', number_format($s['total_users'])],
                ['Active Accounts', number_format($s['active_users'])],
                ['With Activity', number_format($s['users_with_activity'])],
                ['Quotations', number_format($s['total_quotations'])],
                ['Certificates', number_format($s['total_stickers'])],
                ['Premium Generated (KES)', number_format($s['total_premium'], 2)],
                ['Conversion', number_format($s['conversion_rate'], 1) . '%'],
            ];
            $__kpiWidth = round(100 / count($kpis), 2);
        @endphp
        <div class="report-title">Users Performance Report</div>
        <p class="meta-line">
            Report Ref: MR-USR-{{ now()->format('Ymd-His') }}
            &nbsp;&middot;&nbsp; Period: {{ $__period }}
            &nbsp;&middot;&nbsp; Underwriter: {{ $company->name ?? 'All Underwriters' }}
            &nbsp;&middot;&nbsp; Generated: {{ now()->format('d F Y, h:i A') }} by {{ auth()->user()?->username ?? 'System' }}
        </p>

        <div class="section-title">Executive Summary</div>
        <table class="kpi-row">
            <tr>
                @foreach($kpis as [$label, $value])
                    <td class="kpi" style="width:{{ $__kpiWidth }}%">
                        <div class="kpi-label">{{ $label }}</div>
                        <div class="kpi-value">{{ $value }}</div>
                    </td>
                @endforeach
            </tr>
        </table>

        @if(!empty($analytics['insights']))
            <div class="section-title">Key Insights &amp; Observations</div>
            <ul class="insights">
                @foreach($analytics['insights'] as $insight)
                    <li>{{ $insight }}</li>
                @endforeach
            </ul>
        @endif

        <div class="section-title">Performance by Branch</div>
        <table>
            <thead>
                <tr>
                    <th>Branch</th>
                    <th style="text-align:right">Users</th>
                    <th style="text-align:right">Total Premium (KES)</th>
                    <th style="text-align:right">Avg per User (KES)</th>
                    <th style="text-align:right">Share</th>
                    <th style="width:120px">Distribution</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analytics['byCompany'] as $name => $item)
                    <tr>
                        <td>{{ $name }}</td>
                        <td style="text-align:right">{{ $item['count'] }}</td>
                        <td style="text-align:right">{{ number_format($item['total_premium'], 2) }}</td>
                        <td style="text-align:right">{{ number_format($item['average_premium'], 2) }}</td>
                        <td style="text-align:right">{{ number_format($item['share'], 1) }}%</td>
                        <td style="padding:4px 8px"><div class="bar-track"><div class="bar-fill" style="width:{{ max(2, min(100, $item['share'])) }}%"></div></div></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted" style="text-align:center">No data</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Monthly Activity Trend &amp; Forecast</div>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th style="text-align:right">Volume</th>
                    <th style="text-align:right">Premium (KES)</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytics['monthly']['labels'] as $i => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td style="text-align:right">{{ $analytics['monthly']['counts'][$i] }}</td>
                        <td style="text-align:right">{{ number_format($analytics['monthly']['premiums'][$i], 2) }}</td>
                        <td class="muted">Actual</td>
                    </tr>
                @endforeach
                @foreach($analytics['forecast']['labels'] as $i => $label)
                    <tr style="background:#f8f9fb">
                        <td>{{ $label }}</td>
                        <td style="text-align:right">{{ number_format($analytics['forecast']['counts'][$i], 0) }}</td>
                        <td style="text-align:right">{{ number_format($analytics['forecast']['premiums'][$i], 2) }}</td>
                        <td style="color:#0066B3;font-weight:bold">Projected</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>
        <div class="section-title">Detailed Report &mdash; {{ $users->count() }} Users</div>
        <table>
            <thead>
                <tr>
                    <th>Username</th><th>Branch</th><th>Role</th><th>Status</th>
                    <th style="text-align:right">Quotations</th><th style="text-align:right">Certificates</th>
                    <th style="text-align:right">Total Premium (KES)</th><th style="text-align:right">Conversion</th><th>Last Online</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="mono">{{ $u['username'] }}</td>
                        <td>{{ $u['branch'] ?: '—' }}</td>
                        <td>{{ $u['roles'] ?: '—' }}</td>
                        <td><span class="badge {{ $u['is_active'] ? 'badge-on' : 'badge-off' }}">{{ $u['is_active'] ? 'Active' : 'Inactive' }}</span></td>
                        <td style="text-align:right">{{ $u['quotations_count'] }}</td>
                        <td style="text-align:right">{{ $u['stickers_count'] }}</td>
                        <td style="text-align:right;font-weight:bold">{{ number_format($u['total_premium'], 2) }}</td>
                        <td style="text-align:right">{{ number_format($u['conversion'], 1) }}%</td>
                        <td>{{ $u['last_online_at'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="muted" style="text-align:center">No users found.</td></tr>
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
