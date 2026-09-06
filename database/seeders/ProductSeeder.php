<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'        => 'Full-Stack Laravel Handbook',
                'slug'        => 'full-stack-laravel-handbook',
                'subtitle'    => 'PDF + EPUB',
                'description' => 'A practical field guide to shipping real Laravel applications — auth, jobs, and payments.',
                'price_bdt'   => 990,
                'price_usd'   => 9,
            ],
            [
                'name'        => 'API Design Patterns',
                'slug'        => 'api-design-patterns',
                'subtitle'    => 'PDF',
                'description' => 'Patterns for building REST and JSON APIs that age well.',
                'price_bdt'   => 750,
                'price_usd'   => 7,
            ],
            [
                'name'        => 'Queues & Jobs Deep Dive',
                'slug'        => 'queues-and-jobs-deep-dive',
                'subtitle'    => 'PDF + Code samples',
                'description' => 'Background processing, retries, and monitoring for Laravel apps.',
                'price_bdt'   => 650,
                'price_usd'   => 6,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
