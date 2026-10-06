<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * List all sliders.
     */
    public function index(Request $request)
    {
        $query = Slider::query();

        if ($q = $request->query('q')) {
            $query->where('title', 'like', "%{$q}%");
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $sliders = $query->ordered()->paginate(15)->withQueryString();

        $stats = [
            'total'    => Slider::count(),
            'active'   => Slider::where('is_active', true)->count(),
            'inactive' => Slider::where('is_active', false)->count(),
            'scheduled'=> Slider::whereNotNull('starts_at')->orWhereNotNull('ends_at')->count(),
        ];

        return view('admin.sliders.index', compact('sliders', 'stats'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $products   = Product::active()->orderBy('name')->get();
        $categories = Category::active()->whereNull('parent_id')->ordered()->get();

        return view('admin.sliders.create', compact('products', 'categories'));
    }

    /**
     * Store new slider.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        try {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('sliders', 'public');
            }

            if ($request->hasFile('bg_image')) {
                $data['bg_image'] = $request->file('bg_image')->store('sliders', 'public');
            }

            Slider::create($data);

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider created successfully.');
        } catch (\Throwable $e) {
            Log::error('Slider create failed: ' . $e->getMessage());
            return back()->with('error', 'Could not create slider.')->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit(Slider $slider)
    {
        $products   = Product::active()->orderBy('name')->get();
        $categories = Category::active()->whereNull('parent_id')->ordered()->get();

        return view('admin.sliders.edit', compact('slider', 'products', 'categories'));
    }

    /**
     * Update slider.
     */
    public function update(Request $request, Slider $slider)
    {
        $data = $this->validated($request);

        try {
            if ($request->hasFile('image')) {
                if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                    Storage::disk('public')->delete($slider->image);
                }
                $data['image'] = $request->file('image')->store('sliders', 'public');
            }

            if ($request->hasFile('bg_image')) {
                if ($slider->bg_image && Storage::disk('public')->exists($slider->bg_image)) {
                    Storage::disk('public')->delete($slider->bg_image);
                }
                $data['bg_image'] = $request->file('bg_image')->store('sliders', 'public');
            }

            $slider->update($data);

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Slider update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update slider.')->withInput();
        }
    }

    /**
     * Delete slider.
     */
    public function destroy(Slider $slider)
    {
        try {
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }
            if ($slider->bg_image && Storage::disk('public')->exists($slider->bg_image)) {
                Storage::disk('public')->delete($slider->bg_image);
            }

            $slider->delete();

            return back()->with('success', 'Slider deleted.');
        } catch (\Throwable $e) {
            Log::error('Slider delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete slider.');
        }
    }

    /**
     * Toggle active status.
     */
    public function toggle(Slider $slider)
    {
        $slider->update(['is_active' => ! $slider->is_active]);

        return back()->with('success', 'Status updated.');
    }

    /**
     * Remove image.
     */
    public function removeImage(Request $request, Slider $slider)
    {
        $field = $request->query('field', 'image');

        if (! in_array($field, ['image', 'bg_image'])) {
            return back()->with('error', 'Invalid image field.');
        }

        if ($slider->$field && Storage::disk('public')->exists($slider->$field)) {
            Storage::disk('public')->delete($slider->$field);
        }

        $slider->update([$field => null]);

        return back()->with('success', 'Image removed.');
    }

    /**
     * Common validation.
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'         => 'required|string|max:150',
            'subtitle'      => 'nullable|string|max:255',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'bg_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'button_text'   => 'nullable|string|max:50',
            'link_type'     => 'required|in:product,category,custom,url',
            'link_value'    => 'nullable|string|max:500',
            'text_position' => 'required|in:left,right,center',
            'badge_text'    => 'nullable|string|max:50',
            'badge_color'   => 'nullable|string|max:20',
            'sort_order'    => 'nullable|integer|min:0|max:9999',
            'is_active'     => 'boolean',
            'starts_at'     => 'nullable|date',
            'ends_at'       => 'nullable|date|after:starts_at',
        ]);

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        // Conditional link_value validation
        if (in_array($data['link_type'], ['product', 'category', 'custom', 'url']) && empty($data['link_value'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'link_value' => 'Link value is required when link type is set.',
            ]);
        }

        return $data;
    }
}