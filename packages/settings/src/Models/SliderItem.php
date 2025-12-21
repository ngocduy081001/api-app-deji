<?php

namespace Vendor\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class SliderItem extends Model
{
    protected $table = 'slider_items';
    protected $fillable = [
        'id',
        'slider_id',
        'image',
        'title',
        'image_mobile',
        'link',
        'order',
        'description'
    ];
    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the slider that owns the slider item.
     */
    public function slider()
    {
        return $this->belongsTo(Slider::class, 'slider_id');
    }
}
