<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * List subscribers.
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($q = $request->query('q')) {
            $query->where('email', 'like', "%{$q}%");
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $subscribers = $query->latest()->paginate(30)->withQueryString();

        $stats = [
            'total'         => NewsletterSubscriber::count(),
            'active'        => NewsletterSubscriber::where('is_active', true)->count(),
            'unsubscribed'  => NewsletterSubscriber::where('is_active', false)->count(),
            'this_week'     => NewsletterSubscriber::where('subscribed_at', '>=', now()->subWeek())->count(),
        ];

        return view('admin.newsletter.index', compact('subscribers', 'stats'));
    }

    /**
     * Delete subscriber.
     */
    public function destroy(NewsletterSubscriber $subscriber)
    {
        try {
            $subscriber->delete();

            return back()->with('success', 'Subscriber removed.');
        } catch (\Throwable $e) {
            Log::error('Subscriber delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not remove subscriber.');
        }
    }

    /**
     * Export subscribers as CSV.
     */
    public function export(Request $request)
    {
        $subscribers = NewsletterSubscriber::active()->latest()->get();

        $filename = 'newsletter-subscribers-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Subscribed At', 'Status']);

            foreach ($subscribers as $sub) {
                fputcsv($file, [
                    $sub->email,
                    $sub->subscribed_at?->format('Y-m-d H:i:s') ?? '',
                    $sub->is_active ? 'Active' : 'Unsubscribed',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}