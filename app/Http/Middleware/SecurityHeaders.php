<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Built from the app's actual external resource usage (Chart.js CDN,
        // Google Fonts, reCAPTCHA) — kept permissive on script-src/style-src
        // ('unsafe-inline') since the app relies extensively on inline
        // <script> blocks and onclick= handlers throughout its Blade views;
        // tightening that would need a much larger nonce-based rollout. Still
        // closes the highest-value gaps with no legitimate current usage to
        // break: no plugin/object embeds, no <base> tag, no external form
        // actions, no framing by another site.
        //
        // Skipped in 'local' — Vite's dev server (@vite() with public/hot
        // present) serves assets from its own host:port over a WebSocket
        // connection for HMR, which a locked-down CSP blocks outright
        // (breaking every page's CSS/JS in dev). Local dev traffic isn't
        // internet-exposed, so there's no real threat-model reason to
        // enforce this outside staging/production, where @vite() instead
        // emits same-origin built files already covered by 'self'.
        if (!app()->environment('local')) {
            $response->headers->set('Content-Security-Policy', implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com data:",
                "img-src 'self' data: https:",
                "connect-src 'self' https://www.google.com https://www.gstatic.com",
                "frame-src https://www.google.com",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ]));
        }

        return $response;
    }
}
