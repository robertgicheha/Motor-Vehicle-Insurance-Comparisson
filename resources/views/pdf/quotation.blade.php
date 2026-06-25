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
                @if($quotation->company->logo)
                    <img src="{{ public_path('storage/' . $quotation->company->logo) }}" class="company-logo" alt="{{ $quotation->company->name }}">
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

    <table>
        <thead><tr><th>Description</th><th style="text-align:right">Amount (KES)</th></tr></thead>
        <tbody>
            <tr><td>Estimated Vehicle Value</td><td style="text-align:right;font-weight:bold">{{ number_format($quotation->car_valuation, 2) }}</td></tr>
            <tr><td>Basic Annual Premium</td><td style="text-align:right">{{ number_format($quotation->premium, 2) }}</td></tr>
            <tr><td>PVT Premium</td><td style="text-align:right">{{ number_format($quotation->pvt_premium, 2) }}</td></tr>
            @if($quotation->excess_own_damage)
                <tr><td>Excess - Own Damage</td><td style="text-align:right">{{ number_format($quotation->excess_own_damage, 2) }}</td></tr>
            @endif
            @if($quotation->excess_theft)
                <tr><td>Excess - Theft</td><td style="text-align:right">{{ number_format($quotation->excess_theft, 2) }}</td></tr>
            @endif
            @if($quotation->breakdown)
                @foreach($quotation->breakdown as $key => $value)
                    @if(!in_array($key, ['premium', 'pvt_premium', 'total_premium', 'excess_own_damage', 'excess_theft']))
                        <tr><td>{{ ucwords(str_replace('_', ' ', $key)) }}</td><td style="text-align:right">{{ is_numeric($value) ? number_format($value, 2) : $value }}</td></tr>
                    @endif
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row"><td>Total Premium</td><td style="text-align:right">{{ number_format($quotation->total_premium, 2) }}</td></tr>
        </tfoot>
    </table>

    <p style="font-size:10px;color:#64748b;margin-top:15px">
        This quotation is valid for 15 days from the date of issue. Comprehensive cover is subject to a satisfactory valuation report conducted within the first month of cover. Subject to sanction limitation and exclusion clause and standard policy terms and conditions.
    </p>
    <p style="font-size:10px;color:#64748b">
        Geographical limit: Kenya / Uganda / Tanzania.
    </p>

    <div class="footer">
        Generated by MIC Global Risks Insurance (Kenya) Limited | www.micglobalrisks.com<br>
        Quotation {{ $quotation->quotation_number }} | This is a computer-generated document.
    </div>
</body>
</html>
