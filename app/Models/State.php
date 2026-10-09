<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'code',
        'delivery_fee',
        'install_fee',
        'is_active',
    ];

    protected $casts = [
        'delivery_fee' => 'integer',
        'install_fee' => 'integer',
        'is_active' => 'boolean',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
