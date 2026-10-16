<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $user->toArray();

        $data = [
            'profile_completed' => true,
        ];

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/avatars'), $filename);
            $data['avatar'] = 'images/avatars/' . $filename;
        }

        $user->update($data);

        AuditService::log('update', 'User', $user->id, $oldData, $user->toArray());
        AuditService::activity('profile_updated', "User {$user->username} updated profile");

        return back()->with('profile_success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ], [
            'password.min' => 'Your new password must be at least 12 characters long.',
            'password.mixed' => 'Your new password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Your new password must contain at least one number.',
            'password.symbols' => 'Your new password must contain at least one symbol.',
            'password.confirmed' => 'The new password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'default')->withInput();
        }

        $user = auth()->user();
        $user->update(['password' => Hash::make($request->password)]);
        $user->markPasswordChanged();

        // The user has now satisfied the expiry requirement — release them
        // from the forced-change lock so the rest of the app is reachable.
        $request->session()->forget(['force_password_change', 'password_expiry_notice']);

        AuditService::activity('password_changed', "User {$user->username} changed their password");

        return back()->with('password_success', 'Password updated successfully.');
    }
}
