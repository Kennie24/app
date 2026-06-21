<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'phone', 'avatar_path', 'password', 'has_password', 'is_super_admin', 'is_artist', 'artist_name'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'has_password' => 'boolean',
            'is_super_admin' => 'boolean',
            'is_artist' => 'boolean',
        ];
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->avatar_path) {
                return null;
            }

            if (Str::startsWith($this->avatar_path, ['http://', 'https://', '//', 'data:'])) {
                return $this->avatar_path;
            }

            return '/storage/'.ltrim($this->avatar_path, '/');
        });
    }
}
