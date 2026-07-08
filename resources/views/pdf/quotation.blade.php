<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 30px; }
        .header { background: #004f8c; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 4px 0 0; opacity: 0.85; font-size: 12px; }
        .meta { display: flex; justify-content: space-between; padding: 15px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .meta-item { font-size: 10px; }
        .meta-item strong { display: block; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #0066B3; color: white; padding: 8px 12px; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { padding: 7px 12px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .total-row { background: #004f8c; color: white; font-weight: bold; }
        .total-row td { padding: 10px 12px; }
        .footer { margin-top: 20px; padding: 15px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 9px; color: #64748b; text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-active { background: #dcfce7; color: #166534; }
        .company-logo { max-height: 40px; max-width: 140px; }
    </style>
</head>
<body>
    <div class="header">
        <div style="display:flex;justify-content:space-between;align-items:center">
            <div style="display:flex;align-items:center;gap:12px">
                @php $__logoPath = $quotation->company->logo ? public_path('storage/' . $quotation->company->logo) : (isset(\App\Models\Company::hardcodedLogoMap()[$quotation->company->slug]) ? public_path(\App\Models\Company::hardcodedLogoMap()[$quotation->company->slug]) : ''); @endphp
                @if($__logoPath && file_exists($__logoPath))
                    <img src="{{ $__logoPath }}" class="company-logo" alt="{{ $quotation->company->name }}">
                @else
                    <span style="font-size:16px;font-weight:800;color:#fff">{{ $quotation->company->getInitials() }}</span>
                @endif
                <div>
                    <h1>Insurance Quotation</h1>
                    <p>{{ $quotation->company->name }}</p>
                </div>
            </div>
            <div style="text-align:right">
                <span class="badge badge-active">{{ strtoupper($quotation->status) }}</span>
            </div>
        </div>
    </div>

    <div class="meta">
        <div class="meta-item">
            Quotation No.<strong>{{ $quotation->quotation_number }}</strong>
        </div>
        <div class="meta-item">
            Date<strong>{{ $quotation->created_at->format('d M Y') }}</strong>
        </div>
        <div class="meta-item">
            Valid Until<strong>{{ $quotation->created_at->addDays(15)->format('d M Y') }}</strong>
        </div>
    </div>

    <table>
        <tr><td style="width:40%;font-weight:bold">Customer</td><td>{{ $quotation->user?->full_name ?? 'N/A' }}</td></tr>
        <tr><td style="font-weight:bold">Vehicle</td><td>{{ $quotation->car_make }} {{ $quotation->car_model }} ({{ $quotation->car_year }})</td></tr>
        <tr><td style="font-weight:bold">Registration</td><td>{{ $quotation->car_registration ?? 'N/A' }}</td></tr>
        <tr><td style="font-weight:bold">Cover Type</td><td>Comprehensive</td></tr>
    </table>

    $__companyRates = $quotation->company->rates()
        ->where('vehicle_type', $quotation->vehicle_type ?? 'private')
        ->get()
        ->groupBy('category');

    $_breakdown = $quotation->breakdown ?? [];
    $_premiumRate = $_breakdown['premium_rate'] ?? 6.5;
    $_pvtRate = $_breakdown['pvt_rate'] ?? 0;
    $_levisRate = $_breakdown['levis_rate'] ?? 0.0045;
    $_stampDuty = $_breakdown['stamp_duty'] ?? 40;
    $_pvtPremium = $quotation->pvt_premium ?? 0;
    $_pllPerPerson = $_breakdown['pll_per_person'] ?? 0;
    $_numPassengers = $quotation->num_passengers ?? 1;
    $_pllTotal = $_pllPerPerson * $_numPassengers;
    $_levisAmount = ($_pvtPremium + $_pllTotal) * $_levisRate;

    function fmtRateValue($r) {
        $parts = [];
        if ($r->percentage && $r->percentage > 0) $parts[] = $r->percentage . '%';
        if ($r->value && $r->value > 0) $parts[] = 'Kes ' . number_format($r->value, 0);
        if ($r->min_value && $r->min_value > 0) $parts[] = 'Min Kes ' . number_format($r->min_value, 0);
        if ($r->max_value && $r->max_value > 0) $parts[] = 'Max Kes ' . number_format($r->max_value, 0);
        if ($r->min_premium && $r->min_premium > 0) $parts[] = 'Min prem Kes ' . number_format($r->min_premium, 0);
        return implode(', ', $parts) ?: ($r->notes ? substr($r->notes, 0, 60) : '');
    }
@endphp

<table>
    <tr><td style="width:40%;font-weight:bold">Customer</td><td>{{ $quotation->user?->full_name ?? 'N/A' }}</td></tr>
    <tr><td style="font-weight:bold">Vehicle</td><td>{{ $quotation->car_make }} {{ $quotation->car_model }} ({{ $quotation->car_year }})</td></tr>
    <tr><td style="font-weight:bold">Registration</td><td>{{ $quotation->car_registration ?? 'N/A' }}</td></tr>
    <tr><td style="font-weight:bold">Cover Type</td><td>Comprehensive {{ ucfirst($quotation->vehicle_type ?? 'private') }}</td></tr>
</table>

<h3 style="margin:20px 0 0;font-size:12px;color:#004f8c">PREMIUM BREAKDOWN</h3>
<table>
    <thead><tr><th>Description</th><th style="text-align:right">Amount (KES)</th></tr></thead>
    <tbody>
        <tr><td>Estimated Vehicle Value</td><td style="text-align:right;font-weight:bold">{{ number_format($quotation->car_valuation, 2) }}</td></tr>
        <tr><td>Basic Annual Premium ({{ $_premiumRate }}%)</td><td style="text-align:right">{{ number_format($quotation->premium, 2) }}</td></tr>
        @if($_pvtPremium > 0 || $_pvtRate > 0)
        <tr><td>PVT Premium ({{ number_format($_pvtRate, 2) }}%)</td><td style="text-align:right">{{ number_format($_pvtPremium, 2) }}</td></tr>
        @endif
        @if($_pllPerPerson > 0)
        <tr><td>PLL Premium (KES {{ number_format($_pllPerPerson, 0) }} × {{ $_numPassengers }} pax)</td><td style="text-align:right">{{ number_format($_pllTotal, 2) }}</td></tr>
        @endif
        <tr><td>Levis ({{ number_format($_levisRate * 100, 2) }}%)</td><td style="text-align:right">{{ number_format($_levisAmount, 2) }}</td></tr>
        <tr><td>Stamp Duty</td><td style="text-align:right">{{ number_format($_stampDuty, 2) }}</td></tr>
        @if($quotation->excess_own_damage)
            <tr><td>Excess - Own Damage</td><td style="text-align:right">{{ number_format($quotation->excess_own_damage, 2) }}</td></tr>
        @endif
        @if($quotation->excess_theft)
            <tr><td>Excess - Theft</td><td style="text-align:right">{{ number_format($quotation->excess_theft, 2) }}</td></tr>
        @endif
    </tbody>
    <tfoot>
        <tr class="total-row"><td>Total Premium</td><td style="text-align:right">{{ number_format($quotation->total_premium, 2) }}</td></tr>
    </tfoot>
</table>

@if($__companyRates->has('Cover Limits'))
<h3 style="margin:20px 0 0;font-size:12px;color:#004f8c">BENEFITS / COVER LIMITS</h3>
<table>
    <thead><tr><th>Benefit</th><th style="text-align:right">Limit (KES)</th><th>Notes</th></tr></thead>
    <tbody>
    @foreach($__companyRates->get('Cover Limits') as $r)
        <tr>
            <td>{{ $r->item_name }}</td>
            <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : ($r->notes ? substr($r->notes, 0, 30) : '—') }}</td>
            <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if($__companyRates->has('Extras'))
<h3 style="margin:20px 0 0;font-size:12px;color:#004f8c">EXTRAS</h3>
<table>
    <thead><tr><th>Extra</th><th style="text-align:right">Limit (KES)</th><th>Notes</th></tr></thead>
    <tbody>
    @foreach($__companyRates->get('Extras') as $r)
        <tr>
            <td>{{ $r->item_name }}</td>
            <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : '—' }}</td>
            <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if($__companyRates->has('Third Party Limits'))
