<?php

namespace Database\Seeders;

use App\Models\ApplianceCategory;
use App\Models\AppliancePreset;
use App\Models\SolarCalcSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SolarCalculatorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure default Solar Calculator Settings exist
        if (!SolarCalcSetting::where('is_active', true)->exists()) {
            SolarCalcSetting::createDefault();
        }

        // 2. Categories
        $categories = [
            ['name' => 'Lighting', 'slug' => 'lighting', 'icon' => 'lightbulb', 'sort_order' => 1],
            ['name' => 'Cooling', 'slug' => 'cooling', 'icon' => 'fan', 'sort_order' => 2],
            ['name' => 'Kitchen', 'slug' => 'kitchen', 'icon' => 'kitchen-set', 'sort_order' => 3],
            ['name' => 'Home & Utility', 'slug' => 'home-utility', 'icon' => 'house', 'sort_order' => 4],
            ['name' => 'Water Pumping', 'slug' => 'water-pumping', 'icon' => 'faucet-drip', 'sort_order' => 5],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'icon' => 'tv', 'sort_order' => 6],
            ['name' => 'Office & IT', 'slug' => 'office-it', 'icon' => 'laptop', 'sort_order' => 7],
            ['name' => 'Security & Electronics', 'slug' => 'security-electronics', 'icon' => 'shield-halved', 'sort_order' => 8],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $model = ApplianceCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'sort_order' => $cat['sort_order'],
                ]
            );
            $catMap[$cat['slug']] = $model->id;
        }

        // 3. 27 Starter Appliance Presets (§4.2)
        $presets = [
            // Lighting
            [
                'cat' => 'lighting',
                'name' => 'LED bulb',
                'watts' => 10,
                'surge' => 1.0,
                'hours' => 5.0,
                'icon' => 'lightbulb',
                'is_resistive' => false,
                'notes' => 'Energy saving 7W–12W LED bulb',
            ],
            [
                'cat' => 'lighting',
                'name' => 'Security / flood light (LED)',
                'watts' => 30,
                'surge' => 1.0,
                'hours' => 10.0,
                'icon' => 'shield',
                'is_resistive' => false,
                'notes' => 'Outdoor perimeter night illumination',
            ],

            // Cooling
            [
                'cat' => 'cooling',
                'name' => 'Ceiling fan',
                'watts' => 75,
                'surge' => 2.0,
                'hours' => 8.0,
                'icon' => 'fan',
                'is_resistive' => false,
                'notes' => 'Standard 56-inch 3-blade ceiling fan',
            ],
            [
                'cat' => 'cooling',
                'name' => 'Standing / table fan',
                'watts' => 60,
                'surge' => 2.0,
                'hours' => 8.0,
                'icon' => 'fan',
                'is_resistive' => false,
                'notes' => '16 to 18-inch rechargeable or AC standing fan',
            ],
            [
                'cat' => 'cooling',
                'name' => 'Air conditioner 1 HP (inverter)',
                'watts' => 750,
                'surge' => 3.0,
                'hours' => 6.0,
                'icon' => 'snowflake',
                'is_resistive' => false,
                'notes' => 'Energy efficient inverter split unit AC',
            ],
            [
                'cat' => 'cooling',
                'name' => 'Air conditioner 1.5 HP',
                'watts' => 1200,
                'surge' => 3.0,
                'hours' => 6.0,
                'icon' => 'snowflake',
                'is_resistive' => false,
                'notes' => 'Standard non-inverter / inverter 1.5HP split unit',
            ],
            [
                'cat' => 'cooling',
                'name' => 'Air conditioner 2 HP',
                'watts' => 1700,
                'surge' => 3.0,
                'hours' => 6.0,
                'icon' => 'snowflake',
                'is_resistive' => false,
                'notes' => 'Large bedroom or executive office split unit',
            ],

            // Kitchen
            [
                'cat' => 'kitchen',
                'name' => 'Refrigerator (medium)',
                'watts' => 150,
                'surge' => 3.0,
                'hours' => 10.0,
                'icon' => 'temperature-low',
                'is_resistive' => false,
                'notes' => 'Effective compressor duty-cycle running time',
            ],
            [
                'cat' => 'kitchen',
                'name' => 'Chest freezer',
                'watts' => 200,
                'surge' => 4.0,
                'hours' => 10.0,
                'icon' => 'temperature-low',
                'is_resistive' => false,
                'notes' => 'High insulation deep freezer with compressor cycle',
            ],
            [
                'cat' => 'kitchen',
                'name' => 'Blender',
                'watts' => 400,
                'surge' => 2.0,
                'hours' => 0.5,
                'icon' => 'blender',
                'is_resistive' => false,
                'notes' => 'Short pulse food processor / blender',
            ],
            [
                'cat' => 'kitchen',
                'name' => 'Microwave',
                'watts' => 1200,
                'surge' => 1.0,
                'hours' => 0.5,
                'icon' => 'microchip',
                'is_resistive' => false,
                'notes' => 'Food warming & quick baking',
            ],
            [
                'cat' => 'kitchen',
                'name' => 'Electric kettle',
                'watts' => 1500,
                'surge' => 1.0,
                'hours' => 0.5,
                'icon' => 'mug-hot',
                'is_resistive' => true,
                'notes' => 'High-draw resistive heating element',
            ],

            // Home & Utility
            [
                'cat' => 'home-utility',
                'name' => 'Electric iron',
                'watts' => 1000,
                'surge' => 1.0,
                'hours' => 0.5,
                'icon' => 'shirt',
                'is_resistive' => true,
                'notes' => 'Heavy heating element; best used during peak sunlight',
            ],
            [
                'cat' => 'home-utility',
                'name' => 'Washing machine',
                'watts' => 500,
                'surge' => 2.0,
                'hours' => 1.0,
                'icon' => 'soap',
                'is_resistive' => false,
                'notes' => 'Front/top loader wash & spin cycle',
            ],

            // Water & Pumping
            [
                'cat' => 'water-pumping',
                'name' => 'Water pump 0.5 HP',
                'watts' => 370,
                'surge' => 5.0,
                'hours' => 2.0,
                'icon' => 'faucet-drip',
                'is_resistive' => false,
                'notes' => 'Surface or shallow borehole pump with motor surge',
            ],
            [
                'cat' => 'water-pumping',
                'name' => 'Water pump 1 HP',
                'watts' => 750,
                'surge' => 5.0,
                'hours' => 2.0,
                'icon' => 'faucet-drip',
                'is_resistive' => false,
                'notes' => 'Deep well submersible borehole pump',
            ],

            // Entertainment
            [
                'cat' => 'entertainment',
                'name' => 'TV LED 32"',
                'watts' => 50,
                'surge' => 1.0,
                'hours' => 6.0,
                'icon' => 'tv',
                'is_resistive' => false,
                'notes' => 'Compact smart LED screen',
            ],
            [
                'cat' => 'entertainment',
                'name' => 'TV LED 43"–55"',
                'watts' => 80,
                'surge' => 1.0,
                'hours' => 6.0,
                'icon' => 'tv',
                'is_resistive' => false,
                'notes' => 'Living room 4K Ultra HD television',
            ],
            [
                'cat' => 'entertainment',
                'name' => 'DSTV / GOtv decoder',
                'watts' => 20,
                'surge' => 1.0,
                'hours' => 6.0,
                'icon' => 'satellite',
                'is_resistive' => false,
                'notes' => 'HD / Explora decoder',
            ],
            [
                'cat' => 'entertainment',
                'name' => 'Sound system',
                'watts' => 100,
                'surge' => 1.0,
                'hours' => 4.0,
                'icon' => 'volume-high',
                'is_resistive' => false,
                'notes' => 'Home theatre amplifier with subwoofer',
            ],

            // Office & IT
            [
                'cat' => 'office-it',
                'name' => 'Laptop',
                'watts' => 65,
                'surge' => 1.0,
                'hours' => 6.0,
                'icon' => 'laptop',
                'is_resistive' => false,
                'notes' => 'Work / gaming laptop charger',
            ],
            [
                'cat' => 'office-it',
                'name' => 'Desktop PC + monitor',
                'watts' => 200,
                'surge' => 1.0,
                'hours' => 8.0,
                'icon' => 'desktop',
                'is_resistive' => false,
                'notes' => 'Workstation desktop computer and LED monitor',
            ],
            [
                'cat' => 'office-it',
                'name' => 'Wi-Fi router / ONT',
                'watts' => 12,
                'surge' => 1.0,
                'hours' => 24.0,
                'icon' => 'wifi',
                'is_resistive' => false,
                'notes' => '24/7 uninterrupted fiber/broadband router',
            ],
            [
                'cat' => 'office-it',
                'name' => 'Starlink kit',
                'watts' => 75,
                'surge' => 1.0,
                'hours' => 24.0,
                'icon' => 'satellite-dish',
                'is_resistive' => false,
                'notes' => 'SpaceX Starlink Standard dish and Wi-Fi router (average draw)',
            ],
            [
                'cat' => 'office-it',
                'name' => 'Printer',
                'watts' => 50,
                'surge' => 1.0,
                'hours' => 1.0,
                'icon' => 'print',
                'is_resistive' => false,
                'notes' => 'Deskjet or small laser printer',
            ],

            // Security & Electronics
            [
                'cat' => 'security-electronics',
                'name' => 'CCTV (DVR + 4 cameras)',
                'watts' => 50,
                'surge' => 1.0,
                'hours' => 24.0,
                'icon' => 'video',
                'is_resistive' => false,
                'notes' => 'Continuous security recording setup with infrared night vision',
            ],
            [
                'cat' => 'security-electronics',
                'name' => 'Phone charger',
                'watts' => 10,
                'surge' => 1.0,
                'hours' => 3.0,
                'icon' => 'mobile-screen-button',
                'is_resistive' => false,
                'notes' => 'Smartphone fast charge adapter',
            ],
            [
                'cat' => 'security-electronics',
                'name' => 'Sewing machine',
                'watts' => 100,
                'surge' => 2.0,
                'hours' => 4.0,
                'icon' => 'scissors',
                'is_resistive' => false,
                'notes' => 'Electric motor-driven tailoring machine',
            ],
        ];

        foreach ($presets as $index => $item) {
            AppliancePreset::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $catMap[$item['cat']] ?? null,
                    'name' => $item['name'],
                    'watts' => $item['watts'],
                    'surge_multiplier' => $item['surge'],
                    'default_hours' => $item['hours'],
                    'icon' => $item['icon'],
                    'is_resistive' => $item['is_resistive'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'notes' => $item['notes'],
                ]
            );
        }
    }
}
