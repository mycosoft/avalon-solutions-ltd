<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin');
    }

    public function rolesIndex()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[0];
        });

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function rolesStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function rolesUpdate(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
        ]);

        $role->name = $request->name;
        $role->save();

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function rolesDestroy(Role $role)
    {
        if ($role->name === 'superadmin') {
            return redirect()->route('roles.index')->with('error', 'Cannot delete superadmin role.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function permissionsIndex()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function permissionsStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function permissionsUpdate(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->name = $request->name;
        $permission->save();

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function permissionsDestroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
