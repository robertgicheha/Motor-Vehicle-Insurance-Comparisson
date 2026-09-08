<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden;">
    <div style="background: #005F9E; padding: 24px; text-align: center;">
        <h1 style="color: #ffffff; margin: 0; font-size: 18px;">{{ $label }}</h1>
        <p style="color: #93c5fd; margin: 4px 0 0; font-size: 12px;">{{ config('app.name') }}</p>
    </div>
    <div style="padding: 24px;">
        <p style="color: #374151; font-size: 14px; margin: 0 0 8px;">
            The claims report you requested is attached to this email as a PDF.
        </p>
        <p style="color: #6b7280; font-size: 12px; margin: 0;">
            Generated {{ now()->format('d F Y, h:i A') }}.
        </p>
    </div>
    <div style="background: #f3f4f6; padding: 16px; text-align: center;">
        <p style="font-size: 10px; color: #9ca3af; margin: 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
