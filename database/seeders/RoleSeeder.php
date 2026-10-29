<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ═══ Cache clear ═══
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ═══ Guard check ═══
        if (Permission::count() === 0) {
            $this->command->error('❌ No permissions found. Run PermissionSeeder first.');
            return;
        }

        // ═══ Roles + permissions map ═══
        $roles = [
            'admin' => [
                'display_name' => 'Administrator',
                'description'  => 'Full system access — bypasses all permission checks',
                'is_system'    => 1,
                'permissions'  => Permission::all()->pluck('name')->toArray(),
            ],

            // ═══════════════════════════════════════════════════════════
            //  CUSTOMER — extra testing permissions soho
            // ═══════════════════════════════════════════════════════════
            'user' => [
                'display_name' => 'Customer',
                'description'  => 'Regular customer — browse, buy, review (with extra testing permissions)',
                'is_system'    => 1,
                'permissions'  => [
                    // ═══ Core customer ═══
                    'product.view',
                    'category.view',
                    'cart.view',
                    'cart.manage',
                    'checkout.view',
                    'checkout.apply-coupon',
                    'order.view-own',
                    'payment.bkash',
                    'payment.paypal',
                    'payment.sslcommerz',
                    'payment.cash',
                    'profile.view',
                    'profile.edit',
                    'dashboard.view',

                    // ═══ EXTRA (testing) ═══
                    'review.view',        // admin reviews list
                    'order.view-all',     // sob order dekhbe
                    'user.view',          // user list dekhbe
                    'coupon.view',        // coupon list
                    'product.create',     // notun product
                    'product.edit',       // product edit
                    'newsletter.view',    // subscriber list
                ],
            ],

            'delivery_man' => [
                'display_name' => 'Delivery Man',
                'description'  => 'Handles assigned deliveries',
                'is_system'    => 1,
                'permissions'  => [
                    'delivery.view',
                    'delivery.update-status',
                    'delivery.verify-code',
                    'order.view-all',
                    'profile.view',
                    'profile.edit',
                    'dashboard.view',
                ],
            ],

            'vendor' => [
                'display_name' => 'Vendor',
                'description'  => 'Third-party vendor fulfilling orders',
                'is_system'    => 1,
                'permissions'  => [
                    'delivery.view',
                    'delivery.update-status',
                    'delivery.verify-code',
                    'order.view-all',
                    'product.view',
                    'profile.view',
                    'profile.edit',
                    'dashboard.view',
                ],
            ],
        ];

        // ═══ Create/update roles ═══
        foreach ($roles as $roleName => $config) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                [
                    'display_name' => $config['display_name'],
                    'description'  => $config['description'],
                    'is_system'    => $config['is_system'],
                    'is_active'    => 1,
                ]
            );

            $role->update([
                'display_name' => $config['display_name'],
                'description'  => $config['description'],
                'is_system'    => $config['is_system'],
                'is_active'    => 1,
            ]);

            // Filter valid permissions
            $validPermissions = Permission::whereIn('name', $config['permissions'])
                ->where('guard_name', 'web')
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($validPermissions);

            // Clear this role's users cache
            $this->clearRoleUsersCache($role);

            $this->command->info("✓ Role '{$roleName}' → " . count($validPermissions) . " permissions");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  ✅ ROLES SEEDED');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  Roles:       ' . Role::count());
        $this->command->info('  Permissions: ' . Permission::count());
        $this->command->info('═══════════════════════════════════════');
    }

    protected function clearRoleUsersCache(Role $role): void
    {
        User::whereHas('roles', function ($q) use ($role) {
            $q->where('roles.id', $role->id);
        })->each(function ($user) {
            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');
        });
    }
}