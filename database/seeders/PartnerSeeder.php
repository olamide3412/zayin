<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds for Technology & Equipment Partners.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'SpaceX Starlink',
                'description' => 'High-Speed Low-Latency Satellite Internet Systems & Precision Elevated Mounts.',
                'website_url' => 'https://www.starlink.com',
                'logo_url' => 'https://images.unsplash.com/photo-1541185933-ef5d8ed016c2?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'name' => 'MultiChoice DStv',
                'description' => 'Certified Commercial & Residential Satellite TV, Explora & Extra View Installations.',
                'website_url' => 'https://www.dstv.com',
                'logo_url' => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Victron Energy',
                'description' => 'World-Class Dutch Smart Solar Inverters, MPPT Chargers & Monitoring Hardware.',
                'website_url' => 'https://www.victronenergy.com',
                'logo_url' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'name' => 'Felicity Solar',
                'description' => 'Tier-1 LiFePO4 Lithium Battery Storage & Robust Hybrid Inverter Systems.',
                'website_url' => 'https://www.felicitysolar.com',
                'logo_url' => 'https://images.unsplash.com/photo-1508873696983-2df5293cb395?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'name' => 'Huawei FusionSolar',
                'description' => 'Enterprise & Residential Smart Photovoltaic and Energy Storage Solutions.',
                'website_url' => 'https://solar.huawei.com',
                'logo_url' => 'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'name' => 'Growatt New Energy',
                'description' => 'High-Efficiency Grid-Tied and Off-Grid Hybrid Inverters & Smart EV Chargers.',
                'website_url' => 'https://www.growatt.com',
                'logo_url' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 6,
            ],
            [
                'name' => 'Deye Solar',
                'description' => 'Advanced Low & High Voltage Hybrid Inverters with Generator Auto-Start Synchronization.',
                'website_url' => 'https://www.deyeinverter.com',
                'logo_url' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 7,
            ],
            [
                'name' => 'Must Solar',
                'description' => 'Heavy-Duty Copper Transformer Pure Sine Wave Inverters for Surge-Heavy Nigerian Loads.',
                'website_url' => 'https://www.mustpower.com',
                'logo_url' => 'https://images.unsplash.com/photo-1558441719-74e2d3bbd62e?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'sort_order' => 8,
            ],
        ];

        foreach ($partners as $partnerData) {
            Partner::updateOrCreate(
                ['name' => $partnerData['name']],
                $partnerData
            );
        }
    }
}
