<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'         => 'Electronics',
                'description'  => 'Electronic devices and gadgets',
                'is_featured'  => true,
                'icon'         => '📱',
                'sort_order'   => 1,
                'children'     => [
                    ['name' => 'Mobile Phones',   'icon' => '📱', 'sort_order' => 1],
                    ['name' => 'Laptops',          'icon' => '💻', 'sort_order' => 2],
                    ['name' => 'Headphones',       'icon' => '🎧', 'sort_order' => 3],
                    ['name' => 'Cameras',          'icon' => '📷', 'sort_order' => 4],
                ],
            ],
            [
                'name'         => 'Fashion',
                'description'  => 'Clothing and accessories',
                'is_featured'  => true,
                'icon'         => '👕',
                'sort_order'   => 2,
                'children'     => [
                    ['name' => "Men's Clothing",   'icon' => '👔', 'sort_order' => 1],
                    ['name' => "Women's Clothing", 'icon' => '👗', 'sort_order' => 2],
                    ['name' => 'Shoes',            'icon' => '👟', 'sort_order' => 3],
                    ['name' => 'Accessories',      'icon' => '👜', 'sort_order' => 4],
                ],
            ],
            [
                'name'         => 'Home & Living',
                'description'  => 'Home appliances and decor',
                'icon'         => '🏠',
                'sort_order'   => 3,
                'children'     => [
                    ['name' => 'Furniture',    'icon' => '🪑', 'sort_order' => 1],
                    ['name' => 'Kitchen',      'icon' => '🍳', 'sort_order' => 2],
                    ['name' => 'Bedding',      'icon' => '🛏️', 'sort_order' => 3],
                ],
            ],
            [
                'name'         => 'Books',
                'description'  => 'Books and stationery',
                'icon'         => '📚',
                'sort_order'   => 4,
                'children'     => [],
            ],
            [
                'name'         => 'Sports',
                'description'  => 'Sports and outdoor',
                'icon'         => '⚽',
                'sort_order'   => 5,
                'children'     => [
                    ['name' => 'Cricket',  'icon' => '🏏', 'sort_order' => 1],
                    ['name' => 'Football', 'icon' => '⚽', 'sort_order' => 2],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                array_merge($catData, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );

            foreach ($children as $childData) {
                $childSlug = Str::slug($parent->name . '-' . $childData['name']);

                Category::firstOrCreate(
                    ['slug' => $childSlug],
                    array_merge($childData, [
                        'parent_id'  => $parent->id,
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        }

        $this->command->info('Categories seeded successfully.');
    }
}