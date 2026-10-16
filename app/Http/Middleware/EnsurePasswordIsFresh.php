<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Holds an authenticated user whose password has passed the 30-day lifetime
 * at their profile page until they change it. Everything else is redirected
 * away. The blocking is done with a plain string flash message rather than an
 * exception so a bad/expired timestamp can never 500 the application.
 */
class EnsurePasswordIsFresh
{
    /**
     * Routes a locked-out user is still allowed to reach (so they can change
     * their password or sign out).
     *
     * @var list<string>
     */
    protected array $allowedRoutes = [
        'profile.edit',
        'profile.update',
        'profile.password',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $expired = false;

        try {
            $expired = $request->session()->get('force_password_change', false)
                || $user->isPasswordExpired();
        } catch (\Throwable $e) {
            report($e);
            $expired = false;
        }

        if (! $expired) {
            return $next($request);
        }

        $notice = 'Your password has expired and must be changed before you can continue.';

        if ($request->routeIs($this->allowedRoutes) || $request->expectsJson()) {
            // Still surface the reminder on the pages they *can* reach.
            $request->session()->now('password_expiry_notice', $notice);

            return $next($request);
        }

        return redirect()->route('profile.edit')->with('password_expiry_notice', $notice);
    }
}
