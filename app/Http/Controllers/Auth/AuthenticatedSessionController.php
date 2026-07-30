<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\HealthCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function store(LoginRequest $request): RedirectResponse|JsonResponse
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

        if (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')) {
            $this->runHealthChecks();
        }

        if ($request->wantsJson()) {
            $redirect = $request->input('redirect_to', route('dashboard'));
            return response()->json(['redirect' => $redirect]);
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

    protected function runHealthChecks(): void
    {
        try {
            DB::select('SELECT 1');
            HealthCheck::create(['type' => 'database', 'status' => 'up', 'message' => 'Database connection OK', 'checked_at' => now()]);
        } catch (\Exception $e) {
            HealthCheck::create(['type' => 'database', 'status' => 'down', 'message' => $e->getMessage(), 'checked_at' => now()]);
        }
        try {
            $start = microtime(true);
            $client = new \Illuminate\Http\Client\Factory;
            $response = $client->timeout(10)->get(config('app.url'));
            $time = round((microtime(true) - $start) * 1000);
            $status = $response->successful() ? 'up' : ($response->serverError() ? 'down' : 'degraded');
            HealthCheck::create(['type' => 'site', 'status' => $status, 'message' => "{$response->status()} in {$time}ms", 'checked_at' => now()]);
        } catch (\Exception $e) {
            HealthCheck::create(['type' => 'site', 'status' => 'down', 'message' => $e->getMessage(), 'checked_at' => now()]);
        }
        try {
            $total = disk_total_space(base_path());
            $free = disk_free_space(base_path());
            $usedPct = round((1 - $free / $total) * 100);
            HealthCheck::create(['type' => 'server', 'status' => $usedPct < 90 ? 'up' : 'degraded', 'message' => "Disk {$usedPct}% used", 'checked_at' => now()]);
        } catch (\Exception $e) {
            HealthCheck::create(['type' => 'server', 'status' => 'down', 'message' => $e->getMessage(), 'checked_at' => now()]);
        }
        try {
            $apiUrl = config('services.sticker_api.url');
            if ($apiUrl) {
                $start = microtime(true);
                $client = new \Illuminate\Http\Client\Factory;
                $response = $client->timeout(10)->get($apiUrl);
                $time = round((microtime(true) - $start) * 1000);
                $status = $response->successful() ? 'up' : ($response->serverError() ? 'down' : 'degraded');
                HealthCheck::create(['type' => 'sticker_api', 'status' => $status, 'message' => "{$response->status()} in {$time}ms", 'checked_at' => now()]);
            }
        } catch (\Exception $e) {
            HealthCheck::create(['type' => 'sticker_api', 'status' => 'down', 'message' => $e->getMessage(), 'checked_at' => now()]);
        }
    }
}
