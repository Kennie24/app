<?php

namespace App\Services\Payments;

use App\Models\Purchase;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * MTN MoMo Collections API (RequestToPay).
 *
 * Docs: https://momodeveloper.mtn.com/api-documentation/api-description/
 *
 * Sandbox mode (when MTN_MOMO_SUBSCRIPTION_KEY is missing) simulates a
 * pending → success transition so the UI can be exercised end-to-end.
 */
class MtnMomoService
{
    public function __construct(private readonly array $config) {}

    public function sandbox(): bool
    {
        return empty($this->config['subscription_key']) || empty($this->config['api_user']) || empty($this->config['api_key']);
    }

    public function initiate(Purchase $purchase): array
    {
        $referenceId = (string) Str::uuid();

        if ($this->sandbox()) {
            return [
                'provider_ref' => $referenceId,
                'status'       => Purchase::STATUS_PROCESSING,
                'metadata'     => ['sandbox' => true],
            ];
        }

        $token = $this->token();
        $resp = Http::withToken($token)
            ->withHeaders($this->commonHeaders($referenceId))
            ->post($this->config['base_url'].'/collection/v1_0/requesttopay', [
                'amount'       => number_format((float) $purchase->amount, 2, '.', ''),
                'currency'     => $purchase->currency ?: $this->config['currency'],
                'externalId'   => $purchase->reference,
                'payer'        => ['partyIdType' => 'MSISDN', 'partyId' => $this->normalize($purchase->msisdn)],
                'payerMessage' => 'SoundRedeem · '.optional($purchase->asset)->title,
                'payeeNote'    => $purchase->reference,
            ]);

        if (! $resp->successful()) {
            Log::warning('MTN MoMo initiate failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            return ['provider_ref' => $referenceId, 'status' => Purchase::STATUS_FAILED, 'metadata' => ['error' => $resp->body()]];
        }

        return ['provider_ref' => $referenceId, 'status' => Purchase::STATUS_PROCESSING, 'metadata' => []];
    }

    public function refresh(Purchase $purchase): string
    {
        if ($this->sandbox()) {
            // Sandbox: every poll after 5s flips to success.
            return $purchase->created_at?->diffInSeconds(now()) >= 5 ? Purchase::STATUS_SUCCEEDED : Purchase::STATUS_PROCESSING;
        }

        $token = $this->token();
        $resp = Http::withToken($token)
            ->withHeaders([
                'X-Target-Environment'      => $this->config['target_env'],
                'Ocp-Apim-Subscription-Key' => $this->config['subscription_key'],
            ])
            ->get($this->config['base_url'].'/collection/v1_0/requesttopay/'.$purchase->provider_ref);

        if (! $resp->successful()) {
            return Purchase::STATUS_PROCESSING;
        }

        return match (strtoupper($resp->json('status', ''))) {
            'SUCCESSFUL'    => Purchase::STATUS_SUCCEEDED,
            'FAILED'        => Purchase::STATUS_FAILED,
            'PENDING'       => Purchase::STATUS_PROCESSING,
            default         => Purchase::STATUS_PROCESSING,
        };
    }

    private function token(): string
    {
        return Cache::remember('mtn_momo_token', now()->addMinutes(50), function (): string {
            $resp = Http::withBasicAuth($this->config['api_user'], $this->config['api_key'])
                ->withHeaders(['Ocp-Apim-Subscription-Key' => $this->config['subscription_key']])
                ->post($this->config['base_url'].'/collection/token/');

            if (! $resp->successful()) {
                throw new \RuntimeException('Could not authenticate with MTN MoMo: '.$resp->body());
            }

            return (string) $resp->json('access_token');
        });
    }

    private function commonHeaders(string $referenceId): array
    {
        return [
            'X-Reference-Id'            => $referenceId,
            'X-Target-Environment'      => $this->config['target_env'],
            'Ocp-Apim-Subscription-Key' => $this->config['subscription_key'],
            'Content-Type'              => 'application/json',
            'X-Callback-Url'            => $this->config['callback_host'] ? rtrim($this->config['callback_host'], '/').'/webhooks/mtn' : null,
        ];
    }

    /** Strip leading + so the API gets a raw MSISDN. */
    private function normalize(?string $msisdn): string
    {
        return ltrim((string) $msisdn, '+');
    }
}
