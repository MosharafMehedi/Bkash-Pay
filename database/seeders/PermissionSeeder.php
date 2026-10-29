<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ═══ FULL PERMISSION LIST ═══
        // Format: [name, group, display_name, description]
        $permissions = [
            // ══ Product ══
            ['product.view',        'product', 'View Products',        'Browse product list'],
            ['product.create',      'product', 'Create Product',       'Add new product'],
            ['product.edit',        'product', 'Edit Product',         'Update product details'],
            ['product.delete',      'product', 'Delete Product',       'Remove product'],

            // ══ Category ══
            ['category.view',       'category', 'View Categories',     'Browse category list'],
            ['category.create',     'category', 'Create Category',     'Add new category'],
            ['category.edit',       'category', 'Edit Category',       'Update category'],
            ['category.delete',     'category', 'Delete Category',     'Remove category'],

            // ══ Coupon ══
            ['coupon.view',         'coupon', 'View Coupons',          'Browse coupon list'],
            ['coupon.create',       'coupon', 'Create Coupon',         'Add new coupon'],
            ['coupon.edit',         'coupon', 'Edit Coupon',           'Update coupon'],
            ['coupon.delete',       'coupon', 'Delete Coupon',         'Remove coupon'],

            // ══ Order ══
            ['order.view-all',      'order', 'View All Orders',        'See every order'],
            ['order.view-own',      'order', 'View Own Orders',        'See own orders (customer)'],
            ['order.update-status', 'order', 'Update Order Status',    'Change order status'],
            ['order.assign',        'order', 'Assign Delivery',        'Assign delivery man/vendor'],
            ['order.cancel',        'order', 'Cancel Order',           'Cancel order'],
            ['order.return',        'order', 'Return Order',           'Mark order returned'],
            ['order.delete',        'order', 'Delete Order',           'Soft-delete order'],

            // ══ Delivery Charge ══
            ['delivery-charge.view',   'delivery-charge', 'View Delivery Charges',   'Browse charges'],
            ['delivery-charge.create', 'delivery-charge', 'Create Delivery Charge',  'Add new city charge'],
            ['delivery-charge.edit',   'delivery-charge', 'Edit Delivery Charge',    'Update charge'],
            ['delivery-charge.delete', 'delivery-charge', 'Delete Delivery Charge',  'Remove charge'],

            // ══ Delivery Panel ══
            ['delivery.view',        'delivery', 'View Deliveries',    'See assigned deliveries'],
            ['delivery.update-status','delivery', 'Update Delivery',   'Mark out for delivery'],
            ['delivery.verify-code', 'delivery', 'Verify Delivery Code','Complete delivery via code'],

            // ══ Review ══
            ['review.view',         'review', 'View Reviews',          'Browse reviews'],
            ['review.approve',      'review', 'Approve Review',        'Approve pending review'],
            ['review.reject',       'review', 'Reject Review',         'Reject/hide review'],
            ['review.delete',       'review', 'Delete Review',         'Remove review'],

            // ══ Slider ══
            ['slider.view',         'slider', 'View Sliders',          'Browse sliders'],
            ['slider.create',       'slider', 'Create Slider',         'Add new slider'],
            ['slider.edit',         'slider', 'Edit Slider',           'Update slider'],
            ['slider.delete',       'slider', 'Delete Slider',         'Remove slider'],

            // ══ Announcement ══
            ['announcement.view',   'announcement', 'View Announcements',  'Browse announcements'],
            ['announcement.create', 'announcement', 'Create Announcement', 'Add announcement'],
            ['announcement.edit',   'announcement', 'Edit Announcement',   'Update announcement'],
            ['announcement.delete', 'announcement', 'Delete Announcement', 'Remove announcement'],

            // ══ Homepage ══
            ['homepage.view',       'homepage', 'View Homepage Settings', 'See settings'],
            ['homepage.edit',       'homepage', 'Edit Homepage Settings', 'Update settings'],
            ['homepage.reset',      'homepage', 'Reset Homepage',         'Reset to defaults'],

            // ══ Newsletter ══
            ['newsletter.view',     'newsletter', 'View Subscribers',     'Browse subscribers'],
            ['newsletter.export',   'newsletter', 'Export Subscribers',   'Download CSV'],
            ['newsletter.delete',   'newsletter', 'Delete Subscriber',    'Remove subscriber'],

            // ══ User ══
            ['user.view',           'user', 'View Users',              'Browse users'],
            ['user.create',         'user', 'Create User',             'Add new user'],
            ['user.edit',           'user', 'Edit User',               'Update user'],
            ['user.delete',         'user', 'Delete User',             'Remove user'],

            // ══ Role ══
            ['role.view',           'role', 'View Roles',              'Browse roles'],
            ['role.create',         'role', 'Create Role',             'Add role'],
            ['role.edit',           'role', 'Edit Role',               'Update role'],
            ['role.delete',         'role', 'Delete Role',             'Remove role'],

            // ══ Permission ══
            ['permission.view',     'permission', 'View Permissions',   'Browse permissions'],
            ['permission.create',   'permission', 'Create Permission',  'Add permission'],
            ['permission.edit',     'permission', 'Edit Permission',    'Update permission'],
            ['permission.delete',   'permission', 'Delete Permission',  'Remove permission'],

            // ══ Audit Log ══
            ['audit-log.view',      'audit-log', 'View Audit Logs',    'Browse activity logs'],
            ['audit-log.delete',    'audit-log', 'Delete Audit Logs',  'Remove logs'],

            // ══ Cart ══
            ['cart.view',           'cart', 'View Cart',               'See cart'],
            ['cart.manage',         'cart', 'Manage Cart',             'Add/remove items'],

            // ══ Checkout ══
            ['checkout.view',       'checkout', 'View Checkout',        'Access checkout'],
            ['checkout.apply-coupon','checkout','Apply Coupon',         'Use coupon code'],

            // ══ Payment ══
            ['payment.bkash',       'payment', 'bKash Payment',         'Pay via bKash'],
            ['payment.paypal',      'payment', 'PayPal Payment',        'Pay via PayPal'],
            ['payment.sslcommerz',  'payment', 'SSLCommerz Payment',    'Pay via SSLCommerz'],
            ['payment.cash',        'payment', 'Cash on Delivery',      'Pay on delivery'],

            // ══ Profile ══
            ['profile.view',        'profile', 'View Profile',          'See own profile'],
            ['profile.edit',        'profile', 'Edit Profile',          'Update own profile'],

            // ══ Dashboard ══
            ['dashboard.view',      'dashboard', 'View Dashboard',      'Access dashboard'],
        ];

        foreach ($permissions as [$name, $group, $displayName, $description]) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                [
                    'group'        => $group,
                    'display_name' => $displayName,
                    'description'  => $description,
                    'is_active'    => 1,
                ]
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✓ Permissions seeded (' . count($permissions) . ' total)');
    }
}