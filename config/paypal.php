<?php

return [
    // 'sandbox' or 'live'
    'mode' => env('PAYPAL_MODE', 'sandbox'),

    'sandbox_base_url' => 'https://api-m.sandbox.paypal.com',
    'live_base_url'    => 'https://api-m.paypal.com',

    'client_id'     => env('PAYPAL_CLIENT_ID'),
    'client_secret' => env('PAYPAL_CLIENT_SECRET'),

    'currency' => env('PAYPAL_CURRENCY', 'USD'),

    'return_url' => env('PAYPAL_RETURN_URL', '/paypal/callback'),
    'cancel_url' => env('PAYPAL_CANCEL_URL', '/paypal/cancel'),
];
