<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if products already exist (except ones from previous test)
        if (Product::count() >= 20) {
            $this->command->info('✓ Products already exist. Skipping.');
            return;
        }

        $products = [
            // ═══ ELECTRONICS — Mobile Phones ═══
            ['name' => 'iPhone 15 Pro Max 256GB', 'cat' => 'Mobile Phones', 'subtitle' => 'Titanium. Strong. Light. Pro.', 'desc' => 'A17 Pro chip, 48MP camera system, USB-C, titanium design. The most powerful iPhone yet with stunning camera capabilities.', 'price_bdt' => 165000, 'price_usd' => 1499, 'discount' => 155000, 'stock' => 12, 'brand' => 'Apple', 'rating' => 4.9, 'reviews' => 128, 'featured' => true],
            ['name' => 'Samsung Galaxy S24 Ultra', 'cat' => 'Mobile Phones', 'subtitle' => 'Galaxy AI is here', 'desc' => 'Snapdragon 8 Gen 3, 200MP camera, S-Pen, 5000mAh battery with 45W fast charging.', 'price_bdt' => 145000, 'price_usd' => 1299, 'discount' => 135000, 'stock' => 18, 'brand' => 'Samsung', 'rating' => 4.8, 'reviews' => 95, 'featured' => true],
            ['name' => 'Google Pixel 8 Pro', 'cat' => 'Mobile Phones', 'subtitle' => 'The best of Google AI', 'desc' => 'Tensor G3 chip, best-in-class camera, 7 years of OS updates, clean Android experience.', 'price_bdt' => 110000, 'price_usd' => 999, 'discount' => 99000, 'stock' => 8, 'brand' => 'Google', 'rating' => 4.7, 'reviews' => 67, 'featured' => false],
            ['name' => 'Xiaomi 14 Ultra', 'cat' => 'Mobile Phones', 'subtitle' => 'Leica camera system', 'desc' => 'Professional-grade Leica optics, Snapdragon 8 Gen 3, 90W HyperCharge.', 'price_bdt' => 125000, 'price_usd' => 1199, 'discount' => null, 'stock' => 15, 'brand' => 'Xiaomi', 'rating' => 4.6, 'reviews' => 42, 'featured' => false],

            // ═══ ELECTRONICS — Laptops ═══
            ['name' => 'MacBook Pro 14" M3 Pro', 'cat' => 'Laptops & Computers', 'subtitle' => 'Supercharged by M3 Pro', 'desc' => 'M3 Pro chip, 18GB unified memory, 512GB SSD, Liquid Retina XDR display, up to 18 hours battery.', 'price_bdt' => 235000, 'price_usd' => 2199, 'discount' => 225000, 'stock' => 6, 'brand' => 'Apple', 'rating' => 4.9, 'reviews' => 89, 'featured' => true],
            ['name' => 'Dell XPS 15 OLED', 'cat' => 'Laptops & Computers', 'subtitle' => 'Power meets elegance', 'desc' => 'Intel Core i9, RTX 4070, 32GB RAM, 1TB SSD, 15.6" OLED touch display.', 'price_bdt' => 275000, 'price_usd' => 2499, 'discount' => null, 'stock' => 4, 'brand' => 'Dell', 'rating' => 4.7, 'reviews' => 55, 'featured' => false],
            ['name' => 'ASUS ROG Strix G16', 'cat' => 'Laptops & Computers', 'subtitle' => 'Gaming beast', 'desc' => 'Intel i7-13650HX, RTX 4060, 16GB DDR5, 1TB NVMe SSD, 165Hz refresh rate.', 'price_bdt' => 185000, 'price_usd' => 1699, 'discount' => 175000, 'stock' => 10, 'brand' => 'ASUS', 'rating' => 4.6, 'reviews' => 38, 'featured' => false],

            // ═══ ELECTRONICS — Headphones ═══
            ['name' => 'Sony WH-1000XM5', 'cat' => 'Headphones & Audio', 'subtitle' => 'Industry-leading noise cancellation', 'desc' => 'Auto NC Optimizer, 30-hour battery, Hi-Res Audio, multipoint connection, comfortable fit.', 'price_bdt' => 42000, 'price_usd' => 379, 'discount' => 38500, 'stock' => 25, 'brand' => 'Sony', 'rating' => 4.9, 'reviews' => 156, 'featured' => true],
            ['name' => 'Apple AirPods Pro 2nd Gen', 'cat' => 'Headphones & Audio', 'subtitle' => 'Adaptive Audio', 'desc' => 'H2 chip, 2x more Active Noise Cancellation, Adaptive Transparency, USB-C, MagSafe.', 'price_bdt' => 28000, 'price_usd' => 249, 'discount' => 25500, 'stock' => 35, 'brand' => 'Apple', 'rating' => 4.8, 'reviews' => 203, 'featured' => true],
            ['name' => 'Bose QuietComfort Ultra', 'cat' => 'Headphones & Audio', 'subtitle' => 'Immersive audio', 'desc' => 'Bose Immersive Audio, world-class noise cancellation, CustomTune technology.', 'price_bdt' => 48000, 'price_usd' => 429, 'discount' => null, 'stock' => 12, 'brand' => 'Bose', 'rating' => 4.7, 'reviews' => 78, 'featured' => false],

            // ═══ ELECTRONICS — Cameras ═══
            ['name' => 'Sony Alpha A7 IV', 'cat' => 'Cameras & Photography', 'subtitle' => 'Full-frame hybrid camera', 'desc' => '33MP full-frame sensor, 4K 60p video, real-time Eye AF, 10 fps burst shooting.', 'price_bdt' => 285000, 'price_usd' => 2599, 'discount' => 275000, 'stock' => 5, 'brand' => 'Sony', 'rating' => 4.9, 'reviews' => 45, 'featured' => true],
            ['name' => 'Canon EOS R6 Mark II', 'cat' => 'Cameras & Photography', 'subtitle' => 'Speed meets precision', 'desc' => '24.2MP full-frame, 40fps electronic shutter, 6K oversampled 4K video, IBIS.', 'price_bdt' => 265000, 'price_usd' => 2399, 'discount' => null, 'stock' => 7, 'brand' => 'Canon', 'rating' => 4.8, 'reviews' => 32, 'featured' => false],

            // ═══ ELECTRONICS — Smart Watches ═══
            ['name' => 'Apple Watch Ultra 2', 'cat' => 'Smart Watches', 'subtitle' => 'Adventure awaits', 'desc' => '49mm titanium case, S9 chip, brightest display ever, precision GPS, 36hr battery.', 'price_bdt' => 85000, 'price_usd' => 799, 'discount' => 79900, 'stock' => 14, 'brand' => 'Apple', 'rating' => 4.9, 'reviews' => 112, 'featured' => true],

            // ═══ FASHION — Men's ═══
            ['name' => 'Premium Cotton Formal Shirt', 'cat' => "Men's Clothing", 'subtitle' => 'Egyptian cotton', 'desc' => '100% Egyptian cotton, slim fit, wrinkle-resistant, available in multiple colors.', 'price_bdt' => 2800, 'price_usd' => 24, 'discount' => 2200, 'stock' => 45, 'brand' => 'Zara', 'rating' => 4.5, 'reviews' => 88, 'featured' => false],
            ['name' => 'Leather Biker Jacket', 'cat' => "Men's Clothing", 'subtitle' => 'Genuine leather', 'desc' => 'Premium leather jacket with YKK zippers, quilted lining, classic biker style.', 'price_bdt' => 12500, 'price_usd' => 109, 'discount' => 10900, 'stock' => 18, 'brand' => 'H&M', 'rating' => 4.7, 'reviews' => 56, 'featured' => true],

            // ═══ FASHION — Women's ═══
            ['name' => 'Elegant Evening Dress', 'cat' => "Women's Clothing", 'subtitle' => 'Silk blend', 'desc' => 'Flowing silk-blend dress, elegant evening wear, available in black and navy.', 'price_bdt' => 8500, 'price_usd' => 75, 'discount' => 7500, 'stock' => 22, 'brand' => 'Zara', 'rating' => 4.6, 'reviews' => 74, 'featured' => true],
            ['name' => 'Designer Handbag', 'cat' => 'Bags & Accessories', 'subtitle' => 'Genuine leather', 'desc' => 'Handcrafted leather handbag with multiple compartments, gold-tone hardware.', 'price_bdt' => 15000, 'price_usd' => 129, 'discount' => 13500, 'stock' => 12, 'brand' => 'Michael Kors', 'rating' => 4.8, 'reviews' => 92, 'featured' => true],

            // ═══ FASHION — Shoes ═══
            ['name' => 'Nike Air Max 270', 'cat' => 'Shoes & Sneakers', 'subtitle' => 'Iconic comfort', 'desc' => 'Max Air unit for all-day comfort, breathable mesh upper, iconic Nike style.', 'price_bdt' => 12500, 'price_usd' => 109, 'discount' => 11200, 'stock' => 35, 'brand' => 'Nike', 'rating' => 4.7, 'reviews' => 145, 'featured' => true],
            ['name' => 'Adidas Ultraboost 22', 'cat' => 'Shoes & Sneakers', 'subtitle' => 'Energy return', 'desc' => 'BOOST midsole, Primeknit+ upper, Linear Energy Push system, Continental rubber outsole.', 'price_bdt' => 14500, 'price_usd' => 129, 'discount' => null, 'stock' => 28, 'brand' => 'Adidas', 'rating' => 4.8, 'reviews' => 118, 'featured' => false],

            // ═══ HOME & LIVING ═══
            ['name' => 'Modern Leather Sofa 3-Seater', 'cat' => 'Furniture', 'subtitle' => 'Premium Italian leather', 'desc' => 'Italian leather, hardwood frame, memory foam cushions, 3-seater modern design.', 'price_bdt' => 85000, 'price_usd' => 749, 'discount' => 79900, 'stock' => 3, 'brand' => 'IKEA', 'rating' => 4.7, 'reviews' => 34, 'featured' => true],
            ['name' => 'Smart LED Ceiling Light', 'cat' => 'Home Decor', 'subtitle' => 'RGB + WiFi control', 'desc' => '16M colors, WiFi app control, voice assistant compatible, dimmable.', 'price_bdt' => 4500, 'price_usd' => 39, 'discount' => 3900, 'stock' => 55, 'brand' => 'Philips', 'rating' => 4.6, 'reviews' => 67, 'featured' => false],

            // ═══ BEAUTY & HEALTH ═══
            ['name' => 'Premium Skincare Set', 'cat' => 'Skincare', 'subtitle' => 'Complete routine', 'desc' => 'Complete 5-piece skincare routine with cleanser, toner, serum, moisturizer, SPF.', 'price_bdt' => 8500, 'price_usd' => 75, 'discount' => 7200, 'stock' => 40, 'brand' => 'The Ordinary', 'rating' => 4.8, 'reviews' => 156, 'featured' => true],
            ['name' => 'Luxury Perfume 100ml', 'cat' => 'Fragrances', 'subtitle' => 'Long-lasting scent', 'desc' => 'Premium eau de parfum, 100ml bottle, notes of jasmine, vanilla, and amber.', 'price_bdt' => 12500, 'price_usd' => 109, 'discount' => null, 'stock' => 25, 'brand' => 'Dior', 'rating' => 4.9, 'reviews' => 89, 'featured' => true],

            // ═══ SPORTS ═══
            ['name' => 'Professional Cricket Bat', 'cat' => 'Cricket', 'subtitle' => 'English willow', 'desc' => 'Grade 1 English willow bat, handmade in England, professional grade.', 'price_bdt' => 35000, 'price_usd' => 309, 'discount' => 31500, 'stock' => 15, 'brand' => 'Gray-Nicolls', 'rating' => 4.8, 'reviews' => 42, 'featured' => false],
            ['name' => 'Adjustable Dumbbell Set', 'cat' => 'Fitness Equipment', 'subtitle' => '5-52.5 lbs', 'desc' => 'Adjustable dumbbells, replaces 15 sets of weights, compact storage.', 'price_bdt' => 55000, 'price_usd' => 489, 'discount' => 49900, 'stock' => 8, 'brand' => 'Bowflex', 'rating' => 4.7, 'reviews' => 56, 'featured' => true],

            // ═══ BOOKS ═══
            ['name' => 'Atomic Habits — Hardcover', 'cat' => 'Non-Fiction', 'subtitle' => 'By James Clear', 'desc' => 'The #1 bestseller on building good habits and breaking bad ones.', 'price_bdt' => 1200, 'price_usd' => 18, 'discount' => 950, 'stock' => 100, 'brand' => 'Penguin', 'rating' => 4.9, 'reviews' => 234, 'featured' => true],
            ['name' => 'The Psychology of Money', 'cat' => 'Non-Fiction', 'subtitle' => 'By Morgan Housel', 'desc' => 'Timeless lessons on wealth, greed, and happiness.', 'price_bdt' => 1100, 'price_usd' => 16, 'discount' => 890, 'stock' => 85, 'brand' => 'Harper', 'rating' => 4.8, 'reviews' => 187, 'featured' => false],

            // ═══ TOYS ═══
            ['name' => 'PlayStation 5 DualSense', 'cat' => 'Video Games', 'subtitle' => 'Haptic feedback controller', 'desc' => 'Wireless controller with haptic feedback, adaptive triggers, built-in mic.', 'price_bdt' => 8500, 'price_usd' => 75, 'discount' => 7500, 'stock' => 42, 'brand' => 'Sony', 'rating' => 4.8, 'reviews' => 234, 'featured' => true],
            ['name' => 'LEGO Technic Ferrari', 'cat' => 'Kids Toys', 'subtitle' => '3778 pieces', 'desc' => 'Detailed Ferrari 488 GTE model with realistic features.', 'price_bdt' => 18000, 'price_usd' => 159, 'discount' => null, 'stock' => 6, 'brand' => 'LEGO', 'rating' => 4.9, 'reviews' => 67, 'featured' => true],

            // ═══ GROCERIES ═══
            ['name' => 'Premium Coffee Beans 1kg', 'cat' => 'Beverages', 'subtitle' => 'Arabica — Medium roast', 'desc' => 'Freshly roasted Arabica beans, medium roast, ethically sourced.', 'price_bdt' => 1800, 'price_usd' => 16, 'discount' => 1450, 'stock' => 120, 'brand' => 'Starbucks', 'rating' => 4.7, 'reviews' => 98, 'featured' => false],
            ['name' => 'Organic Green Tea 100 bags', 'cat' => 'Beverages', 'subtitle' => 'Antioxidant rich', 'desc' => '100% organic green tea bags, natural antioxidants, refreshing taste.', 'price_bdt' => 850, 'price_usd' => 8, 'discount' => 650, 'stock' => 200, 'brand' => 'Twinings', 'rating' => 4.6, 'reviews' => 67, 'featured' => false],
        ];

        $deals = [];
        $counter = 0;

        foreach ($products as $data) {
            // Find category
            $category = Category::where('name', $data['cat'])->first();

            if (! $category) {
                continue;
            }

            $counter++;

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'             => $data['name'],
                    'subtitle'         => $data['subtitle'] ?? null,
                    'description'      => $data['desc'],
                    'price_bdt'        => $data['price_bdt'],
                    'price_usd'        => $data['price_usd'],
                    'discount_price'   => $data['discount'] ?? null,
                    'quantity'         => $data['stock'] + 20,
                    'stock'            => $data['stock'],
                    'sku'              => 'SKU-' . str_pad($counter, 5, '0', STR_PAD_LEFT),
                    'category_id'      => $category->id,
                    'brand'            => $data['brand'],
                    'tags'             => ['new', 'hot', $data['brand']],
                    'rating'           => $data['rating'],
                    'review_count'     => $data['reviews'],
                    'is_active'        => true,
                    'is_featured'      => $data['featured'],
                    'published_at'     => now(),
                    'meta_title'       => $data['name'] . ' — Buy Online',
                    'meta_description' => Str::limit($data['desc'], 150),
                ]
            );

            // Add some products as deals
            if ($counter <= 6 || $counter % 5 === 0) {
                $product->update([
                    'is_deal' => true,
                    'deal_price' => $data['discount'] ?? round($data['price_bdt'] * 0.85),
                    'deal_ends_at' => now()->addDays(rand(1, 5))->addHours(rand(1, 23)),
                ]);
            }
        }

        $this->command->info("✓ Demo products seeded: {$counter}");
    }
}