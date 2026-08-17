<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broker extends BaseModel
{
    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'address',
        'city',
        'zip_code',
        'logo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
