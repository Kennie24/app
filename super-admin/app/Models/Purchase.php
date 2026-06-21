<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reference', 'user_id', 'asset_id', 'method', 'status',
    'amount', 'currency', 'msisdn', 'email',
    'provider', 'provider_ref', 'client_secret',
    'metadata', 'failure_reason', 'completed_at',
])]
class Purchase extends Model
{
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCEEDED  = 'succeeded';
    public const STATUS_FAILED     = 'failed';
    public const STATUS_CANCELLED  = 'cancelled';

    protected function casts(): array
    {
        return [
            'amount'        => 'decimal:2',
            'metadata'      => 'array',
            'completed_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [self::STATUS_SUCCEEDED, self::STATUS_FAILED, self::STATUS_CANCELLED], true);
    }
}
