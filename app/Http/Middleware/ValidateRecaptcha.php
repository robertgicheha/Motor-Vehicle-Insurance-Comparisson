<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ValidateRecaptcha
{
    public function handle(Request $request, Closure $next)
    {
        // Admin-configured key (Settings > Security) takes priority; falls back to .env.
        $secretKey = Setting::getValue('recaptcha_secret_key') ?: config('services.recaptcha.secret_key');

        // Not configured yet — don't lock real users out; the endpoint still has
        // rate limiting. Logged so it's visible operationally.
        if (!$secretKey) {
            Log::warning("reCAPTCHA secret key not configured — skipping verification for {$request->path()}");
            return $next($request);
        }

        // reCAPTCHA site keys are bound to the domain(s) registered for them
        // in Google's admin console — normally the production domain, not
        // localhost. Verifying against Google from local dev reliably fails
        // (confirmed: Google returns invalid-input-response for a token
        // generated on an unregistered domain, even with a correct, valid
        // secret key), which would lock every developer out of login during
        // local testing. Local traffic isn't the bot threat model this
        // exists for, so skip active verification here — staging/production
        // (where the domain does match) still fully enforce it.
        if (app()->environment('local')) {
            return $next($request);
        }

        $token = $request->input('g-recaptcha-response');
        if (!$token) {
            return back()->withErrors(['captcha' => 'reCAPTCHA verification required. Please try again.']);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);
        $body = $response->json();

        if (!($body['success'] ?? false) || ($body['score'] ?? 0) < 0.5) {
            AuditService::activity('recaptcha_failed', "reCAPTCHA verification failed for {$request->path()} (score: " . ($body['score'] ?? 'n/a') . ')', 'warning');
            return back()->withErrors(['captcha' => 'We could not verify you are human. Please try again.']);
        }

        $request->merge(['recaptcha_score' => $body['score'] ?? null]);
        return $next($request);
    }
}
