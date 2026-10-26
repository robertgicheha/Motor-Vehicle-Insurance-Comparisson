<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0; background: #f4f7f9; }
        .wrapper { max-width: 700px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e40af, #7c3aed); padding: 28px 32px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 6px 0 0; font-size: 13px; }
        .body-content { padding: 28px 32px; }
        .body-content h2 { color: #1a1a2e; font-size: 15px; margin: 20px 0 10px; }
        .body-content h2:first-of-type { margin-top: 0; }
        .body-content p { color: #475569; font-size: 13px; line-height: 1.6; margin: 0 0 12px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 8px 0 16px; }
        .details-table td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .details-table td:first-child { color: #64748b; width: 40%; }
        .details-table td:last-child { color: #1a1a2e; font-weight: 600; }
        .premium-box { background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; text-align: center; margin: 16px 0; }
        .premium-box .amount { font-size: 28px; font-weight: 800; color: #0066B3; }
        .premium-box .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #dcfce7; color: #166534; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 11px; margin: 4px 0; }
        .alert-box { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 14px 18px; margin: 16px 0; }
        .alert-box strong { color: #92400e; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Request for A Sticker</h1>
            {{-- <p>{{ $sticker->sticker_number }}</p> --}}
        </div>

        <div class="body-content">
            <h2>Dear {{ $underwriter_name }},</h2>

            @if($client_type === 'corporate')
            <p>A new motor insurance sticker has been requested on behalf of the corporate client below. The KYC documents have been uploaded and verified. Please find the client and vehicle details below, along with the quotation attached for your perusal, and respond to this email thread with the Sticker and Policy Document attached.</p>
            @else
            <p>A new motor insurance sticker has been requested for the individual client below. The KYC documents have been uploaded and verified. Please find the client and vehicle details below, along with the quotation attached for your perusal, and respond to this email thread with the Sticker and Policy Document attached.</p>
            @endif

            @if($admin_notes)
            <div class="alert-box">
                <strong>Admin Notes:</strong><br>
                {{ $admin_notes }}
            </div>
            @endif

            <div class="premium-box">
                <div class="label">Total Premium</div>
                <div class="amount">KES {{ $premium }}</div>
            </div>
            <p style="font-size:11px;color:#94a3b8;text-align:center;margin-top:-10px">The amount above is subject to the underwriter's confirmation.</p>

            <h2>Client Details</h2>
            <table class="details-table">
                <tr><td>Client Type</td><td>{{ $client_type_label }}</td></tr>
            </table>

            <h2>Vehicle Details</h2>
            <table class="details-table">
                <tr><td>Registration No</td><td>{{ $registration }}</td></tr>
                <tr><td>Engine No</td><td>{{ $engine_number }}</td></tr>
                <tr><td>Chassis No</td><td>{{ $chassis_number }}</td></tr>
                <tr><td>Make of Vehicle</td><td>{{ $make }}</td></tr>
                <tr><td>Model Name</td><td>{{ $model }}</td></tr>
                <tr><td>Year of Manufacture</td><td>{{ $year ?: 'N/A' }}</td></tr>
                <tr><td>Value of Vehicle</td><td>KES {{ $valuation }}</td></tr>
            </table>

            <h2>Sticker &amp; Quotation Reference</h2>
            <table class="details-table">
                {{-- <tr><td>Sticker ID</td><td>{{ $sticker->sticker_number }}</td></tr> --}}
                <tr><td>Insurance Company</td><td>{{ $company }}</td></tr>
                <tr><td>Cover Type</td><td>{{ $cover_type }}</td></tr>
                <tr><td>Quotation Number</td><td>{{ $quotation_number }}</td></tr>
                <tr><td>Issued At</td><td>{{ $issued_at }}</td></tr>
            </table>

            <h2>Attached Documents</h2>
            <p>The following documents are attached for your perusal: the quotation, the KYC documents uploaded and virus-scanned by the client{{ $client_type === 'corporate' ? ' (KRA PIN, Certificate of Incorporation, Vehicle Logbook / Import Documents, and any additional supporting documents)' : ' (ID, KRA PIN, logbook, and any additional supporting documents)' }}. Please respond to this thread with the Sticker and Policy Document.</p>

            <div class="alert-box">
                <strong>Note:</strong> Authenticity of the documents submitted will be subject to verification by the bank.
            </div>
        </div>

        <div class="footer">
            <p><strong>LOLC Microfinance Bank</strong></p>
            <p>www.micglobalrisks.com | This is an automated sticker issuance notification.</p>
        </div>
    </div>
</body>
</html>
