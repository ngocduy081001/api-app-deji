<?php

namespace Vendor\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'menu_group_id',
        'type',
        'category_id',
        'page_id',
        'name',
        'slug',
        'url',
        'route',
        'icon',
        'parent_id',
        'order',
        'target',
        'is_active',
        'location',
        'attributes',
        'link',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'menu_group_id' => 'integer',
        'category_id' => 'integer',
        'page_id' => 'integer',
        'parent_id' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
        'attributes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    protected static function booted(): void {}

    /**
     * Get the menu group.
     */
    public function menuGroup(): BelongsTo
    {
        return $this->belongsTo(MenuGroup::class, 'menu_group_id');
    }

    /**
     * Get the category (if type is category).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\Vendor\Product\Models\ProductCategory::class, 'category_id');
    }

    /**
     * Get the page (if type is page).
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * Get the parent menu.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get the child menus.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get active child menus.
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Scope a query to only include active menus.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by menu group.
     */
    public function scopeByMenuGroup($query, int $menuGroupId)
    {
        return $query->where('menu_group_id', $menuGroupId);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include root menus (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the full URL for the menu item.
     */
    public function getFullUrlAttribute(): ?string
    {
        // If type is category, use category URL
        if ($this->type === 'category' && $this->category) {
            return route('category.show', ['slug' => $this->category->slug]);
        }

        // Custom URL
        if ($this->url) {
            return $this->url;
        }

        if ($this->route) {
            return route($this->route);
        }

        // Default to slug if available
        if ($this->slug) {
            return '/' . $this->slug;
        }

        return '#';
    }

    /**
     * Get the display name for the menu item (from category if applicable).
     */
    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        if ($this->type === 'category' && $this->category) {
            return $this->category->name;
        }

        return '';
    }
}
