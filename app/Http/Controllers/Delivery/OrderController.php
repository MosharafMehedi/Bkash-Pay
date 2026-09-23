<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * List orders assigned to this delivery man / vendor.
     */
    public function index(Request $request)
    {
        $query = $this->assignedQuery();

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Search
        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('order_number', 'like', "%{$q}%")
                  ->orWhere('delivery_name', 'like', "%{$q}%")
                  ->orWhere('delivery_phone', 'like', "%{$q}%");
            });
        }

        $orders = $query->with(['product', 'user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('delivery.orders.index', compact('orders'));
    }

    /**
     * Order detail with code verify form.
     */
    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load(['product', 'user', 'coupon', 'statusLogs.user']);

        return view('delivery.orders.show', compact('order'));
    }

    /**
     * Mark order as out for delivery.
     */
    public function markOutForDelivery(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'processing') {
            return back()->with('error', 'Order must be in "processing" status.');
        }

        if ($order->isPickup()) {
            return back()->with('error', 'Pickup orders cannot be marked out for delivery.');
        }

        try {
            $this->orderService->updateStatus(
                $order,
                'out_for_delivery',
                auth()->user(),
                'Marked out for delivery by ' . auth()->user()->name
            );

            return back()->with('success', 'Order is now out for delivery.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Verify delivery code and complete delivery.
     */
    public function verifyCode(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // Already delivered?
        if (in_array($order->status, ['delivered', 'picked_up'])) {
            return back()->with('error', 'This order is already completed.');
        }

        // Must be out for delivery (or ready for pickup)
        $allowedStatuses = $order->isPickup()
            ? ['processing', 'ready_for_pickup']
            : ['out_for_delivery'];

        if (! in_array($order->status, $allowedStatuses)) {
            return back()->with('error', 'Order is not ready for verification yet.');
        }

        try {
            [$success, $message, $order] = $this->orderService->verifyDeliveryCode(
                $request->input('code'),
                $order
            );

            if ($success) {
                return back()->with('success', $message);
            }

            return back()->with('error', $message);
        } catch (\Throwable $e) {
            Log::error('Code verify failed: ' . $e->getMessage());
            return back()->with('error', 'Verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Scope query by role.
     */
    protected function assignedQuery()
    {
        $user  = auth()->user();
        $query = Order::query();

        if ($user->hasRole('vendor')) {
            $query->where('vendor_id', $user->id);
        } else {
            $query->where('assigned_to', $user->id);
        }

        return $query;
    }

    /**
     * Ensure the order belongs to this delivery person.
     */
    protected function authorizeOrder(Order $order): void
    {
        $user = auth()->user();

        $belongs = $user->hasRole('vendor')
            ? $order->vendor_id === $user->id
            : $order->assigned_to === $user->id;

        abort_unless($belongs || $user->hasRole('admin'), 403);
    }
}