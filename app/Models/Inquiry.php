<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends BaseModel
{
    protected $fillable = ['user_id', 'property_id', 'name', 'email', 'phone_number', 'message', 'status'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
