<?php

return [
    // Sandbox base URL for bKash Tokenized Checkout API
    'base_url' => env('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized'),

    'username'   => env('BKASH_USERNAME'),
    'password'   => env('BKASH_PASSWORD'),
    'app_key'    => env('BKASH_APP_KEY'),
    'app_secret' => env('BKASH_APP_SECRET'),

    'callback_url' => env('BKASH_CALLBACK_URL', '/bkash/callback'),
];