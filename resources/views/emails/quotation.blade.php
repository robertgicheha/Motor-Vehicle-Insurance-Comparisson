<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0; background: #f4f7f9; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #004f8c, #0066B3); padding: 28px 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 13px; }
        .body-content { padding: 28px 32px; }
        .body-content h2 { color: #1a1a2e; font-size: 16px; margin: 0 0 16px; }
        .body-content p { color: #475569; font-size: 13px; line-height: 1.6; margin: 0 0 12px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .details-table td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .details-table td:first-child { color: #64748b; width: 40%; }
        .details-table td:last-child { color: #1a1a2e; font-weight: 600; }
        .premium-box { background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; text-align: center; margin: 16px 0; }
        .premium-box .amount { font-size: 28px; font-weight: 800; color: #0066B3; }
        .premium-box .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 11px; margin: 4px 0; }
        .btn { display: inline-block; padding: 10px 24px; background: #0066B3; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; margin-top: 12px; }
        .company-logo { max-height: 50px; max-width: 160px; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ $message->embed(public_path('img/MIC-Global-Risks_logo_no_background.png')) }}" alt="MIC Global Risks" style="max-height:50px;max-width:160px;margin-bottom:8px;filter:brightness(0) invert(1)">
            <h1>Insurance Quotation Generated</h1>
            <p>{{ $quotation->quotation_number }}</p>
        </div>

        <div class="body-content">
            <h2>Dear {{ $quotation->user?->full_name ?? 'Valued Customer' }},</h2>

            <p>Thank you for choosing <strong>{{ $quotation->company->name }}</strong>. Your comprehensive motor insurance quotation has been successfully generated. Please find a summary of your quotation below.</p>

            <div class="premium-box">
                <div class="label">Total Premium</div>
                <div class="amount">KES {{ number_format($quotation->total_premium, 2) }}</div>
            </div>

            <table class="details-table">
                <tr><td>Quotation Number</td><td>{{ $quotation->quotation_number }}</td></tr>
                <tr><td>Insurance Company</td><td>{{ $quotation->company->name }}</td></tr>
                <tr><td>Vehicle</td><td>{{ $quotation->car_make }} {{ $quotation->car_model }} ({{ $quotation->car_year }})</td></tr>
                <tr><td>Registration</td><td>{{ $quotation->car_registration ?? 'N/A' }}</td></tr>
                <tr><td>Vehicle Valuation</td><td>KES {{ number_format($quotation->car_valuation, 2) }}</td></tr>
                <tr><td>Basic Premium</td><td>KES {{ number_format($quotation->premium, 2) }}</td></tr>
                <tr><td>Cover Type</td><td>Comprehensive</td></tr>
                <tr><td>Status</td><td>{{ ucfirst($quotation->status) }}</td></tr>
                <tr><td>Valid Until</td><td>{{ $quotation->created_at->addDays(15)->format('d M Y') }}</td></tr>
            </table>

            <p>Please find the detailed quotation PDF attached to this email. You can also view and download your quotation at any time by visiting our portal.</p>

            <div style="text-align:center">
                <a href="{{ $url ?? '#' }}" class="btn">View Quotation Online</a>
            </div>

            <p style="margin-top:16px;font-size:12px;color:#94a3b8">
                This quotation is valid for 15 days from the date of issue. Subject to sanction limitation and exclusion clause and standard policy terms and conditions.
            </p>
        </div>

        <div class="footer">
            <p><strong>MIC Global Risks Insurance (Kenya) Limited</strong></p>
            <p>www.micglobalrisks.com | This is a computer-generated quotation.</p>
        </div>
    </div>
</body>
</html>
