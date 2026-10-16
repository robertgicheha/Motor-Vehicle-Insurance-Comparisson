<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Self-service password reset from the login page. Accounts are username-only
 * (no email/SMS), so a user proves identity with their username *plus* the
 * password they are currently using/remember. This covers passwords that have
 * expired after 30 days; a completely forgotten password still falls back to an
 * administrator resetting it from User Management.
 */
class PasswordResetController extends Controller
{
    /**
     * Show the reset form.
     */
    public function show(): View
    {
        return view('auth.reset-password');
    }

    /**
     * Verify the username + current password and set a new (strong) password.
     * Resetting restarts the 30-day expiry clock so the account is usable again.
     */
    public function reset(Request $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $request->validate([
            'username' => ['required', 'string'],
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(12)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password.min' => 'Your new password must be at least 12 characters long.',
            'password.mixed' => 'Your new password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Your new password must contain at least one number.',
            'password.symbols' => 'Your new password must contain at least one symbol.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.different' => 'Your new password must be different from your current password.',
        ]);

        $username = (string) $request->input('username');
        $currentPassword = (string) $request->input('current_password');

        $user = User::where('username', $username)->first();

        // Username and current password are checked together so a wrong pair
        // reveals nothing about which part was wrong.
        if (! $user || ! Hash::check($currentPassword, $user->password)) {
            RateLimiter::hit($this->throttleKey($request));
            AuditService::activity(
                'password_reset_failed',
                "Failed password reset attempt for '{$username}' from IP {$request->ip()}",
                'warning',
                $user?->id
            );

            throw ValidationException::withMessages([
                'current_password' => 'Your username or current password is incorrect.',
            ]);
        }

        if (! $user->is_active) {
            AuditService::activity(
                'password_reset_blocked',
                "Password reset blocked for {$user->username} (account inactive)",
                'warning',
                $user->id
            );

            throw ValidationException::withMessages([
                'username' => 'Your account is inactive. Please contact support.',
            ]);
        }

        $newPassword = (string) $request->input('password');

        $user->update(['password' => Hash::make($newPassword)]);
        // Restarts the 30-day clock: the new password expires 30 days from now.
        $user->markPasswordChanged();

        RateLimiter::clear($this->throttleKey($request));

        AuditService::log('password_reset', 'User', $user->id, null, null, $user->id);
        AuditService::activity(
            'password_reset',
            "User {$user->username} reset their password from the login page",
            'info',
            $user->id
        );

        // The plaintext is flashed once so the success screen can offer a copy
        // button (there is no email channel to send it through).
        return redirect()->route('password.reset.success')
            ->with('reset_username', $user->username)
            ->with('reset_password', $newPassword);
    }

    /**
     * Confirmation screen showing the newly-set password with a copy button.
     */
    public function success(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('reset_password')) {
            return redirect()->route('login');
        }

        // Keep the flash for one more request so refreshing the page (or the
        // user lingering) still shows the password; it disappears as soon as
        // they navigate anywhere else.
        $request->session()->keep(['reset_username', 'reset_password']);

        return view('auth.reset-password-success');
    }

    /**
     * Throttle reset attempts per username + IP, mirroring the login throttle.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return 'password-reset|'.Str::transliterate(
            Str::lower((string) $request->input('username')).'|'.$request->ip()
        );
    }
}
