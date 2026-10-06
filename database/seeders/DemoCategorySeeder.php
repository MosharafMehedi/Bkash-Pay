<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics', 'icon' => '📱', 'featured' => true, 'sort' => 1,
                'children' => [
                    ['name' => 'Mobile Phones', 'icon' => '📱', 'sort' => 1],
                    ['name' => 'Laptops & Computers', 'icon' => '💻', 'sort' => 2],
                    ['name' => 'Headphones & Audio', 'icon' => '🎧', 'sort' => 3],
                    ['name' => 'Cameras & Photography', 'icon' => '📷', 'sort' => 4],
                    ['name' => 'Smart Watches', 'icon' => '⌚', 'sort' => 5],
                ],
            ],
            [
                'name' => 'Fashion', 'icon' => '👕', 'featured' => true, 'sort' => 2,
                'children' => [
                    ['name' => "Men's Clothing", 'icon' => '👔', 'sort' => 1],
                    ['name' => "Women's Clothing", 'icon' => '👗', 'sort' => 2],
                    ['name' => 'Shoes & Sneakers', 'icon' => '👟', 'sort' => 3],
                    ['name' => 'Bags & Accessories', 'icon' => '👜', 'sort' => 4],
                ],
            ],
            [
                'name' => 'Home & Living', 'icon' => '🏠', 'featured' => true, 'sort' => 3,
                'children' => [
                    ['name' => 'Furniture', 'icon' => '🪑', 'sort' => 1],
                    ['name' => 'Kitchen & Dining', 'icon' => '🍳', 'sort' => 2],
                    ['name' => 'Home Decor', 'icon' => '🖼️', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Beauty & Health', 'icon' => '💄', 'featured' => true, 'sort' => 4,
                'children' => [
                    ['name' => 'Skincare', 'icon' => '🧴', 'sort' => 1],
                    ['name' => 'Makeup', 'icon' => '💄', 'sort' => 2],
                    ['name' => 'Fragrances', 'icon' => '🌸', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Sports & Outdoors', 'icon' => '⚽', 'featured' => true, 'sort' => 5,
                'children' => [
                    ['name' => 'Cricket', 'icon' => '🏏', 'sort' => 1],
                    ['name' => 'Football', 'icon' => '⚽', 'sort' => 2],
                    ['name' => 'Fitness Equipment', 'icon' => '🏋️', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Books & Stationery', 'icon' => '📚', 'featured' => true, 'sort' => 6,
                'children' => [
                    ['name' => 'Fiction', 'icon' => '📖', 'sort' => 1],
                    ['name' => 'Non-Fiction', 'icon' => '📘', 'sort' => 2],
                    ['name' => 'Notebooks', 'icon' => '📓', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Toys & Games', 'icon' => '🎮', 'featured' => true, 'sort' => 7,
                'children' => [
                    ['name' => 'Video Games', 'icon' => '🎮', 'sort' => 1],
                    ['name' => 'Board Games', 'icon' => '🎲', 'sort' => 2],
                    ['name' => 'Kids Toys', 'icon' => '🧸', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Groceries', 'icon' => '🛒', 'featured' => false, 'sort' => 8,
                'children' => [
                    ['name' => 'Snacks', 'icon' => '🍿', 'sort' => 1],
                    ['name' => 'Beverages', 'icon' => '🥤', 'sort' => 2],
                    ['name' => 'Organic Foods', 'icon' => '🥗', 'sort' => 3],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            $featured = $catData['featured'] ?? false;
            $sort     = $catData['sort'] ?? 0;
            unset($catData['children'], $catData['featured'], $catData['sort']);

            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name'        => $catData['name'],
                    'icon'        => $catData['icon'] ?? null,
                    'description' => 'Explore our wide range of ' . $catData['name'],
                    'is_active'   => true,
                    'is_featured' => $featured,
                    'sort_order'  => $sort,
                ]
            );

            foreach ($children as $child) {
                $childSort = $child['sort'] ?? 0;
                unset($child['sort']);

                Category::firstOrCreate(
                    ['slug' => Str::slug($parent->name . '-' . $child['name'])],
                    [
                        'parent_id'   => $parent->id,
                        'name'        => $child['name'],
                        'icon'        => $child['icon'] ?? null,
                        'description' => 'Shop ' . $child['name'] . ' at best prices',
                        'is_active'   => true,
                        'sort_order'  => $childSort,
                    ]
                );
            }
        }

        $this->command->info('✓ Demo categories seeded.');
    }
}