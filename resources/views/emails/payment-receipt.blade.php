<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
</head>
<body style="margin:0; padding:0; background:#1d1c26; font-family: 'Courier New', monospace;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#1d1c26; padding:40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="400" cellpadding="0" cellspacing="0" style="background:#f6f1e4; color:#201d1a;">
                    <tr>
                        <td style="padding:32px 28px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-size:11px; letter-spacing:0.05em; color:#6b6558;">PAYMENT RECEIPT</td>
                                    <td align="right" style="font-size:11px; color:#6b6558;">No. {{ $transaction->invoice_number }}</td>
                                </tr>
                            </table>

                            <div style="border-top:1px dashed #c9c2ae; margin:16px 0;"></div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px; line-height:1.9;">
                                <tr>
                                    <td style="color:#6b6558;">Status</td>
                                    <td align="right" style="color:#46654c; font-weight:bold;">PAID</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b6558;">Paid via</td>
                                    <td align="right">{{ $gateway }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b6558;">Reference</td>
                                    <td align="right">{{ $reference ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b6558;">Date</td>
                                    <td align="right">{{ $transaction->updated_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>

                            <div style="border-top:1px dashed #c9c2ae; margin:16px 0;"></div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-family: Georgia, serif; font-size:16px;">Total Paid</td>
                                    <td align="right" style="font-family: Georgia, serif; font-size:22px; font-weight:bold;">
                                        {{ $transaction->currency === 'USD' ? '$' : '৳' }}{{ number_format($transaction->amount, 2) }}
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:12px; color:#6b6558; margin-top:28px; text-align:center;">
                                This is a sandbox test transaction — no real money was charged.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
