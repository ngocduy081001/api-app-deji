<?php

namespace Vendor\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        $flush = function () {
            static::flushCache();
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Get setting value by key.
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key.
     */
    public static function setValue(string $key, $value, string $type = 'text', string $group = 'general'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
            ]
        );

        static::flushCache();

        return $setting;
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup(string $group)
    {
        return static::where('group', $group)->orderBy('order')->get();
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Flush cached settings collection.
     */
    public static function flushCache(): void
    {
        if (!config('settings.cache.enabled', true)) {
            return;
        }

        Cache::forget(config('settings.cache.key', 'settings.cache.all'));
    }
}
