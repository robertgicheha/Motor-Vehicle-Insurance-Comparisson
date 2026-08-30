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
    /**
     * Admins (not super-admins) only ever see/manage the "user" role and
     * custom roles they created themselves (e.g. "viewer") — never
     * super-admin, admin, or another admin's custom roles.
     */
    protected function assertCanManage(Role $role): void
    {
        $actor = auth()->user();
        $isAdminOnly = $actor->hasRole('admin') && !$actor->hasRole('super-admin');
        if ($isAdminOnly && $role->name !== 'user' && (int) $role->created_by !== $actor->id) {
            abort(403);
        }
    }

    /**
     * The only permissions an admin (not super-admin) may grant when
     * creating or editing a role: view-only, already held by the "user"
     * role, and never a companies/rates/reports/settings/admin-panel
     * permission or anything admin/super-admin-exclusive.
     */
    protected function assignablePermissionsForAdmin()
    {
        $excludedKeywords = ['companies', 'rates', 'reports', 'settings', 'admin panel'];
        $userPermissionNames = Role::where('name', 'user')->first()?->permissions->pluck('name') ?? collect();

        return Permission::whereIn('name', $userPermissionNames)
            ->where('name', 'like', 'view %')
            ->get()
            ->reject(fn ($p) => collect($excludedKeywords)->contains(fn ($kw) => str_contains($p->name, $kw)))
            ->values();
    }

    public function index()
    {
        $actor = auth()->user();
        $isSuperAdmin = $actor->hasRole('super-admin');

        $query = Role::with('permissions');
        if (!$isSuperAdmin) {
            $query->where(function ($q) use ($actor) {
                $q->where('name', 'user')->orWhere('created_by', $actor->id);
            });
        }

        $roles = $query->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $actor = auth()->user();
        $isAdminOnly = $actor->hasRole('admin') && !$actor->hasRole('super-admin');

        $permissionSet = $isAdminOnly ? $this->assignablePermissionsForAdmin() : Permission::all();
        $permissions = $permissionSet->groupBy(function ($p) {
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

        $actor = auth()->user();
        $isAdminOnly = $actor->hasRole('admin') && !$actor->hasRole('super-admin');
        if ($isAdminOnly && in_array(strtolower($request->name), ['admin', 'super-admin', 'super admin'])) {
            return back()->with('error', 'You cannot create a role named Admin or Super Admin.')->withInput();
        }

        if ($isAdminOnly) {
            $allowedIds = $this->assignablePermissionsForAdmin()->pluck('id');
            $requestedIds = collect($request->permissions)->map(fn ($id) => (int) $id);
            if ($requestedIds->diff($allowedIds)->isNotEmpty()) {
                return back()->with('error', 'You can only assign the view-level permissions available to the User role.')->withInput();
            }
        }

        $role = Role::create(['name' => $request->name, 'created_by' => $actor->id]);
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
        $this->assertCanManage($role);

        $actor = auth()->user();
        $isAdminOnly = $actor->hasRole('admin') && !$actor->hasRole('super-admin');

        $permissionSet = $isAdminOnly ? $this->assignablePermissionsForAdmin() : Permission::all();
        $permissions = $permissionSet->groupBy(function ($p) {
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
        $this->assertCanManage($role);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $actor = auth()->user();
        $isAdminOnly = $actor->hasRole('admin') && !$actor->hasRole('super-admin');
        if ($isAdminOnly) {
            $allowedIds = $this->assignablePermissionsForAdmin()->pluck('id');
            $requestedIds = collect($request->permissions)->map(fn ($id) => (int) $id);
            if ($requestedIds->diff($allowedIds)->isNotEmpty()) {
                return back()->with('error', 'You can only assign the view-level permissions available to the User role.')->withInput();
            }
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
        $this->assertCanManage($role);

        $role->delete();
        AuditService::activity('role_deleted', "Role {$role->name} deleted");

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }
}
