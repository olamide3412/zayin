<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class SolarCalcSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'constants',
        'inverter_catalog',
        'controller_ratings',
        'psh_by_zone',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'version' => 'integer',
        'constants' => 'array',
        'inverter_catalog' => 'array',
        'controller_ratings' => 'array',
        'psh_by_zone' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('solar_calc_active_settings');
        });
        static::deleted(function () {
            Cache::forget('solar_calc_active_settings');
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getActive(): self
    {
        return Cache::remember('solar_calc_active_settings', 3600, function () {
            $setting = static::where('is_active', true)->latest('version')->first();

            if (!$setting) {
                $setting = static::createDefault();
            }

            return $setting;
        });
    }

    public static function createDefault(): self
    {
        return static::create([
            'version' => 1,
            'is_active' => true,
            'constants' => [
                'safety_factor' => 1.25,
                'power_factor' => 0.80,
                'inverter_efficiency' => 0.90,
                'system_derate' => 0.75,
                'dod_lithium' => 0.80,
                'dod_tubular' => 0.50,
                'surge_capacity_factor' => 2.0,
                'default_panel_watts' => 550,
                'panel_watts_options' => [400, 450, 550],
                'diversity_factor' => 1.0,
                'days_of_autonomy' => 1,
                'lithium_unit_wh' => 5120, // 48V 100Ah or 2x 24V
                'tubular_unit_wh' => 2400, // 12V 200Ah
            ],
            'inverter_catalog' => [
                ['kva' => 1.0, 'voltage' => 12],
                ['kva' => 1.5, 'voltage' => 12],
                ['kva' => 2.5, 'voltage' => 24],
                ['kva' => 3.5, 'voltage' => 24],
                ['kva' => 5.0, 'voltage' => 48],
                ['kva' => 7.5, 'voltage' => 48],
                ['kva' => 10.0, 'voltage' => 48],
            ],
            'controller_ratings' => [30, 40, 50, 60, 80, 100, 120],
            'psh_by_zone' => [
                'north_west' => 5.5,
                'north_east' => 5.5,
                'north_central' => 5.0,
                'south_west' => 4.5,
                'south_east' => 4.2,
                'south_south' => 4.2,
            ],
        ]);
    }
}
