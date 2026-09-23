<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BkashService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected string $appKey;
    protected string $appSecret;

    public function __construct()
    {
        // ⚠️ base_url e "/checkout" INCLUDE koro na
        // Just: https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized
        $this->baseUrl   = rtrim(config('bkash.base_url'), '/');
        $this->username  = (string) config('bkash.username');
        $this->password  = (string) config('bkash.password');
        $this->appKey    = (string) config('bkash.app_key');
        $this->appSecret = (string) config('bkash.app_secret');
    }

    /**
     * HTTP client with SSL verification disabled (sandbox-friendly).
     */
    protected function client()
    {
        return Http::withOptions([
            'verify'          => false,   // ← SSL verify OFF (sandbox fix)
            'connect_timeout' => 10,
            'timeout'         => 30,
        ]);
    }

    /**
     * Get a valid id_token (cached for 55 minutes).
     */
    public function getToken(): ?string
    {
        return Cache::remember('bkash_id_token', 55 * 60, function () {
            $url = "{$this->baseUrl}/checkout/token/grant";

            Log::info('bKash [grant-token] REQUEST', [
                'url'      => $url,
                'username' => $this->username,
                // password, app_secret log koro na
            ]);

            try {
                $response = $this->client()
                    ->withHeaders([
                        'username'     => $this->username,
                        'password'     => $this->password,
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ])
                    ->post($url, [
                        'app_key'    => $this->appKey,
                        'app_secret' => $this->appSecret,
                    ]);
            } catch (\Throwable $e) {
                Log::error('bKash [grant-token] EXCEPTION', [
                    'message' => $e->getMessage(),
                ]);
                return null;
            }

            $this->log('grant-token', $response);

            if ($response->failed() || ! $response->json('id_token')) {
                return null;
            }

            return $response->json('id_token');
        });
    }

    /**
     * Create a payment session.
     */
    public function createPayment(float $amount, string $invoiceNumber = null): array
    {
        $token = $this->getToken();

        if (! $token) {
            return ['error' => 'Could not obtain bKash token. Check credentials.'];
        }

        $url = "{$this->baseUrl}/checkout/create";
        $callback = url(config('bkash.callback_url'));

        Log::info('bKash [create-payment] REQUEST', [
            'url'      => $url,
            'amount'   => $amount,
            'callback' => $callback,
            'invoice'  => $invoiceNumber,
        ]);

        try {
            $response = $this->client()
                ->withHeaders($this->authHeaders($token))
                ->post($url, [
                    'mode'                  => '0011',
                    'payerReference'        => 'N/A',
                    'callbackURL'           => $callback,
                    'amount'                => number_format($amount, 2, '.', ''),
                    'currency'              => 'BDT',
                    'intent'                => 'sale',
                    'merchantInvoiceNumber' => $invoiceNumber ?? ('INV-' . Str::upper(Str::random(8))),
                ]);
        } catch (\Throwable $e) {
            Log::error('bKash [create-payment] EXCEPTION', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return ['error' => 'Exception: ' . $e->getMessage()];
        }

        $this->log('create-payment', $response);

        $json = $response->json();

        if (empty($json)) {
            // Extra debug — raw body
            Log::error('bKash [create-payment] EMPTY BODY', [
                'status' => $response->status(),
                'raw'    => $response->body(),
            ]);
            return ['error' => 'Empty response from bKash (HTTP ' . $response->status() . ')'];
        }

        return $json;
    }

    /**
     * Execute (confirm) a payment.
     */
    public function executePayment(string $paymentId): array
    {
        $token = $this->getToken();

        if (! $token) {
            return ['error' => 'Could not obtain bKash token'];
        }

        try {
            $response = $this->client()
                ->withHeaders($this->authHeaders($token))
                ->post("{$this->baseUrl}/checkout/execute", [
                    'paymentID' => $paymentId,
                ]);
        } catch (\Throwable $e) {
            Log::error('bKash [execute-payment] EXCEPTION', ['message' => $e->getMessage()]);
            return ['error' => 'Exception: ' . $e->getMessage()];
        }

        $this->log('execute-payment', $response);

        return $response->json() ?? ['error' => 'Empty response from bKash'];
    }

    /**
     * Query payment status.
     */
    public function queryPayment(string $paymentId): array
    {
        $token = $this->getToken();

        if (! $token) {
            return ['error' => 'Could not obtain bKash token'];
        }

        try {
            $response = $this->client()
                ->withHeaders($this->authHeaders($token))
                ->post("{$this->baseUrl}/checkout/payment/status", [
                    'paymentID' => $paymentId,
                ]);
        } catch (\Throwable $e) {
            Log::error('bKash [query-payment] EXCEPTION', ['message' => $e->getMessage()]);
            return ['error' => 'Exception: ' . $e->getMessage()];
        }

        $this->log('query-payment', $response);

        return $response->json() ?? ['error' => 'Empty response from bKash'];
    }

    protected function authHeaders(string $token): array
    {
        return [
            'Authorization' => $token,
            'X-App-Key'     => $this->appKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    protected function log(string $step, $response): void
    {
        Log::info("bKash [{$step}] RESPONSE", [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
    }
}