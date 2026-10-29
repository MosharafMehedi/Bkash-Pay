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

    public function createCategory(array $data): Category
    {
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

        $this->validateUniqueName($data['name'], $data['parent_id'] ?? null);

        return DB::transaction(function () use ($data) {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $data['image']->store('categories', 'public');
            } else {
                unset($data['image']);
            }

            $data['slug'] = $this->generateSlug($data['name']);

            return Category::create($data);
        });
    }

    public function updateCategory(Category $category, array $data): Category
    {
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

            if ($category->hasChildren()) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Cannot make this a subcategory while it has its own children.',
                ]);
            }
        }

        $this->validateUniqueName($data['name'], $data['parent_id'] ?? null, $category->id);

        return DB::transaction(function () use ($category, $data) {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
                $data['image'] = $data['image']->store('categories', 'public');
            } else {
                unset($data['image']);
            }

            if (isset($data['name']) && $data['name'] !== $category->name) {
                $data['slug'] = $this->generateSlug($data['name'], $category->id);
            }

            $category->update($data);
            return $category->fresh();
        });
    }

    public function deleteCategory(Category $category): void
    {
        DB::transaction(function () use ($category) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            Product::where('category_id', $category->id)->update(['category_id' => null]);

            foreach ($category->children as $child) {
                if ($child->image && Storage::disk('public')->exists($child->image)) {
                    Storage::disk('public')->delete($child->image);
                }
                Product::where('category_id', $child->id)->update(['category_id' => null]);
                $child->delete();
            }

            $category->delete();
        });
    }

    public function removeImage(Category $category): void
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        $category->update(['image' => null]);
    }

    public function toggleActive(Category $category): Category
    {
        $category->update(['is_active' => ! $category->is_active]);
        return $category->fresh();
    }

    public function toggleFeatured(Category $category): Category
    {
        $category->update(['is_featured' => ! $category->is_featured]);
        return $category->fresh();
    }

    public function getStats(): array
    {
        return [
            'total'    => Category::count(),
            'active'   => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'parents'  => Category::whereNull('parent_id')->count(),
            'children' => Category::whereNotNull('parent_id')->count(),
            'featured' => Category::where('is_featured', true)->count(),
        ];
    }

    protected function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 1;

        while (
            Category::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

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