<?php

namespace Vendor\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Vendor\Product\Database\Factories\AttributeFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'is_required',
        'is_visible',
        'is_filterable',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
        'is_filterable' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Valid attribute types.
     */
    const TYPE_SELECT = 'select';
    const TYPE_COLOR = 'color';
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });

        static::updating(function ($attribute) {
            if ($attribute->isDirty('name') && empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    /**
     * Get the attribute values for the attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->orderBy('sort_order');
    }

    /**
     * Get active attribute values.
     */
    public function activeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Scope a query to only include visible attributes.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to only include filterable attributes.
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope a query to only include required attributes.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Get attribute types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SELECT => 'Lựa chọn',
            self::TYPE_COLOR => 'Màu sắc',
            self::TYPE_TEXT => 'Văn bản',
            self::TYPE_NUMBER => 'Số',
        ];
    }

    /**
     * Check if attribute type is valid.
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, [
            self::TYPE_SELECT,
            self::TYPE_COLOR,
            self::TYPE_TEXT,
            self::TYPE_NUMBER,
        ]);
    }
}

