<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'state_id',
        'city',
        'slug',
        'headline',
        'body',
        'services_offered',
        'lat',
        'lng',
        'is_published',
    ];

    protected $casts = [
        'services_offered' => 'array',
        'is_published' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
