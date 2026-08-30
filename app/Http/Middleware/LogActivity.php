<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditService;

class LogActivity
{
    protected ?int $userIdAtStart = null;

    public function handle(Request $request, Closure $next): Response
    {
        // Captured before the route runs, so a logout mid-request is still attributed.
        $this->userIdAtStart = auth()->id();

        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if ($request->method() === 'GET') {
            return;
        }

        $userId = auth()->id() ?? $this->userIdAtStart;
        if (!$userId) {
            return;
        }

        // A route that already called AuditService::activity() itself has logged
        // something specific and readable — don't add a redundant generic entry.
        if (AuditService::hasLoggedExplicitly()) {
            return;
        }

        $routeName = $request->route()?->getName();
        $label = $routeName
            ? \Illuminate\Support\Str::of($routeName)->replace(['.', '-', '_'], ' ')->headline()
            : ($request->method() . ' ' . $request->path());

        AuditService::activity(
            $routeName ?? ($request->method() . ' ' . $request->path()),
            "User performed \"{$label}\" ({$request->method()} /{$request->path()})",
            'info',
            $userId
        );
    }
}
