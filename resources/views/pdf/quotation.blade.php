<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #1a1a2e; margin: 0; padding: 0; }
        .mic-banner { text-align: center; padding: 6px 0 4px; }
        .mic-banner img { max-height: 36px; }
        .letterhead {
            display: flex; justify-content: space-between; align-items: stretch; min-height: 64px; border-bottom: 1.5px solid #dde3ed;
        }
        .lh-left {
            display: flex; flex-direction: column; align-items: flex-start; justify-content: center; padding: 8px 26px; min-width: 220px;
        }
        .lh-left img { max-height: 34px; max-width: 180px; object-fit: contain; }
        .lh-left .lh-fallback-name { font-size: 18px; font-weight: 800; color: #1a1a2e; letter-spacing: .01em; display: none; }
        .lh-left .lh-caption { margin-top: 4px; font-size: 10px; }
        .lh-left .lh-caption span { display: block; color: #6b7280; line-height: 1.4; }
        .lh-left .lh-caption .lh-company-name { font-weight: 700; color: #1a1a2e; }
        .lh-right { flex: 1; display: flex; align-items: center; justify-content: flex-end; padding: 8px 26px; text-align: right; }
        .lh-right p { font-size: 10px; line-height: 1.4; color: #6b7280; margin: 0; }
        .pdf-body { padding: 10px 26px; }
        .pdf-body h2 { font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; margin: 0; text-decoration: underline; display: inline-block; }
        .re-row { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 6px; }
        .date-ref-row { width: 100%; border-collapse: collapse; margin: 4px 0 5px; font-size: 10.5px; }
        .date-ref-row td { border: none; padding: 0; }
        .pdf-body p { font-size: 10.5px; line-height: 1.35; margin: 0 0 5px; }
        .pdf-body table { width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 10px; }
        .pdf-body table th { background: #f0f4fa; padding: 3px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; border: 1px solid #dde3ed; }
        .pdf-body table td { padding: 3px 8px; border: 1px solid #dde3ed; }
        .pdf-footer-text {
            padding: 8px 26px; background: #f8f9fb; border-top: 1px solid #dde3ed;
            font-size: 9px; color: #6b7280; line-height: 1.4;
        }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #1a1a2e; margin: 8px 0 3px; padding-bottom: 3px; border-bottom: 1.5px solid #dde3ed; }
        .total-row { background: #004f8c; color: white; font-weight: bold; }
        .total-row td { padding: 5px 8px; font-size: 11px; }
        .company-logo { max-height: 40px; max-width: 140px; }
        .grid-2col td { width: 25%; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
@include('pdf.partials.quotation-body', ['quotation' => $quotation])
</body>
</html>
