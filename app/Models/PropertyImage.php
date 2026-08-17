<?php

namespace App\Models;

class PropertyImage extends BaseModel
{
    protected $fillable = ['property_id', 'url', 'alt_text', 'sort_order', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean', 'sort_order' => 'integer'];
}
