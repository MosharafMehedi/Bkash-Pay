<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ═══ ADMIN (bypasses all via Gate::before — but still has all for reference) ═══
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            [
                'display_name' => 'Administrator',
                'description'  => 'Full system access — bypasses all permission checks',
                'is_system'    => 1,
                'is_active'    => 1,
            ]
        );

        // Assign ALL permissions to admin (for reference / UI display)
        $admin->syncPermissions(Permission::all());

        // ═══ CUSTOMER (user) ═══
        $customer = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            [
                'display_name' => 'Customer',
                'description'  => 'Regular customer — browse, buy, review',
                'is_system'    => 1,
                'is_active'    => 1,
            ]
        );

        $customer->syncPermissions([
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
        ]);

        // ═══ DELIVERY MAN ═══
        $deliveryMan = Role::firstOrCreate(
            ['name' => 'delivery_man', 'guard_name' => 'web'],
            [
                'display_name' => 'Delivery Man',
                'description'  => 'Handles assigned deliveries',
                'is_system'    => 1,
                'is_active'    => 1,
            ]
        );

        $deliveryMan->syncPermissions([
            'delivery.view',
            'delivery.update-status',
            'delivery.verify-code',
            'order.view-all',
            'profile.view',
            'profile.edit',
            'dashboard.view',
        ]);

        // ═══ VENDOR ═══
        $vendor = Role::firstOrCreate(
            ['name' => 'vendor', 'guard_name' => 'web'],
            [
                'display_name' => 'Vendor',
                'description'  => 'Third-party vendor fulfilling orders',
                'is_system'    => 1,
                'is_active'    => 1,
            ]
        );

        $vendor->syncPermissions([
            'delivery.view',
            'delivery.update-status',
            'delivery.verify-code',
            'order.view-all',
            'product.view',
            'profile.view',
            'profile.edit',
            'dashboard.view',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✓ Roles seeded: admin, user, delivery_man, vendor');
    }
}