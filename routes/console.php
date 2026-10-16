<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// php artisan send-mail you@example.com
// Sends one test email straight through the Mailtrap Sending API (bypassing
// Laravel's mail config entirely) — useful for confirming the API token
// itself is valid, independent of which mailer app-wide config points at.
Artisan::command('send-mail {to=robertgicheha3@gmail.com}', function (string $to) {
    $email = (new MailtrapEmail())
        ->from(new Address(config('mail.from.address'), config('mail.from.name')))
        ->to(new Address($to))
        ->subject('You are awesome!')
        ->category('Integration Test')
        ->text('Congrats for sending test email with Mailtrap!');

    $response = MailtrapClient::initSendingEmails(
        apiKey: config('services.mailtrap.api_token')
    )->send($email);

    $this->info('Sent.');
    dump(ResponseHelper::toArray($response));
})->purpose('Send a Mailtrap test email');

// Uptime monitoring — requires the server's cron to call `schedule:run` every minute
// (standard Laravel setup: * * * * * php artisan schedule:run).
Schedule::command('health:check')->everyFifteenMinutes();

// Log retention: activity logs and audit-trail entries are kept for one month
// and then permanently deleted from the database. Runs daily at 02:30.
Schedule::command('logs:prune --days=30')->dailyAt('02:30');
