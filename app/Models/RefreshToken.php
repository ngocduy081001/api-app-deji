<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RefreshToken extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'token_hash',
        'expires_at',
        'revoked_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Get the user that owns the refresh token.
     * For customer API, this returns Customer
     */
    public function user(): BelongsTo
    {
        // Try Customer first (for customer API)
        try {
            return $this->belongsTo(\Vendor\Customer\Models\Customer::class, 'user_id');
        } catch (\Exception $e) {
            // Fallback to User if Customer doesn't exist
            return $this->belongsTo(\App\Models\User::class, 'user_id');
        }
    }

    /**
     * Get the customer that owns the refresh token
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Vendor\Customer\Models\Customer::class, 'user_id');
    }

    /**
     * Get the device that owns the refresh token.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Check if the token is revoked.
     */
    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    /**
     * Check if the token is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the token is valid.
     */
    public function isValid(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }
}
