<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['msg' => 'Google authentication failed. Please try again.']);
        }

        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar ?? $user->avatar,
                ]);
            }
        } else {
            $names = explode(' ', $googleUser->name, 2);
            $user = User::create([
                'first_name' => $names[0] ?? '',
                'last_name' => $names[1] ?? '',
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => Hash::make(str()->random(16)),
                'is_active' => true,
                'email_verified_at' => now(),
                'profile_completed' => false,
            ]);
            // syncRoles (not assignRole): guarantees a freshly created Google account
            // ends up with exactly the 'user' role and nothing else.
            $user->syncRoles(['user']);

            $admins = User::role(['super-admin', 'admin'])->get();
            if ($admins->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\UserRegistered($user));
            }
        }

        AuditService::log('login', 'User', $user->id, null, ['method' => 'google']);
        AuditService::activity('login', "User {$user->name} logged in via Google");

        Auth::login($user);
        $user->update(['last_online_at' => now()]);

        if (!$user->profile_completed) {
            return redirect()->route('profile.edit')->with('profile_incomplete', 'Please complete your profile by adding your phone number and other details.');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
