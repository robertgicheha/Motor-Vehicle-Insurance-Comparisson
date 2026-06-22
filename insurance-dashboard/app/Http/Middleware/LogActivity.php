<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditService;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (auth()->check() && $request->method() !== 'GET') {
            AuditService::activity(
                $request->method() . ' ' . $request->path(),
                "User performed {$request->method()} on {$request->path()}",
                'info'
            );
        }
    }
}
