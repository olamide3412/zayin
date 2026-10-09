<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SolarPackagesSeeder extends Seeder
{
    public function run(): void
    {
        $solarProdCat = DB::table('product_categories')->where('slug', 'solar-inverters')->value('id');
        $solarServiceId = DB::table('services')->where('slug', 'solar-installation')->value('id');

        // Update 1.5kVA specs
        DB::table('products')->where('slug', '1-5kva-essential-home-inverter-system')->update([
            'specs' => json_encode([
                'capacity_kva' => 1.5,
                'system_voltage' => 12,
                'battery_kwh' => 2.64,
                'battery_type' => 'tubular',
                'panels_w' => 0,
                'panel_count' => 0,
                'Capacity' => '1.5kVA / 1200 Watts Continuous',
                'System Voltage' => '12 Volts DC',
                'Battery' => '1x 220Ah Tubular Deep Cycle Gel Battery',
                'Runtime' => '8–14 Hours on Typical Household Load (Fans, TV, Lighting)',
                'Changeover' => 'Automated Instant 15ms Transfer',
            ]),
        ]);

        // Update 3.5kVA specs
        DB::table('products')->where('slug', '3-5kva-family-solar-package-lithium')->update([
            'specs' => json_encode([
                'capacity_kva' => 3.5,
                'system_voltage' => 24,
                'battery_kwh' => 2.56,
                'battery_type' => 'lithium',
                'panels_w' => 2200,
                'panel_count' => 4,
                'Inverter Capacity' => '3.5kVA / 3000 Watts Output',
                'Solar Panels' => '4x 550W Monocrystalline Half-Cell (2,200W Array)',
                'Storage' => '24V 100Ah (2.56kWh) LiFePO4 Lithium Battery with Smart BMS',
                'Appliances' => 'Inverter Refrigerator, Home Cinema, Lighting, Laptops, Blend/Wash',
                'Daily Generation' => 'Approx. 8–11kWh per sunny day',
            ]),
        ]);

        // Update 5kVA specs
        DB::table('products')->where('slug', '5kva-premium-hybrid-solar-system')->update([
            'specs' => json_encode([
                'capacity_kva' => 5.0,
                'system_voltage' => 48,
                'battery_kwh' => 5.12,
                'battery_type' => 'lithium',
                'panels_w' => 3300,
                'panel_count' => 6,
                'Inverter Rating' => '5.0kVA / 5000 Watts Pure Sine Wave',
                'Solar Panels' => '6x 550W Monocrystalline Panels (3,300W Array)',
                'Battery Storage' => '5.12kWh 48V 100Ah LiFePO4 Wall-Mount Battery',
                'Heavy Loads' => '1HP Inverter AC, Deep Freezer, 0.75HP Water Pump (daytime)',
                'Monitoring' => 'Built-in Wi-Fi Dongle for Mobile App Telemetry',
            ]),
        ]);

        // Ensure 2.5kVA exists
        $p2b = DB::table('products')->where('slug', '2-5kva-smart-home-solar-package-lithium')->value('id');
        if (!$p2b) {
            $p2b = DB::table('products')->insertGetId([
                'category_id' => $solarProdCat,
                'service_id' => $solarServiceId,
                'type' => 'solar_package',
                'name' => '2.5kVA / 24V Smart Home Solar Package with Lithium Battery',
                'slug' => '2-5kva-smart-home-solar-package-lithium',
                'sku' => 'ZAY-SLR-2K5',
                'summary' => 'Ideal for 2–3 bedroom apartments: powers medium refrigerator, sound system, multiple TVs, workstation, fans, and home lighting.',
                'description' => 'The sweet spot for modern Nigerian homes. Equipped with a 2.5kVA 24V hybrid inverter, 2.56kWh Grade-A LiFePO4 battery, and 3x 550W Tier-1 Monocrystalline solar panels for fast solar replenishment.',
                'price' => 145000000,
                'compare_at_price' => 160000000,
                'price_label' => 'Installed Price',
                'requires_quote' => false,
                'installation_included' => true,
                'warranty_text' => '5 Years Battery Warranty, 2 Years Inverter, 25 Years Panels',
                'specs' => json_encode([
                    'capacity_kva' => 2.5,
                    'system_voltage' => 24,
                    'battery_kwh' => 2.56,
                    'battery_type' => 'lithium',
                    'panels_w' => 1650,
                    'panel_count' => 3,
                    'Inverter Rating' => '2.5kVA / 2200 Watts Output',
                    'Solar Panels' => '3x 550W Monocrystalline Panels (1,650W Array)',
                    'Storage' => '24V 100Ah (2.56kWh) LiFePO4 Lithium Battery with Smart BMS',
                    'Appliances' => 'Medium Refrigerator, Workstation, LED TVs, Fans, Lighting',
                    'Daily Generation' => 'Approx. 6–8kWh per sunny day',
                ]),
                'brand' => 'Felicity / Must / Jinko',
                'image' => 'https://images.unsplash.com/photo-1548337138-e87d889cc369?q=80&w=800&auto=format&fit=crop',
                'stock_qty' => 12,
                'track_stock' => true,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('product_inclusions')->insert([
                ['product_id' => $p2b, 'item_name' => '2.5kVA 24V Pure Sine Wave Hybrid Solar Inverter with MPPT', 'quantity' => '1 Unit', 'description' => 'Built-in fast MPPT solar charger', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p2b, 'item_name' => '24V 100Ah (2.56kWh) LiFePO4 Lithium Battery', 'quantity' => '1 Unit', 'description' => 'Wall-mount or floor cabinet with active BMS', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p2b, 'item_name' => '550W Tier-1 Monocrystalline Solar Panels', 'quantity' => '3 Panels', 'description' => 'High weather durability', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p2b, 'item_name' => 'Rooftop Rail Mounting & Clamp System', 'quantity' => '1 Set', 'description' => 'Corrosion-proof aluminium', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p2b, 'item_name' => 'DC/AC Combiner Box with Surge Protective Devices (SPD)', 'quantity' => '1 Unit', 'description' => 'Over-voltage and lightning safety', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p2b, 'item_name' => 'Turnkey Engineering Installation & Delivery', 'quantity' => 'Included', 'description' => 'Testing and commissioning', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Ensure 10kVA exists
        $p3b = DB::table('products')->where('slug', '10kva-commercial-mansion-solar-system')->value('id');
        if (!$p3b) {
            $p3b = DB::table('products')->insertGetId([
                'category_id' => $solarProdCat,
                'service_id' => $solarServiceId,
                'type' => 'solar_package',
                'name' => '10kVA / 48V Commercial & Mansion Solar System (Multi-AC Ready)',
                'slug' => '10kva-commercial-mansion-solar-system',
                'sku' => 'ZAY-SLR-10K',
                'summary' => 'Heavy-duty industrial power for large mansions, corporate offices, clinics, and supermarkets. Powers 3x Inverter ACs, deep freezers, and borehole pumping machine.',
                'description' => 'Engineered for relentless heavy loads. Featuring 10kVA pure sine wave output, 10.24kWh high-capacity lithium storage, and a 6.6kW Tier-1 solar array designed to keep commercial operations active 24/7 without burning costly diesel.',
                'price' => 680000000,
                'compare_at_price' => 740000000,
                'price_label' => 'Turnkey Commercial',
                'requires_quote' => false,
                'installation_included' => true,
                'warranty_text' => '5 Years Battery Warranty, 3 Years Inverter, 25 Years Panels',
                'specs' => json_encode([
                    'capacity_kva' => 10.0,
                    'system_voltage' => 48,
                    'battery_kwh' => 10.24,
                    'battery_type' => 'lithium',
                    'panels_w' => 6600,
                    'panel_count' => 12,
                    'Inverter Rating' => '10kVA / 10,000 Watts Continuous High-Surge Output',
                    'Solar Panels' => '12x 550W Tier-1 Monocrystalline Panels (6,600W Array)',
                    'Battery Storage' => '10.24kWh 48V 200Ah LiFePO4 Rack / Wall-Mount Storage',
                    'Heavy Loads' => '3x Inverter ACs, Deep Freezers, Borehole Pump, Medical / IT equipment',
                    'Monitoring' => 'Cloud Telemetry with Mobile App & Generator Auto-Start Module',
                ]),
                'brand' => 'Deye / Victron / Canadian Solar',
                'image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop',
                'stock_qty' => 5,
                'track_stock' => true,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('product_inclusions')->insert([
                ['product_id' => $p3b, 'item_name' => '10kVA 48V Pure Sine Wave Dual MPPT Hybrid Solar Inverter', 'quantity' => '1 Unit', 'description' => 'Industrial grade with generator integration', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p3b, 'item_name' => '10.24kWh 48V 200Ah High Voltage LiFePO4 Lithium Battery Rack', 'quantity' => '1 Unit', 'description' => '6,000 cycles with active balancer', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p3b, 'item_name' => '550W Tier-1 Monocrystalline Solar Panels', 'quantity' => '12 Panels', 'description' => 'High weather durability', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p3b, 'item_name' => 'Commercial Heavy Duty Anodized Aluminum Rooftop Rails', 'quantity' => '1 Set', 'description' => 'Engineered for high wind rating', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p3b, 'item_name' => 'Full IP65 Combiner Enclosure, Breakers, SPDs & Changeover', 'quantity' => '1 Set', 'description' => 'Industrial electrical switchgear', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $p3b, 'item_name' => 'Turnkey Commissioning, Earthing & Lightning Protection', 'quantity' => 'Included', 'description' => 'Done by senior registered electrical engineers', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
