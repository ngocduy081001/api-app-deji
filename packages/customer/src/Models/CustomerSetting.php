<?php

namespace Vendor\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Get the customer that owns the settings.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get a setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value.
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
    }

    /**
     * Set multiple settings.
     */
    public function setSettings(array $settings): void
    {
        $currentSettings = $this->settings ?? [];
        $this->settings = array_merge($currentSettings, $settings);
    }

    /**
     * Get all settings.
     */
    public function getAllSettings(): array
    {
        return $this->settings ?? [];
    }

    /**
     * Create or get settings for a customer with defaults.
     */
    public static function getOrCreate(int $customerId): self
    {
        $setting = self::firstOrNew(['customer_id' => $customerId]);
        
        if (!$setting->exists || empty($setting->settings)) {
            $defaults = [
                'push_notifications' => true,
                'email_notifications' => true,
            ];
            $setting->settings = $defaults;
            $setting->save();
        }

        return $setting;
    }
}
