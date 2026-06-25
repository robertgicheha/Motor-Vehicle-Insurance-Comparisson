<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

         // Detect city from IP for weather personalisation
        try {
            $ip = request()->ip();
            // Use ip-api.com (free, no key needed)
            $geo = \Illuminate\Support\Facades\Http::timeout(3)
                ->get("http://ip-api.com/json/{$ip}", ['fields' => 'city,lat,lon,status']);
            if ($geo->successful() && $geo->json('status') === 'success') {
                session(['weather_city' => $geo->json('city')]);
            }
        } catch (\Exception $e) {
            // silently fail — weather will default to Nairobi
        }

        if ($redirectTo = $request->input('redirect_to')) {
            return redirect($redirectTo);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
