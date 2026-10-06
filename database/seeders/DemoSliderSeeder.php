<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class DemoSliderSeeder extends Seeder
{
    public function run(): void
    {
        if (Slider::count() > 0) {
            $this->command->info('✓ Sliders already exist. Skipping.');
            return;
        }

        $sliders = [
            [
                'title'         => 'Mega Sale — Up to 50% OFF',
                'subtitle'      => 'Discover unbeatable deals on premium products across all categories. Limited time offer with free shipping.',
                'button_text'   => 'Shop the Sale',
                'link_type'     => 'url',
                'link_value'    => '/products',
                'text_position' => 'left',
                'badge_text'    => '🔥 MEGA DEAL',
                'badge_color'   => 'pink',
                'sort_order'    => 1,
                'is_active'     => true,
            ],
            [
                'title'         => 'New Arrivals Just Landed',
                'subtitle'      => 'Be the first to grab the latest trends and cutting-edge technology. Fresh stock added daily.',
                'button_text'   => 'Explore Now',
                'link_type'     => 'url',
                'link_value'    => '/products',
                'text_position' => 'left',
                'badge_text'    => '✨ NEW',
                'badge_color'   => 'cyan',
                'sort_order'    => 2,
                'is_active'     => true,
            ],
            [
                'title'         => 'Free Shipping on Orders ৳500+',
                'subtitle'      => 'Fast, secure, and completely free delivery on all qualifying orders across Bangladesh.',
                'button_text'   => 'Start Shopping',
                'link_type'     => 'url',
                'link_value'    => '/products',
                'text_position' => 'left',
                'badge_text'    => '🚚 FREE DELIVERY',
                'badge_color'   => 'green',
                'sort_order'    => 3,
                'is_active'     => true,
            ],
        ];

        foreach ($sliders as $data) {
            Slider::create($data);
        }

        $this->command->info('✓ Demo sliders seeded: ' . count($sliders));
    }
}