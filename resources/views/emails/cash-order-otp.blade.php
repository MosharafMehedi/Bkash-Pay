<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Order Verification</title>
</head>
<body style="font-family: -apple-system, 'Segoe UI', Roboto, sans-serif; background: #f4f5f7; padding: 24px; margin: 0;">
    <div style="max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">

        <div style="background: linear-gradient(135deg, #29e7ff, #a78bfa); padding: 28px 24px; color: #06050c;">
            <div style="font-size: 12px; letter-spacing: 0.15em; text-transform: uppercase; opacity: 0.8;">Cash on Delivery</div>
            <h1 style="margin: 6px 0 0; font-size: 22px;">Verify your order</h1>
        </div>

        <div style="padding: 28px 24px;">
            <p style="margin: 0 0 16px; color: #334155; font-size: 14px;">Hi {{ $order->user->name }},</p>

            <p style="margin: 0 0 20px; color: #334155; font-size: 14px; line-height: 1.6;">
                You placed a Cash on Delivery order for <strong>{{ $order->product->name }}</strong>.
                Use the verification code below to confirm your order:
            </p>

            <div style="text-align: center; margin: 28px 0;">
                <div style="display: inline-block; padding: 18px 32px; border-radius: 12px; background: #f1f5f9; border: 2px dashed #cbd5e1;">
                    <div style="font-size: 34px; letter-spacing: 0.4em; font-weight: 700; color: #0f172a; font-family: monospace;">
                        {{ $order->otp }}
                    </div>
                </div>
            </div>

            <p style="margin: 0 0 8px; color: #64748b; font-size: 13px;">This code expires in <strong>10 minutes</strong>.</p>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #64748b;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Order #</span><strong style="color: #0f172a;">{{ $order->order_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Product</span><strong style="color: #0f172a;">{{ $order->product->name }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Amount</span><strong style="color: #0f172a;">৳{{ number_format($order->amount, 0) }}</strong>
                </div>
            </div>

            <p style="margin: 24px 0 0; font-size: 12px; color: #94a3b8;">
                If you didn't request this, please ignore this email.
            </p>
        </div>
    </div>
</body>
</html>