<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:50|alpha_dash|unique:roles,name',
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string',
            'permissions'  => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name'         => Str::slug($data['name'], '_'),
            'display_name' => $data['display_name'],
            'description'  => $data['description'] ?? null,
            'is_system'    => false,
            'guard_name'   => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:50', 'alpha_dash',
                               \Illuminate\Validation\Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string',
            'permissions'  => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Prevent renaming system roles
        if (! $role->is_system) {
            $role->update(['name' => Str::slug($data['name'], '_')]);
        }

        $role->update([
            'display_name' => $data['display_name'],
            'description'  => $data['description'] ?? null,
        ]);

        // Admin role is super admin — permission sync na
        if ($role->name !== 'admin') {
            $role->syncPermissions($data['permissions'] ?? []);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role assigned to users.');
        }

        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', 'Role deleted.');
    }
}