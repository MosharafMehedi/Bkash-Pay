<?php

namespace Database\Seeders;

use App\Models\DeliveryCharge;
use Illuminate\Database\Seeder;

class DemoDeliveryChargeSeeder extends Seeder
{
    public function run(): void
    {
        $charges = [
            ['city' => 'Dhaka',        'charge' => 60,  'free_above' => 500,  'days' => 1],
            ['city' => 'Chittagong',   'charge' => 100, 'free_above' => 1000, 'days' => 2],
            ['city' => 'Sylhet',       'charge' => 120, 'free_above' => 1500, 'days' => 3],
            ['city' => 'Rajshahi',     'charge' => 120, 'free_above' => 1500, 'days' => 3],
            ['city' => 'Khulna',       'charge' => 130, 'free_above' => 1500, 'days' => 3],
            ['city' => 'Barisal',      'charge' => 150, 'free_above' => 2000, 'days' => 4],
            ['city' => 'Rangpur',      'charge' => 150, 'free_above' => 2000, 'days' => 4],
            ['city' => 'Mymensingh',   'charge' => 100, 'free_above' => 1000, 'days' => 2],
            ['city' => 'Comilla',      'charge' => 100, 'free_above' => 1000, 'days' => 2],
            ['city' => 'Narayanganj',  'charge' => 80,  'free_above' => 800,  'days' => 1],
        ];

        foreach ($charges as $data) {
            DeliveryCharge::updateOrCreate(
                ['city' => $data['city']],
                [
                    'charge'         => $data['charge'],
                    'free_above'     => $data['free_above'],
                    'estimated_days' => $data['days'],
                    'is_active'      => true,
                ]
            );
        }

        $this->command->info('✓ Demo delivery charges seeded: ' . count($charges));
    }
}