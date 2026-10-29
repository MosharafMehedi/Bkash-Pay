<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MyOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id())
            ->with(['product'])
            ->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        $stats = [
            'total'     => Order::where('user_id', auth()->id())->count(),
            'pending'   => Order::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'processing'])->count(),
            'shipping'  => Order::where('user_id', auth()->id())
                ->whereIn('status', ['out_for_delivery', 'ready_for_pickup'])->count(),
            'completed' => Order::where('user_id', auth()->id())
                ->whereIn('status', ['delivered', 'picked_up'])->count(),
        ];

        return view('my-orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['product', 'coupon', 'statusLogs.user']);

        return view('my-orders.show', compact('order'));
    }
}