<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Purchase;
use App\Services\Payments\PesaPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(private readonly PesaPalService $pesapal) {}

    /**
     * Begin a PesaPal checkout.
     */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_key' => ['required', 'string'],
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

        $user = $request->user();

        $purchase = Purchase::create([
            'reference' => 'TTM-' . Str::upper(Str::random(12)),
            'user_id'   => $user?->id,
            'asset_id'  => $asset->id,
            'method'    => 'pesapal',
            'status'    => Purchase::STATUS_PENDING,
            'amount'    => $asset->price,
            'currency'  => config('services.pesapal.currency', 'UGX'),
            'email'     => $data['email'] ?? $user?->email,
        ]);

        $callbackUrl = config('services.pesapal.callback_url');

        $result = $this->pesapal->initiate($purchase, $callbackUrl);

        $purchase->fill([
            'provider'     => 'pesapal',
            'provider_ref' => $result['provider_ref'],
            'status'       => $result['status'],
            'metadata'     => $result['metadata'],
        ])->save();

        return response()->json([
            'purchase'     => $this->payload($purchase),
            'redirect_url' => $result['redirect_url'],
            'sandbox'      => $this->pesapal->sandbox(),
        ], 201);
    }

    /**
     * Poll the current status of a purchase.
     */
    public function status(string $reference): JsonResponse
    {
        $purchase = Purchase::where('reference', $reference)->firstOrFail();

        if (! $purchase->isFinal()) {
            $newStatus = $this->pesapal->refresh($purchase);

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
     * PesaPal IPN webhook — called server-to-server by PesaPal.
     */
    public function pesapalIpn(Request $request): JsonResponse
    {
        $trackingId        = $request->input('OrderTrackingId');
        $merchantReference = $request->input('OrderMerchantReference');
        $notificationType  = $request->input('OrderNotificationType');

        Log::info('PesaPal IPN received', compact('trackingId', 'merchantReference', 'notificationType'));

        if (! $trackingId || ! $merchantReference) {
            return response()->json(['orderNotificationType' => 'IPNCHANGE', 'orderTrackingId' => $trackingId, 'orderMerchantReference' => $merchantReference, 'status' => 200]);
        }

        $purchase = Purchase::where('reference', $merchantReference)
            ->orWhere('provider_ref', $trackingId)
            ->first();

        if ($purchase && ! $purchase->isFinal()) {
            $newStatus = $this->pesapal->refresh($purchase);
            if ($newStatus !== $purchase->status) {
                $purchase->status = $newStatus;
                if ($newStatus === Purchase::STATUS_SUCCEEDED) {
                    $purchase->completed_at = now();
                }
                $purchase->save();
            }
        }

        // PesaPal requires this exact response format.
        return response()->json([
            'orderNotificationType'  => 'IPNCHANGE',
            'orderTrackingId'        => $trackingId,
            'orderMerchantReference' => $merchantReference,
            'status'                 => 200,
        ]);
    }

    private function payload(Purchase $purchase): array
    {
        return [
            'reference'      => $purchase->reference,
            'status'         => $purchase->status,
            'amount'         => number_format((float) $purchase->amount, 2, '.', ''),
            'currency'       => $purchase->currency,
            'method'         => $purchase->method,
            'provider'       => $purchase->provider,
            'provider_ref'   => $purchase->provider_ref,
            'email'          => $purchase->email,
            'completed_at'   => optional($purchase->completed_at)?->toIso8601String(),
            'failure_reason' => $purchase->failure_reason,
        ];
    }
}
