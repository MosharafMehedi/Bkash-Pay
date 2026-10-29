<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function __construct(protected AuditLogService $audit) {}

    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('is_active', 1)
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:50|alpha_dash|unique:roles,name',
            'display_name'  => 'required|string|max:100',
            'description'   => 'nullable|string|max:500',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            $role = Role::create([
                'name'         => Str::slug($data['name'], '_'),
                'display_name' => $data['display_name'],
                'description'  => $data['description'] ?? null,
                'is_system'    => false,
                'is_active'    => true,
                'guard_name'   => 'web',
                'created_by'   => auth()->id(),
            ]);

            $role->syncPermissions($data['permissions'] ?? []);

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $this->audit->logAction(
                "Role created: {$role->name}",
                $role,
                ['new' => [
                    'name'        => $role->name,
                    'permissions' => $data['permissions'] ?? [],
                ]]
            );

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Throwable $e) {
            Log::error('Role create failed: ' . $e->getMessage());
            return back()->with('error', 'Could not create role.')->withInput();
        }
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('is_active', 1)
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:50', 'alpha_dash',
                                Rule::unique('roles')->ignore($role->id)],
            'display_name'  => 'required|string|max:100',
            'description'   => 'nullable|string|max:500',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            // Prevent renaming system roles
            if (! $role->is_system) {
                $role->update(['name' => Str::slug($data['name'], '_')]);
            }

            $role->update([
                'display_name' => $data['display_name'],
                'description'  => $data['description'] ?? null,
                'updated_by'   => auth()->id(),
            ]);

            // Admin role is super admin — permission sync not needed (Gate::before bypasses)
            if ($role->name !== 'admin') {
                $role->syncPermissions($data['permissions'] ?? []);
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $this->audit->logAction(
                "Role updated: {$role->name}",
                $role,
                ['new' => [
                    'name'        => $role->name,
                    'permissions' => $data['permissions'] ?? [],
                ]]
            );

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Role update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update role.')->withInput();
        }
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role assigned to users.');
        }

        try {
            $name = $role->name;
            $role->delete();

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $this->audit->logAction("Role deleted: {$name}");

            return back()->with('success', 'Role deleted.');
        } catch (\Throwable $e) {
            Log::error('Role delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete role.');
        }
    }
}