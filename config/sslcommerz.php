<?php

return [
    // 'sandbox' or 'live'
    'mode' => env('SSLCOMMERZ_MODE', 'sandbox'),

    'sandbox_base_url' => 'https://sandbox.sslcommerz.com',
    'live_base_url'    => 'https://securepay.sslcommerz.com',

    'store_id'     => env('SSLCOMMERZ_STORE_ID'),
    'store_passwd' => env('SSLCOMMERZ_STORE_PASSWORD'),

    'currency' => env('SSLCOMMERZ_CURRENCY', 'BDT'),

    'success_url' => env('SSLCOMMERZ_SUCCESS_URL', '/sslcommerz/success'),
    'fail_url'    => env('SSLCOMMERZ_FAIL_URL', '/sslcommerz/fail'),
    'cancel_url'  => env('SSLCOMMERZ_CANCEL_URL', '/sslcommerz/cancel'),
    'ipn_url'     => env('SSLCOMMERZ_IPN_URL', '/sslcommerz/ipn'),
];
