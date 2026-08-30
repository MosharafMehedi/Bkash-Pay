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
        $this->baseUrl   = config('bkash.base_url');
        $this->username  = config('bkash.username');
        $this->password  = config('bkash.password');
        $this->appKey    = config('bkash.app_key');
        $this->appSecret = config('bkash.app_secret');
    }

    /**
     * Get a valid id_token, from cache if possible, otherwise
     * request a fresh one from bKash Grant Token API.
     */
    public function getToken(): ?string
    {
        return Cache::remember('bkash_id_token', 55 * 60, function () {
            $response = Http::withHeaders([
                'username'     => $this->username,
                'password'     => $this->password,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post("{$this->baseUrl}/checkout/token/grant", [
                'app_key'    => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

            $this->log('grant-token', $response);

            if ($response->failed() || !$response->json('id_token')) {
                return null;
            }

            return $response->json('id_token');
        });
    }

    /**
     * Create a payment session. Returns the bKash response array
     * (contains paymentID and bkashURL on success).
     */
    public function createPayment(float $amount, string $invoiceNumber = null): array
    {
        $token = $this->getToken();

        if (!$token) {
            return ['error' => 'Could not obtain bKash token'];
        }

        $response = Http::withHeaders($this->authHeaders($token))
            ->post("{$this->baseUrl}/checkout/create", [
                'mode'                  => '0011',
                'payerReference'        => 'N/A',
                'callbackURL'           => url(config('bkash.callback_url')),
                'amount'                => number_format($amount, 2, '.', ''),
                'currency'              => 'BDT',
                'intent'                => 'sale',
                'merchantInvoiceNumber' => $invoiceNumber ?? ('INV-' . Str::upper(Str::random(8))),
            ]);

        $this->log('create-payment', $response);

        return $response->json() ?? ['error' => 'Empty response from bKash'];
    }

    /**
     * Execute (confirm) a payment after the user approves it in
     * the bKash redirect flow.
     */
    public function executePayment(string $paymentId): array
    {
        $token = $this->getToken();

        if (!$token) {
            return ['error' => 'Could not obtain bKash token'];
        }

        $response = Http::withHeaders($this->authHeaders($token))
            ->post("{$this->baseUrl}/checkout/execute", [
                'paymentID' => $paymentId,
            ]);

        $this->log('execute-payment', $response);

        return $response->json() ?? ['error' => 'Empty response from bKash'];
    }

    /**
     * Query the status of a transaction by paymentID.
     */
    public function queryPayment(string $paymentId): array
    {
        $token = $this->getToken();

        if (!$token) {
            return ['error' => 'Could not obtain bKash token'];
        }

        $response = Http::withHeaders($this->authHeaders($token))
            ->post("{$this->baseUrl}/checkout/payment/status", [
                'paymentID' => $paymentId,
            ]);

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
        Log::info("bKash [{$step}]", [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);
    }
}
