<?php

namespace App\Services\Payments;

use App\Models\Purchase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Airtel Money (Africa) - USSD push collection.
 *
 * Docs: https://developers.airtel.africa
 * Sandbox simulates a 5-second pending → success transition.
 */
class AirtelMoneyService
{
    public function __construct(private readonly array $config) {}

    public function sandbox(): bool
    {
        return empty($this->config['client_id']) || empty($this->config['client_secret']);
    }

    public function initiate(Purchase $purchase): array
    {
        $reference = 'SR-'.Str::upper(Str::random(10));

        if ($this->sandbox()) {
            return [
                'provider_ref' => $reference,
                'status'       => Purchase::STATUS_PROCESSING,
                'metadata'     => ['sandbox' => true],
            ];
        }

        $token = $this->token();
        $resp = Http::withToken($token)
            ->withHeaders([
                'X-Country'  => $this->config['country'],
                'X-Currency' => $this->config['currency'],
            ])
            ->post($this->config['base_url'].'/merchant/v1/payments/', [
                'reference'     => $purchase->reference,
                'subscriber'    => ['country' => $this->config['country'], 'currency' => $this->config['currency'], 'msisdn' => $this->normalize($purchase->msisdn)],
                'transaction'   => ['amount' => (int) round((float) $purchase->amount), 'country' => $this->config['country'], 'currency' => $this->config['currency'], 'id' => $reference],
            ]);

        if (! $resp->successful()) {
            Log::warning('Airtel initiate failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            return ['provider_ref' => $reference, 'status' => Purchase::STATUS_FAILED, 'metadata' => ['error' => $resp->body()]];
        }

        return ['provider_ref' => $reference, 'status' => Purchase::STATUS_PROCESSING, 'metadata' => $resp->json()];
    }

    public function refresh(Purchase $purchase): string
    {
        if ($this->sandbox()) {
            return $purchase->created_at?->diffInSeconds(now()) >= 5 ? Purchase::STATUS_SUCCEEDED : Purchase::STATUS_PROCESSING;
        }

        $token = $this->token();
        $resp = Http::withToken($token)
            ->withHeaders(['X-Country' => $this->config['country'], 'X-Currency' => $this->config['currency']])
            ->get($this->config['base_url'].'/standard/v1/payments/'.$purchase->provider_ref);

        if (! $resp->successful()) {
            return Purchase::STATUS_PROCESSING;
        }

        $code = strtoupper($resp->json('data.transaction.status', ''));

        return match ($code) {
            'TS', 'SUCCESS' => Purchase::STATUS_SUCCEEDED,
            'TF', 'FAILED'  => Purchase::STATUS_FAILED,
            'TIP', 'IN_PROGRESS', 'PROCESSING', 'PENDING' => Purchase::STATUS_PROCESSING,
            default         => Purchase::STATUS_PROCESSING,
        };
    }

    private function token(): string
    {
        return Cache::remember('airtel_money_token', now()->addMinutes(50), function (): string {
            $resp = Http::asJson()
                ->post($this->config['base_url'].'/auth/oauth2/token', [
                    'client_id'     => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'grant_type'    => 'client_credentials',
                ]);

            if (! $resp->successful()) {
                throw new \RuntimeException('Could not authenticate with Airtel Money: '.$resp->body());
            }

            return (string) $resp->json('access_token');
        });
    }

    private function normalize(?string $msisdn): string
    {
        return ltrim((string) $msisdn, '+');
    }
}
