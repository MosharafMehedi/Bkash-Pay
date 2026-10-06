<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    /**
     * List all announcements.
     */
    public function index(Request $request)
    {
        $query = Announcement::query();

        if ($q = $request->query('q')) {
            $query->where('text', 'like', "%{$q}%");
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $announcements = $query->ordered()->paginate(20)->withQueryString();

        $stats = [
            'total'      => Announcement::count(),
            'active'     => Announcement::where('is_active', true)->count(),
            'inactive'   => Announcement::where('is_active', false)->count(),
            'scheduled'  => Announcement::whereNotNull('starts_at')->orWhereNotNull('ends_at')->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'stats'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store new announcement.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        try {
            Announcement::create($data);

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement created successfully.');
        } catch (\Throwable $e) {
            Log::error('Announcement create failed: ' . $e->getMessage());
            return back()->with('error', 'Could not create announcement.')->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update announcement.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $data = $this->validated($request);

        try {
            $announcement->update($data);

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Announcement updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Announcement update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update announcement.')->withInput();
        }
    }

    /**
     * Delete announcement.
     */
    public function destroy(Announcement $announcement)
    {
        try {
            $announcement->delete();

            return back()->with('success', 'Announcement deleted.');
        } catch (\Throwable $e) {
            Log::error('Announcement delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete announcement.');
        }
    }

    /**
     * Toggle active status.
     */
    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        return back()->with('success', 'Status updated.');
    }

    /**
     * Common validation.
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'text'           => 'required|string|max:255',
            'icon'           => 'nullable|string|max:10',
            'link'           => 'nullable|string|max:500',
            'bg_color'       => 'nullable|string|max:20',
            'sort_order'     => 'nullable|integer|min:0|max:9999',
            'is_active'      => 'boolean',
            'is_dismissible' => 'boolean',
            'starts_at'      => 'nullable|date',
            'ends_at'        => 'nullable|date|after:starts_at',
        ]);

        $data['is_active']      = $request->boolean('is_active', true);
        $data['is_dismissible'] = $request->boolean('is_dismissible', true);
        $data['sort_order']     = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}