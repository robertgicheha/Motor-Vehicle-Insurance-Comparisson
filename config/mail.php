<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array. Examples of each type of mailer are provided.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Mail Delivery Provider
    |--------------------------------------------------------------------------
    |
    | Selects which configured transport actually sends application mail.
    | "default" uses the standard MAIL_* mailer chosen below. "lolc" routes
    | every email through LOLC's own SMTP server using the LOLC_MAIL_*
    | credentials (see the "lolc" mailer), so mail leaves from LOLC's mail
    | server rather than the platform's. Set from the Email Settings page.
    |
    */

    'provider' => env('MAIL_PROVIDER', 'default'),

    'default' => env('MAIL_PROVIDER', 'default') === 'lolc'
        ? 'lolc'
        : env('MAIL_MAILER', 'log'),

    // The base mailer chosen under the "default" provider. Kept separate so
    // the Email Settings page can still display the configured transport
    // while the LOLC provider is active (which overrides 'default').
    'base_default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            // Laravel 11+ replaced the old MAIL_ENCRYPTION flag with a scheme
            // ("smtp" = STARTTLS on 587, "smtps" = implicit SSL on 465). The
            // superadmin-facing Email Settings page still speaks in the familiar
            // tls/ssl terms, so translate them here. When MAIL_ENCRYPTION is
            // absent the explicit MAIL_SCHEME (if any) is used, unchanged.
            'scheme' => match (strtolower((string) env('MAIL_ENCRYPTION', ''))) {
                'tls' => 'smtp',
                'ssl' => 'smtps',
                default => env('MAIL_SCHEME'),
            },
            // Read-only mirror of MAIL_ENCRYPTION so the Email Settings page
            // can display the TLS/SSL choice without calling env() directly
            // (which returns null once the config is cached).
            'encryption' => env('MAIL_ENCRYPTION'),
            // Must be null (not an empty string) when unset: Laravel's
            // MailManager::getConfig() treats isset($config['url']) as "a
            // DSN was given" and, on that branch, overwrites 'transport'
            // with the (here, absent) 'driver' key — silently turning every
            // mailer using this block into an unresolvable one. A blank
            // MAIL_URL= line in .env is exactly the isset()-true, value-''
            // case that trips this.
            'url' => env('MAIL_URL') ?: null,
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            // Same empty-string-vs-null trap as 'url' above: env('X', $default)
            // only falls back to $default when X is truly absent, not when
            // it's present-but-blank (MAIL_EHLO_DOMAIN= in .env) — and an
            // empty HELO/EHLO hostname gets a hard "501 Syntax" rejection
            // from Mailtrap's SMTP server.
            'local_domain' => env('MAIL_EHLO_DOMAIN') ?: parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST),
        ],

        // Custom SMTP supplied by LOLC. When MAIL_PROVIDER=lolc this mailer
        // becomes the application default, so all mail is delivered through
        // LOLC's own mail server. Mirrors the 'smtp' block — including the
        // null-when-unset 'url' guard and the tls/ssl → scheme translation —
        // but reads the separate LOLC_MAIL_* credentials so they never clash
        // with the platform's default transport.
        'lolc' => [
            'transport' => 'smtp',
            'scheme' => match (strtolower((string) env('LOLC_MAIL_ENCRYPTION', ''))) {
                'tls' => 'smtp',
                'ssl' => 'smtps',
                default => null,
            },
            'encryption' => env('LOLC_MAIL_ENCRYPTION'),
            'url' => env('LOLC_MAIL_URL') ?: null,
            'host' => env('LOLC_MAIL_HOST'),
            'port' => env('LOLC_MAIL_PORT', 587),
            'username' => env('LOLC_MAIL_USERNAME'),
            'password' => env('LOLC_MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('LOLC_MAIL_EHLO_DOMAIN') ?: parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST),
            // Mailer-scoped "from" (MailManager honours this over mail.from),
            // so LOLC-delivered mail is sent from LOLC's own address.
            'from' => [
                'address' => env('LOLC_MAIL_FROM_ADDRESS') ?: env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('LOLC_MAIL_FROM_NAME') ?: env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel')),
            ],
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'nav' => [
            'transport' => 'nav',
        ],

        'mailtrap' => [
            'transport' => 'mailtrap',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel')),
    ],

];
