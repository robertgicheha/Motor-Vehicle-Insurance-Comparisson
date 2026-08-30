<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'openweather' => [
        'key' => env('OPENWEATHER_API_KEY'),
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'clamav' => [
        'host' => env('CLAMAV_HOST', '127.0.0.1'),
        'port' => (int) env('CLAMAV_PORT', 3310),
    ],

    'ai_document' => [
        'api_key' => env('AI_DOCUMENT_API_KEY', ''),
        'endpoint' => env('AI_DOCUMENT_ENDPOINT', 'https://api.openai.com/v1/chat/completions'),
    ],

    'sticker' => [
        'turnaround_minutes' => (int) env('STICKER_TURNAROUND_MINUTES', 30),
        'mic_cc_email' => env('MIC_CC_EMAIL', config('mail.from.address')),
    ],

    'mailtrap' => [
        'api_token' => env('MAILTRAP_API_TOKEN', ''),
    ],

    'nav' => [
        'enabled' => env('NAV_MAIL_ENABLED', false),
        'mail_wsdl' => env('NAV_MAIL_WSDL', ''),
        'mail_function' => env('NAV_MAIL_FUNCTION', 'SendMail'),
        'ntlm_domain' => env('NAV_NTLM_DOMAIN', ''),
        'ntlm_username' => env('NAV_NTLM_USERNAME', ''),
        'ntlm_password' => env('NAV_NTLM_PASSWORD', ''),
        'function_certificate_email' => env('NAV_FUNCTION_CERTIFICATE_EMAIL', 'SendCertificateEmail'),
        'function_quotation_email' => env('NAV_FUNCTION_QUOTATION_EMAIL', 'SendQuotationEmail'),
        'function_claim_email' => env('NAV_FUNCTION_CLAIM_EMAIL', 'SendClaimEmail'),
    ],

];
