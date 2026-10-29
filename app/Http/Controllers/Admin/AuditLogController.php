<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * List all audit logs with filters.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Search
        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('description', 'like', "%{$q}%")
                  ->orWhere('auditable_type', 'like', "%{$q}%")
                  ->orWhere('event', 'like', "%{$q}%");
            });
        }

        // Filter by event
        if ($event = $request->query('event')) {
            $query->where('event', $event);
        }

        // Filter by user
        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter by model type
        if ($modelType = $request->query('model_type')) {
            $query->where('auditable_type', $modelType);
        }

        // Filter by date range
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->paginate(30)->withQueryString();

        $stats = [
            'total'   => AuditLog::count(),
            'today'   => AuditLog::whereDate('created_at', today())->count(),
            'week'    => AuditLog::where('created_at', '>=', now()->subWeek())->count(),
            'created' => AuditLog::where('event', 'created')->count(),
            'updated' => AuditLog::where('event', 'updated')->count(),
            'deleted' => AuditLog::where('event', 'deleted')->count(),
        ];

        $users = User::orderBy('name')->get(['id', 'name']);

        $modelTypes = AuditLog::distinct('auditable_type')
            ->whereNotNull('auditable_type')
            ->pluck('auditable_type')
            ->map(fn ($t) => [
                'value' => $t,
                'label' => class_basename($t),
            ]);

        return view('admin.audit-logs.index', compact('logs', 'stats', 'users', 'modelTypes'));
    }

    /**
     * Show single log detail.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * Delete single log.
     */
    public function destroy(AuditLog $auditLog)
    {
        $auditLog->delete();

        return back()->with('success', 'Audit log deleted.');
    }

    /**
     * Purge logs older than N days.
     */
    public function purge(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $deleted = AuditLog::where('created_at', '<', now()->subDays($request->days))->delete();

        return back()->with('success', "Purged {$deleted} logs older than {$request->days} days.");
    }
}