<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppliancePreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'watts',
        'surge_multiplier',
        'default_hours',
        'icon',
        'is_resistive',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'watts' => 'integer',
        'surge_multiplier' => 'float',
        'default_hours' => 'float',
        'is_resistive' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ApplianceCategory::class, 'category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
