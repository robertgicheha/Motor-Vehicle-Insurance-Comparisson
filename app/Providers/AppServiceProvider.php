<?php

namespace App\Providers;

use App\Services\Nav\NavMailService;
use App\Services\Nav\NavTransport;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Mailtrap\Api\AbstractApi;
use Mailtrap\Bridge\Transport\MailtrapSdkTransport;
use Mailtrap\Config as MailtrapConfig;
use Mailtrap\MailtrapClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sensitive data (KYC documents, customer PII) must never transit in
        // plaintext — force every generated URL, and any request already
        // reaching the app, onto https:// in production.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Mail::extend('nav', function () {
            return new NavTransport(app(NavMailService::class));
        });

        // Mailtrap Sending API — the app's active mailer for now (see .env
        // MAIL_MAILER) while NAV's own codeunit functions aren't live yet.
        // NAV and SMTP stay fully configured and ready to switch back to,
        // just not the default.
        Mail::extend('mailtrap', function () {
            $apiToken = (string) config('services.mailtrap.api_token');
            $config = (new MailtrapConfig($apiToken))->setHost(AbstractApi::SENDMAIL_TRANSACTIONAL_HOST);
            $emailsApi = MailtrapClient::initSendingEmails(apiKey: $apiToken);

            return new MailtrapSdkTransport($emailsApi, $config);
        });

        RateLimiter::for('quotations', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api-global', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Case-insensitive search helpers — used everywhere a registration
        // number (or similar free-typed identifier) is searched, so "kdx"
        // matches a "KDX 123A" stored in any case, regardless of DB driver
        // collation.
        Builder::macro('whereLikeCI', function (string $column, ?string $value) {
            /** @var Builder $this */
            return $this->whereRaw("LOWER($column) LIKE ?", ['%' . mb_strtolower(trim((string) $value)) . '%']);
        });

        Builder::macro('orWhereLikeCI', function (string $column, ?string $value) {
            /** @var Builder $this */
            return $this->orWhereRaw("LOWER($column) LIKE ?", ['%' . mb_strtolower(trim((string) $value)) . '%']);
        });

        Builder::macro('whereEqualsCI', function (string $column, ?string $value) {
            /** @var Builder $this */
            return $this->whereRaw("LOWER($column) = ?", [mb_strtolower(trim((string) $value))]);
        });
    }
}
