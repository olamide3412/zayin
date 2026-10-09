<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplianceCategory;
use App\Models\AppliancePreset;
use App\Models\SolarCalcSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class SolarCalcSettingController extends Controller
{
    public function index(): Response
    {
        $setting = SolarCalcSetting::getActive();
        $presets = AppliancePreset::with('category')->orderBy('sort_order')->get();
        $categories = ApplianceCategory::orderBy('sort_order')->get(['id', 'name']);

        return Inertia::render('Auth/SolarCalc/Index', [
            'constants' => $setting->constants ?? [],
            'inverter_catalog' => $setting->inverter_catalog ?? [],
            'controller_ratings' => $setting->controller_ratings ?? [],
            'psh_by_zone' => $setting->psh_by_zone ?? [],
            'presets' => $presets,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'constants' => 'required|array',
            'inverter_catalog' => 'required|array',
            'controller_ratings' => 'required|array',
            'psh_by_zone' => 'required|array',
        ]);

        $current = SolarCalcSetting::getActive();
        
        // Version bump and save
        $new = SolarCalcSetting::create([
            'version' => ($current->version ?? 1) + 1,
            'constants' => $validated['constants'],
            'inverter_catalog' => $validated['inverter_catalog'],
            'controller_ratings' => $validated['controller_ratings'],
            'psh_by_zone' => $validated['psh_by_zone'],
            'is_active' => true,
        ]);

        // Deactivate previous records
        SolarCalcSetting::where('id', '!=', $new->id)->update(['is_active' => false]);

        return redirect()->back()->with('message', 'Solar calculator engineering parameters updated successfully!');
    }

    public function updatePreset(Request $request, AppliancePreset $preset): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'watts' => 'required|integer|min:1',
            'surge_multiplier' => 'required|numeric|min:1',
            'default_hours' => 'required|numeric|min:0.1|max:24',
            'is_resistive' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $preset->update($validated);

        return redirect()->back()->with('message', "Preset {$preset->name} updated successfully!");
    }
}
