<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\DeliveryCharge;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if demo data already exists
        if (Category::count() > 0) {
            $this->command->warn('⚠ Demo data already exists. Skipping...');
            return;
        }

        $this->seedUsers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedCoupons();
        $this->seedDeliveryCharges();
        $this->seedSliders();
        $this->seedAnnouncements();
        $this->seedNewsletterSubscribers();
        $this->seedOrders();
        $this->seedReviews();

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  ✅ DEMO DATA SEEDED');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  Categories:   ' . Category::count());
        $this->command->info('  Products:     ' . Product::count());
        $this->command->info('  Coupons:      ' . Coupon::count());
        $this->command->info('  Orders:       ' . Order::count());
        $this->command->info('  Reviews:      ' . Review::count());
        $this->command->info('───────────────────────────────────────────');
        $this->command->info('  Demo Users:');
        $this->command->info('    customer@mpay.com  / password');
        $this->command->info('    delivery@mpay.com  / password');
        $this->command->info('    vendor@mpay.com    / password');
        $this->command->info('═══════════════════════════════════════════');
    }

    // ═══════════════════════════════════════════════════════════
    //  USERS
    // ═══════════════════════════════════════════════════════════

    protected function seedUsers(): void
    {
        $users = [
            ['customer@mpay.com', 'John Customer', 'user'],
            ['delivery@mpay.com', 'Karim Delivery', 'delivery_man'],
            ['vendor@mpay.com',   'Rahim Vendor',   'vendor'],
        ];

        foreach ($users as [$email, $name, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $name,
                    'password'  => Hash::make('password'),
                    'phone'     => '0170000' . rand(1000, 9999),
                    'city'      => 'Dhaka',
                    'is_active' => 1,
                ]
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        $this->command->info('✓ Demo users seeded (3)');
    }

    // ═══════════════════════════════════════════════════════════
    //  CATEGORIES
    // ═══════════════════════════════════════════════════════════

    protected function seedCategories(): void
    {
        $categories = [
            // Parents
            ['Electronics', '📱', null],
            ['Fashion', '👕', null],
            ['Home & Kitchen', '🏠', null],
            ['Sports', '⚽', null],
            ['Books', '📚', null],

            // Electronics children
            ['Smartphones', '📱', 'Electronics'],
            ['Laptops', '💻', 'Electronics'],
            ['Headphones', '🎧', 'Electronics'],
            ['Smart Watches', '⌚', 'Electronics'],

            // Fashion children
            ['Men\'s Clothing', '👔', 'Fashion'],
            ['Women\'s Clothing', '👗', 'Fashion'],
            ['Shoes', '👞', 'Fashion'],
            ['Accessories', '💍', 'Fashion'],

            // Home & Kitchen children
            ['Furniture', '🪑', 'Home & Kitchen'],
            ['Kitchen Appliances', '🍳', 'Home & Kitchen'],
            ['Home Decor', '🖼️', 'Home & Kitchen'],
        ];

        $created = [];

        foreach ($categories as [$name, $icon, $parentName]) {
            $parent = $parentName ? ($created[$parentName] ?? Category::where('name', $parentName)->first()) : null;

            $cat = Category::create([
                'parent_id'   => $parent?->id,
                'name'        => $name,
                'slug'        => Str::slug($name),
                'description' => "Browse {$name} products",
                'icon'        => $icon,
                'is_active'   => 1,
                'is_featured' => in_array($name, ['Electronics', 'Fashion']),
                'sort_order'  => 0,
            ]);

            $created[$name] = $cat;
        }

        $this->command->info('✓ Categories seeded (' . Category::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  PRODUCTS
    // ═══════════════════════════════════════════════════════════

    protected function seedProducts(): void
    {
        $products = [
            // Smartphones
            ['iPhone 15 Pro', 'Smartphones', 120000, 1100, 4000, 'Apple', 12, true, true],
            ['Samsung Galaxy S24', 'Smartphones', 95000, 870, 3000, 'Samsung', 8, true, false],
            ['Xiaomi 14', 'Smartphones', 45000, 410, 2000, 'Xiaomi', 15, false, true],

            // Laptops
            ['MacBook Air M3', 'Laptops', 150000, 1370, 5000, 'Apple', 5, true, false],
            ['Dell XPS 13', 'Laptops', 110000, 1000, 3000, 'Dell', 6, true, false],
            ['HP Pavilion', 'Laptops', 75000, 685, 2000, 'HP', 10, false, false],

            // Headphones
            ['Sony WH-1000XM5', 'Headphones', 35000, 320, 1500, 'Sony', 20, true, true],
            ['AirPods Pro 2', 'Headphones', 28000, 256, 1000, 'Apple', 25, true, false],
            ['JBL Tune 770', 'Headphones', 15000, 137, 500, 'JBL', 30, false, false],

            // Smart Watches
            ['Apple Watch Series 9', 'Smart Watches', 45000, 410, 2000, 'Apple', 10, true, false],
            ['Samsung Galaxy Watch 6', 'Smart Watches', 32000, 292, 1000, 'Samsung', 12, false, false],

            // Men's Clothing
            ['Classic Cotton T-Shirt', 'Men\'s Clothing', 1500, 14, 0, 'Fashion Co', 100, false, false],
            ['Formal Dress Shirt', 'Men\'s Clothing', 2500, 23, 0, 'Fashion Co', 60, false, false],
            ['Denim Jeans', 'Men\'s Clothing', 3000, 27, 500, 'Levi\'s', 45, true, false],

            // Women's Clothing
            ['Floral Summer Dress', 'Women\'s Clothing', 3500, 32, 0, 'Fashion Co', 50, true, false],
            ['Silk Scarf', 'Women\'s Clothing', 1200, 11, 0, 'Fashion Co', 80, false, false],

            // Shoes
            ['Running Sneakers', 'Shoes', 5000, 46, 800, 'Nike', 35, true, false],
            ['Leather Dress Shoes', 'Shoes', 7000, 64, 1000, 'Bata', 25, false, false],

            // Accessories
            ['Leather Wallet', 'Accessories', 2000, 18, 0, 'Fashion Co', 70, false, false],
            ['Sunglasses UV400', 'Accessories', 3000, 27, 0, 'Ray-Ban', 40, true, false],

            // Furniture
            ['Modern Sofa', 'Furniture', 45000, 410, 5000, 'Home Co', 3, true, false],
            ['Wooden Dining Table', 'Furniture', 30000, 274, 3000, 'Home Co', 5, false, false],

            // Kitchen Appliances
            ['Microwave Oven', 'Kitchen Appliances', 20000, 182, 2000, 'LG', 8, false, false],
            ['Coffee Maker', 'Kitchen Appliances', 8000, 73, 800, 'Philips', 15, true, false],

            // Home Decor
            ['Wall Art Painting', 'Home Decor', 3500, 32, 0, 'Art Gallery', 20, false, false],
            ['LED Table Lamp', 'Home Decor', 2500, 23, 0, 'Philips', 30, false, false],

            // Books
            ['Atomic Habits', 'Books', 800, 7, 0, 'Penguin', 50, true, false],
            ['The Lean Startup', 'Books', 1200, 11, 0, 'Crown', 40, false, false],
        ];

        foreach ($products as $p) {
            [$name, $categoryName, $priceBdt, $priceUsd, $discount, $brand, $stock, $isFeatured, $isDeal] = $p;

            $category = Category::where('name', $categoryName)->first();

            if (! $category) continue;

            $dealPrice = $isDeal ? $priceBdt * 0.8 : null;
            $dealEndsAt = $isDeal ? now()->addDays(7) : null;

            Product::create([
                'category_id'     => $category->id,
                'name'            => $name,
                'slug'            => Str::slug($name) . '-' . Str::random(4),
                'subtitle'        => "Premium {$name}",
                'description'     => "High-quality {$name}. Perfect for everyday use with premium build quality and modern design.",
                'price_bdt'       => $priceBdt,
                'price_usd'       => $priceUsd,
                'discount_price'  => $discount > 0 ? $priceBdt - $discount : null,
                'quantity'        => $stock,
                'stock'           => $stock,
                'brand'           => $brand,
                'tags'            => ['new', 'featured'],
                'is_featured'     => $isFeatured,
                'is_deal'         => $isDeal,
                'deal_price'      => $dealPrice,
                'deal_ends_at'    => $dealEndsAt,
                'is_active'       => 1,
                'published_at'    => now(),
                'meta_title'      => $name . ' — Buy Online',
                'meta_description' => "Buy {$name} at best price. {$brand} quality.",
            ]);
        }

        $this->command->info('✓ Products seeded (' . Product::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  COUPONS
    // ═══════════════════════════════════════════════════════════

    protected function seedCoupons(): void
    {
        $coupons = [
            ['WELCOME10',  'Welcome 10% Off',    'percent', 10,   0,     500,  100, 1, true],
            ['SAVE500',    'Save ৳500',          'fixed',   500,  3000,  null, 50,  2, true],
            ['FLASH20',    'Flash Sale 20% Off', 'percent', 20,   2000,  1000, 30,  1, true],
            ['VIP50',      'VIP 50% Off',        'percent', 50,   5000,  5000, 10,  1, false],
            ['FREESHIP',   'Free Shipping',      'fixed',   100,  1000,  null, 200, 1, true],
        ];

        foreach ($coupons as [$code, $name, $type, $value, $minOrder, $maxDiscount, $usageLimit, $perUser, $active]) {
            Coupon::firstOrCreate(
                ['code' => $code],
                [
                    'name'           => $name,
                    'description'    => $name,
                    'type'           => $type,
                    'value'          => $value,
                    'min_order'      => $minOrder,
                    'max_discount'   => $maxDiscount,
                    'usage_limit'    => $usageLimit,
                    'used_count'     => 0,
                    'per_user_limit' => $perUser,
                    'starts_at'      => now()->subDays(1),
                    'expires_at'     => now()->addDays(30),
                    'is_active'      => $active,
                ]
            );
        }

        $this->command->info('✓ Coupons seeded (' . Coupon::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  DELIVERY CHARGES
    // ═══════════════════════════════════════════════════════════

    protected function seedDeliveryCharges(): void
    {
        $charges = [
            ['Dhaka',      60,   1000, 1],
            ['Chittagong', 100,  2000, 2],
            ['Sylhet',     120,  2000, 3],
            ['Khulna',     120,  2000, 3],
            ['Rajshahi',   130,  2000, 3],
            ['Barisal',    140,  2500, 4],
            ['Rangpur',    150,  2500, 4],
            ['Mymensingh', 110,  2000, 2],
        ];

        foreach ($charges as [$city, $charge, $freeAbove, $days]) {
            DeliveryCharge::firstOrCreate(
                ['city' => $city],
                [
                    'charge'         => $charge,
                    'free_above'     => $freeAbove,
                    'estimated_days' => $days,
                    'is_active'      => 1,
                ]
            );
        }

        $this->command->info('✓ Delivery charges seeded (' . DeliveryCharge::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  SLIDERS
    // ═══════════════════════════════════════════════════════════

    protected function seedSliders(): void
    {
        $sliders = [
            [
                'title'         => 'Mega Sale — Up to 50% Off',
                'subtitle'      => 'Limited time offer on all electronics',
                'button_text'   => 'Shop Now',
                'link_type'     => 'category',
                'link_value'    => 'electronics',
                'text_position' => 'left',
                'badge_text'    => 'HOT DEAL',
                'badge_color'   => 'pink',
                'sort_order'    => 1,
                'is_active'     => 1,
            ],
            [
                'title'         => 'New Arrivals',
                'subtitle'      => 'Check out the latest products',
                'button_text'   => 'Explore',
                'link_type'     => 'category',
                'link_value'    => 'fashion',
                'text_position' => 'right',
                'badge_text'    => 'NEW',
                'badge_color'   => 'cyan',
                'sort_order'    => 2,
                'is_active'     => 1,
            ],
            [
                'title'         => 'Free Shipping',
                'subtitle'      => 'On orders above ৳1000',
                'button_text'   => 'Start Shopping',
                'link_type'     => 'url',
                'link_value'    => '/products',
                'text_position' => 'center',
                'badge_text'    => 'OFFER',
                'badge_color'   => 'green',
                'sort_order'    => 3,
                'is_active'     => 1,
            ],
        ];

        foreach ($sliders as $s) {
            Slider::create($s);
        }

        $this->command->info('✓ Sliders seeded (' . Slider::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  ANNOUNCEMENTS
    // ═══════════════════════════════════════════════════════════

    protected function seedAnnouncements(): void
    {
        $announcements = [
            [
                'text'           => '🎉 Mega Sale — Up to 50% off on all electronics!',
                'icon'           => '🔥',
                'link'           => '/products?category=electronics',
                'bg_color'       => 'pink',
                'sort_order'     => 1,
                'is_dismissible' => 1,
                'is_active'      => 1,
            ],
            [
                'text'           => '🚚 Free delivery on orders over ৳1000',
                'icon'           => '🚚',
                'bg_color'       => 'cyan',
                'sort_order'     => 2,
                'is_dismissible' => 1,
                'is_active'      => 1,
            ],
        ];

        foreach ($announcements as $a) {
            Announcement::create($a);
        }

        $this->command->info('✓ Announcements seeded (' . Announcement::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  NEWSLETTER
    // ═══════════════════════════════════════════════════════════

    protected function seedNewsletterSubscribers(): void
    {
        $emails = [
            'subscriber1@example.com',
            'subscriber2@example.com',
            'subscriber3@example.com',
            'subscriber4@example.com',
            'subscriber5@example.com',
        ];

        foreach ($emails as $email) {
            NewsletterSubscriber::firstOrCreate(
                ['email' => $email],
                [
                    'token'         => Str::random(64),
                    'subscribed_at' => now(),
                    'is_active'     => 1,
                ]
            );
        }

        $this->command->info('✓ Newsletter subscribers seeded (' . NewsletterSubscriber::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  ORDERS
    // ═══════════════════════════════════════════════════════════

    protected function seedOrders(): void
    {
        $customer = User::where('email', 'customer@mpay.com')->first();
        $products = Product::inRandomOrder()->take(5)->get();

        if (! $customer || $products->isEmpty()) return;

        $statuses = ['pending', 'processing', 'delivered'];

        foreach ($statuses as $i => $status) {
            $product = $products[$i] ?? $products->first();

            $order = Order::create([
                'user_id'                  => $customer->id,
                'order_number'             => 'ORD-' . now()->year . '-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'delivery_code'            => strtoupper(Str::random(6)),
                'delivery_code_expires_at' => now()->addDays(7),
                'delivery_code_attempts'   => 0,
                'source_type'              => 'demo',
                'subtotal'                 => $product->final_price_bdt,
                'discount_amount'          => 0,
                'delivery_charge'          => 60,
                'total_amount'             => $product->final_price_bdt + 60,
                'currency'                 => 'BDT',
                'order_type'               => 'delivery',
                'delivery_method'          => 'self',
                'delivery_name'            => $customer->name,
                'delivery_phone'           => $customer->phone,
                'delivery_address'         => 'House 12, Road 5, Dhanmondi',
                'delivery_city'            => 'Dhaka',
                'status'                   => $status,
                'payment_status'           => $status === 'delivered' ? 'paid' : 'unpaid',
            ]);

            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => 1,
                'unit_price'   => $product->final_price_bdt,
                'total_price'  => $product->final_price_bdt,
            ]);
        }

        $this->command->info('✓ Orders seeded (' . Order::count() . ')');
    }

    // ═══════════════════════════════════════════════════════════
    //  REVIEWS
    // ═══════════════════════════════════════════════════════════

    protected function seedReviews(): void
    {
        $customer = User::where('email', 'customer@mpay.com')->first();
        $products = Product::inRandomOrder()->take(8)->get();

        if (! $customer || $products->isEmpty()) return;

        $reviews = [
            [5, 'Excellent product!', 'Very happy with this purchase. Quality is amazing and delivery was fast.'],
            [4, 'Good value', 'Works as described. Good quality for the price.'],
            [5, 'Highly recommend', 'Superb product, exceeded my expectations!'],
            [4, 'Satisfied', 'Good product overall. Would buy again.'],
            [5, 'Best purchase', 'Absolutely love it. Great quality and fast delivery.'],
            [3, 'Okay', 'Product is decent but delivery was a bit slow.'],
            [5, 'Perfect!', 'Exactly what I wanted. Very satisfied.'],
            [4, 'Nice product', 'Good quality, worth the money.'],
        ];

        foreach ($products as $i => $product) {
            if (! isset($reviews[$i])) break;

            [$rating, $title, $comment] = $reviews[$i];

            Review::firstOrCreate(
                ['user_id' => $customer->id, 'product_id' => $product->id],
                [
                    'rating'      => $rating,
                    'title'       => $title,
                    'comment'     => $comment,
                    'is_verified' => 1,
                    'is_approved' => 1,
                ]
            );

            // Recalculate product rating
            $product->recalculateRating();
        }

        $this->command->info('✓ Reviews seeded (' . Review::count() . ')');
    }
}