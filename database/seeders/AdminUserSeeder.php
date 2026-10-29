<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = User::firstOrCreate(
            ['email' => 'admin@mpay.com'],
            [
                'name'       => 'Super Admin',
                'password'   => Hash::make('password'),
                'phone'      => '01700000000',
                'city'       => 'Dhaka',
                'address'    => '123 Main Street',
                'is_active'  => 1,
            ]
        );

        $admin->syncRoles(['admin']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✓ Admin user seeded');
        $this->command->info('  Email:    admin@mpay.com');
        $this->command->info('  Password: password');
    }
}