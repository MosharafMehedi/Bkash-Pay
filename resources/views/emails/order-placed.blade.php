<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmed</title>
</head>
<body style="font-family: -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f4f5f7; padding: 24px; margin: 0;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">

        {{-- Header --}}
        <div style="background: linear-gradient(135deg, #29e7ff, #a78bfa); padding: 28px 24px; color: #06050c;">
            <div style="font-size: 12px; letter-spacing: 0.15em; text-transform: uppercase; opacity: 0.8;">Order Confirmed</div>
            <h1 style="margin: 6px 0 0; font-size: 22px;">Thanks for your order!</h1>
        </div>

        {{-- Body --}}
        <div style="padding: 28px 24px;">
            <p style="margin: 0 0 16px; color: #334155; font-size: 14px;">Hi {{ $order->user->name }},</p>

            <p style="margin: 0 0 20px; color: #334155; font-size: 14px; line-height: 1.6;">
                Your order has been placed successfully. Save your <strong>delivery code</strong> below — you'll need to give it to the delivery person when your order arrives.
            </p>

            {{-- Delivery code box --}}
            <div style="text-align: center; margin: 28px 0;">
                <div style="display: inline-block; padding: 18px 32px; border-radius: 12px; background: #f1f5f9; border: 2px dashed #cbd5e1;">
                    <div style="font-size: 12px; color: #64748b; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 6px;">Delivery Code</div>
                    <div style="font-size: 32px; letter-spacing: 0.3em; font-weight: 700; color: #0f172a; font-family: monospace;">
                        {{ $order->delivery_code }}
                    </div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                        Expires {{ $order->delivery_code_expires_at->format('M d, Y') }}
                    </div>
                </div>
            </div>

            {{-- Order details --}}
            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #64748b;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Order #</span>
                    <strong style="color: #0f172a;">{{ $order->order_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Product</span>
                    <strong style="color: #0f172a;">{{ $order->product_name }} × {{ $order->quantity }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Type</span>
                    <strong style="color: #0f172a;">{{ $order->isPickup() ? 'Pickup' : 'Home Delivery' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Deliver to</span>
                    <strong style="color: #0f172a;">{{ $order->delivery_name }}, {{ $order->delivery_phone }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 8px; margin-top: 8px; border-top: 1px dashed #e2e8f0;">
                    <span>Total</span>
                    <strong style="color: #29e7ff; font-size: 16px;">৳{{ number_format($order->total_amount, 0) }}</strong>
                </div>
            </div>

            <p style="margin: 24px 0 0; font-size: 12px; color: #94a3b8;">
                Keep this code safe. Do not share it with anyone except the delivery person.
            </p>
        </div>
    </div>
</body>
</html>