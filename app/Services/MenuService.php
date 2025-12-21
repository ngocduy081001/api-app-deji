<?php

namespace App\Services;

class MenuService
{
    protected static array $menus = [];
    protected static bool $sorted = false;

    /**
     * Register a menu item
     */
    public static function register(array $item): void
    {
        $defaults = [
            'title' => '',
            'route' => null,
            'url' => null,
            'icon' => null,
            'order' => 100,
            'badge' => null,
            'badge_color' => 'gray',
            'active' => [],
            'permissions' => [],
            'children' => [],
            'group' => null,
        ];

        $item = array_merge($defaults, $item);

        // Merge defaults for children as well
        if (!empty($item['children'])) {
            foreach ($item['children'] as $index => $child) {
                $item['children'][$index] = array_merge($defaults, $child);
            }
        }

        // Generate unique key
        $key = $item['route'] ?? $item['url'] ?? md5($item['title']);

        static::$menus[$key] = $item;
        static::$sorted = false;
    }

    /**
     * Register multiple menu items
     */
    public static function registerMany(array $items): void
    {
        foreach ($items as $item) {
            static::register($item);
        }
    }

    /**
     * Get all registered menus
     */
    public static function all(): array
    {
        if (!static::$sorted) {
            uasort(static::$menus, fn($a, $b) => $a['order'] <=> $b['order']);
            static::$sorted = true;
        }

        return static::$menus;
    }

    /**
     * Get menus grouped by category
     */
    public static function grouped(): array
    {
        $menus = static::all();
        $grouped = [];

        foreach ($menus as $menu) {
            $group = $menu['group'] ?? 'default';

            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }

            $grouped[$group][] = $menu;
        }

        return $grouped;
    }

    /**
     * Check if menu item is active
     */
    public static function isActive(array $item): bool
    {
        $currentRoute = request()->route()?->getName();
        $currentUrl = request()->url();

        // Check route match
        if (!empty($item['route']) && $currentRoute === $item['route']) {
            return true;
        }

        // Check active patterns
        if (!empty($item['active'])) {
            foreach ($item['active'] as $pattern) {
                if ($currentRoute && str_contains($currentRoute, $pattern)) {
                    return true;
                }
            }
        }

        // Check URL match
        if (!empty($item['url']) && $currentUrl === $item['url']) {
            return true;
        }

        // Check children
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if (static::isActive($child)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Clear all registered menus
     */
    public static function clear(): void
    {
        static::$menus = [];
        static::$sorted = false;
    }
}
