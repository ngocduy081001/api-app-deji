<?php

namespace Vendor\Warranty\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warranty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'warranty_code',
        'status',
        'active_date',
        'time_expired',
        'month',
        'customer_id',
        'qr_path',
        'printed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'customer_id' => 'integer',
        'month' => 'integer',
        // 'active_date' => 'date',
        // 'time_expired' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'claim_url',
    ];

    /**
     * Get the product that owns the warranty.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\Vendor\Product\Models\Product::class);
    }

    /**
     * Get the customer that owns the warranty.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Vendor\Customer\Models\Customer::class, 'customer_id');
    }

    /**
     * Scope a query to only include active warranties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('time_expired')
                    ->orWhere('time_expired', '>', now());
            });
    }

    /**
     * Scope a query to only include expired warranties.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->whereNotNull('time_expired')
                    ->where('time_expired', '<=', now());
            });
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by product.
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to filter by customer.
     */
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Check if warranty is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active'
            && ($this->time_expired === null || $this->time_expired > now());
    }

    /**
     * Check if warranty is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'expired'
            || ($this->time_expired !== null && $this->time_expired <= now());
    }

    /**
     * Calculate expiration date based on active_date and month.
     */
    public function calculateExpirationDate(): ?\DateTime
    {
        if (!$this->active_date || !$this->month) {
            return null;
        }

        $expirationDate = clone $this->active_date;
        $expirationDate->modify("+{$this->month} months");

        return $expirationDate;
    }

    /**
     * Activate warranty.
     */
    public function activate(?\DateTime $activeDate = null): void
    {
        $this->active_date = $activeDate ?? now();
        $this->status = 'active';

        // Calculate expiration date if month is set
        if ($this->month) {
            $this->time_expired = $this->calculateExpirationDate();
        }

        $this->save();
    }

    /**
     * Expire warranty.
     */
    public function expire(): void
    {
        $this->status = 'expired';
        $this->save();
    }

    /**
     * Get claim URL attribute.
     */
    public function getClaimUrlAttribute(): ?string
    {
        if (!$this->warranty_code) {
            return null;
        }

        try {
            return route('warranty.claim.show', ['code' => $this->warranty_code], absolute: true);
        } catch (\Throwable $th) {
            // Route may not be registered during some CLI operations (e.g. before routes are cached).
            return null;
        }
    }
}
