<?php

namespace Database\Seeders;

use Database\Seeders\AdminUserSeeder;
use Database\Seeders\HomepageSettingSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
            HomepageSettingSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}