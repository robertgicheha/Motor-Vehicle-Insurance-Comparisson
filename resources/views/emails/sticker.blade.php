<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0; background: #f4f7f9; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e40af, #7c3aed); padding: 28px 32px; text-align: center; }
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
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #dcfce7; color: #166534; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 11px; margin: 4px 0; }
        .btn { display: inline-block; padding: 10px 24px; background: #0066B3; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Insurance Certificate Generated</h1>
            <p>{{ $sticker->sticker_number }}</p>
        </div>

        <div class="body-content">
            <h2>Dear {{ $sticker->customer_name }},</h2>

            <p>Your motor insurance certificate has been successfully generated with <strong>{{ $sticker->company->name }}</strong>. Please find a summary of your certificate below.</p>

            <div class="premium-box">
                <div class="label">Premium</div>
                <div class="amount">KES {{ number_format($sticker->premium, 2) }}</div>
            </div>

            <table class="details-table">
                <tr><td>Certificate Number</td><td>{{ $sticker->sticker_number }}</td></tr>
                <tr><td>Insurance Company</td><td>{{ $sticker->company->name }}</td></tr>
                <tr><td>Customer Name</td><td>{{ $sticker->customer_name }}</td></tr>
                <tr><td>Vehicle</td><td>{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</td></tr>
                <tr><td>Registration</td><td>{{ $sticker->vehicle_registration ?? 'N/A' }}</td></tr>
                <tr><td>Vehicle Valuation</td><td>KES {{ number_format($sticker->car_valuation, 2) }}</td></tr>
                <tr><td>Cover Type</td><td>{{ $sticker->cover_type }}</td></tr>
                <tr><td>Status</td><td><span class="badge">{{ ucfirst($sticker->status) }}</span></td></tr>
            </table>

            <p>Please find the detailed certificate PDF attached to this email. You can also view and download your certificate at any time.</p>

            <div style="text-align:center">
                <a href="{{ $url ?? '#' }}" class="btn">View Certificate Online</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>MIC Global Risks Insurance (Kenya) Limited</strong></p>
            <p>www.micglobalrisks.com | This is a computer-generated certificate.</p>
        </div>
    </div>
</body>
</html>
