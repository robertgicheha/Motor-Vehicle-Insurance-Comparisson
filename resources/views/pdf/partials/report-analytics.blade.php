@php
    $s = $analytics['summary'];
    $showDownloads = $showDownloads ?? false;
    $forecastCount = $analytics['forecast']['counts'][0] ?? 0;
    $kpis = [
        ['Total ' . $unit, number_format($s['total_count'])],
        ['Total Premium (KES)', number_format($s['total_premium'], 2)],
        ['Avg Premium (KES)', number_format($s['average_premium'], 2)],
        ['Median Premium (KES)', number_format($s['median_premium'], 2)],
        ['Avg Valuation (KES)', number_format($s['average_valuation'], 0)],
        ['Forecast Next ' . $unit, number_format($forecastCount, 0)],
    ];
    if ($showDownloads && ($s['total_downloads'] ?? 0) > 0) {
        $kpis[] = ['Total Downloads', number_format($s['total_downloads'])];
    }
    $kpiWidth = round(100 / count($kpis), 2);
@endphp

<div class="section-title">Executive Summary</div>
<table class="kpi-row">
    <tr>
        @foreach($kpis as [$label, $value])
            <td class="kpi" style="width:{{ $kpiWidth }}%">
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

<div class="section-title">{{ $unit }} by Underwriter</div>
<table>
    <thead>
        <tr>
            <th>Underwriter</th>
            <th style="text-align:right">Count</th>
            <th style="text-align:right">Total Premium (KES)</th>
            <th style="text-align:right">Avg Premium (KES)</th>
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
        @if($analytics['byCompany']->isNotEmpty())
            <tr class="total-row">
                <td>Total</td>
                <td style="text-align:right">{{ $s['total_count'] }}</td>
                <td style="text-align:right">{{ number_format($s['total_premium'], 2) }}</td>
                <td style="text-align:right">—</td>
                <td style="text-align:right">100%</td>
                <td></td>
            </tr>
        @endif
    </tbody>
</table>

<div class="section-title">Monthly Trend &amp; Forecast</div>
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

<div class="section-title">Valuation Mix</div>
<table>
    <thead>
        <tr>
            <th>Valuation Band (KES)</th>
            <th style="text-align:right">Count</th>
            <th style="text-align:right">Share</th>
            <th style="width:180px">Distribution</th>
        </tr>
    </thead>
    <tbody>
        @php $__totalVals = array_sum($analytics['valuationBuckets']) ?: 1; @endphp
        @foreach($analytics['valuationBuckets'] as $band => $count)
            @php $__pct = ($count / $__totalVals) * 100; @endphp
            <tr>
                <td>{{ $band }}</td>
                <td style="text-align:right">{{ $count }}</td>
                <td style="text-align:right">{{ number_format($__pct, 1) }}%</td>
                <td style="padding:4px 8px"><div class="bar-track"><div class="bar-fill" style="width:{{ max(2, min(100, $__pct)) }}%"></div></div></td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($analytics['byStatus']->count())
    <div class="section-title">Status Breakdown</div>
    <table>
        <thead><tr><th>Status</th><th style="text-align:right">Count</th><th style="text-align:right">Share</th></tr></thead>
        <tbody>
            @php $__totalStatus = $analytics['byStatus']->sum() ?: 1; @endphp
            @foreach($analytics['byStatus'] as $status => $count)
                <tr>
                    <td>{{ ucfirst($status ?: 'unknown') }}</td>
                    <td style="text-align:right">{{ $count }}</td>
                    <td style="text-align:right">{{ number_format(($count / $__totalStatus) * 100, 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
