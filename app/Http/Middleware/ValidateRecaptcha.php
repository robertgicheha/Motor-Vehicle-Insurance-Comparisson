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
