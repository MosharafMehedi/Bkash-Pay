<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class DemoCouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code'           => 'WELCOME10',
                'name'           => 'Welcome Discount',
                'description'    => 'Get 10% off on your first order',
                'type'           => 'percent',
                'value'          => 10,
                'min_order'      => 500,
                'max_discount'   => 200,
                'usage_limit'    => 1000,
                'per_user_limit' => 1,
                'expires_at'     => now()->addMonths(6),
                'is_active'      => true,
            ],
            [
                'code'           => 'SAVE200',
                'name'           => 'Fixed ৳200 OFF',
                'description'    => 'Get flat ৳200 off on orders above ৳2000',
                'type'           => 'fixed',
                'value'          => 200,
                'min_order'      => 2000,
                'max_discount'   => null,
                'usage_limit'    => 500,
                'per_user_limit' => 1,
                'expires_at'     => now()->addMonths(3),
                'is_active'      => true,
            ],
            [
                'code'           => 'EID25',
                'name'           => 'Eid Special',
                'description'    => '25% off for Eid festival',
                'type'           => 'percent',
                'value'          => 25,
                'min_order'      => 1000,
                'max_discount'   => 1000,
                'usage_limit'    => 2000,
                'per_user_limit' => 2,
                'expires_at'     => now()->addMonths(2),
                'is_active'      => true,
            ],
            [
                'code'           => 'FREESHIP',
                'name'           => 'Free Shipping',
                'description'    => 'Free shipping on orders above ৳1500',
                'type'           => 'fixed',
                'value'          => 100,
                'min_order'      => 1500,
                'max_discount'   => null,
                'usage_limit'    => null,
                'per_user_limit' => 5,
                'expires_at'     => now()->addMonths(6),
                'is_active'      => true,
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }

        $this->command->info('✓ Demo coupons seeded: ' . count($coupons));
    }
}