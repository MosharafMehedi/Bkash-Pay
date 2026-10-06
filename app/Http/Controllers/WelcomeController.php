<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\HomepageSetting;
use App\Models\Product;
use App\Models\Review;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    /**
     * Homepage.
     */
    public function index()
    {
        // ── Settings ──
        $settings = $this->getSettings();

        // ── Announcements ──
        $announcements = Announcement::active()
            ->ordered()
            ->take(3)
            ->get();

        // ── Sliders ──
        $sliders = collect();
        if ($settings['show_slider']) {
            $sliders = Slider::active()
                ->ordered()
                ->take(5)
                ->get();
        }

        // ── Categories ──
        $categories = collect();
        if ($settings['show_categories']) {
            $categories = Category::active()
                ->whereNull('parent_id')
                ->ordered()
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
                ->take(8)
                ->get();
        }

        // ── Featured Products ──
        $featuredProducts = collect();
        if ($settings['show_featured_products']) {
            $featuredProducts = Product::active()
                ->featured()
                ->with('category')
                ->orderByDesc('rating')
                ->take(8)
                ->get();
        }

        // ── New Arrivals ──
        $newArrivals = collect();
        if ($settings['show_new_arrivals']) {
            $newArrivals = Product::active()
                ->with('category')
                ->latest()
                ->take(8)
                ->get();
        }

        // ── Deals ──
        $deals = collect();
        if ($settings['show_deals']) {
            $deals = Product::active()
                ->deals()
                ->with('category')
                ->orderBy('deal_ends_at')
                ->take(6)
                ->get();
        }

        // ── Best Sellers ──
        $bestSellers = collect();
        if ($settings['show_best_sellers']) {
            $bestSellers = Product::active()
                ->with('category')
                ->where('rating', '>=', 4)
                ->orderByDesc('rating')
                ->orderByDesc('review_count')
                ->take(8)
                ->get();
        }

        // ── Brands (auto from products) ──
        $brands = collect();
        if ($settings['show_brands']) {
            $brands = Product::active()
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand')
                ->take(10);
        }

        // ── Testimonials (top 5★ reviews) ──
        $testimonials = collect();
        if ($settings['show_testimonials']) {
            $testimonials = Review::with(['user', 'product'])
                ->where('is_approved', true)
                ->where('rating', 5)
                ->whereNotNull('comment')
                ->latest()
                ->take(6)
                ->get();

            // Only show if at least 3
            if ($testimonials->count() < 3) {
                $testimonials = collect();
            }
        }

        return view('welcome', compact(
            'settings',
            'announcements',
            'sliders',
            'categories',
            'featuredProducts',
            'newArrivals',
            'deals',
            'bestSellers',
            'brands',
            'testimonials'
        ));
    }

    /**
     * Get homepage settings as array.
     */
    protected function getSettings(): array
    {
        $defaults = [
            'show_slider'            => true,
            'show_categories'        => true,
            'show_featured_products' => true,
            'show_new_arrivals'      => true,
            'show_deals'             => true,
            'show_best_sellers'      => true,
            'show_brands'            => true,
            'show_testimonials'      => true,
            'show_newsletter'        => true,
            'show_trust_badges'      => true,

            'site_title'             => config('app.name', 'mPay Gateway'),
            'site_tagline'           => 'Modern Payment Gateway Experience',
            'hero_title'             => 'Pick something to buy',
            'hero_subtitle'          => 'Secure checkout — explore our catalog',

            'heading_categories'     => 'Shop by Category',
            'heading_featured'       => 'Featured Products',
            'heading_new'            => 'New Arrivals',
            'heading_deals'          => 'Deals of the Day',
            'heading_best_sellers'   => 'Best Sellers',
            'heading_brands'         => 'Top Brands',
            'heading_testimonials'   => 'What our customers say',

            'trust_badge_1_icon'     => '🚚',
            'trust_badge_1_title'    => 'Free Shipping',
            'trust_badge_1_sub'      => 'On orders ৳500+',

            'trust_badge_2_icon'     => '🔒',
            'trust_badge_2_title'    => 'Secure Payment',
            'trust_badge_2_sub'      => '256-bit SSL',

            'trust_badge_3_icon'     => '↩️',
            'trust_badge_3_title'    => 'Easy Returns',
            'trust_badge_3_sub'      => '7-day return',

            'trust_badge_4_icon'     => '📞',
            'trust_badge_4_title'    => '24/7 Support',
            'trust_badge_4_sub'      => 'Always available',

            'social_facebook'        => '',
            'social_instagram'       => '',
            'social_twitter'         => '',
            'social_youtube'         => '',
            'social_whatsapp'        => '',

            'contact_email'          => 'hello@mpay.com',
            'contact_phone'          => '+880 1XXX-XXXXXX',
            'contact_address'        => '123 Main Street, Dhaka',
            'footer_about'           => 'Modern payment gateway for seamless ecommerce.',
            'footer_copyright'       => 'All rights reserved.',
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = HomepageSetting::get($key, $default);
        }

        return $settings;
    }
}