<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Get distinct category strings
        $existingCategories = DB::table('products')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->toArray();

        foreach ($existingCategories as $categoryName) {
            $slug = Str::slug($categoryName);

            // Skip if slug exists
            if (DB::table('categories')->where('slug', $slug)->exists()) {
                $categoryId = DB::table('categories')->where('slug', $slug)->value('id');
            } else {
                $categoryId = DB::table('categories')->insertGetId([
                    'name'       => $categoryName,
                    'slug'       => $slug,
                    'is_active'  => true,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update products
            DB::table('products')
                ->where('category', $categoryName)
                ->update(['category_id' => $categoryId]);
        }
    }

    public function down(): void
    {
        // Reset category_id
        DB::table('products')->update(['category_id' => null]);
    }
};