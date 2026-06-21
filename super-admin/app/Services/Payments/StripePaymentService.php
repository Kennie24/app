<?php

namespace App\Services\Payments;

use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\StripeClient;

/**
 * Stripe-backed payment service for Card and PayPal.
 *
 * In sandbox mode (no STRIPE_SECRET_KEY configured), this returns a simulated
 * PaymentIntent so the rest of the flow can be tested end-to-end without keys.
 */
class StripePaymentService
{
    public function __construct(private readonly array $config) {}

    public function sandbox(): bool
    {
        return empty($this->config['secret']);
    }

    public function client(): ?StripeClient
    {
        return $this->sandbox() ? null : new StripeClient($this->config['secret']);
    }

    /**
     * Begin a Card or PayPal payment.
     *
     * @return array{provider_ref:?string, client_secret:?string, status:string, metadata:array}
     */
    public function initiate(Purchase $purchase): array
    {
        $methodMap = ['card' => 'card', 'paypal' => 'paypal'];
        $methodType = $methodMap[$purchase->method] ?? 'card';

        if ($this->sandbox()) {
            return [
                'provider_ref'  => 'pi_sandbox_'.Str::random(16),
                'client_secret' => 'pi_sandbox_secret_'.Str::random(24),
                'status'        => Purchase::STATUS_PROCESSING,
                'metadata'      => ['sandbox' => true, 'method' => $methodType],
            ];
        }

        $intent = $this->client()->paymentIntents->create([
            'amount'   => (int) round(((float) $purchase->amount) * 100),
            'currency' => strtolower($purchase->currency ?: $this->config['currency']),
            'payment_method_types' => [$methodType],
            'metadata' => [
                'purchase_ref' => $purchase->reference,
                'asset_id'     => (string) $purchase->asset_id,
                'user_id'      => (string) ($purchase->user_id ?? ''),
            ],
            'receipt_email' => $purchase->email,
        ]);

        return [
            'provider_ref'  => $intent->id,
            'client_secret' => $intent->client_secret,
            'status'        => Purchase::STATUS_PROCESSING,
            'metadata'      => ['payment_method_type' => $methodType],
        ];
    }

    /**
     * Map a Stripe status string to our Purchase status.
     */
    public function refresh(Purchase $purchase): string
    {
        if ($this->sandbox()) {
            // For sandbox we treat repeated polls as eventual success.
            return Purchase::STATUS_SUCCEEDED;
        }

        $intent = $this->client()->paymentIntents->retrieve($purchase->provider_ref);

        return match ($intent->status) {
            'succeeded'                        => Purchase::STATUS_SUCCEEDED,
            'canceled'                         => Purchase::STATUS_CANCELLED,
            'requires_payment_method', 'requires_confirmation', 'requires_action', 'processing' => Purchase::STATUS_PROCESSING,
            default                            => Purchase::STATUS_FAILED,
        };
    }

    public function verifyWebhook(string $payload, ?string $signature): array
    {
        if ($this->sandbox() || empty($this->config['webhook_secret']) || empty($signature)) {
            return ['ok' => false, 'reason' => 'webhook not configured'];
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $this->config['webhook_secret']);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook verify failed', ['err' => $e->getMessage()]);
            return ['ok' => false, 'reason' => 'invalid signature'];
        }

        return ['ok' => true, 'event' => $event];
    }
}
