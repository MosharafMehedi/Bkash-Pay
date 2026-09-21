<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Starter Plan', 'For beginners', 'Basic access plan with core features.', 499, 4.99, 100, 4.5, 42],
            ['Pro Plan',     'Best value',    'Everything in Starter + priority support.', 1499, 13.99, 50, 4.8, 128],
            ['Business Plan','For teams',     'Unlimited users, analytics & API access.', 4999, 44.99, 25, 4.9, 76],
        ];

        foreach ($products as [$name, $sub, $desc, $bdt, $usd, $stock, $rating, $reviews]) {
            Product::create([
                'name'          => $name,
                'slug'          => Str::slug($name),
                'subtitle'      => $sub,
                'description'   => $desc,
                'price_bdt'     => $bdt,
                'price_usd'     => $usd,
                'quantity'      => $stock,
                'stock'         => $stock,
                'rating'        => $rating,
                'review_count'  => $reviews,
                'category'      => 'Plans',
                'is_active'     => true,
                'is_featured'   => $name === 'Pro Plan',
            ]);
        }
    }
}