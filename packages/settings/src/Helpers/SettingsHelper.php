<?php

namespace Vendor\Settings\Helpers;

use Illuminate\Support\Arr;
use Vendor\Settings\Models\Setting;

class SettingsHelper
{
    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $configRoot = config('settings.loader.config_key', 'site');
        $flat = config("{$configRoot}.flat");

        if (is_array($flat) && Arr::exists($flat, $key)) {
            return $flat[$key];
        }

        return Setting::getValue($key, $default);
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, string $type = 'text', string $group = 'general'): Setting
    {
        return Setting::setValue($key, $value, $type, $group);
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group)
    {
        return Setting::getByGroup($group);
    }

    /**
     * Get settings as array (key => value)
     */
    public static function getArray(string $group = null): array
    {
        $query = Setting::query();

        if ($group) {
            $query->byGroup($group);
        }

        return $query->get()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->value];
        })->toArray();
    }

    /**
     * Get multiple settings by keys
     */
    public static function getMultiple(array $keys): array
    {
        $settings = Setting::whereIn('key', $keys)->get();

        return $settings->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->value];
        })->toArray();
    }

    /**
     * Check if setting exists
     */
    public static function has(string $key): bool
    {
        return Setting::where('key', $key)->exists();
    }
}
