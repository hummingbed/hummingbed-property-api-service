<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class PropertyCharacteristic extends BaseModel
{
    const NAIRA_SYMBOL = "₦";
    protected $fillable = [
        "property_id",
        "price",
        "bedrooms",
        "bathrooms",
        "square_feet",
        "price_square_feet",
        "property_type",
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public function getPriceAttribute($value)
    {
        return self::NAIRA_SYMBOL . number_format($value, 2);
    }
}
