<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 Running Full Demo Data Seeder...');
        $this->command->info('');

        $this->call([
            RolePermissionSeeder::class,
            DemoUserSeeder::class,
            DemoCategorySeeder::class,
            DemoProductSeeder::class,
            DemoSliderSeeder::class,
            DemoAnnouncementSeeder::class,
            DemoDeliveryChargeSeeder::class,
            DemoCouponSeeder::class,
            DemoHomepageSettingSeeder::class,
            DemoReviewSeeder::class,
            DemoNewsletterSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('');
        $this->command->info('🔑 Login credentials:');
        $this->command->info('   Admin:       admin@example.com / password');
        $this->command->info('   Customer:    arafat@example.com / password');
        $this->command->info('   Delivery:    delivery@example.com / password');
        $this->command->info('   Vendor:      vendor@example.com / password');
        $this->command->info('');
    }
}