<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Show settings panel (Admin Only).
     */
    public function index()
    {
        $heroSlides = json_decode(Setting::get('hero_slides', '[]'), true);
        $cleaned = false;
        if (is_array($heroSlides)) {
            foreach ($heroSlides as &$slide) {
                if (isset($slide['value'])) {
                    // Normalize absolute localhost paths to root-relative paths
                    if (str_starts_with($slide['value'], 'http://localhost/storage/')) {
                        $slide['value'] = str_replace('http://localhost/storage/', '/storage/', $slide['value']);
                        $cleaned = true;
                    } elseif (str_starts_with($slide['value'], 'http://127.0.0.1:8000/storage/')) {
                        $slide['value'] = str_replace('http://127.0.0.1:8000/storage/', '/storage/', $slide['value']);
                        $cleaned = true;
                    }
                }
            }
            if ($cleaned) {
                Setting::set('hero_slides', json_encode($heroSlides));
            }
        }

        return Inertia::render('Auth/Settings', [
            'settings' => [
                'enable_appointments' => filter_var(Setting::get('enable_appointments', 'true'), FILTER_VALIDATE_BOOLEAN),
                'hero_slides' => $heroSlides,
                'about_mission' => Setting::get('about_mission', '<p>To provide Nigerian homes and enterprises with reliable solar power, high-speed Starlink satellite internet, and precision digital installations with unmatched engineering excellence.</p>'),
                'about_vision' => Setting::get('about_vision', '<p>To be Nigeria\'s most dependable turnkey engineering solutions provider for renewable energy and satellite telecommunications.</p>'),
                'about_founder_name' => Setting::get('about_founder_name', 'Engr. Zayin Lead'),
                'about_founder_title' => Setting::get('about_founder_title', 'Managing Director & Lead Systems Engineer'),
                'about_founder_bio'   => Setting::get('about_founder_bio', '<p>Headquartered in Abraka, Delta State, Zayin Technologies deploys certified field teams across Nigeria to solve energy poverty and high-latency connectivity with robust Tier-1 equipment.</p>'),
                'about_founder_image' => Setting::get('about_founder_image', ''),
                'news_layout_style'   => Setting::get('news_layout_style', 'hero_split'),
            ]
        ]);
    }

    /**
     * Update settings keys.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'enable_appointments' => 'required|boolean',
            'hero_slides'         => 'nullable|array',
            'about_mission'       => 'nullable|string',
            'about_vision'        => 'nullable|string',
            'about_founder_name'  => 'nullable|string|max:200',
            'about_founder_title' => 'nullable|string|max:200',
            'about_founder_bio'   => 'nullable|string',
            'about_founder_image' => 'nullable|string|max:500',
            'news_layout_style'   => 'nullable|in:hero_split,magazine_grid,editorial_list',
        ]);

        Setting::set('enable_appointments', $validated['enable_appointments'] ? 'true' : 'false');

        $heroSlides = [];
        if (isset($validated['hero_slides']) && is_array($validated['hero_slides'])) {
            foreach ($validated['hero_slides'] as $slide) {
                if (!empty($slide['value']) && in_array($slide['type'] ?? '', ['link', 'upload'])) {
                    $heroSlides[] = [
                        'type' => $slide['type'],
                        'value' => $slide['value'],
                    ];
                }
            }
        }
        Setting::set('hero_slides', json_encode($heroSlides));

        if (isset($validated['about_mission'])) Setting::set('about_mission', $validated['about_mission']);
        if (isset($validated['about_vision'])) Setting::set('about_vision', $validated['about_vision']);
        if (isset($validated['about_founder_name'])) Setting::set('about_founder_name', $validated['about_founder_name']);
        if (isset($validated['about_founder_title'])) Setting::set('about_founder_title', $validated['about_founder_title']);
        if (isset($validated['about_founder_bio'])) Setting::set('about_founder_bio', $validated['about_founder_bio']);
        if (isset($validated['about_founder_image'])) Setting::set('about_founder_image', $validated['about_founder_image']);
        if (isset($validated['news_layout_style'])) Setting::set('news_layout_style', $validated['news_layout_style']);

        return redirect()->back()->with('message', 'Settings updated successfully!');
    }
}
