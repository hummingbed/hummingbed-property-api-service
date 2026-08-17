<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewingAppointment extends BaseModel
{
    protected $fillable = ['user_id', 'property_id', 'scheduled_at', 'status', 'notes'];

    protected $casts = ['scheduled_at' => 'datetime'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
