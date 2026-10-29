<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $baseQuery = $this->assignedQuery();

        $stats = [
            'assigned'  => (clone $baseQuery)->whereIn('status', ['processing', 'out_for_delivery'])->count(),
            'out'       => (clone $baseQuery)->where('status', 'out_for_delivery')->count(),
            'delivered' => (clone $baseQuery)->whereIn('status', ['delivered', 'picked_up'])->count(),
            'today'     => (clone $baseQuery)->whereDate('delivered_at', today())->count(),
        ];

        $activeOrders = (clone $baseQuery)
            ->with(['product', 'user'])
            ->whereIn('status', ['processing', 'out_for_delivery'])
            ->latest()
            ->take(10)
            ->get();

        $recentDeliveries = (clone $baseQuery)
            ->with(['product', 'user'])
            ->whereIn('status', ['delivered', 'picked_up'])
            ->latest('delivered_at')
            ->take(5)
            ->get();

        return view('delivery.dashboard', compact('stats', 'activeOrders', 'recentDeliveries'));
    }

    protected function assignedQuery()
    {
        $user = auth()->user();
        $query = Order::query();

        if ($user->hasRole('vendor')) {
            $query->where('vendor_id', $user->id);
        } else {
            $query->where('assigned_to', $user->id);
        }

        return $query;
    }
}