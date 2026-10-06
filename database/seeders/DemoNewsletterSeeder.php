<?php

namespace Database\Seeders;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoNewsletterSeeder extends Seeder
{
    public function run(): void
    {
        if (NewsletterSubscriber::count() >= 10) {
            $this->command->info('✓ Newsletter subscribers already exist. Skipping.');
            return;
        }

        $emails = [
            'arafat@example.com',
            'rahim@example.com',
            'karim@example.com',
            'fatima@example.com',
            'tanvir@example.com',
            'shakib@example.com',
            'nusrat@example.com',
            'mehedi@example.com',
            'sadia@example.com',
            'hasan@example.com',
            'priya@example.com',
            'rakib@example.com',
            'sumaiya@example.com',
            'arman@example.com',
            'tania@example.com',
        ];

        $count = 0;

        foreach ($emails as $email) {
            NewsletterSubscriber::updateOrCreate(
                ['email' => $email],
                [
                    'token'         => Str::random(64),
                    'is_active'     => true,
                    'subscribed_at' => now()->subDays(rand(1, 60)),
                    'ip_address'    => '192.168.1.' . rand(1, 255),
                ]
            );
            $count++;
        }

        $this->command->info("✓ Demo newsletter subscribers seeded: {$count}");
    }
}