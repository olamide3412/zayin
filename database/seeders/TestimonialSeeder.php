<?php

namespace Database\Seeders;

use App\Models\Testimony;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimony::truncate();

        $reviews = [
            [
                'name' => 'Engr. Babatunde Fashanu',
                'occupation' => 'Homeowner, Lekki Phase 1, Lagos',
                'rating' => 5,
                'message' => 'The 5kVA solar inverter system installed by Zayin Technologies has run continuously for 9 months with zero downtime. My generator petrol expenses dropped from ₦180,000 monthly to under ₦20,000. Clean wiring and excellent earthing.',
                'status' => 'approved',
                'created_at' => now()->subDays(45),
            ],
            [
                'name' => 'Dr. Amina Bello',
                'occupation' => 'Medical Director, Maitama, Abuja',
                'rating' => 5,
                'message' => 'Installed Starlink with custom steel roof mount and lightning surge arrestor for our clinic. We get reliable 180Mbps download speeds even during heavy rainstorms. The engineers are polite, punctual, and highly skilled.',
                'status' => 'approved',
                'created_at' => now()->subDays(32),
            ],
            [
                'name' => 'Chief Emeka Okonkwo',
                'occupation' => 'Hotel General Manager, Victoria Island',
                'rating' => 5,
                'message' => 'Zayin overhauled our 32-room DSTV multi-switch and SMATV signal distribution. No more blurry channels or E48-32 signal drops when rain falls. True professionals who know their craft.',
                'status' => 'approved',
                'created_at' => now()->subDays(20),
            ],
            [
                'name' => 'Mrs. Folake Adeyemi',
                'occupation' => 'Resident, Magodo Phase 2, Lagos',
                'rating' => 5,
                'message' => 'Upgraded from old tubular batteries to their 10kWh Felicity lithium system. The difference is night and day! Powers our inverter AC, refrigerator, and lighting throughout the night effortlessly.',
                'status' => 'approved',
                'created_at' => now()->subDays(15),
            ],
            [
                'name' => 'Arch. Kenneth Nnamdi',
                'occupation' => 'Principal Architect, Port Harcourt',
                'rating' => 5,
                'message' => 'We specify Zayin Technologies for all our luxury residential builds. From neat flush roof conduits to Starlink mesh extensions, their electrical and telecom finishing matches international standards.',
                'status' => 'approved',
                'created_at' => now()->subDays(8),
            ],
            [
                'name' => 'Alhaji Sanusi Garba',
                'occupation' => 'Farm Owner & Agro Facility, Kaduna',
                'rating' => 5,
                'message' => 'Starlink internet and solar borehole pumping setup done in rural Kaduna where no GSM network exists. We can now monitor CCTV feeds live from our phones in Abuja.',
                'status' => 'approved',
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($reviews as $rev) {
            Testimony::create($rev);
        }
    }
}
