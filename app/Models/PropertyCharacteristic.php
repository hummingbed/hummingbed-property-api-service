<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyCharacteristic extends BaseModel
{
    protected $fillable = [
        'property_id',
        'price',
        'bedrooms',
        'bathrooms',
        'square_feet',
        'price_square_feet',
        'property_type',
        'status',
    ];

    protected $casts = [
        'price' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'square_feet' => 'integer',
        'price_square_feet' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
