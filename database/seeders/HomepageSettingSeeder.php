<?php

namespace Database\Seeders;

use App\Models\HomepageSetting;
use Illuminate\Database\Seeder;

class HomepageSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ═══ Section toggles ═══
            ['show_slider',              '1', 'boolean', 'sections', 'Hero Slider'],
            ['show_categories',          '1', 'boolean', 'sections', 'Category Grid'],
            ['show_featured_products',   '1', 'boolean', 'sections', 'Featured Products'],
            ['show_new_arrivals',        '1', 'boolean', 'sections', 'New Arrivals'],
            ['show_deals',               '1', 'boolean', 'sections', 'Deals of the Day'],
            ['show_best_sellers',        '1', 'boolean', 'sections', 'Best Sellers'],
            ['show_brands',              '1', 'boolean', 'sections', 'Brands'],
            ['show_testimonials',        '1', 'boolean', 'sections', 'Testimonials'],
            ['show_newsletter',          '1', 'boolean', 'sections', 'Newsletter'],
            ['show_trust_badges',        '1', 'boolean', 'sections', 'Trust Badges'],

            // ═══ General ═══
            ['site_title',               'mPay Gateway', 'string', 'general', 'Site Title'],
            ['site_tagline',             'Modern Payment Gateway Experience', 'string', 'general', 'Tagline'],
            ['hero_title',               'Pick something to buy', 'string', 'general', 'Hero Title'],
            ['hero_subtitle',            'Secure checkout — explore our catalog', 'string', 'general', 'Hero Subtitle'],

            // ═══ Headings ═══
            ['heading_categories',       'Shop by Category', 'string', 'headings', 'Categories Heading'],
            ['heading_featured',         'Featured Products', 'string', 'headings', 'Featured Heading'],
            ['heading_new',              'New Arrivals', 'string', 'headings', 'New Arrivals Heading'],
            ['heading_deals',            'Deals of the Day', 'string', 'headings', 'Deals Heading'],
            ['heading_best_sellers',     'Best Sellers', 'string', 'headings', 'Best Sellers Heading'],
            ['heading_brands',           'Top Brands', 'string', 'headings', 'Brands Heading'],
            ['heading_testimonials',     'What our customers say', 'string', 'headings', 'Testimonials Heading'],

            // ═══ Trust Badges ═══
            ['trust_badge_1_icon',  '🚚', 'string', 'trust', 'Trust Badge 1 Icon'],
            ['trust_badge_1_title', 'Free Shipping', 'string', 'trust', 'Trust Badge 1 Title'],
            ['trust_badge_1_sub',   'On orders ৳500+', 'string', 'trust', 'Trust Badge 1 Sub'],

            ['trust_badge_2_icon',  '🔒', 'string', 'trust', 'Trust Badge 2 Icon'],
            ['trust_badge_2_title', 'Secure Payment', 'string', 'trust', 'Trust Badge 2 Title'],
            ['trust_badge_2_sub',   '256-bit SSL', 'string', 'trust', 'Trust Badge 2 Sub'],

            ['trust_badge_3_icon',  '↩️', 'string', 'trust', 'Trust Badge 3 Icon'],
            ['trust_badge_3_title', 'Easy Returns', 'string', 'trust', 'Trust Badge 3 Title'],
            ['trust_badge_3_sub',   '7-day return', 'string', 'trust', 'Trust Badge 3 Sub'],

            ['trust_badge_4_icon',  '📞', 'string', 'trust', 'Trust Badge 4 Icon'],
            ['trust_badge_4_title', '24/7 Support', 'string', 'trust', 'Trust Badge 4 Title'],
            ['trust_badge_4_sub',   'Always available', 'string', 'trust', 'Trust Badge 4 Sub'],

            // ═══ Social ═══
            ['social_facebook',   '', 'string', 'social', 'Facebook URL'],
            ['social_instagram',  '', 'string', 'social', 'Instagram URL'],
            ['social_twitter',    '', 'string', 'social', 'Twitter / X URL'],
            ['social_youtube',    '', 'string', 'social', 'YouTube URL'],
            ['social_whatsapp',   '', 'string', 'social', 'WhatsApp Number'],

            // ═══ Contact ═══
            ['contact_email',     'hello@mpay.com', 'string', 'contact', 'Contact Email'],
            ['contact_phone',     '+880 1XXX-XXXXXX', 'string', 'contact', 'Contact Phone'],
            ['contact_address',   '123 Main Street, Dhaka', 'string', 'contact', 'Address'],
            ['footer_about',      'Modern payment gateway for seamless ecommerce.', 'string', 'contact', 'Footer About Text'],
            ['footer_copyright',  'All rights reserved.', 'string', 'contact', 'Copyright Text'],
        ];

        foreach ($settings as [$key, $value, $type, $group, $label]) {
            HomepageSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type'  => $type,
                    'group' => $group,
                    'label' => $label,
                ]
            );
        }

        $this->command->info('✓ Homepage settings seeded (' . count($settings) . ' keys)');
    }
}