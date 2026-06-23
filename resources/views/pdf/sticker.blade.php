<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Insurance Sticker {{ $sticker->sticker_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 0; }
        .sticker { width: 210mm; min-height: 297mm; padding: 0; position: relative; }
        .header-bar { background: #004f8c; color: white; padding: 12px 20px; }
        .header-bar h1 { margin: 0; font-size: 14px; }
        .header-bar p { margin: 2px 0 0; opacity: 0.85; font-size: 10px; }
        .content { padding: 15px 20px; }
        .field { margin-bottom: 6px; }
        .field-label { font-size: 8px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
        .field-value { font-size: 12px; font-weight: bold; color: #1a1a2e; }
        .grid-2 { display: flex; flex-wrap: wrap; }
        .grid-2 .col { width: 48%; margin-right: 2%; }
        .divider { border: none; border-top: 2px dashed #cbd5e1; margin: 12px 0; }
        .footer-bar { position: absolute; bottom: 0; left: 0; right: 0; background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 8px 20px; font-size: 7px; color: #94a3b8; text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 8px; font-weight: bold; background: #dcfce7; color: #166534; }
        .company-name { font-size: 16px; font-weight: bold; color: #004f8c; }
        .sticker-number { font-size: 8px; color: #94a3b8; }
        .qr-placeholder { width: 60px; height: 60px; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; font-size: 7px; color: #94a3b8; text-align: center; float: right; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="sticker">
        <div class="header-bar">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <h1>Motor Insurance Certificate</h1>
                    <p>{{ $sticker->company->name }}</p>
                </div>
                <div style="text-align:right">
                    <span class="badge">{{ strtoupper($sticker->cover_type) }}</span>
                </div>
            </div>
        </div>

        <div class="content">
            <div style="text-align:center;margin-bottom:12px">
                <div class="company-name">{{ $sticker->company->name }}</div>
                <div class="sticker-number">Sticker #{{ $sticker->sticker_number }}</div>
            </div>

            <hr class="divider">

            <div class="grid-2">
                <div class="col">
                    <div class="field">
                        <div class="field-label">Customer Name</div>
                        <div class="field-value">{{ $sticker->customer_name }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Vehicle</div>
                        <div class="field-value">{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Registration</div>
                        <div class="field-value">{{ $sticker->vehicle_registration ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col">
                    <div class="field">
                        <div class="field-label">Vehicle Valuation</div>
                        <div class="field-value">KES {{ number_format($sticker->car_valuation, 2) }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Premium Paid</div>
                        <div class="field-value">KES {{ number_format($sticker->premium, 2) }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Rate</div>
                        <div class="field-value">{{ $sticker->premium_rate }}%</div>
                    </div>
                </div>
            </div>

            <hr class="divider">

            <div style="font-size:9px;color:#475569">
                <p><strong>Cover:</strong> This certificate confirms that the above-named vehicle is covered under a {{ $sticker->cover_type }} motor insurance policy with {{ $sticker->company->name }}.</p>
                <p><strong>Validity:</strong> Subject to policy terms and conditions. This certificate is valid for the period specified in the policy schedule.</p>
                <p><strong>Generated:</strong> {{ $sticker->generated_at?->format('d M Y h:i A') ?? $sticker->created_at->format('d M Y h:i A') }}</p>
            </div>

            @if($sticker->breakdown)
                <hr class="divider">
                <table style="width:100%;border-collapse:collapse;font-size:9px">
                    <thead><tr style="background:#f1f5f9"><th style="padding:4px 8px;text-align:left">Item</th><th style="padding:4px 8px;text-align:right">Amount (KES)</th></tr></thead>
                    <tbody>
                        @foreach($sticker->breakdown as $key => $value)
                            <tr><td style="padding:3px 8px;border-bottom:1px solid #e2e8f0">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td style="padding:3px 8px;border-bottom:1px solid #e2e8f0;text-align:right">{{ is_numeric($value) ? number_format($value, 2) : $value }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="footer-bar">
            MIC Global Risks Insurance (Kenya) Limited | www.micglobalrisks.com | Sticker {{ $sticker->sticker_number }}
        </div>
    </div>
</body>
</html>
