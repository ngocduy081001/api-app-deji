<?php

namespace Vendor\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'location',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the menus for this group.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'menu_group_id')->orderBy('order');
    }

    /**
     * Get root menus (no parent) for this group.
     */
    public function rootMenus(): HasMany
    {
        return $this->menus()->whereNull('parent_id');
    }

    /**
     * Get active root menus for this group.
     */
    public function activeRootMenus(): HasMany
    {
        return $this->rootMenus()->where('is_active', true);
    }

    /**
     * Scope a query to only include active menu groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }
}

