<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Imports\UsersImport;
use App\Exports\UsersExport;

class UserController extends Controller
{
    /**
     * Admins (not super-admins) never see, edit, deactivate, or delete an
     * admin/super-admin account — only accounts with the plain "user" role.
     */
    protected function assertCanManage(User $target): void
    {
        $actor = auth()->user();
        if ($actor->hasRole('admin') && !$actor->hasRole('super-admin')
            && ($target->hasRole('admin') || $target->hasRole('super-admin'))) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $actor = auth()->user();
        $isSuperAdmin = $actor->hasRole('super-admin');

        $query = User::query();

        if (!$isSuperAdmin) {
            $query->whereHas('roles', fn($q) => $q->where('name', 'user'));
        }

        if ($request->search) {
            $query->where('username', 'like', "%{$request->search}%");
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($request->online === 'yes') {
            $query->where('last_online_at', '>=', now()->subMinutes(5));
        }

        $users = $query->with('roles')->latest()->paginate(15);

        $statsBase = fn() => $isSuperAdmin
            ? User::query()
            : User::whereHas('roles', fn($q) => $q->where('name', 'user'));

        $totalUsers = $statsBase()->count();
        $onlineUsers = $statsBase()->where('last_online_at', '>=', now()->subMinutes(5))->count();
        $roles = $isSuperAdmin ? Role::all() : Role::where('name', 'user')->get();

        return view('admin.users.index', compact('users', 'totalUsers', 'onlineUsers', 'roles'));
    }

    public function create()
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $roles = $isAdmin ? Role::where('name', 'user')->get() : Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch' => 'required|string|max:255',
            'role' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:12',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $isAdmin = auth()->user()->hasRole('admin');
        $requestedRole = Role::findById($request->role);
        if ($isAdmin && $requestedRole->name !== 'user') {
            return back()->with('error', 'You can only create users with the User role.')->withInput();
        }

        $generatedPassword = $request->filled('password') ? $request->password : $this->generateStrongPassword();

        $user = User::create([
            'username' => User::generateUsername($request->branch, $requestedRole->name),
            'branch' => $request->branch,
            'password' => Hash::make($generatedPassword),
            'is_active' => true,
            'verified_at' => now(),
            'profile_completed' => true,
            'password_changed_at' => now(),
        ]);

        $user->syncRoles([$requestedRole->name]);

        AuditService::log('create', 'User', $user->id, null, $user->toArray());
        AuditService::activity('user_created', "User {$user->username} created by admin");

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully. Login username: ' . $user->username)
            ->with('generated_password', $generatedPassword);
    }

    public function edit(User $user)
    {
        $this->assertCanManage($user);

        $isAdmin = auth()->user()->hasRole('admin');
        $roles = $isAdmin ? Role::where('name', 'user')->get() : Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->assertCanManage($user);

        $validator = Validator::make($request->all(), [
            'branch' => 'required|string|max:255',
            'role' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $user->toArray();

        $requestedRole = $request->role ? Role::findById($request->role) : null;

        $data = [
            'branch' => $request->branch,
            'is_active' => $request->boolean('is_active', true),
        ];

        // super-admin/admin usernames are fixed (superadmin/admin); the plain
        // "user" role is numbered per branch. Regenerate the login ID when the
        // branch or role changes so it always matches the account.
        $roleName = $requestedRole?->name ?? $user->getRoleNames()->first() ?? 'user';
        if ($user->branch !== $request->branch || !$user->hasRole($roleName)) {
            $data['username'] = User::generateUsername($request->branch, $roleName);
        }

        $user->update($data);

        if ($requestedRole) {
            $isAdmin = auth()->user()->hasRole('admin');
            if ($isAdmin && $requestedRole->name !== 'user') {
                return back()->with('error', 'You can only assign the User role.')->withInput();
            }
            $user->syncRoles([$requestedRole->name]);
        }

        AuditService::log('update', 'User', $user->id, $oldData, $user->toArray());
        AuditService::activity('user_updated', "User {$user->username} updated by admin");

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $this->assertCanManage($user);

        $user->update(['is_active' => !$user->is_active]);

        AuditService::log($user->is_active ? 'activate' : 'deactivate', 'User', $user->id);
        AuditService::activity(
            $user->is_active ? 'user_activated' : 'user_deactivated',
            "User {$user->username} " . ($user->is_active ? 'activated' : 'deactivated')
        );

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Cannot delete super admin.');
        }

        $this->assertCanManage($user);

        $actor = auth()->user();
        $userData = $user->toArray();
        $user->update(['deleted_by' => $actor->id]);
        $user->delete();

        AuditService::log('delete', 'User', $user->id, $userData, null);
        AuditService::activity('user_deleted', "User {$user->username} deleted by {$actor->username}");

        foreach (User::role('super-admin')->get() as $superAdmin) {
            $superAdmin->notify(new \App\Notifications\UserDeleted($user, $actor));
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    /**
     * Soft-deleted users — super-admin only, so they can review and
     * restore an account an admin removed by mistake.
     */
    public function trash()
    {
        $users = User::onlyTrashed()->with(['roles', 'deletedBy'])->latest('deleted_at')->paginate(15);
        return view('admin.users.trash', compact('users'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $user->update(['deleted_by' => null]);

        AuditService::log('restore', 'User', $user->id, null, $user->toArray());
        AuditService::activity('user_restored', "User {$user->username} restored by " . auth()->user()->username);

        return back()->with('success', 'User restored.');
    }

    public function export()
    {
        $actor = auth()->user();
        $usersOnly = $actor->hasRole('admin') && !$actor->hasRole('super-admin');

        return (new UsersExport)->download('users.xlsx', $usersOnly);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $type = $file->getClientOriginalExtension();
        $path = $file->store('imports');

        $importer = new UsersImport;
        $result = $importer->import(storage_path('app/' . $path), $type);

        $message = $result['imported'] . ' users imported successfully.';
        if (!empty($result['errors'])) {
            $message .= ' Errors: ' . implode('; ', array_slice($result['errors'], 0, 5));
        }

        AuditService::activity('users_imported', "{$result['imported']} users imported via file upload");

        return back()->with('success', $message);
    }

    /**
     * Fallback for a user who has completely forgotten their password: an
     * administrator generates a new strong password and reads it to them. The
     * plaintext is flashed back once so the admin can copy it. Like every
     * password change this restarts the 30-day expiry clock.
     */
    public function resetPassword(User $user)
    {
        $this->assertCanManage($user);

        $generatedPassword = $this->generateStrongPassword();

        $user->update(['password' => Hash::make($generatedPassword)]);
        $user->markPasswordChanged();

        AuditService::log('password_reset', 'User', $user->id, null, null, auth()->id());
        AuditService::activity('password_reset_admin', "Password reset for {$user->username} by " . auth()->user()->username, 'info', $user->id);

        return redirect()->route('admin.users.index')
            ->with('success', "Password reset for {$user->username}.")
            ->with('generated_password', $generatedPassword)
            ->with('generated_password_for', $user->username);
    }

    protected function generateStrongPassword(int $length = 12): string
    {
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        $all = $upper . $lower . $numbers . $symbols;

        $password = '';
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }
}
