<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'artist', 'release_type', 'slug', 'price', 'redemption_limit',
        'redemptions', 'status', 'cover_path', 'description',
    ];

    protected function casts(): array
    {
        return [
            'price'            => 'decimal:2',
            'redemption_limit' => 'integer',
            'redemptions'      => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Asset $asset) {
            if (empty($asset->slug)) {
                $base = Str::slug($asset->title.'-'.$asset->artist);
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->where('id', '!=', $asset->id)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $asset->slug = $slug;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(AssetTrack::class)->orderBy('position');
    }

    public function coverUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->cover_path) return null;
            if (Str::startsWith($this->cover_path, ['http://', 'https://', '//', 'data:'])) {
                return $this->cover_path;
            }
            // Root-relative so the image resolves on any host (localhost,
            // Herd, prod) — Storage::url() would bake in APP_URL and break
            // when the actual host differs.
            return '/storage/'.ltrim($this->cover_path, '/');
        });
    }

    public function progressPercent(): Attribute
    {
        return Attribute::get(function () {
            if ($this->redemption_limit <= 0) return 0;
            return min(100, (int) round(($this->redemptions / $this->redemption_limit) * 100));
        });
    }

    public function statusLabel(): Attribute
    {
        return Attribute::get(fn () => Str::title($this->status));
    }

    public function priceFormatted(): Attribute
    {
        return Attribute::get(fn () => '$'.number_format((float) $this->price, 2));
    }
}
