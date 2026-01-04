<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Device extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'platform',
        'fcm_token',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the device.
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
     * Get the customer that owns the device
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Vendor\Customer\Models\Customer::class, 'user_id');
    }

    /**
     * Get the refresh tokens for the device.
     */
    public function refreshTokens(): HasMany
    {
        return $this->hasMany(RefreshToken::class);
    }
}
