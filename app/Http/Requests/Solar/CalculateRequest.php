<?php

namespace App\Http\Requests\Solar;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appliances' => ['required', 'array', 'min:1', 'max:40'],
            'appliances.*.name' => ['required', 'string', 'max:80'],
            'appliances.*.watts' => ['required', 'numeric', 'min:1', 'max:10000'],
            'appliances.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'appliances.*.hours' => ['required', 'numeric', 'min:0.1', 'max:24'],
            'appliances.*.surge' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'appliances.*.is_resistive' => ['nullable', 'boolean'],
            'appliances.*.preset_id' => ['nullable', 'exists:appliance_presets,id'],
            'backup_hours' => ['required', 'numeric', 'min:1', 'max:24'],
            'goal' => ['required', 'in:backup,full_solar'],
            'battery_type' => ['required', 'in:lithium,tubular'],
            'zone' => ['required', 'in:north_west,north_east,north_central,south_west,south_east,south_south'],
            'days_of_autonomy' => ['nullable', 'integer', 'min:1', 'max:3'],
            'panel_watts' => ['nullable', 'in:400,450,550'],
            'diversity_factor' => ['nullable', 'numeric', 'min:0.5', 'max:1.0'],
            'custom_peak_sun_hours' => ['nullable', 'numeric', 'min:1.0', 'max:10.0'],
        ];
    }
}
