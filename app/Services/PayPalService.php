<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('paypal.mode') === 'live'
            ? config('paypal.live_base_url')
            : config('paypal.sandbox_base_url');

        $this->clientId     = config('paypal.client_id');
        $this->clientSecret = config('paypal.client_secret');
    }

    /**
     * Get an OAuth2 access token (client_credentials grant), cached
     * until it's close to expiry.
     */
    public function getToken(): ?string
    {
        return Cache::remember('paypal_access_token', 55 * 60, function () {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            $this->log('oauth-token', $response);

            if ($response->failed() || !$response->json('access_token')) {
                return null;
            }

            return $response->json('access_token');
        });
    }

    /**
     * Create an order. Returns the PayPal response (contains order id
     * and an "approve" link on success).
     */
    public function createOrder(float $amount, string $invoiceNumber = null): array
    {
        $token = $this->getToken();

        if (!$token) {
            return ['error' => 'Could not obtain PayPal access token'];
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id'  => $invoiceNumber,
                    'amount' => [
                        'currency_code' => config('paypal.currency'),
                        'value'          => number_format($amount, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => url(config('paypal.return_url')),
                    'cancel_url' => url(config('paypal.cancel_url')),
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        $this->log('create-order', $response);

        return $response->json() ?? ['error' => 'Empty response from PayPal'];
    }

    /**
     * Capture (finalize) an order after the buyer approves it on
     * PayPal's side.
     */
    public function captureOrder(string $orderId): array
    {
        $token = $this->getToken();

        if (!$token) {
            return ['error' => 'Could not obtain PayPal access token'];
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        $this->log('capture-order', $response);

        return $response->json() ?? ['error' => 'Empty response from PayPal'];
    }

    /**
     * Get the current status/details of an order.
     */
    public function getOrder(string $orderId): array
    {
        $token = $this->getToken();

        if (!$token) {
            return ['error' => 'Could not obtain PayPal access token'];
        }

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/v2/checkout/orders/{$orderId}");

        $this->log('get-order', $response);

        return $response->json() ?? ['error' => 'Empty response from PayPal'];
    }

    protected function log(string $step, $response): void
    {
        Log::info("PayPal [{$step}]", [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);
    }
}
