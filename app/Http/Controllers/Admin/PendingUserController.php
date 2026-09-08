<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountVerifiedMail;
use App\Models\User;
use App\Notifications\UserVerified;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class PendingUserController extends Controller
{
    protected function pendingQuery()
    {
        return User::where('is_active', false)->whereNull('verified_at');
    }

    public function index(Request $request)
    {
        $query = $this->pendingQuery();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('mobile', 'like', "%{$request->search}%");
            });
        }

        $pendingUsers = $query->latest()->paginate(15)->withQueryString();
        $totalPending = $this->pendingQuery()->count();

        return view('admin.pending-users.index', compact('pendingUsers', 'totalPending'));
    }

    public function activate(Request $request, User $user)
    {
        if (!$user->isPendingVerification()) {
            return back()->with('error', 'This user is not pending verification.');
        }

        $this->verify($user, auth()->user());

        return back()->with('success', "{$user->name} has been verified and can now log in.");
    }

    public function bulkActivate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $users = $this->pendingQuery()->whereIn('id', $request->user_ids)->get();
        $actor = auth()->user();

        foreach ($users as $user) {
            $this->verify($user, $actor);
        }

        return back()->with('success', $users->count() . ' user(s) verified successfully.');
    }

    protected function verify(User $user, User $actor): void
    {
        $user->update([
            'is_active' => true,
            'verified_at' => now(),
        ]);

        AuditService::log('activate', 'User', $user->id, ['is_active' => false], ['is_active' => true, 'verified_at' => now()->toISOString()], $actor->id);
        AuditService::activity('user_verified', "User {$user->name} ({$user->email}) verified by {$actor->name}", 'info', $actor->id);

        try {
            Mail::to($user->email)->send(new AccountVerifiedMail($user));
        } catch (\Throwable $e) {
            Log::warning("Failed to send account-verified email to {$user->email}: " . $e->getMessage());
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            try {
                Notification::send($admins, new UserVerified($user, $actor));
            } catch (\Throwable $e) {
                Log::warning("Failed to notify admins that {$user->email} was verified: " . $e->getMessage());
            }
        }
    }
}
