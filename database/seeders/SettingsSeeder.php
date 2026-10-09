<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'company_name' => 'Zayin Technologies',
            'tagline' => 'Solar, Starlink Internet & DSTV Solutions in Nigeria',
            'phone' => '+234 803 123 4567',
            'phone_whatsapp' => '2348031234567',
            'whatsapp' => '+234 803 123 4567',
            'email' => 'support@zayin.ng',
            'address' => 'Abraka, Ethiope East, Delta State, Nigeria',
            'headquarters' => 'Abraka, Ethiope East, Delta State',
            'coverage_scope' => 'Nationwide Service — Operating across all 36 States & FCT',
            'branch_lagos' => 'Lekki / Ikeja Operations Hub, Lagos State',
            'branch_abuja' => 'Central Business District Operations Hub, Abuja, FCT',
            'hours' => 'Mon – Sat: 8:00 AM – 6:00 PM (24/7 Emergency Line)',
            'bank_name' => 'Zenith Bank Plc',
            'bank_account_name' => 'Zayin Technologies Nigeria Ltd',
            'bank_account_number' => '1012345678',
            'vat_percent' => '7.5',
            'hero_title' => 'Power Your Life 24/7. Connect Faster. Enjoy Entertainment.',
            'hero_subtitle' => 'Nigeria’s premier provider of engineered Solar & Inverter installations, zero-obstruction Starlink satellite networking, and certified DSTV satellite solutions.',
        ];

        foreach ($settings as $key => $val) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $val,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
