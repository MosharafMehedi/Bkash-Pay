@php
    $labels = [
        'pending'          => 'Pending',
        'processing'       => 'Processing',
        'out_for_delivery' => 'Out for Delivery',
        'delivered'        => 'Delivered',
        'ready_for_pickup' => 'Ready for Pickup',
        'picked_up'        => 'Picked Up',
        'cancelled'        => 'Cancelled',
        'returned'         => 'Returned',
    ];
    $label = $labels[$newStatus] ?? ucfirst($newStatus);

    $messages = [
        'pending'          => 'Your order has been received.',
        'processing'       => 'We are preparing your order.',
        'out_for_delivery' => 'Your order is on the way! Get your delivery code ready.',
        'delivered'        => 'Your order has been delivered. Thank you!',
        'ready_for_pickup' => 'Your order is ready for pickup.',
        'picked_up'        => 'Your order has been picked up. Thank you!',
        'cancelled'        => 'Your order has been cancelled.',
        'returned'         => 'Your order was returned.',
    ];
    $msg = $messages[$newStatus] ?? 'Your order status has been updated.';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Update</title>
</head>
<body style="font-family: -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f4f5f7; padding: 24px; margin: 0;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">

        <div style="background: linear-gradient(135deg, #29e7ff, #a78bfa); padding: 24px; color: #06050c;">
            <div style="font-size: 12px; letter-spacing: 0.15em; text-transform: uppercase; opacity: 0.8;">Order Update</div>
            <h1 style="margin: 6px 0 0; font-size: 20px;">{{ $label }}</h1>
        </div>

        <div style="padding: 28px 24px;">
            <p style="margin: 0 0 16px; color: #334155; font-size: 14px;">Hi {{ $order->user->name }},</p>

            <p style="margin: 0 0 20px; color: #334155; font-size: 14px; line-height: 1.6;">
                {{ $msg }}
            </p>

            <div style="padding: 16px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: #64748b;">
                    <span>Order #</span>
                    <strong style="color: #0f172a;">{{ $order->order_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: #64748b;">
                    <span>Product</span>
                    <strong style="color: #0f172a;">{{ $order->product_name }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #64748b;">
                    <span>Total</span>
                    <strong style="color: #29e7ff;">৳{{ number_format($order->total_amount, 0) }}</strong>
                </div>
            </div>

            <p style="margin: 24px 0 0; font-size: 12px; color: #94a3b8;">
                You can track your order from your account dashboard.
            </p>
        </div>
    </div>
</body>
</html>