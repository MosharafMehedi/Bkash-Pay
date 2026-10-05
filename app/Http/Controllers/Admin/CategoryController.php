<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categories)
    {
    }

    /**
     * List all categories.
     */
    public function index(Request $request)
    {
        $categories = $this->categories->getForAdminList(
            search: $request->query('q'),
            status: $request->query('status')
        );

        $stats = $this->categories->getStats();

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $parents = $this->categories->getParentOptions();

        return view('admin.categories.create', compact('parents'));
    }

    /**
     * Store new category.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'parent_id'        => 'nullable|exists:categories,id',
            'description'      => 'nullable|string|max:1000',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'icon'             => 'nullable|string|max:10',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
            'sort_order'       => 'nullable|integer|min:0|max:9999',
            'meta_title'       => 'nullable|string|max:150',
            'meta_description' => 'nullable|string|max:300',
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', false);
        $data['sort_order']  = (int) ($data['sort_order'] ?? 0);

        try {
            $this->categories->createCategory($data);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Category create failed: ' . $e->getMessage());
            return back()->with('error', 'Could not create category.')->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category)
    {
        $parents = $this->categories->getParentOptions($category->id);

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    /**
     * Update category.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'parent_id'        => 'nullable|exists:categories,id',
            'description'      => 'nullable|string|max:1000',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'icon'             => 'nullable|string|max:10',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
            'sort_order'       => 'nullable|integer|min:0|max:9999',
            'meta_title'       => 'nullable|string|max:150',
            'meta_description' => 'nullable|string|max:300',
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', false);
        $data['sort_order']  = (int) ($data['sort_order'] ?? 0);

        try {
            $this->categories->updateCategory($category, $data);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update category.')->withInput();
        }
    }

    /**
     * Delete category.
     */
    public function destroy(Category $category)
    {
        try {
            $this->categories->deleteCategory($category);

            return back()->with('success', 'Category deleted. Products moved to uncategorized.');
        } catch (\Throwable $e) {
            Log::error('Category delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete category.');
        }
    }

    /**
     * Toggle active status.
     */
    public function toggle(Category $category)
    {
        try {
            $this->categories->toggleActive($category);

            return back()->with('success', 'Status updated.');
        } catch (\Throwable $e) {
            Log::error('Category toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update status.');
        }
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Category $category)
    {
        try {
            $this->categories->toggleFeatured($category);

            return back()->with('success', 'Featured status updated.');
        } catch (\Throwable $e) {
            Log::error('Category featured toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update featured status.');
        }
    }

    /**
     * Remove category image.
     */
    public function removeImage(Category $category)
    {
        try {
            $this->categories->removeImage($category);

            return back()->with('success', 'Image removed.');
        } catch (\Throwable $e) {
            Log::error('Category image remove failed: ' . $e->getMessage());
            return back()->with('error', 'Could not remove image.');
        }
    }
}