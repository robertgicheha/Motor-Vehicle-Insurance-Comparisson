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
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('mobile', 'like', "%{$request->search}%");
            });
        }

        if ($request->gender) {
            $query->where('gender', $request->gender);
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
        $maleCount = $statsBase()->where('gender', 'male')->count();
        $femaleCount = $statsBase()->where('gender', 'female')->count();
        $roles = $isSuperAdmin ? Role::all() : Role::where('name', 'user')->get();

        return view('admin.users.index', compact('users', 'totalUsers', 'onlineUsers', 'maleCount', 'femaleCount', 'roles'));
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'role' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8',
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
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($generatedPassword),
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'is_active' => true,
            'verified_at' => now(),
            'profile_completed' => true,
        ]);

        $user->syncRoles([$requestedRole->name]);

        session(['last_created_user_id' => $user->id]);

        AuditService::log('create', 'User', $user->id, null, $user->toArray());
        AuditService::activity('user_created', "User {$user->name} created by admin");

        $emailStatus = '';
        if ($request->boolean('email_credentials')) {
            try {
                \Mail::to($user->email)->send(new \App\Mail\UserCredentialsMail($user, $generatedPassword));
                AuditService::activity('credentials_emailed', "Login credentials emailed to new user {$user->name} ({$user->email})");
                $emailStatus = ' Credentials emailed to ' . $user->email . '.';
            } catch (\Throwable $e) {
                AuditService::activity('credentials_email_failed', "Failed to email login credentials to {$user->name} ({$user->email}): {$e->getMessage()}");
                $emailStatus = ' However, the credentials email failed to send — use "Send via Email" below to retry.';
            }

            $this->notifyAdminsOfCredentials($user, $generatedPassword, auth()->user());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.' . $emailStatus)
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'role' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $user->toArray();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->role) {
            $requestedRole = Role::findById($request->role);
            $isAdmin = auth()->user()->hasRole('admin');
            if ($isAdmin && $requestedRole->name !== 'user') {
                return back()->with('error', 'You can only assign the User role.')->withInput();
            }
            $user->syncRoles([$requestedRole->name]);
        }

        AuditService::log('update', 'User', $user->id, $oldData, $user->toArray());
        AuditService::activity('user_updated', "User {$user->name} updated by admin");

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $this->assertCanManage($user);

        $user->update(['is_active' => !$user->is_active]);

        AuditService::log($user->is_active ? 'activate' : 'deactivate', 'User', $user->id);
        AuditService::activity(
            $user->is_active ? 'user_activated' : 'user_deactivated',
            "User {$user->name} " . ($user->is_active ? 'activated' : 'deactivated')
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
        AuditService::activity('user_deleted', "User {$user->name} deleted by {$actor->name}");

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
        AuditService::activity('user_restored', "User {$user->name} restored by " . auth()->user()->name);

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

    public function sendCredentials(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = User::find(session('last_created_user_id'));

        if (!$user) {
            return back()->with('error', 'No recently created user found.');
        }

        \Mail::to($user->email)->send(new \App\Mail\UserCredentialsMail($user, $request->password));
        \App\Services\AuditService::activity('credentials_emailed', "Login credentials emailed to new user {$user->name} ({$user->email})");

        $this->notifyAdminsOfCredentials($user, $request->password, auth()->user());

        return back()->with('success', 'Login credentials sent to ' . $user->email);
    }

    /**
     * The admin/superadmin get their own copy of a newly created account's
     * credentials — separate from the recipient's own email, and never
     * allowed to fail the request it's called from.
     */
    protected function notifyAdminsOfCredentials(User $user, string $password, User $actor): void
    {
        $admins = User::role(['super-admin', 'admin'])->get();

        foreach ($admins as $admin) {
            try {
                \Mail::to($admin->email)->send(new \App\Mail\UserCredentialsAdminMail($user, $password, $admin));
            } catch (\Throwable $e) {
                AuditService::activity('credentials_admin_email_failed', "Failed to notify admin {$admin->email} of credentials for {$user->name} ({$user->email}): {$e->getMessage()}");
            }
        }
    }
}
