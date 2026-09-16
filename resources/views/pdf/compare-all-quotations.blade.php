<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Compare All Insurers</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1a1a2e; margin: 0; padding: 0; }
        .letterhead { padding: 10px 24px; border-bottom: 1.5px solid #dde3ed; text-align: center; }
        .letterhead img { max-height: 34px; max-width: 180px; object-fit: contain; }
        .letterhead h1 { font-size: 15px; margin: 6px 0 2px; color: #1a1a2e; }
        .letterhead p { font-size: 9px; color: #6b7280; margin: 0; }
        .pdf-body { padding: 10px 24px; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 6px 0 12px; font-size: 9px; }
        .summary-table td { padding: 2px 10px 2px 0; color: #374151; }
        .summary-table td.label { color: #9ca3af; text-transform: uppercase; letter-spacing: .03em; font-size: 7.5px; padding-bottom: 0; }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #1a1a2e; margin: 10px 0 4px; padding-bottom: 3px; border-bottom: 1.5px solid #dde3ed; }
        table.cmp-table { width: 100%; border-collapse: collapse; margin: 4px 0 10px; font-size: 8.5px; }
        table.cmp-table th { background: #f0f4fa; padding: 4px 6px; text-align: center; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; border: 1px solid #dde3ed; }
        table.cmp-table th.desc-col, table.cmp-table td.desc-col { text-align: left; width: 22%; }
        table.cmp-table th .th-logo { text-align: center; margin: 0 0 2px; }
        table.cmp-table th .th-logo img { max-height: 20px; max-width: 70px; }
        table.cmp-table th .th-name { text-align: center; }
        table.cmp-table td { padding: 3px 6px; border: 1px solid #dde3ed; text-align: right; }
        table.cmp-table tbody tr:nth-child(even) { background: #fafbfc; }
        table.cmp-table tfoot td { background: #004f8c; color: #fff; font-weight: bold; font-size: 9.5px; }
        .pdf-footer-text { padding: 8px 24px; background: #f8f9fb; border-top: 1px solid #dde3ed; font-size: 8px; color: #6b7280; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="letterhead">
        <img src="{{ public_path('images/MIC-Global-Risks_logo_no_background.png') }}" alt="MIC Global Risks">
        <h1>Underwriters Benefits/Cover Comparison - Motor Insurance Quotation</h1>
        <p>Side-by-side premium and benefits comparison across every underwriter that covers this vehicle. Generated {{ now()->format('d F Y, H:i') }}.</p>
    </div>

    <div class="pdf-body">
        <table class="summary-table">
            <tr>
                <td class="label">Customer</td>
                <td class="label">Phone</td>
                <td class="label">Email</td>
                <td class="label">Registration</td>
                <td class="label">Make / Model</td>
                <td class="label">Year</td>
                <td class="label">Chassis No.</td>
                <td class="label">Body Type</td>
                <td class="label">Estimated Value</td>
            </tr>
            <tr>
                <td>{{ $customer['name'] ?? '—' }}</td>
                <td>{{ $customer['phone'] ?? '—' }}</td>
                <td>{{ $customer['email'] ?? '—' }}</td>
                <td>{{ $vehicle['reg'] ?? '—' }}</td>
                <td>{{ trim(($vehicle['make'] ?? '') . ' ' . ($vehicle['model'] ?? '')) ?: '—' }}</td>
                <td>{{ $vehicle['year'] ?? '—' }}</td>
                <td>{{ $vehicle['chassis'] ?? '—' }}</td>
                <td>{{ $vehicle['body_type'] ?? '—' }}</td>
                <td>KES {{ number_format($valuation, 2) }}</td>
            </tr>
        </table>

        <div class="section-title">Premium Comparison &mdash; All Underwriters</div>
        <table class="cmp-table">
            <thead>
                <tr>
                    <th class="desc-col">Description</th>
                    @foreach($quotations as $q)
                    <th>
                        @php($logoPath = $q->company->logoPath())
                        <div class="th-logo">
                            @if($logoPath && file_exists($logoPath))
                                <img src="{{ $logoPath }}" alt="{{ $q->company->name }}">
                            @endif
                        </div>
                        <div class="th-name">{{ $q->company->name }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="desc-col">Estimated Vehicle Value</td>
                    @foreach($quotations as $q)
                    <td>KES {{ number_format($valuation, 2) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">Premium Rate</td>
                    @foreach($quotations as $q)
                    <td>{{ \App\Models\Rate::fmtPercent($q->breakdown['premium_rate'] ?? 0) }}%</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">Basic Annual Premium</td>
                    @foreach($quotations as $q)
                    <td>KES {{ number_format($q->breakdown['basic_premium'] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">PVT Premium</td>
                    @foreach($quotations as $q)
                    <td>{{ !empty($q->breakdown['is_pvt_inclusive']) ? 'Inclusive' : 'KES ' . number_format($q->breakdown['pvt_premium'] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">PLL Premium</td>
                    @foreach($quotations as $q)
                    <td>KES {{ number_format($q->breakdown['pll_total'] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">Levies</td>
                    @foreach($quotations as $q)
                    <td>KES {{ number_format($q->breakdown['levis'] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">Stamp Duty</td>
                    @foreach($quotations as $q)
                    <td>KES {{ number_format($q->breakdown['stamp_duty'] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">Excess Protector (Own Damage)</td>
                    @foreach($quotations as $q)
                    <td>No Cover</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">Excess Protector (Theft)</td>
                    @foreach($quotations as $q)
                    <td>No Cover</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="desc-col">Loss of Use</td>
                    @foreach($quotations as $q)
                    <td>Not Applicable</td>
                    @endforeach
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="desc-col">Total Premium (incl. taxes)</td>
                    @foreach($quotations as $q)
                    <td>KES {{ number_format($q->breakdown['total_premium'] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
            </tfoot>
        </table>

        @foreach($benefitsComparison as $categoryLabel => $section)
        <div class="section-title">{{ $categoryLabel }}</div>
        <table class="cmp-table">
            <thead>
                <tr>
                    <th class="desc-col">Benefit</th>
                    @foreach($section['companies'] as $company)
                    <th>
                        <div class="th-logo">
                            @if($company['logo'] && file_exists($company['logo']))
                                <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}">
                            @endif
                        </div>
                        <div class="th-name">{{ $company['name'] }}</div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($section['rows'] as $row)
                <tr>
                    <td class="desc-col">{{ $row['item'] }}</td>
                    @foreach($section['companies'] as $company)
                    <td>{!! $row['values'][$company['slug']] !!}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    </div>

    <div class="pdf-footer-text">
        This comparison is an invitation to treat. Subject to satisfactory vehicle valuation, sanction limitation and exclusion clauses, and each underwriter's standard policy terms and conditions.
    </div>
</body>
</html>
