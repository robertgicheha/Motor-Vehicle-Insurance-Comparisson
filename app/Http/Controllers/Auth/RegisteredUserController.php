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
            // Self-registered accounts start deactivated and unverified — an
            // admin must approve them from the Pending Users screen before
            // they can log in (see LoginRequest::authenticate()'s is_active
            // check). Admin-created and Google/Socialite accounts are
            // created active + verified elsewhere and are unaffected.
            'is_active' => false,
            'verified_at' => null,
        ]);

        // syncRoles (not assignRole): guarantees a self-registered account ends up
        // with exactly the 'user' role and nothing else, even if a stray role pivot
        // row somehow already existed for this id.
        $user->syncRoles(['user']);

        \App\Services\AuditService::log('create', 'User', $user->id, null, ['email' => $user->email, 'method' => 'self_registration'], $user->id);
        \App\Services\AuditService::activity('user_registered', "New user {$user->name} ({$user->email}) registered — pending verification", 'info', $user->id);

        event(new Registered($user));

<<<<<<< HEAD
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
=======
        // Let the user know their account is pending verification. Wrapped in
        // try/catch (as with the quotation mail) so an SMTP/API hiccup can
        // never fail an otherwise good signup.
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountPendingVerificationMail($user));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to send pending-verification email to {$user->email}: " . $e->getMessage());
>>>>>>> 6e09d9fe61c33867596115ee207a46e63c627a60
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            try {
                Notification::send($admins, new \App\Notifications\UserRegistered($user, pendingVerification: true));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to notify admins of pending registration for {$user->email}: " . $e->getMessage());
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'pending_verification' => true,
                'redirect' => route('register.pending'),
                'message' => 'Your account has been created and is pending verification. You will receive an email once it has been verified.',
            ]);
        }

        return redirect()->route('register.pending');
    }
}
