<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryStatusLog;
use App\Models\Order;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected AuditLogService $audit
    ) {}

    public function index(Request $request)
    {
        $query = Order::with(['user', 'product', 'deliveryMan', 'vendor']);

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('order_number', 'like', "%{$q}%")
                  ->orWhere('delivery_name', 'like', "%{$q}%")
                  ->orWhere('delivery_phone', 'like', "%{$q}%")
                  ->orWhere('product_name', 'like', "%{$q}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->query('type')) {
            $query->where('order_type', $type);
        }

        if ($payment = $request->query('payment')) {
            $query->where('payment_status', $payment);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

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

    public function show(Order $order)
    {
        $order->load(['user', 'product', 'coupon', 'deliveryMan', 'vendor', 'statusLogs.user']);

        $deliveryMen = User::role('delivery_man')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $vendors = User::role('vendor')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('admin.orders.show', compact('order', 'deliveryMen', 'vendors'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,out_for_delivery,delivered,ready_for_pickup,picked_up,cancelled,returned',
            'note'   => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;

        try {
            if ($request->status === 'cancelled') {
                $this->orderService->cancelOrder($order, auth()->user(), $request->note);
            } elseif ($request->status === 'returned') {
                $this->orderService->returnOrder($order, auth()->user(), $request->note);
            } else {
                $this->orderService->updateStatus($order, $request->status, auth()->user(), $request->note);
            }

            $this->audit->logAction(
                "Order {$order->order_number}: {$oldStatus} → {$request->status}",
                $order,
                ['old' => ['status' => $oldStatus], 'new' => ['status' => $request->status]]
            );

            return back()->with('success', 'Order status updated to ' . str_replace('_', ' ', $request->status) . '.');
        } catch (\Throwable $e) {
            Log::error('Order status update failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

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

        try {
            $order->update([
                'delivery_method' => $request->delivery_method,
                'assigned_to'     => $request->delivery_method === 'self' ? $request->assigned_to : null,
                'vendor_id'       => $request->delivery_method === 'vendor' ? $request->vendor_id : null,
                'tracking_number' => $request->tracking_number,
            ]);

            DeliveryStatusLog::create([
                'order_id'   => $order->id,
                'status'     => $order->status,
                'changed_by' => auth()->id(),
                'note'       => 'Assigned via ' . $request->delivery_method,
            ]);

            $this->audit->logAction(
                "Order {$order->order_number} assigned via {$request->delivery_method}",
                $order
            );

            return back()->with('success', 'Delivery assignment updated.');
        } catch (\Throwable $e) {
            Log::error('Order assign failed: ' . $e->getMessage());
            return back()->with('error', 'Could not assign delivery.');
        }
    }

    public function cancel(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $this->orderService->cancelOrder($order, auth()->user(), $request->note);

            $this->audit->logAction("Order {$order->order_number} cancelled", $order);

            return back()->with('success', 'Order cancelled.');
        } catch (\Throwable $e) {
            Log::error('Order cancel failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function return(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $this->orderService->returnOrder($order, auth()->user(), $request->note);

            $this->audit->logAction("Order {$order->order_number} returned", $order);

            return back()->with('success', 'Order marked as returned.');
        } catch (\Throwable $e) {
            Log::error('Order return failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
}