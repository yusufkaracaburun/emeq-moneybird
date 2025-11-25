<?php

namespace Emeq\Moneybird\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property string|null $tenant_id
 * @property string $name
 * @property string $administration_id
 * @property string $access_token
 * @property string|null $refresh_token
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_active
 * @property array|null $metadata
 */
class MoneybirdConnection extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'name',
        'administration_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
        'metadata'   => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function isExpired(): bool
    {
        if (! $this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function needsRefresh(): bool
    {
        if (! $this->refresh_token) {
            return false;
        }

        if (! $this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast() || $this->expires_at->subMinutes(5)->isPast();
    }
}
