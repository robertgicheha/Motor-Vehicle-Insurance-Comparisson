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
    public function index(Request $request)
    {
        $query = User::query();

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
        $totalUsers = User::count();
        $onlineUsers = User::where('last_online_at', '>=', now()->subMinutes(5))->count();
        $maleCount = User::where('gender', 'male')->count();
        $femaleCount = User::where('gender', 'female')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'onlineUsers', 'maleCount', 'femaleCount'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'mobile' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'role' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'is_active' => true,
            'profile_completed' => true,
        ]);

        $user->assignRole(Role::findById($request->role)->name);

        AuditService::log('create', 'User', $user->id, null, $user->toArray());
        AuditService::activity('user_created', "User {$user->name} created by admin");

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
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
            $user->syncRoles([Role::findById($request->role)->name]);
        }

        AuditService::log('update', 'User', $user->id, $oldData, $user->toArray());
        AuditService::activity('user_updated', "User {$user->name} updated by admin");

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
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

        $userData = $user->toArray();
        $user->delete();

        AuditService::log('delete', 'User', $user->id, $userData, null);
        AuditService::activity('user_deleted', "User {$user->name} deleted");

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function export()
    {
        return (new UsersExport)->download('users.xlsx');
    }
}
