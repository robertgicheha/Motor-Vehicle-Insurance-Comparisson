<?php

namespace App\Http\Controllers;

use App\Mail\EmailChangedMail;
use App\Mail\PasswordChangedMail;
use App\Mail\PendingEmailVerification;
use App\Models\User;
use App\Notifications\UserEmailChanged;
use App\Notifications\UserPasswordChanged;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $user->toArray();

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
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
        AuditService::activity('profile_updated', "User {$user->name} updated profile");

        if ($request->filled('email') && $request->email !== $user->email) {
            $token = bin2hex(random_bytes(32));

            $user->update([
                'pending_email' => $request->email,
                'email_verify_token' => $token,
            ]);

            $verificationUrl = URL::temporarySignedRoute(
                'profile.verify.email',
                now()->addMinutes(60),
                ['token' => $token]
            );

            Mail::to($request->email)->send(new PendingEmailVerification($user, $verificationUrl));
            AuditService::activity('email_verification_sent', "Verification link emailed to {$request->email} for pending email change");

            return back()->with('profile_success', 'A verification link has been sent to your new email address. Your email will be updated once you verify it.');
        }

        return back()->with('profile_success', 'Profile updated successfully.');
    }

    public function verifyNewEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        if (!URL::hasValidSignature($request)) {
            return redirect()->route('profile.edit')->withErrors(['email' => 'The verification link is invalid or has expired.']);
        }

        $user = auth()->user();

        if ($user->email_verify_token !== $request->token) {
            return redirect()->route('profile.edit')->withErrors(['email' => 'Invalid verification token.']);
        }

        if (!$user->pending_email) {
            return redirect()->route('profile.edit')->withErrors(['email' => 'No pending email change found.']);
        }

        $previousEmail = $user->email;

        $user->update([
            'email' => $user->pending_email,
            'pending_email' => null,
            'email_verify_token' => null,
        ]);

        AuditService::activity('email_changed', "User {$user->name} changed email address");

        try {
            Mail::to($user->email)->send(new EmailChangedMail($user, $previousEmail));
        } catch (\Throwable $e) {
            Log::warning("Failed to send email-changed confirmation to {$user->email}: " . $e->getMessage());
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            try {
                Notification::send($admins, new UserEmailChanged($user, $previousEmail));
            } catch (\Throwable $e) {
                Log::warning("Failed to notify admins that {$user->email} changed their email: " . $e->getMessage());
            }
        }

        return redirect()->route('profile.edit')->with('profile_success', 'Your email address has been updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = auth()->user();
        $user->update(['password' => Hash::make($request->password)]);
        AuditService::activity('password_changed', 'User changed password');

        try {
            Mail::to($user->email)->send(new PasswordChangedMail($user));
        } catch (\Throwable $e) {
            Log::warning("Failed to send password-changed confirmation to {$user->email}: " . $e->getMessage());
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            try {
                Notification::send($admins, new UserPasswordChanged($user));
            } catch (\Throwable $e) {
                Log::warning("Failed to notify admins that {$user->email} changed their password: " . $e->getMessage());
            }
        }

        return back()->with('password_success', 'Password updated.');
    }
}
