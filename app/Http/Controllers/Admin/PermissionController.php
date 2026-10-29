<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function __construct(protected AuditLogService $audit) {}

    /**
     * List all permissions (grouped).
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('display_name', 'like', "%{$q}%")
                  ->orWhere('group', 'like', "%{$q}%");
            });
        }

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        $permissions = $query->orderBy('group')->orderBy('name')->get()->groupBy('group');
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();

        $stats = [
            'total'    => Permission::count(),
            'active'   => Permission::where('is_active', 1)->count(),
            'inactive' => Permission::where('is_active', 0)->count(),
            'groups'   => Permission::distinct('group')->count('group'),
        ];

        return view('admin.permissions.index', compact('permissions', 'groups', 'stats'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();

        return view('admin.permissions.create', compact('groups'));
    }

    /**
     * Store new permission.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100|alpha_dash|unique:permissions,name',
            'group'        => 'required|string|max:50|alpha_dash',
            'display_name' => 'required|string|max:255',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        // Enforce module.action format
        $fullName = $data['group'] . '.' . $data['name'];

        if (Permission::where('name', $fullName)->exists()) {
            return back()->with('error', "Permission '{$fullName}' already exists.")->withInput();
        }

        $permission = Permission::create([
            'name'         => $fullName,
            'guard_name'   => 'web',
            'group'        => $data['group'],
            'display_name' => $data['display_name'],
            'description'  => $data['description'] ?? null,
            'is_active'    => $request->boolean('is_active', true) ? 1 : 0,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->audit->logAction(
            "Permission created: {$permission->name}",
            $permission,
            ['new' => $permission->only(['name', 'group', 'display_name'])]
        );

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Edit form.
     */
    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();

        // Split name into group + action (e.g. "product.view" → "product" + "view")
        [$groupPart, $actionPart] = array_pad(explode('.', $permission->name, 2), 2, '');

        return view('admin.permissions.edit', compact('permission', 'groups', 'groupPart', 'actionPart'));
    }

    /**
     * Update permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'display_name' => 'required|string|max:255',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        // DO NOT allow changing name — routes reference it
        $old = $permission->only(['display_name', 'description', 'is_active']);

        $permission->update([
            'display_name' => $data['display_name'],
            'description'  => $data['description'] ?? null,
            'is_active'    => $request->boolean('is_active', true) ? 1 : 0,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->audit->logAction(
            "Permission updated: {$permission->name}",
            $permission,
            ['old' => $old, 'new' => $permission->only(['display_name', 'description', 'is_active'])]
        );

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated.');
    }

    /**
     * Delete permission.
     */
    public function destroy(Permission $permission)
    {
        // Protect system permissions — those referenced in route names / seeders
        $systemGroups = ['product', 'category', 'coupon', 'order', 'user', 'role', 'permission', 'dashboard', 'cart', 'checkout', 'profile', 'payment', 'delivery', 'delivery-charge', 'review', 'slider', 'announcement', 'homepage', 'newsletter'];

        if (in_array($permission->group, $systemGroups)) {
            return back()->with('error', 'System permissions cannot be deleted (routes reference them).');
        }

        // Check if permission is assigned to any role
        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'Cannot delete permission assigned to roles. Remove from roles first.');
        }

        $name = $permission->name;
        $permission->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->audit->logAction("Permission deleted: {$name}");

        return back()->with('success', 'Permission deleted.');
    }
}