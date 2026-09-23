<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Base query — self or vendor based on role
        $baseQuery = $this->assignedQuery();

        $stats = [
            'assigned'    => (clone $baseQuery)->whereIn('status', ['processing', 'out_for_delivery'])->count(),
            'out'         => (clone $baseQuery)->where('status', 'out_for_delivery')->count(),
            'delivered'   => (clone $baseQuery)->whereIn('status', ['delivered', 'picked_up'])->count(),
            'today'       => (clone $baseQuery)->whereDate('delivered_at', today())->count(),
        ];

        // Active orders (need action)
        $activeOrders = (clone $baseQuery)
            ->with(['product', 'user'])
            ->whereIn('status', ['processing', 'out_for_delivery'])
            ->latest()
            ->take(10)
            ->get();

        // Recent deliveries
        $recentDeliveries = (clone $baseQuery)
            ->with(['product', 'user'])
            ->whereIn('status', ['delivered', 'picked_up'])
            ->latest('delivered_at')
            ->take(5)
            ->get();

        return view('delivery.dashboard', compact('stats', 'activeOrders', 'recentDeliveries'));
    }

    /**
     * Scope query based on role — delivery_man vs vendor.
     */
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