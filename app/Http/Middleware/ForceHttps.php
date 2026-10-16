<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects any plain-HTTP request to its HTTPS equivalent so no portal
 * traffic can travel unencrypted in transit. Behind a TLS-terminating proxy
 * (Render) the X-Forwarded-Proto header is trusted (see bootstrap/app.php),
 * so $request->secure() reflects the real public scheme.
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = config('app.force_https') || app()->environment('production');

        if ($enabled && ! $request->secure() && ! $request->is('up')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
