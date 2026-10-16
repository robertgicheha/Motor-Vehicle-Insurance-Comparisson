<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden;">
    <div style="background: #005F9E; padding: 24px; text-align: center;">
        <h1 style="color: #ffffff; margin: 0; font-size: 18px;">New Claim Logged</h1>
        <p style="color: #93c5fd; margin: 4px 0 0; font-size: 12px;">{{ config('app.name') }}</p>
    </div>
    <div style="padding: 24px;">
        <p style="color: #374151; font-size: 14px; margin: 0 0 16px;">
            A Claim has been successfully logged and the details of the claim are as follows:
        </p>

        <p style="font-size: 11px; font-weight: 700; color: #005F9E; text-transform: uppercase; letter-spacing: 0.05em; margin: 20px 0 6px;">Claim Reference</p>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Claim Number</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb; font-weight: 700;">{{ $claim['claim_number'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Underwriter</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $claim['company_name'] ?? 'N/A' }}</td>
            </tr>
        </table>

        <p style="font-size: 11px; font-weight: 700; color: #005F9E; text-transform: uppercase; letter-spacing: 0.05em; margin: 20px 0 6px;">Vehicle Details</p>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Registration No</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $claim['registration_number'] ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Make / Model</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ trim(($claim['vehicle_details']['make'] ?? '') . ' ' . ($claim['vehicle_details']['model'] ?? '')) ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Year</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $claim['vehicle_details']['year'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Chassis No</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $claim['vehicle_details']['chassis_number'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Engine No</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $claim['vehicle_details']['engine_number'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; font-weight: 600;">Vehicle Valuation</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827;">{{ !empty($claim['vehicle_details']['valuation']) ? 'KES ' . number_format($claim['vehicle_details']['valuation']) : 'N/A' }}</td>
            </tr>
        </table>

        <p style="font-size: 11px; font-weight: 700; color: #005F9E; text-transform: uppercase; letter-spacing: 0.05em; margin: 20px 0 6px;">Incident Details</p>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
            @if(!empty($claim['claim_type']))
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Type of Claim</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">
                    {{ $claim['claim_type'] }}
                    @if(!empty($claim['claim_type_other'])) — {{ $claim['claim_type_other'] }} @endif
                </td>
            </tr>
            @endif
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; border-bottom: 1px solid #e5e7eb; font-weight: 600;">Incident Date</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ \Carbon\Carbon::parse($claim['incident_date'])->format('d M Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 12px; color: #6b7280; font-weight: 600;">Police Notified</td>
                <td style="padding: 8px 12px; font-size: 12px; color: #111827;">{{ $claim['police_notified'] ? 'Yes' : 'No' }}</td>
            </tr>
        </table>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-bottom: 20px;">
            <p style="font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px;">Incident Description</p>
            <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.5;">{{ $claim['incident_description'] }}</p>
        </div>

        <p style="font-size: 12px; color: #6b7280; text-align: center; margin: 0;">
            Please review this claim and take appropriate action.
        </p>
    </div>
    <div style="background: #f3f4f6; padding: 16px; text-align: center;">
        <p style="font-size: 10px; color: #9ca3af; margin: 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
