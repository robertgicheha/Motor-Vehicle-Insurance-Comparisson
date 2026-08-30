<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserOnline
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->last_online_at || $user->last_online_at->lt(now()->subMinute())) {
                $user->update(['last_online_at' => now()]);
            }
        }
        return $next($request);
    }
}
