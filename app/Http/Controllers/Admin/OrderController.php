<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryStatusLog;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * Order list with filters + stats.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'product', 'deliveryMan', 'vendor']);

        // Search
        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('order_number', 'like', "%{$q}%")
                  ->orWhere('delivery_name', 'like', "%{$q}%")
                  ->orWhere('delivery_phone', 'like', "%{$q}%")
                  ->orWhere('product_name', 'like', "%{$q}%");
            });
        }

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by order type
        if ($type = $request->query('type')) {
            $query->where('order_type', $type);
        }

        // Filter by payment status
        if ($payment = $request->query('payment')) {
            $query->where('payment_status', $payment);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total'      => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipping'   => Order::whereIn('status', ['out_for_delivery', 'ready_for_pickup'])->count(),
            'delivered'  => Order::whereIn('status', ['delivered', 'picked_up'])->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Order detail with all info + actions.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'product', 'coupon', 'deliveryMan', 'vendor', 'statusLogs.user']);

        // Delivery men list (role: delivery_man)
        $deliveryMen = User::role('delivery_man')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Vendors list (role: vendor)
        $vendors = User::role('vendor')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.orders.show', compact('order', 'deliveryMen', 'vendors'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,out_for_delivery,delivered,ready_for_pickup,picked_up,cancelled,returned',
            'note'   => 'nullable|string|max:500',
        ]);

        try {
            // Cancel with stock return
            if ($request->status === 'cancelled') {
                $this->orderService->cancelOrder($order, auth()->user(), $request->note);
            }
            // Return with stock return
            elseif ($request->status === 'returned') {
                $this->orderService->returnOrder($order, auth()->user(), $request->note);
            }
            // Normal status update
            else {
                $this->orderService->updateStatus(
                    $order,
                    $request->status,
                    auth()->user(),
                    $request->note
                );
            }

            return back()->with('success', 'Order status updated to ' . str_replace('_', ' ', $request->status) . '.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign delivery man / vendor to order.
     */
    public function assign(Request $request, Order $order)
    {
        $request->validate([
            'delivery_method' => 'required|in:self,vendor,pickup',
            'assigned_to'     => 'nullable|exists:users,id',
            'vendor_id'       => 'nullable|exists:users,id',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        if ($request->delivery_method === 'self' && ! $request->assigned_to) {
            return back()->with('error', 'Please select a delivery man.');
        }

        if ($request->delivery_method === 'vendor' && ! $request->vendor_id) {
            return back()->with('error', 'Please select a vendor.');
        }

        $order->update([
            'delivery_method' => $request->delivery_method,
            'assigned_to'     => $request->delivery_method === 'self' ? $request->assigned_to : null,
            'vendor_id'       => $request->delivery_method === 'vendor' ? $request->vendor_id : null,
            'tracking_number' => $request->tracking_number,
        ]);

        // Log
        DeliveryStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $order->status,
            'changed_by' => auth()->id(),
            'note'       => 'Assigned via ' . $request->delivery_method,
        ]);

        return back()->with('success', 'Delivery assignment updated.');
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $this->orderService->cancelOrder($order, auth()->user(), $request->note);
            return back()->with('success', 'Order cancelled.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark order as returned.
     */
    public function return(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $this->orderService->returnOrder($order, auth()->user(), $request->note);
            return back()->with('success', 'Order marked as returned.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}