<?php

namespace Vendor\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\Customer\Models\Customer;

class CustomerAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'email',
        'address',
        'province',
        'district',
        'ward',
        'note',
        'is_default',
        'last_used_at',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the address.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope to get addresses by phone number.
     */
    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    /**
     * Scope to get default address.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to order by last used.
     */
    public function scopeOrderByLastUsed($query)
    {
        return $query->orderBy('last_used_at', 'desc')
            ->orderBy('created_at', 'desc');
    }
}

