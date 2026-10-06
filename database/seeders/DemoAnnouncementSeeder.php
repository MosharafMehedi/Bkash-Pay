<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class DemoAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        if (Announcement::count() > 0) {
            $this->command->info('✓ Announcements already exist. Skipping.');
            return;
        }

        $announcements = [
            [
                'text'           => 'Free shipping on orders ৳500+ · Shop now',
                'icon'           => '🚚',
                'link'           => '/products',
                'bg_color'       => null,
                'sort_order'     => 1,
                'is_active'      => true,
                'is_dismissible' => true,
            ],
            [
                'text'           => 'Eid Sale — Up to 50% OFF on selected items',
                'icon'           => '🎉',
                'link'           => '/products',
                'bg_color'       => 'pink',
                'sort_order'     => 2,
                'is_active'      => true,
                'is_dismissible' => true,
            ],
            [
                'text'           => 'New arrivals every week — Stay tuned!',
                'icon'           => '✨',
                'link'           => null,
                'bg_color'       => 'violet',
                'sort_order'     => 3,
                'is_active'      => false,
                'is_dismissible' => true,
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::create($data);
        }

        $this->command->info('✓ Demo announcements seeded: ' . count($announcements));
    }
}