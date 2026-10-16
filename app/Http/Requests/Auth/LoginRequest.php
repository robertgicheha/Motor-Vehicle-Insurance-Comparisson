<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $username = $this->string('username')->toString();
        $password = $this->string('password')->toString();

        $user = User::where('username', $username)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            \App\Services\AuditService::activity('login_failed', "Failed login attempt for unknown username '{$username}' from IP {$this->ip()}", 'warning');
            throw ValidationException::withMessages([
                'username' => 'Username is not valid, try again',
            ]);
        }

        if (!Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey());
            \App\Services\AuditService::activity('login_failed', "Failed login attempt for {$user->username} (wrong password)", 'warning', $user->id);
            throw ValidationException::withMessages([
                'password' => 'Password is not correct, please try again',
            ]);
        }

        if (!$user->is_active) {
            RateLimiter::hit($this->throttleKey());
            \App\Services\AuditService::activity('login_failed', "Login blocked for {$user->username} (account inactive)", 'warning', $user->id);
            throw ValidationException::withMessages([
                'username' => 'Your account is inactive. Please contact support.',
            ]);
        }

        // An expired password can no longer be used to sign in: the user must
        // reset it from the login page (username + current password), which
        // restarts the 30-day clock. Checked before Auth::login so no session
        // is ever established with an expired credential. isPasswordExpired()
        // is fail-open (a bad timestamp reads as "not expired"), so a corrupt
        // value can never take the login flow down.
        if ($user->isPasswordExpired()) {
            \App\Services\AuditService::activity('password_expired_login', "Login blocked for {$user->username} (password expired)", 'warning', $user->id);

            throw ValidationException::withMessages([
                'username' => 'Your password has expired and can no longer be used to sign in. Please reset it using the "Reset password" link below.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());

        \App\Services\AuditService::log('login', 'User', $user->id, null, ['method' => 'password'], $user->id);
        \App\Services\AuditService::activity('login', "User {$user->name} logged in", 'info', $user->id);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $username = $this->string('username')->toString();
        \App\Services\AuditService::activity('login_lockout', "Account locked out after repeated failed attempts for '{$username}' from IP {$this->ip()}", 'error');

        $admins = \App\Models\User::role(['super-admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\FailedLoginLockout($username, $this->ip()));
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
