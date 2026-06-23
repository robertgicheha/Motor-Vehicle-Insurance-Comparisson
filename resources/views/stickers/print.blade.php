<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Insurance Certificate - {{ $sticker->sticker_number }}</title>
    <style>
        @page { 
            margin: 0; 
            size: A4 portrait;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 10mm;
            background: #fff;
            color: #1a1a2e;
        }
        .certificate-wrapper {
            border: 3px solid #004f8c;
            border-radius: 12px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .certificate-header {
            background: linear-gradient(135deg, #004f8c, #0066B3);
            color: white;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .certificate-header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .certificate-header .badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .certificate-body {
            padding: 18px 22px;
        }
        .sticker-number-display {
            text-align: center;
            margin-bottom: 14px;
            padding: 8px;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
        }
        .sticker-number-display .num {
            font-size: 22px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
            color: #004f8c;
            letter-spacing: 2px;
        }
        .sticker-number-display .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 2px;
        }
        .company-name-display {
            text-align: center;
            font-size: 20px;
            font-weight: 800;
            color: #004f8c;
            margin-bottom: 12px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 20px;
            margin-bottom: 14px;
        }
        .info-grid .item .lbl {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .info-grid .item .val {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .divider {
            border: none;
            border-top: 2px solid #e2e8f0;
            margin: 12px 0;
        }
        .premium-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 14px;
            background: #f0f7ff;
            border-radius: 8px;
            border: 1px solid #bfdbfe;
            margin-bottom: 12px;
        }
        .premium-row .lbl {
            font-size: 11px;
            text-transform: uppercase;
            color: #475569;
            font-weight: 600;
        }
        .premium-row .amt {
            font-size: 20px;
            font-weight: 900;
            color: #0066B3;
        }
        .terms {
            font-size: 8px;
            color: #94a3b8;
            line-height: 1.4;
            text-align: center;
        }
        .windscreen-notice {
            background: #fef9c3;
            border: 1px solid #facc15;
            border-radius: 6px;
            padding: 8px 12px;
            margin: 10px 0;
            text-align: center;
            font-size: 9px;
            font-weight: 600;
            color: #854d0e;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center;margin-bottom:10px;font-size:12px;color:#64748b;">
        Print this page in <strong>color</strong>, cut along the border and affix to your windscreen.
    </div>

    <div class="certificate-wrapper">
        <div class="certificate-header">
            <h1>{{ $sticker->company->name }}</h1>
            <span class="badge">{{ $sticker->cover_type }}</span>
        </div>

        <div class="certificate-body">
            <div class="sticker-number-display">
                <div class="label">Certificate Number</div>
                <div class="num">{{ $sticker->sticker_number }}</div>
            </div>

            <div class="company-name-display">MOTOR INSURANCE CERTIFICATE</div>

            <hr class="divider">

            <div class="info-grid">
                <div class="item">
                    <div class="lbl">Customer</div>
                    <div class="val">{{ $sticker->customer_name }}</div>
                </div>
                <div class="item">
                    <div class="lbl">Registration</div>
                    <div class="val">{{ $sticker->vehicle_registration ?? 'N/A' }}</div>
                </div>
                <div class="item">
                    <div class="lbl">Vehicle</div>
                    <div class="val">{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</div>
                </div>
                <div class="item">
                    <div class="lbl">Insurance Company</div>
                    <div class="val">{{ $sticker->company->name }}</div>
                </div>
                <div class="item">
                    <div class="lbl">Valuation</div>
                    <div class="val">KES {{ number_format($sticker->car_valuation) }}</div>
                </div>
                <div class="item">
                    <div class="lbl">Cover Type</div>
                    <div class="val">{{ $sticker->cover_type }}</div>
                </div>
            </div>

            <div class="premium-row">
                <span class="lbl">Premium Paid</span>
                <span class="amt">KES {{ number_format($sticker->premium) }}</span>
            </div>

            <div class="windscreen-notice">
                This certificate must be displayed on the vehicle windscreen at all times.
                Print in color on standard paper and affix to the inside of the windscreen.
            </div>

            <div class="terms">
                <strong>{{ $sticker->company->name }}</strong> &mdash; Policy subject to standard terms and conditions.<br>
                Generated: {{ $sticker->generated_at?->format('d M Y h:i A') ?? $sticker->created_at->format('d M Y h:i A') }}<br>
                Certificate #{{ $sticker->sticker_number }} &mdash; MIC Global Risks Insurance (Kenya) Limited
            </div>
        </div>
    </div>

    <div class="no-print" style="text-align:center;margin-top:14px;">
        <button onclick="window.print()" style="padding:10px 28px;background:#0066B3;color:white;border:none;border-radius:8px;font-size:14px;font-weight:bold;cursor:pointer;">
            Print Certificate
        </button>
    </div>

    <script>
    window.onload = function() { setTimeout(function() { window.print(); }, 500); };
    </script>
</body>
</html>
