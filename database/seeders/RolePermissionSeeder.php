<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions (grouped by module) ──
        $permissions = [
            // Products
            'product.view', 'product.create', 'product.edit', 'product.delete',
            // Coupons
            'coupon.view', 'coupon.create', 'coupon.edit', 'coupon.delete',
            // Users
            'user.view', 'user.create', 'user.edit', 'user.delete', 'user.assign-role',
            // Roles
            'role.view', 'role.create', 'role.edit', 'role.delete',
            // Permissions
            'permission.view', 'permission.assign',
            // Orders (future)
            'order.view-all', 'order.view-own', 'order.update-status',
            'order.assign-delivery', 'order.cancel', 'order.verify-code',
            // Delivery Charges (future)
            'delivery-charge.view', 'delivery-charge.create',
            'delivery-charge.edit', 'delivery-charge.delete',
            // Dashboard
            'dashboard.view', 'dashboard.analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Roles ──
        $roles = [
            [
                'name'         => 'admin',
                'display_name' => 'Super Admin',
                'description'  => 'Full system access — bypass all permissions',
                'is_system'    => true,
            ],
            [
                'name'         => 'user',
                'display_name' => 'Customer',
                'description'  => 'Default role for registered users',
                'is_system'    => true,
            ],
            [
                'name'         => 'delivery_man',
                'display_name' => 'Delivery Man',
                'description'  => 'Handles deliveries and code verification',
                'is_system'    => true,
            ],
            [
                'name'         => 'vendor',
                'display_name' => 'Vendor',
                'description'  => 'Third-party delivery partner',
                'is_system'    => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description'  => $roleData['description'],
                    'is_system'    => $roleData['is_system'],
                    'guard_name'   => 'web',
                ]
            );
        }

        // ── Assign permissions to non-admin roles ──
        // (admin auto bypass — permission lage na)

        // user: dashboard + view products + own orders
        Role::where('name', 'user')->first()->syncPermissions([
            'dashboard.view',
            'product.view',
            'order.view-own',
        ]);

        // delivery_man: dashboard + own orders + status update + verify code
        Role::where('name', 'delivery_man')->first()->syncPermissions([
            'dashboard.view',
            'order.view-own',
            'order.update-status',
            'order.verify-code',
        ]);

        // vendor: same as delivery man
        Role::where('name', 'vendor')->first()->syncPermissions([
            'dashboard.view',
            'order.view-own',
            'order.update-status',
            'order.verify-code',
        ]);

        // ── Assign admin role to existing first user (if none) ──
        $firstUser = User::first();
        if ($firstUser && ! $firstUser->hasRole('admin')) {
            $firstUser->assignRole('admin');
        }

        $this->command->info('Roles & permissions seeded successfully.');
    }
}