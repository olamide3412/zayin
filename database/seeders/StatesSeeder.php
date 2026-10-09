<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatesSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['name' => 'Abia', 'code' => 'AB', 'delivery_fee' => 1500000, 'install_fee' => 3000000],
            ['name' => 'Adamawa', 'code' => 'AD', 'delivery_fee' => 2500000, 'install_fee' => 4500000],
            ['name' => 'Akwa Ibom', 'code' => 'AK', 'delivery_fee' => 1800000, 'install_fee' => 3500000],
            ['name' => 'Anambra', 'code' => 'AN', 'delivery_fee' => 1500000, 'install_fee' => 3000000],
            ['name' => 'Bauchi', 'code' => 'BA', 'delivery_fee' => 2200000, 'install_fee' => 4000000],
            ['name' => 'Bayelsa', 'code' => 'BY', 'delivery_fee' => 2000000, 'install_fee' => 4000000],
            ['name' => 'Benue', 'code' => 'BE', 'delivery_fee' => 1800000, 'install_fee' => 3500000],
            ['name' => 'Borno', 'code' => 'BO', 'delivery_fee' => 2800000, 'install_fee' => 5000000],
            ['name' => 'Cross River', 'code' => 'CR', 'delivery_fee' => 2000000, 'install_fee' => 3800000],
            ['name' => 'Delta', 'code' => 'DE', 'delivery_fee' => 1500000, 'install_fee' => 3200000],
            ['name' => 'Ebonyi', 'code' => 'EB', 'delivery_fee' => 1600000, 'install_fee' => 3000000],
            ['name' => 'Edo', 'code' => 'ED', 'delivery_fee' => 1200000, 'install_fee' => 2800000],
            ['name' => 'Ekiti', 'code' => 'EK', 'delivery_fee' => 1000000, 'install_fee' => 2500000],
            ['name' => 'Enugu', 'code' => 'EN', 'delivery_fee' => 1400000, 'install_fee' => 3000000],
            ['name' => 'Federal Capital Territory', 'code' => 'FC', 'delivery_fee' => 1000000, 'install_fee' => 2500000],
            ['name' => 'Gombe', 'code' => 'GO', 'delivery_fee' => 2200000, 'install_fee' => 4200000],
            ['name' => 'Imo', 'code' => 'IM', 'delivery_fee' => 1500000, 'install_fee' => 3000000],
            ['name' => 'Jigawa', 'code' => 'JI', 'delivery_fee' => 2500000, 'install_fee' => 4500000],
            ['name' => 'Kaduna', 'code' => 'KD', 'delivery_fee' => 1800000, 'install_fee' => 3500000],
            ['name' => 'Kano', 'code' => 'KN', 'delivery_fee' => 2000000, 'install_fee' => 3800000],
            ['name' => 'Katsina', 'code' => 'KT', 'delivery_fee' => 2200000, 'install_fee' => 4000000],
            ['name' => 'Kebbi', 'code' => 'KE', 'delivery_fee' => 2500000, 'install_fee' => 4500000],
            ['name' => 'Kogi', 'code' => 'KO', 'delivery_fee' => 1400000, 'install_fee' => 3000000],
            ['name' => 'Kwara', 'code' => 'KW', 'delivery_fee' => 1200000, 'install_fee' => 2500000],
            ['name' => 'Lagos', 'code' => 'LA', 'delivery_fee' => 500000, 'install_fee' => 1500000], // ₦5,000 / ₦15,000 baseline
            ['name' => 'Nasarawa', 'code' => 'NA', 'delivery_fee' => 1500000, 'install_fee' => 3000000],
            ['name' => 'Niger', 'code' => 'NI', 'delivery_fee' => 1600000, 'install_fee' => 3200000],
            ['name' => 'Ogun', 'code' => 'OG', 'delivery_fee' => 600000, 'install_fee' => 1800000],
            ['name' => 'Ondo', 'code' => 'ON', 'delivery_fee' => 1000000, 'install_fee' => 2500000],
            ['name' => 'Osun', 'code' => 'OS', 'delivery_fee' => 900000, 'install_fee' => 2200000],
            ['name' => 'Oyo', 'code' => 'OY', 'delivery_fee' => 800000, 'install_fee' => 2000000],
            ['name' => 'Plateau', 'code' => 'PL', 'delivery_fee' => 1800000, 'install_fee' => 3500000],
            ['name' => 'Rivers', 'code' => 'RI', 'delivery_fee' => 1600000, 'install_fee' => 3200000],
            ['name' => 'Sokoto', 'code' => 'SO', 'delivery_fee' => 2600000, 'install_fee' => 4800000],
            ['name' => 'Taraba', 'code' => 'TA', 'delivery_fee' => 2400000, 'install_fee' => 4500000],
            ['name' => 'Yobe', 'code' => 'YO', 'delivery_fee' => 2600000, 'install_fee' => 4800000],
            ['name' => 'Zamfara', 'code' => 'ZA', 'delivery_fee' => 2400000, 'install_fee' => 4500000],
        ];

        foreach ($states as $state) {
            DB::table('states')->updateOrInsert(
                ['code' => $state['code']],
                [
                    'name' => $state['name'],
                    'slug' => Str::slug($state['name']),
                    'delivery_fee' => $state['delivery_fee'],
                    'install_fee' => $state['install_fee'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
