<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Notifications\QuotationStatusChanged;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'mobile' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // syncRoles (not assignRole): guarantees a self-registered account ends up
        // with exactly the 'user' role and nothing else, even if a stray role pivot
        // row somehow already existed for this id.
        $user->syncRoles(['user']);

        \App\Services\AuditService::log('create', 'User', $user->id, null, ['email' => $user->email, 'method' => 'self_registration'], $user->id);
        \App\Services\AuditService::activity('user_registered', "New user {$user->name} ({$user->email}) registered", 'info', $user->id);

        event(new Registered($user));

        // Welcome the account holder. Wrapped in try/catch (as with the quotation
        // mail) so an SMTP/API hiccup can never fail an otherwise good signup.
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new \App\Notifications\UserRegistered($user));
        }

        Auth::login($user);

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

        if ($request->wantsJson()) {
            $redirect = $request->input('redirect_to', route('dashboard'));
            return response()->json(['redirect' => $redirect]);
        }

        if ($redirectTo = $request->input('redirect_to')) {
            return redirect($redirectTo);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
