<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ValidateRecaptcha
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->input('g-recaptcha-response');
        if (!$token) {
            if ($request->is('login') || $request->is('register')) {
                return $next($request);
            }
            return back()->withErrors(['captcha' => 'reCAPTCHA verification required.']);
        }
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);
        $body = $response->json();
        if (!($body['success'] ?? false) || ($body['score'] ?? 0) < 0.5) {
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed.']);
        }
        $request->merge(['recaptcha_score' => $body['score'] ?? null]);
        return $next($request);
    }
}
