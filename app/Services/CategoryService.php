<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    /**
     * Get all categories as tree (parents with children).
     */
    public function getTree(bool $onlyActive = false): Collection
    {
        $query = Category::with(['children' => function ($q) use ($onlyActive) {
            if ($onlyActive) {
                $q->where('is_active', true);
            }
            $q->orderBy('sort_order')->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    /**
     * Get all categories flat, ordered.
     */
    public function getAll(bool $onlyActive = false): Collection
    {
        $query = Category::query()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($onlyActive) {
            $query->active();
        }

        return $query->get();
    }

    /**
     * Get categories with counts for admin list.
     */
    public function getForAdminList(?string $search = null, ?string $status = null): Collection
    {
        $query = Category::with('parent')
            ->withCount(['products', 'children'])
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        return $query->get();
    }

    /**
     * Get parent categories for select dropdown.
     */
    public function getParentOptions(?int $excludeId = null): Collection
    {
        $query = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    /**
     * Get categories for product form dropdown (with parent indication).
     */
    public function getForProductSelect(): Collection
    {
        $parents = Category::with(['children' => function ($q) {
            $q->orderBy('sort_order')->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        $options = collect();

        foreach ($parents as $parent) {
            $options->push($parent);

            foreach ($parent->children as $child) {
                $options->push($child);
            }
        }

        return $options;
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data): Category
    {
        // Validate parent (2-level only)
        if (! empty($data['parent_id'])) {
            $parent = Category::find($data['parent_id']);

            if (! $parent) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Selected parent category does not exist.',
                ]);
            }

            if ($parent->parent_id !== null) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Cannot create sub-subcategory. Maximum 2 levels allowed.',
                ]);
            }
        }

        // Validate unique name per parent
        $this->validateUniqueName($data['name'], $data['parent_id'] ?? null);

        return DB::transaction(function () use ($data) {
            // Handle image
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $data['image']->store('categories', 'public');
            } else {
                unset($data['image']);
            }

            // Generate slug
            $data['slug'] = $this->generateSlug($data['name']);

            return Category::create($data);
        });
    }

    /**
     * Update a category.
     */
    public function updateCategory(Category $category, array $data): Category
    {
        // Validate parent (2-level only)
        if (! empty($data['parent_id'])) {
            if ($data['parent_id'] == $category->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Category cannot be its own parent.',
                ]);
            }

            $parent = Category::find($data['parent_id']);

            if (! $parent) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Selected parent category does not exist.',
                ]);
            }

            if ($parent->parent_id !== null) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Cannot assign to a subcategory. Only parent categories allowed.',
                ]);
            }

            // Cannot assign parent if this category has children
            if ($category->hasChildren()) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Cannot make this a subcategory while it has its own children.',
                ]);
            }
        }

        // Validate unique name per parent
        $this->validateUniqueName($data['name'], $data['parent_id'] ?? null, $category->id);

        return DB::transaction(function () use ($category, $data) {
            // Handle image
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old image
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                $data['image'] = $data['image']->store('categories', 'public');
            } else {
                unset($data['image']);
            }

            // If name changed, regenerate slug
            if (isset($data['name']) && $data['name'] !== $category->name) {
                $data['slug'] = $this->generateSlug($data['name'], $category->id);
            }

            $category->update($data);

            return $category->fresh();
        });
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(Category $category): void
    {
        DB::transaction(function () use ($category) {
            // Delete image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // Set products to null (uncategorized)
            Product::where('category_id', $category->id)
                ->update(['category_id' => null]);

            // Children categories — set to null products + delete
            foreach ($category->children as $child) {
                if ($child->image && Storage::disk('public')->exists($child->image)) {
                    Storage::disk('public')->delete($child->image);
                }

                Product::where('category_id', $child->id)
                    ->update(['category_id' => null]);

                $child->delete();
            }

            // Delete category
            $category->delete();
        });
    }

    /**
     * Remove category image.
     */
    public function removeImage(Category $category): void
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->update(['image' => null]);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(Category $category): Category
    {
        $category->update(['is_active' => ! $category->is_active]);

        return $category->fresh();
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Category $category): Category
    {
        $category->update(['is_featured' => ! $category->is_featured]);

        return $category->fresh();
    }

    /**
     * Reorder categories (bulk update sort_order).
     */
    public function reorder(array $orders): void
    {
        DB::transaction(function () use ($orders) {
            foreach ($orders as $id => $sortOrder) {
                Category::where('id', $id)->update(['sort_order' => (int) $sortOrder]);
            }
        });
    }

    /**
     * Get stats for admin dashboard.
     */
    public function getStats(): array
    {
        return [
            'total'         => Category::count(),
            'active'        => Category::where('is_active', true)->count(),
            'inactive'      => Category::where('is_active', false)->count(),
            'parents'       => Category::whereNull('parent_id')->count(),
            'children'      => Category::whereNotNull('parent_id')->count(),
            'featured'      => Category::where('is_featured', true)->count(),
            'with_products' => Category::whereHas('products')->count(),
        ];
    }

    /**
     * Generate unique slug.
     */
    protected function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 1;

        while (
            Category::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Validate unique name per parent.
     */
    protected function validateUniqueName(string $name, ?int $parentId, ?int $ignoreId = null): void
    {
        $query = Category::where('name', $name)
            ->where(function ($q) use ($parentId) {
                if ($parentId) {
                    $q->where('parent_id', $parentId);
                } else {
                    $q->whereNull('parent_id');
                }
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A category with this name already exists under the same parent.',
            ]);
        }
    }
}