<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            return explode(' ', $p->name)[1] ?? 'general';
        });
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions(Permission::whereIn('id', $request->permissions)->get());

        AuditService::log('create', 'Role', $role->id, null, $role->toArray());
        AuditService::activity('role_created', "Role {$role->name} created");

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin role cannot be edited.');
        }
        $permissions = Permission::all()->groupBy(function ($p) {
            return explode(' ', $p->name)[1] ?? 'general';
        });
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin role cannot be edited.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $role->toArray();
        $role->update(['name' => $request->name]);
        $role->syncPermissions(Permission::whereIn('id', $request->permissions)->get());

        AuditService::log('update', 'Role', $role->id, $oldData, $role->toArray());
        AuditService::activity('role_updated', "Role {$role->name} updated");

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super-admin' || $role->name === 'admin' || $role->name === 'user') {
            return back()->with('error', 'Cannot delete system roles.');
        }

        $role->delete();
        AuditService::activity('role_deleted', "Role {$role->name} deleted");

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }
}
