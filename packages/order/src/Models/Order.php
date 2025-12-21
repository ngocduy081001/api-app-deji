<?php

namespace Vendor\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'subtotal',
        'tax',
        'shipping_fee',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'appointment_date',
        'appointment_time',
        'appointment_note',
        'appointment_status',
        'notes',
        'metadata',

        'district',
        'city',
        'province',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Order status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    /**
     * Payment status constants
     */
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_SUCCESS = 'success';

    /**
     * Appointment status constants
     */
    const APPOINTMENT_STATUS_PENDING = 'pending';
    const APPOINTMENT_STATUS_CONFIRMED = 'confirmed';
    const APPOINTMENT_STATUS_COMPLETED = 'completed';
    const APPOINTMENT_STATUS_CANCELLED = 'cancelled';

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Vendor\Customer\Models\Customer::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\Vendor\Product\Models\Product::class, 'order_items', 'order_id', 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by appointment status.
     */
    public function scopeByAppointmentStatus($query, string $status)
    {
        return $query->where('appointment_status', $status);
    }

    /**
     * Scope a query to filter by appointment date.
     */
    public function scopeByAppointmentDate($query, $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    /**
     * Scope a query to get orders with appointments.
     */
    public function scopeWithAppointments($query)
    {
        return $query->whereNotNull('appointment_date');
    }

    /**
     * Check if order has appointment
     */
    public function hasAppointment(): bool
    {
        return !is_null($this->appointment_date);
    }

    /**
     * Get full appointment datetime
     */
    public function getAppointmentDateTimeAttribute(): ?\Carbon\Carbon
    {
        if (!$this->appointment_date) {
            return null;
        }

        $date = \Carbon\Carbon::parse($this->appointment_date);

        if ($this->appointment_time) {
            $time = \Carbon\Carbon::parse($this->appointment_time);
            $date->setTime($time->hour, $time->minute, $time->second);
        }

        return $date;
    }
}
