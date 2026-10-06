<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomepageController extends Controller
{
    /**
     * Section toggles + settings list.
     */
    public const SETTINGS = [
        // Sections
        ['key' => 'show_slider',              'label' => 'Hero Slider',         'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_categories',          'label' => 'Category Grid',       'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_featured_products',   'label' => 'Featured Products',   'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_new_arrivals',        'label' => 'New Arrivals',        'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_deals',               'label' => 'Deals of the Day',    'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_best_sellers',        'label' => 'Best Sellers',        'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_brands',              'label' => 'Brands',              'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_testimonials',        'label' => 'Testimonials',        'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_newsletter',          'label' => 'Newsletter',          'group' => 'sections', 'type' => 'boolean', 'default' => true],
        ['key' => 'show_trust_badges',        'label' => 'Trust Badges',        'group' => 'sections', 'type' => 'boolean', 'default' => true],

        // General
        ['key' => 'site_title',               'label' => 'Site Title',          'group' => 'general', 'type' => 'string', 'default' => 'mPay Gateway'],
        ['key' => 'site_tagline',             'label' => 'Tagline',             'group' => 'general', 'type' => 'string', 'default' => 'Modern Payment Gateway Experience'],
        ['key' => 'hero_title',               'label' => 'Hero Title',          'group' => 'general', 'type' => 'string', 'default' => 'Pick something to buy'],
        ['key' => 'hero_subtitle',            'label' => 'Hero Subtitle',       'group' => 'general', 'type' => 'string', 'default' => 'Secure checkout — explore our catalog'],

        // Section Headings
        ['key' => 'heading_categories',       'label' => 'Categories Heading',  'group' => 'headings', 'type' => 'string', 'default' => 'Shop by Category'],
        ['key' => 'heading_featured',         'label' => 'Featured Heading',    'group' => 'headings', 'type' => 'string', 'default' => 'Featured Products'],
        ['key' => 'heading_new',              'label' => 'New Arrivals Heading','group' => 'headings', 'type' => 'string', 'default' => 'New Arrivals'],
        ['key' => 'heading_deals',            'label' => 'Deals Heading',       'group' => 'headings', 'type' => 'string', 'default' => 'Deals of the Day'],
        ['key' => 'heading_best_sellers',     'label' => 'Best Sellers Heading','group' => 'headings', 'type' => 'string', 'default' => 'Best Sellers'],
        ['key' => 'heading_brands',           'label' => 'Brands Heading',      'group' => 'headings', 'type' => 'string', 'default' => 'Top Brands'],
        ['key' => 'heading_testimonials',     'label' => 'Testimonials Heading','group' => 'headings', 'type' => 'string', 'default' => 'What our customers say'],

        // Trust Badges (4 items)
        ['key' => 'trust_badge_1_icon',       'label' => 'Trust Badge 1 Icon',  'group' => 'trust', 'type' => 'string', 'default' => '🚚'],
        ['key' => 'trust_badge_1_title',      'label' => 'Trust Badge 1 Title', 'group' => 'trust', 'type' => 'string', 'default' => 'Free Shipping'],
        ['key' => 'trust_badge_1_sub',        'label' => 'Trust Badge 1 Sub',   'group' => 'trust', 'type' => 'string', 'default' => 'On orders ৳500+'],

        ['key' => 'trust_badge_2_icon',       'label' => 'Trust Badge 2 Icon',  'group' => 'trust', 'type' => 'string', 'default' => '🔒'],
        ['key' => 'trust_badge_2_title',      'label' => 'Trust Badge 2 Title', 'group' => 'trust', 'type' => 'string', 'default' => 'Secure Payment'],
        ['key' => 'trust_badge_2_sub',        'label' => 'Trust Badge 2 Sub',   'group' => 'trust', 'type' => 'string', 'default' => '256-bit SSL'],

        ['key' => 'trust_badge_3_icon',       'label' => 'Trust Badge 3 Icon',  'group' => 'trust', 'type' => 'string', 'default' => '↩️'],
        ['key' => 'trust_badge_3_title',      'label' => 'Trust Badge 3 Title', 'group' => 'trust', 'type' => 'string', 'default' => 'Easy Returns'],
        ['key' => 'trust_badge_3_sub',        'label' => 'Trust Badge 3 Sub',   'group' => 'trust', 'type' => 'string', 'default' => '7-day return'],

        ['key' => 'trust_badge_4_icon',       'label' => 'Trust Badge 4 Icon',  'group' => 'trust', 'type' => 'string', 'default' => '📞'],
        ['key' => 'trust_badge_4_title',      'label' => 'Trust Badge 4 Title', 'group' => 'trust', 'type' => 'string', 'default' => '24/7 Support'],
        ['key' => 'trust_badge_4_sub',        'label' => 'Trust Badge 4 Sub',   'group' => 'trust', 'type' => 'string', 'default' => 'Always available'],

        // Social Links
        ['key' => 'social_facebook',          'label' => 'Facebook URL',        'group' => 'social', 'type' => 'string', 'default' => ''],
        ['key' => 'social_instagram',         'label' => 'Instagram URL',       'group' => 'social', 'type' => 'string', 'default' => ''],
        ['key' => 'social_twitter',           'label' => 'Twitter / X URL',     'group' => 'social', 'type' => 'string', 'default' => ''],
        ['key' => 'social_youtube',           'label' => 'YouTube URL',         'group' => 'social', 'type' => 'string', 'default' => ''],
        ['key' => 'social_whatsapp',          'label' => 'WhatsApp Number',     'group' => 'social', 'type' => 'string', 'default' => ''],

        // Contact
        ['key' => 'contact_email',            'label' => 'Contact Email',       'group' => 'contact', 'type' => 'string', 'default' => 'hello@mpay.com'],
        ['key' => 'contact_phone',            'label' => 'Contact Phone',       'group' => 'contact', 'type' => 'string', 'default' => '+880 1XXX-XXXXXX'],
        ['key' => 'contact_address',          'label' => 'Address',             'group' => 'contact', 'type' => 'string', 'default' => '123 Main Street, Dhaka'],
        ['key' => 'footer_about',             'label' => 'Footer About Text',   'group' => 'contact', 'type' => 'string', 'default' => 'Modern payment gateway for seamless ecommerce.'],
        ['key' => 'footer_copyright',         'label' => 'Copyright Text',      'group' => 'contact', 'type' => 'string', 'default' => 'All rights reserved.'],
    ];

    /**
     * Settings page.
     */
    public function index()
    {
        $settings = [];
        foreach (self::SETTINGS as $setting) {
            $settings[$setting['key']] = HomepageSetting::get($setting['key'], $setting['default']);
        }

        $groups = collect(self::SETTINGS)->groupBy('group');

        return view('admin.homepage.index', compact('settings', 'groups'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        try {
            foreach (self::SETTINGS as $setting) {
                $key   = $setting['key'];
                $type  = $setting['type'];
                $group = $setting['group'];
                $label = $setting['label'];

                if ($type === 'boolean') {
                    $value = $request->boolean($key, false);
                } else {
                    $value = $request->input($key, $setting['default']);
                }

                HomepageSetting::set($key, $value, $type, $group, $label);
            }

            return back()->with('success', 'Homepage settings updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Homepage settings update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update settings.');
        }
    }

    /**
     * Reset all settings to default.
     */
    public function reset()
    {
        try {
            HomepageSetting::truncate();

            return back()->with('success', 'All settings reset to defaults.');
        } catch (\Throwable $e) {
            Log::error('Homepage settings reset failed: ' . $e->getMessage());
            return back()->with('error', 'Could not reset settings.');
        }
    }
}