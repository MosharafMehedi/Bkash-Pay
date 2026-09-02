<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzService
{
    protected string $baseUrl;
    protected string $storeId;
    protected string $storePasswd;

    public function __construct()
    {
        $this->baseUrl = config('sslcommerz.mode') === 'live'
            ? config('sslcommerz.live_base_url')
            : config('sslcommerz.sandbox_base_url');

        $this->storeId     = config('sslcommerz.store_id');
        $this->storePasswd = config('sslcommerz.store_passwd');
    }

    /**
     * Initiate a payment session. Returns the SSLCommerz response
     * (contains GatewayPageURL on success).
     */
    public function initiatePayment(float $amount, string $transactionId, array $customer = []): array
    {
        $payload = [
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePasswd,
            'total_amount' => number_format($amount, 2, '.', ''),
            'currency'     => config('sslcommerz.currency'),
            'tran_id'      => $transactionId,

            'success_url' => url(config('sslcommerz.success_url')),
            'fail_url'    => url(config('sslcommerz.fail_url')),
            'cancel_url'  => url(config('sslcommerz.cancel_url')),
            'ipn_url'     => url(config('sslcommerz.ipn_url')),

            // Customer info — SSLCommerz requires these even for sandbox testing
            'cus_name'    => $customer['name'] ?? 'Test Customer',
            'cus_email'   => $customer['email'] ?? 'test@example.com',
            'cus_add1'    => $customer['address'] ?? 'Dhaka',
            'cus_city'    => $customer['city'] ?? 'Dhaka',
            'cus_postcode' => $customer['postcode'] ?? '1200',
            'cus_country' => 'Bangladesh',
            'cus_phone'   => $customer['phone'] ?? '01700000000',

            // Shipping info — required by the API even for digital/non-shipped goods
            'shipping_method' => 'NO',
            'product_name'     => 'Test Product',
            'product_category' => 'General',
            'product_profile'  => 'general',
        ];

        $response = Http::asForm()->post("{$this->baseUrl}/gwprocess/v4/api.php", $payload);

        $this->log('initiate-payment', $response);

        return $response->json() ?? ['error' => 'Empty response from SSLCommerz'];
    }

    /**
     * Validate a completed transaction using the Order Validation API.
     * Always call this from your success/IPN handler before trusting
     * the payment — never trust the redirect params alone.
     */
    public function validateTransaction(string $validationId): array
    {
        $response = Http::get("{$this->baseUrl}/validator/api/validationserverAPI.php", [
            'val_id'       => $validationId,
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePasswd,
            'format'       => 'json',
        ]);

        $this->log('validate-transaction', $response);

        return $response->json() ?? ['error' => 'Empty response from SSLCommerz'];
    }

    protected function log(string $step, $response): void
    {
        Log::info("SSLCommerz [{$step}]", [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);
    }
}