<h3 style="margin:20px 0 0;font-size:12px;color:#004f8c">THIRD PARTY LIMITS OF LIABILITY</h3>
<table>
    <thead><tr><th>Cover</th><th style="text-align:right">Limit (KES)</th></tr></thead>
    <tbody>
    @foreach($__companyRates->get('Third Party Limits') as $r)
        <tr>
            <td>{{ $r->item_name }}</td>
            <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : ($r->notes ?? '—') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if($__companyRates->has('Excess'))
<h3 style="margin:20px 0 0;font-size:12px;color:#004f8c">POLICY EXCESSES</h3>
<table>
    <thead><tr><th>Excess Type</th><th style="text-align:right">Excess Amount</th><th>Notes</th></tr></thead>
    <tbody>
    @foreach($__companyRates->get('Excess') as $r)
        <tr>
            <td>{{ $r->item_name }}</td>
            <td style="text-align:right">{{ fmtRateValue($r) ?: '—' }}</td>
            <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if($__companyRates->has('Additional Cover'))
<h3 style="margin:20px 0 0;font-size:12px;color:#004f8c">SPECIAL CONDITIONS / ADDITIONAL COVER</h3>
<table>
    <thead><tr><th>Condition</th><th>Status / Details</th></tr></thead>
    <tbody>
    @foreach($__companyRates->get('Additional Cover') as $r)
        <tr>
            <td>{{ $r->item_name }}</td>
            <td style="font-size:10px">{{ $r->notes ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

@if($__companyRates->has('Optional Benefits'))
<h3 style="margin:20px 0 0;font-size:12px;color:#004f8c">OPTIONAL BENEFITS</h3>
<table>
    <thead><tr><th>Benefit</th><th style="text-align:right">Premium (KES)</th><th>Notes</th></tr></thead>
    <tbody>
    @foreach($__companyRates->get('Optional Benefits') as $r)
        <tr>
            <td>{{ $r->item_name }}</td>
            <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : '—' }}</td>
            <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

<p style="font-size:10px;color:#64748b;margin-top:15px">
    This quotation is valid for 15 days from the date of issue. Comprehensive cover is subject to a satisfactory valuation report conducted within the first month of cover. Subject to sanction limitation and exclusion clause and standard policy terms and conditions.
</p>
<p style="font-size:10px;color:#64748b">
    Geographical limit: The geographical area is specified in the Special Conditions above.
</p>

    <div class="footer">
        Generated by MIC Global Risks Insurance (Kenya) Limited | www.micglobalrisks.com<br>
        Quotation {{ $quotation->quotation_number }} | This is a computer-generated document.
    </div>
</body>
</html>
