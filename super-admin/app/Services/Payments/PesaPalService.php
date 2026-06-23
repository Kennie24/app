<?php

namespace App\Services\Payments;

use App\Models\Purchase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesaPalService
{
    public function __construct(private readonly array $config) {}

    public function sandbox(): bool
    {
        return str_contains($this->config['base_url'], 'cybqa');
    }

    // ── Auth ────────────────────────────────────────────────────────────────

    private function token(): string
    {
        return Cache::remember('pesapal_token', 55 * 60, function () {
            $response = Http::acceptJson()
                ->post("{$this->config['base_url']}/Auth/RequestToken", [
                    'consumer_key'    => $this->config['consumer_key'],
                    'consumer_secret' => $this->config['consumer_secret'],
                ]);

            if (! $response->successful()) {
                Log::error('PesaPal auth failed', ['body' => $response->body()]);
                throw new \RuntimeException('PesaPal authentication failed.');
            }

            return $response->json('token');
        });
    }

    // ── IPN registration ────────────────────────────────────────────────────

    public function registerIpn(string $ipnUrl): string
    {
        $response = Http::acceptJson()
            ->withToken($this->token())
            ->post("{$this->config['base_url']}/URLSetup/RegisterIPN", [
                'url'                   => $ipnUrl,
                'ipn_notification_type' => 'POST',
            ]);

        if (! $response->successful()) {
            Log::error('PesaPal IPN registration failed', ['body' => $response->body()]);
            throw new \RuntimeException('PesaPal IPN registration failed.');
        }

        return (string) $response->json('ipn_id');
    }

    // ── Initiate payment ────────────────────────────────────────────────────

    /**
     * @return array{provider_ref:string, redirect_url:string, status:string, metadata:array}
     */
    public function initiate(Purchase $purchase, string $callbackUrl): array
    {
        $response = Http::acceptJson()
            ->withToken($this->token())
            ->post("{$this->config['base_url']}/Transactions/SubmitOrderRequest", [
                'id'               => $purchase->reference,
                'currency'         => $purchase->currency,
                'amount'           => (float) $purchase->amount,
                'description'      => "Order {$purchase->reference}",
                'callback_url'     => $callbackUrl,
                'cancellation_url' => $callbackUrl,
                'notification_id'  => $this->config['ipn_id'],
                'billing_address'  => [
                    'email_address' => $purchase->email ?? '',
                    'phone_number'  => $purchase->msisdn ?? '',
                    'country_code'  => 'UG',
                    'first_name'    => '',
                    'last_name'     => '',
                ],
            ]);

        if (! $response->successful() || ! $response->json('redirect_url')) {
            Log::error('PesaPal SubmitOrderRequest failed', [
                'ref'  => $purchase->reference,
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Could not initiate PesaPal payment.');
        }

        return [
            'provider_ref' => $response->json('order_tracking_id'),
            'redirect_url' => $response->json('redirect_url'),
            'status'       => Purchase::STATUS_PENDING,
            'metadata'     => ['order_tracking_id' => $response->json('order_tracking_id')],
        ];
    }

    // ── Status check ────────────────────────────────────────────────────────

    public function refresh(Purchase $purchase): string
    {
        if (empty($purchase->provider_ref)) {
            return $purchase->status;
        }

        $response = Http::acceptJson()
            ->withToken($this->token())
            ->get("{$this->config['base_url']}/Transactions/GetTransactionStatus", [
                'orderTrackingId' => $purchase->provider_ref,
            ]);

        if (! $response->successful()) {
            Log::warning('PesaPal status check failed', ['ref' => $purchase->provider_ref, 'body' => $response->body()]);
            return $purchase->status;
        }

        return match ($response->json('payment_status_description')) {
            'Completed'                        => Purchase::STATUS_SUCCEEDED,
            'Failed', 'Invalid', 'Reversed'   => Purchase::STATUS_FAILED,
            default                            => Purchase::STATUS_PROCESSING,
        };
    }
}
