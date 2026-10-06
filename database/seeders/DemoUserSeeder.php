<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Arafat Hossain',
                'email'    => 'arafat@example.com',
                'password' => 'password',
                'phone'    => '01711111111',
                'city'     => 'Dhaka',
                'role'     => 'user',
            ],
            [
                'name'     => 'Rahim Khan',
                'email'    => 'rahim@example.com',
                'password' => 'password',
                'phone'    => '01722222222',
                'city'     => 'Chittagong',
                'role'     => 'user',
            ],
            [
                'name'     => 'Karim Ahmed',
                'email'    => 'karim@example.com',
                'password' => 'password',
                'phone'    => '01733333333',
                'city'     => 'Dhaka',
                'role'     => 'user',
            ],
            [
                'name'     => 'Fatima Begum',
                'email'    => 'fatima@example.com',
                'password' => 'password',
                'phone'    => '01744444444',
                'city'     => 'Sylhet',
                'role'     => 'user',
            ],
            [
                'name'     => 'Tanvir Rahman',
                'email'    => 'tanvir@example.com',
                'password' => 'password',
                'phone'    => '01755555555',
                'city'     => 'Rajshahi',
                'role'     => 'user',
            ],
            [
                'name'     => 'Jahid Hasan (Delivery)',
                'email'    => 'delivery@example.com',
                'password' => 'password',
                'phone'    => '01766666666',
                'city'     => 'Dhaka',
                'role'     => 'delivery_man',
            ],
            [
                'name'     => 'Pathao Express (Vendor)',
                'email'    => 'vendor@example.com',
                'password' => 'password',
                'phone'    => '01777777777',
                'city'     => 'Dhaka',
                'role'     => 'vendor',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'] ?? 'user';
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'          => Hash::make($data['password']),
                    'status'         => 1,
                    'email_verified_at' => now(),
                ])
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        $this->command->info('✓ Demo users seeded (password: password)');
    }
}