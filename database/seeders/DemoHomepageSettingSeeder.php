<?php

namespace Database\Seeders;

use App\Models\HomepageSetting;
use Illuminate\Database\Seeder;

class DemoHomepageSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Sections (all on)
            ['key' => 'show_slider',            'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Hero Slider'],
            ['key' => 'show_categories',        'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Category Grid'],
            ['key' => 'show_featured_products', 'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Featured Products'],
            ['key' => 'show_new_arrivals',      'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'New Arrivals'],
            ['key' => 'show_deals',             'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Deals of the Day'],
            ['key' => 'show_best_sellers',      'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Best Sellers'],
            ['key' => 'show_brands',            'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Brands'],
            ['key' => 'show_testimonials',      'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Testimonials'],
            ['key' => 'show_newsletter',        'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Newsletter'],
            ['key' => 'show_trust_badges',      'value' => '1', 'type' => 'boolean', 'group' => 'sections', 'label' => 'Trust Badges'],

            // General
            ['key' => 'site_title',      'value' => config('app.name', 'mPay Gateway'), 'type' => 'string', 'group' => 'general', 'label' => 'Site Title'],
            ['key' => 'site_tagline',    'value' => 'Modern Payment Gateway Experience', 'type' => 'string', 'group' => 'general', 'label' => 'Tagline'],
            ['key' => 'hero_title',      'value' => 'Pick something to buy', 'type' => 'string', 'group' => 'general', 'label' => 'Hero Title'],
            ['key' => 'hero_subtitle',   'value' => 'Secure sandbox checkout — explore our catalog. No real money moves.', 'type' => 'string', 'group' => 'general', 'label' => 'Hero Subtitle'],

            // Headings
            ['key' => 'heading_categories',   'value' => 'Shop by Category', 'type' => 'string', 'group' => 'headings', 'label' => 'Categories Heading'],
            ['key' => 'heading_featured',     'value' => 'Featured Products', 'type' => 'string', 'group' => 'headings', 'label' => 'Featured Heading'],
            ['key' => 'heading_new',          'value' => 'New Arrivals', 'type' => 'string', 'group' => 'headings', 'label' => 'New Arrivals Heading'],
            ['key' => 'heading_deals',        'value' => 'Deals of the Day', 'type' => 'string', 'group' => 'headings', 'label' => 'Deals Heading'],
            ['key' => 'heading_best_sellers', 'value' => 'Best Sellers', 'type' => 'string', 'group' => 'headings', 'label' => 'Best Sellers Heading'],
            ['key' => 'heading_brands',       'value' => 'Top Brands', 'type' => 'string', 'group' => 'headings', 'label' => 'Brands Heading'],
            ['key' => 'heading_testimonials', 'value' => 'What our customers say', 'type' => 'string', 'group' => 'headings', 'label' => 'Testimonials Heading'],

            // Trust Badges
            ['key' => 'trust_badge_1_icon',  'value' => '🚚', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 1 Icon'],
            ['key' => 'trust_badge_1_title', 'value' => 'Free Shipping', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 1 Title'],
            ['key' => 'trust_badge_1_sub',   'value' => 'On orders ৳500+', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 1 Sub'],

            ['key' => 'trust_badge_2_icon',  'value' => '🔒', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 2 Icon'],
            ['key' => 'trust_badge_2_title', 'value' => 'Secure Payment', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 2 Title'],
            ['key' => 'trust_badge_2_sub',   'value' => '256-bit SSL', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 2 Sub'],

            ['key' => 'trust_badge_3_icon',  'value' => '↩️', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 3 Icon'],
            ['key' => 'trust_badge_3_title', 'value' => 'Easy Returns', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 3 Title'],
            ['key' => 'trust_badge_3_sub',   'value' => '7-day return', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 3 Sub'],

            ['key' => 'trust_badge_4_icon',  'value' => '📞', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 4 Icon'],
            ['key' => 'trust_badge_4_title', 'value' => '24/7 Support', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 4 Title'],
            ['key' => 'trust_badge_4_sub',   'value' => 'Always available', 'type' => 'string', 'group' => 'trust', 'label' => 'Trust Badge 4 Sub'],

            // Social
            ['key' => 'social_facebook',  'value' => 'https://facebook.com/mpay', 'type' => 'string', 'group' => 'social', 'label' => 'Facebook URL'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/mpay', 'type' => 'string', 'group' => 'social', 'label' => 'Instagram URL'],
            ['key' => 'social_twitter',   'value' => 'https://twitter.com/mpay', 'type' => 'string', 'group' => 'social', 'label' => 'Twitter URL'],
            ['key' => 'social_youtube',   'value' => 'https://youtube.com/@mpay', 'type' => 'string', 'group' => 'social', 'label' => 'YouTube URL'],
            ['key' => 'social_whatsapp',  'value' => '+8801700000000', 'type' => 'string', 'group' => 'social', 'label' => 'WhatsApp Number'],

            // Contact
            ['key' => 'contact_email',    'value' => 'hello@mpay.com', 'type' => 'string', 'group' => 'contact', 'label' => 'Contact Email'],
            ['key' => 'contact_phone',    'value' => '+880 1711-000000', 'type' => 'string', 'group' => 'contact', 'label' => 'Contact Phone'],
            ['key' => 'contact_address',  'value' => '123 Main Street, Dhanmondi, Dhaka 1205, Bangladesh', 'type' => 'string', 'group' => 'contact', 'label' => 'Address'],
            ['key' => 'footer_about',     'value' => 'mPay Gateway is your one-stop destination for premium products at unbeatable prices. Fast delivery, secure payment, and 24/7 support.', 'type' => 'string', 'group' => 'contact', 'label' => 'Footer About'],
            ['key' => 'footer_copyright', 'value' => 'All rights reserved.', 'type' => 'string', 'group' => 'contact', 'label' => 'Copyright'],
        ];

        foreach ($settings as $setting) {
            HomepageSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✓ Demo homepage settings seeded: ' . count($settings));
    }
}