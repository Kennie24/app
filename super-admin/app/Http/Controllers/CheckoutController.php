<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Purchase;
use App\Services\Payments\AirtelMoneyService;
use App\Services\Payments\MtnMomoService;
use App\Services\Payments\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly StripePaymentService $stripe,
        private readonly MtnMomoService $mtn,
        private readonly AirtelMoneyService $airtel,
    ) {}

    /**
     * Begin a checkout: validate, create a Purchase row, kick off the provider call.
     */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_key' => ['required', 'string'],
            'method'    => ['required', Rule::in(['mtn_momo', 'airtel_money', 'card', 'paypal'])],
            'msisdn'    => ['nullable', 'string', 'regex:/^\+[1-9]\d{7,14}$/'],
            'email'     => ['nullable', 'email', 'max:255'],
        ]);

        $asset = Asset::with('user:id,is_artist')
            ->where('status', 'live')
            ->whereHas('user', fn ($q) => $q->where('is_artist', true))
            ->where(function ($q) use ($data) {
                $q->where('id', is_numeric($data['asset_key']) ? (int) $data['asset_key'] : 0)
                  ->orWhere('slug', $data['asset_key']);
            })
            ->firstOrFail();

        if (in_array($data['method'], ['mtn_momo', 'airtel_money']) && empty($data['msisdn'])) {
            return response()->json(['message' => 'Mobile money number is required.'], 422);
        }

        $user = $request->user();

        $purchase = Purchase::create([
            'reference' => 'SR-'.Str::upper(Str::random(12)),
            'user_id'   => $user?->id,
            'asset_id'  => $asset->id,
            'method'    => $data['method'],
            'status'    => Purchase::STATUS_PENDING,
            'amount'    => $asset->price,
            'currency'  => match ($data['method']) {
                'mtn_momo'      => config('services.mtn_momo.currency'),
                'airtel_money'  => config('services.airtel_money.currency'),
                default         => strtoupper(config('services.stripe.currency')),
            },
            'msisdn'    => $data['msisdn'] ?? null,
            'email'     => $data['email'] ?? $user?->email,
        ]);

        $providerResult = match ($data['method']) {
            'card', 'paypal'    => $this->stripe->initiate($purchase),
            'mtn_momo'          => $this->mtn->initiate($purchase),
            'airtel_money'      => $this->airtel->initiate($purchase),
        };

        $purchase->fill([
            'provider'      => match ($data['method']) {
                'card', 'paypal' => 'stripe',
                'mtn_momo'        => 'mtn',
                'airtel_money'    => 'airtel',
            },
            'provider_ref'  => $providerResult['provider_ref'] ?? null,
            'client_secret' => $providerResult['client_secret'] ?? null,
            'status'        => $providerResult['status'] ?? Purchase::STATUS_PROCESSING,
            'metadata'      => $providerResult['metadata'] ?? null,
        ])->save();

        return response()->json([
            'purchase' => $this->payload($purchase),
            'sandbox'  => $this->isSandbox($data['method']),
        ], 201);
    }

    /**
     * Poll the current status of a purchase.
     */
    public function status(string $reference): JsonResponse
    {
        $purchase = Purchase::where('reference', $reference)->firstOrFail();

        if (! $purchase->isFinal()) {
            $newStatus = match ($purchase->provider) {
                'stripe' => $this->stripe->refresh($purchase),
                'mtn'    => $this->mtn->refresh($purchase),
                'airtel' => $this->airtel->refresh($purchase),
                default  => $purchase->status,
            };

            if ($newStatus !== $purchase->status) {
                $purchase->status = $newStatus;
                if ($newStatus === Purchase::STATUS_SUCCEEDED) {
                    $purchase->completed_at = now();
                }
                $purchase->save();
            }
        }

        return response()->json(['purchase' => $this->payload($purchase)]);
    }

    /**
     * Stripe webhook (Card / PayPal).
     */
    public function stripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $check = $this->stripe->verifyWebhook($payload, $signature);

        if (! ($check['ok'] ?? false)) {
            return response()->json(['ok' => false], 400);
        }

        $event = $check['event'];
        $intent = $event->data->object ?? null;

        if ($intent && $purchase = Purchase::where('provider_ref', $intent->id)->first()) {
            $newStatus = match ($intent->status ?? '') {
                'succeeded' => Purchase::STATUS_SUCCEEDED,
                'canceled'  => Purchase::STATUS_CANCELLED,
                'requires_payment_method', 'requires_action' => Purchase::STATUS_PROCESSING,
                default     => $purchase->status,
            };

            $purchase->status = $newStatus;
            if ($newStatus === Purchase::STATUS_SUCCEEDED) {
                $purchase->completed_at = now();
            }
            $purchase->save();
        }

        return response()->json(['ok' => true]);
    }

    /**
     * MTN MoMo callback (status delivered to X-Callback-Url).
     */
    public function mtnWebhook(Request $request): JsonResponse
    {
        $ref = $request->input('referenceId') ?: $request->input('externalId');
        if (! $ref) {
            return response()->json(['ok' => false], 400);
        }
        $purchase = Purchase::where('provider_ref', $ref)->orWhere('reference', $ref)->first();
        if (! $purchase) {
            return response()->json(['ok' => false], 404);
        }

        $purchase->status = match (strtoupper((string) $request->input('status'))) {
            'SUCCESSFUL' => Purchase::STATUS_SUCCEEDED,
            'FAILED'     => Purchase::STATUS_FAILED,
            default      => $purchase->status,
        };
        if ($purchase->status === Purchase::STATUS_SUCCEEDED) {
            $purchase->completed_at = now();
        }
        $purchase->failure_reason = $request->input('reason');
        $purchase->save();

        return response()->json(['ok' => true]);
    }

    /**
     * Airtel Money callback.
     */
    public function airtelWebhook(Request $request): JsonResponse
    {
        $ref = $request->input('transaction.id') ?: $request->input('id');
        if (! $ref) {
            return response()->json(['ok' => false], 400);
        }
        $purchase = Purchase::where('provider_ref', $ref)->orWhere('reference', $ref)->first();
        if (! $purchase) {
            return response()->json(['ok' => false], 404);
        }

        $status = strtoupper((string) ($request->input('transaction.status_code') ?: $request->input('status')));
        $purchase->status = match ($status) {
            'TS', 'SUCCESS' => Purchase::STATUS_SUCCEEDED,
            'TF', 'FAILED'  => Purchase::STATUS_FAILED,
            default         => $purchase->status,
        };
        if ($purchase->status === Purchase::STATUS_SUCCEEDED) {
            $purchase->completed_at = now();
        }
        $purchase->save();

        return response()->json(['ok' => true]);
    }

    private function payload(Purchase $purchase): array
    {
        return [
            'reference'     => $purchase->reference,
            'status'        => $purchase->status,
            'amount'        => number_format((float) $purchase->amount, 2, '.', ''),
            'currency'      => $purchase->currency,
            'method'        => $purchase->method,
            'provider'      => $purchase->provider,
            'provider_ref'  => $purchase->provider_ref,
            'client_secret' => $purchase->client_secret,
            'msisdn'        => $purchase->msisdn,
            'email'         => $purchase->email,
            'completed_at'  => optional($purchase->completed_at)?->toIso8601String(),
            'failure_reason'=> $purchase->failure_reason,
        ];
    }

    private function isSandbox(string $method): bool
    {
        return match ($method) {
            'card', 'paypal' => $this->stripe->sandbox(),
            'mtn_momo'        => $this->mtn->sandbox(),
            'airtel_money'    => $this->airtel->sandbox(),
        };
    }
}
