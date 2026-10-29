<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomepageController extends Controller
{
    public function __construct(protected AuditLogService $audit) {}

    public const SETTINGS = [
        // (apnar existing SETTINGS array same)
        ['key' => 'show_slider', 'label' => 'Hero Slider', 'group' => 'sections', 'type' => 'boolean', 'default' => true],
        // ... baki sob same
    ];

    public function index()
    {
        $settings = [];
        foreach (self::SETTINGS as $setting) {
            $settings[$setting['key']] = HomepageSetting::get($setting['key'], $setting['default']);
        }

        $groups = collect(self::SETTINGS)->groupBy('group');

        return view('admin.homepage.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        try {
            foreach (self::SETTINGS as $setting) {
                $key   = $setting['key'];
                $type  = $setting['type'];
                $group = $setting['group'];
                $label = $setting['label'];

                $value = $type === 'boolean'
                    ? $request->boolean($key, false)
                    : $request->input($key, $setting['default']);

                HomepageSetting::set($key, $value, $type, $group, $label);
            }

            $this->audit->logAction("Homepage settings updated");

            return back()->with('success', 'Homepage settings updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Homepage settings update failed: ' . $e->getMessage());
            return back()->with('error', 'Could not update settings.');
        }
    }

    public function reset()
    {
        try {
            HomepageSetting::truncate();

            $this->audit->logAction("Homepage settings reset to defaults");

            return back()->with('success', 'All settings reset to defaults.');
        } catch (\Throwable $e) {
            Log::error('Homepage settings reset failed: ' . $e->getMessage());
            return back()->with('error', 'Could not reset settings.');
        }
    }
}