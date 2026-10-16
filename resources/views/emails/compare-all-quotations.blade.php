<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0; background: #f4f7f9; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #263B8B, #0066B3); padding: 28px 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 13px; }
        .body-content { padding: 28px 32px; }
        .body-content h2 { color: #1a1a2e; font-size: 16px; margin: 0 0 16px; }
        .body-content p { color: #475569; font-size: 13px; line-height: 1.6; margin: 0 0 12px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .details-table th { text-align: left; padding: 8px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        .details-table td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; color: #1a1a2e; }
        .details-table td.amount { font-weight: 700; text-align: right; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 11px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/LOLC-Kenya.webp')) }}" alt="LOLC Microfinance Bank" style="max-height:50px;max-width:160px;margin-bottom:8px;filter:brightness(0) invert(1)">
            <h1>Your Quotation Comparison</h1>
            <p>{{ $quotations->count() }} insurer{{ $quotations->count() === 1 ? '' : 's' }} compared</p>
        </div>

        <div class="body-content">
            <h2>Dear Customer,</h2>

            <p>As requested, here is a side-by-side comparison of your motor insurance quotation across every underwriter that covers your vehicle. The full quotation document for each insurer is attached as a single combined PDF.</p>

            <table class="details-table">
                <thead>
                    <tr><th>Insurer</th><th style="text-align:right">Rate</th><th style="text-align:right">Total Premium (KES)</th></tr>
                </thead>
                <tbody>
                    @foreach($quotations as $q)
                    <tr>
                        <td>{{ $q->company->name }}</td>
                        <td style="text-align:right">{{ \App\Models\Rate::fmtPercent($q->breakdown['premium_rate'] ?? 0) }}%</td>
                        <td class="amount">{{ number_format($q->breakdown['total_premium'] ?? $q->total_premium, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin-top:16px;font-size:12px;color:#94a3b8">
                Rates shown are subject to satisfactory valuation report and standard policy terms and conditions.
            </p>
        </div>

        <div class="footer">
            <p><strong>LOLC Microfinance Bank</strong></p>
            <p>www.micglobalrisks.com | This is a computer-generated email.</p>
        </div>
    </div>
</body>
</html>
