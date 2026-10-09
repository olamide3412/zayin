<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Service Categories
        $solarCatId = DB::table('service_categories')->insertGetId([
            'name' => 'Solar Energy & Power Backup',
            'slug' => 'solar-energy',
            'description' => 'Clean, uninterrupted solar and inverter power solutions for Nigerian homes and enterprises.',
            'icon' => 'solar-panel',
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $netCatId = DB::table('service_categories')->insertGetId([
            'name' => 'Starlink & Internet Networking',
            'slug' => 'starlink-networking',
            'description' => 'High-speed Starlink installations, long-range Wi-Fi extensions, and structured network cabling.',
            'icon' => 'satellite-dish',
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dstvCatId = DB::table('service_categories')->insertGetId([
            'name' => 'DSTV & Satellite TV Solutions',
            'slug' => 'dstv-satellite',
            'description' => 'Certified DSTV, GOtv installations, dish realignment, Extra View and signal troubleshooting.',
            'icon' => 'tv',
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Services
        $solarServiceId = DB::table('services')->insertGetId([
            'category_id' => $solarCatId,
            'name' => 'Solar & Inverter Installation',
            'slug' => 'solar-installation',
            'summary' => 'Complete design, sizing, and installation of pure sine wave inverters, lithium batteries, and tier-1 solar panels.',
            'body' => 'Say goodbye to exorbitant fuel expenses and erratic grid outages. Our certified engineers design bespoke solar setups engineered specifically for Nigerian harsh ambient temperatures and humidity. We guarantee zero power cutovers, seamless generator integration, and real-time remote monitoring via smartphone.',
            'hero_image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=1600&auto=format&fit=crop',
            'icon' => 'solar-panel',
            'benefits' => json_encode([
                'Slash monthly petrol and diesel generator spending by up to 90%',
                'Zero-second switchover (no computer reboot or flicker during outages)',
                'Grade-A LiFePO4 lithium batteries with 10+ year lifespan',
                'Comprehensive surge, lightning, and reverse polarity electrical protection',
                'Mobile app monitoring showing real-time solar generation and battery state',
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Energy Load Audit', 'desc' => 'We itemize your appliances, peak surges, and nighttime energy requirements.'],
                ['step' => 2, 'title' => 'System Design & Quotation', 'desc' => 'You receive a detailed quotation with itemized equipment, warranties, and ROI timeline.'],
                ['step' => 3, 'title' => 'Clean & Neat Installation', 'desc' => 'Concealed trunking, AC/DC distribution boxes, and safety earthing completed in 24–48 hours.'],
                ['step' => 4, 'title' => 'Commissioning & Handover', 'desc' => 'We test full load capacity, set up your mobile monitoring app, and provide warranty documentation.'],
            ]),
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $solarMaintServiceId = DB::table('services')->insertGetId([
            'category_id' => $solarCatId,
            'name' => 'Solar Maintenance & Battery Health Audit',
            'slug' => 'solar-maintenance',
            'summary' => 'Preventive maintenance, dust/soot panel scrubbing, cell balancing, and inverter diagnostics.',
            'body' => 'Dust, atmospheric smog, and loose connections can reduce solar output by over 35%. Our technicians provide scheduled preventive maintenance to keep your solar investment operating at peak efficiency all year round.',
            'hero_image' => 'https://images.unsplash.com/photo-1548337138-e87d889cc369?q=80&w=1600&auto=format&fit=crop',
            'icon' => 'wrench',
            'benefits' => json_encode([
                'De-ionized solar panel cleaning restoring 15–30% lost wattage',
                'Lithium BMS health analysis and lead-acid specific gravity testing',
                'Thermal camera inspection to detect solar hot-spots before panel failure',
                'Inverter terminal torquing and cooling fan decontamination',
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Schedule Inspection', 'desc' => 'Pick a suitable time slot for our technicians to inspect your system.'],
                ['step' => 2, 'title' => 'Diagnostics & Panel Wash', 'desc' => 'Detailed electrical inspection, string testing, and chemical-free panel wash.'],
                ['step' => 3, 'title' => 'Comprehensive Report', 'desc' => 'Written health audit with actionable recommendations and optimization tips.'],
            ]),
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $starlinkServiceId = DB::table('services')->insertGetId([
            'category_id' => $netCatId,
            'name' => 'Starlink Installation & Enterprise Wi-Fi',
            'slug' => 'starlink-installation',
            'summary' => 'Zero-obstruction Starlink satellite mounting, weather-proof cabling, and whole-compound mesh Wi-Fi.',
            'body' => 'Get lightning-fast 100Mbps–250Mbps satellite broadband anywhere in Nigeria. We engineer sturdy steel mast mountings, clear tree/building obstructions, drill neat external cable passages, and link multi-room Wi-Fi mesh systems so you enjoy strong internet in every room, patio, and compound gatehouse.',
            'hero_image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?q=80&w=1600&auto=format&fit=crop',
            'icon' => 'satellite-dish',
            'benefits' => json_encode([
                'Obstruction-free 360° horizon mapping using Starlink satellite scanners',
                'Custom steel pole mounting resistant to heavy tropical downpours and wind gusts',
                'Whole-premises coverage eliminating Wi-Fi dead zones across duplexes and offices',
                'Neat external UV-resistant Cat6 cabling with drip loops and weather seals',
                'Dedicated configuration of guest Wi-Fi networks and bandwidth QoS limits',
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Site Survey & Elevation Scan', 'desc' => 'We survey your rooftop for zero satellite obstruction and choose optimal cable entry.'],
                ['step' => 2, 'title' => 'Precision Mast Installation', 'desc' => 'Mounting base is securely bolted using galvanized brackets and rust-proof hardware.'],
                ['step' => 3, 'title' => 'Cabling & Router Integration', 'desc' => 'Cables run through protective conduit straight to your indoor server or living area.'],
                ['step' => 4, 'title' => 'Speed & Latency Testing', 'desc' => 'We calibrate your router, conduct live speedtests (150+ Mbps), and configure devices.'],
            ]),
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dstvServiceId = DB::table('services')->insertGetId([
            'category_id' => $dstvCatId,
            'name' => 'DSTV Installation, Extra View & Repair',
            'slug' => 'dstv-installation',
            'summary' => 'Digital dish realignment, Smart LNB upgrades, Multi-Room Extra View, and fixing E48-32 signal errors.',
            'body' => 'Never miss a match or favourite show due to rain fade, poor dish alignment, or frustrating E48-32 "No Signal" errors. Our technicians use digital satellite meters to achieve 98%+ signal strength and quality on your DSTV HD and Explora decoders.',
            'hero_image' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?q=80&w=1600&auto=format&fit=crop',
            'icon' => 'tv',
            'benefits' => json_encode([
                'High-precision dish alignment peaking both Signal Strength & Signal Quality',
                'Certified Extra View configurations (watch separate channels in 2 or 3 rooms)',
                'Anti-rust galvanized dish brackets with heavy-duty concrete anchor bolts',
                'Smart LNB installations ready for DSTV Explora Ultra and 4K streaming',
                'Quick troubleshooting for tricky cabling shorts and decoder activation delays',
            ]),
            'process' => json_encode([
                ['step' => 1, 'title' => 'Dish Positioning', 'desc' => 'Mount dish securely with clear line of sight to IS-20 satellite at 68.5° East.'],
                ['step' => 2, 'title' => 'Meter Frequency Peaking', 'desc' => 'Digital signal meter calibration for highest rain fade tolerance.'],
                ['step' => 3, 'title' => 'Decoder Cabling & Heartbeat', 'desc' => 'Connect Smart LNB, run RG6 coaxial cables, and link heartbeat cable for Extra View.'],
                ['step' => 4, 'title' => 'Account Pairing & Picture Check', 'desc' => 'Clear error codes, verify clear HD picture, and hand over clean setup.'],
            ]),
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Product Categories
        $solarProdCat = DB::table('product_categories')->insertGetId([
            'name' => 'Solar Packages',
            'slug' => 'solar',
            'description' => 'Complete all-in-one solar systems with inverter, lithium battery, panels, and installation included.',
            'icon' => 'solar-panel',
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $starlinkProdCat = DB::table('product_categories')->insertGetId([
            'name' => 'Starlink Kits & Mounts',
            'slug' => 'starlink',
            'description' => 'Genuine Starlink standard kits, high-performance pipe adapters, Ethernet accessories, and mesh routers.',
            'icon' => 'satellite-dish',
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dstvProdCat = DB::table('product_categories')->insertGetId([
            'name' => 'DSTV & Satellite Kits',
            'slug' => 'dstv',
            'description' => 'DSTV HD & Explora decoders, dish kits, smart LNBs, and Extra View installation bundles.',
            'icon' => 'tv',
            'sort_order' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $accessoriesCat = DB::table('product_categories')->insertGetId([
            'name' => 'Batteries & Accessories',
            'slug' => 'accessories',
            'description' => 'LiFePO4 lithium batteries, changeover switches, surge protectors, and solar cables.',
            'icon' => 'battery-full',
            'sort_order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Products & Inclusions
        // Product A: 1.5kVA Inverter System
        $p1 = DB::table('products')->insertGetId([
            'category_id' => $solarProdCat,
            'service_id' => $solarServiceId,
            'type' => 'solar_package',
            'name' => '1.5kVA / 12V Essential Home Inverter System',
            'slug' => '1-5kva-essential-home-inverter-system',
            'sku' => 'ZAY-SLR-1K5',
            'summary' => 'Ideal for 1–2 bedroom apartments: powers TVs, decoders, lighting, laptops, fans, and phones all night.',
            'description' => 'A dependable starter package for flats and small homes. Engineered with a pure sine wave inverter and a heavy-duty deep-cycle battery to guarantee continuous lighting and entertainment without generator noise.',
            'price' => 65000000, // ₦650,000 in kobo
            'compare_at_price' => 72000000, // ₦720,000
            'price_label' => 'Fixed Price',
            'requires_quote' => false,
            'installation_included' => true,
            'warranty_text' => '1 Year Comprehensive Equipment & Workmanship Warranty',
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
            'brand' => 'Felicity / Luminous',
            'image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop',
            'stock_qty' => 15,
            'track_stock' => true,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_inclusions')->insert([
            ['product_id' => $p1, 'item_name' => '1.5kVA 12V Pure Sine Wave Hybrid Inverter', 'quantity' => '1 Unit', 'description' => 'LCD status display with overload protection', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p1, 'item_name' => '220Ah 12V Deep Cycle Tall Tubular Battery', 'quantity' => '1 Unit', 'description' => 'Long life cycle engineered for backup power', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p1, 'item_name' => 'Heavy Duty 35mm² Pure Copper Battery Cables', 'quantity' => '1 Set', 'description' => 'With crimped brass lugs', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p1, 'item_name' => 'AC Input/Output Breaker Distribution Box', 'quantity' => '1 Unit', 'description' => 'Includes manual bypass changeover switch', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p1, 'item_name' => 'Standard Installation & Testing Service', 'quantity' => 'Included', 'description' => 'Neat concealed cabling and safety check', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Product B: 3.5kVA Solar Package
        $p2 = DB::table('products')->insertGetId([
            'category_id' => $solarProdCat,
            'service_id' => $solarServiceId,
            'type' => 'solar_package',
            'name' => '3.5kVA / 24V Family Solar Package with Lithium Battery',
            'slug' => '3-5kva-family-solar-package-lithium',
            'sku' => 'ZAY-SLR-3K5',
            'summary' => 'Perfect for 3–4 bedroom apartments: powers refrigerator, washing machine, TVs, sound system, lighting, and fans.',
            'description' => 'Upgrade your lifestyle with reliable solar energy. This system pairs a high-efficiency 3.5kVA hybrid solar inverter with a fast-charging 2.56kWh LiFePO4 lithium battery and 4x 550W Tier-1 Monocrystalline solar panels.',
            'price' => 185000000, // ₦1,850,000 in kobo
            'compare_at_price' => 205000000,
            'price_label' => 'Installed Price',
            'requires_quote' => false,
            'installation_included' => true,
            'warranty_text' => '5 Years Battery Warranty, 2 Years Inverter, 25 Years Panel Performance Warranty',
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
            'brand' => 'Growatt / Felicity / Jinko',
            'image' => 'https://images.unsplash.com/photo-1548337138-e87d889cc369?q=80&w=800&auto=format&fit=crop',
            'stock_qty' => 10,
            'track_stock' => true,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_inclusions')->insert([
            ['product_id' => $p2, 'item_name' => '3.5kVA 24V Pure Sine Wave Hybrid Solar Inverter with MPPT', 'quantity' => '1 Unit', 'description' => '98% MPPT tracker efficiency', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p2, 'item_name' => '24V 100Ah (2.56kWh) LiFePO4 Lithium Battery', 'quantity' => '1 Unit', 'description' => '6,000+ deep discharge cycles', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p2, 'item_name' => '550W Tier-1 Monocrystalline Solar Panels', 'quantity' => '4 Panels', 'description' => 'High weather durability', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p2, 'item_name' => 'Aluminum Rooftop Rail Mounting & Clamp System', 'quantity' => '1 Set', 'description' => 'Corrosion-proof anodized aluminium', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p2, 'item_name' => 'DC/AC Combiner Box with Surge Protective Devices (SPD)', 'quantity' => '1 Unit', 'description' => 'Lightning and over-voltage protection', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p2, 'item_name' => 'Professional Turnkey Installation & Cable Trunking', 'quantity' => 'Included', 'description' => 'Done by certified electrical solar engineers', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Product C: 5kVA Solar Package
        $p3 = DB::table('products')->insertGetId([
            'category_id' => $solarProdCat,
            'service_id' => $solarServiceId,
            'type' => 'solar_package',
            'name' => '5kVA / 48V Premium Hybrid Solar System (1HP Inverter AC Ready)',
            'slug' => '5kva-premium-hybrid-solar-system',
            'sku' => 'ZAY-SLR-5K0',
            'summary' => 'Our best-selling package for 4–5 bedroom duplexes and corporate offices: powers 1HP Inverter AC, pumping machine, deep freezer, and all house circuits.',
            'description' => 'Enjoy complete energy independence. Engineered with a robust 48V architecture, 5.12kWh wall-mounted lithium storage, and 6x 550W high-yield solar panels. Capable of smoothly running daytime air conditioning, deep freezers, and water pumps.',
            'price' => 345000000, // ₦3,450,000 in kobo
            'compare_at_price' => 375000000,
            'price_label' => 'Installed Price',
            'requires_quote' => false,
            'installation_included' => true,
            'warranty_text' => '5 Years Battery Warranty, 2 Years Inverter, 25 Years Panels',
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
            'brand' => 'Deye / Felicity / Canadian Solar',
            'image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=800&auto=format&fit=crop',
            'stock_qty' => 8,
            'track_stock' => true,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_inclusions')->insert([
            ['product_id' => $p3, 'item_name' => '5kVA 48V Hybrid Solar Inverter with Wi-Fi Monitoring', 'quantity' => '1 Unit', 'description' => 'Dual MPPT with generator auto-start contacts', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p3, 'item_name' => '5.12kWh 48V 100Ah LiFePO4 Wall-Mounted Lithium Battery', 'quantity' => '1 Unit', 'description' => 'Smart BMS with CAN/RS485 communication', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p3, 'item_name' => '550W Half-Cut Monocrystalline Solar Panels', 'quantity' => '6 Panels', 'description' => 'Tier-1 high performance modules', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p3, 'item_name' => 'Rooftop Anodized Aluminum Mounting Structure', 'quantity' => '1 Set', 'description' => 'Engineered for wind shear and storm resistance', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p3, 'item_name' => 'Heavy Duty DC Surge Protectors, Fuses & AC Changeover', 'quantity' => '1 Set', 'description' => 'Full IP65 distribution enclosure', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p3, 'item_name' => 'Copper Earthing Rod & Lightning Arrestor Integration', 'quantity' => '1 Set', 'description' => 'Protects equipment against voltage spikes', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p3, 'item_name' => 'Turnkey Installation by Senior Electrical Engineers', 'quantity' => 'Included', 'description' => 'Complete testing and commissioning', 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Product C2: 2.5kVA Solar Package
        $p2b = DB::table('products')->insertGetId([
            'category_id' => $solarProdCat,
            'service_id' => $solarServiceId,
            'type' => 'solar_package',
            'name' => '2.5kVA / 24V Smart Home Solar Package with Lithium Battery',
            'slug' => '2-5kva-smart-home-solar-package-lithium',
            'sku' => 'ZAY-SLR-2K5',
            'summary' => 'Ideal for 2–3 bedroom apartments: powers medium refrigerator, sound system, multiple TVs, workstation, fans, and home lighting.',
            'description' => 'The sweet spot for modern Nigerian homes. Equipped with a 2.5kVA 24V hybrid inverter, 2.56kWh Grade-A LiFePO4 battery, and 3x 550W Tier-1 Monocrystalline solar panels for fast solar replenishment.',
            'price' => 145000000, // ₦1,450,000 in kobo
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

        // Product C3: 10kVA Commercial Package
        $p3b = DB::table('products')->insertGetId([
            'category_id' => $solarProdCat,
            'service_id' => $solarServiceId,
            'type' => 'solar_package',
            'name' => '10kVA / 48V Commercial & Mansion Solar System (Multi-AC Ready)',
            'slug' => '10kva-commercial-mansion-solar-system',
            'sku' => 'ZAY-SLR-10K',
            'summary' => 'Heavy-duty industrial power for large mansions, corporate offices, clinics, and supermarkets. Powers 3x Inverter ACs, deep freezers, and borehole pumping machine.',
            'description' => 'Engineered for relentless heavy loads. Featuring 10kVA pure sine wave output, 10.24kWh high-capacity lithium storage, and a 6.6kW Tier-1 solar array designed to keep commercial operations active 24/7 without burning costly diesel.',
            'price' => 680000000, // ₦6,800,000 in kobo
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

        // Product D: Starlink Standard Kit & Pro Installation
        $p4 = DB::table('products')->insertGetId([
            'category_id' => $starlinkProdCat,
            'service_id' => $starlinkServiceId,
            'type' => 'starlink_kit',
            'name' => 'Starlink Standard Kit + Heavy-Duty Steel Pipe Mast & Installation',
            'slug' => 'starlink-standard-kit-pro-installation',
            'sku' => 'ZAY-STRLK-01',
            'summary' => 'Complete hardware kit + customized roof mast, weatherproof cabling, and professional alignment for 100–250 Mbps speeds.',
            'description' => 'Get genuine Starlink satellite internet set up cleanly without ugly dangling cables or signal blockages. Includes the dish, Gen 3 / Gen 2 Wi-Fi router, power supplies, custom welded heavy-duty steel pipe mount, and professional weather-sealed wall entries.',
            'price' => 65000000, // ₦650,000 in kobo
            'compare_at_price' => 70000000,
            'price_label' => 'Hardware + Install',
            'requires_quote' => false,
            'installation_included' => true,
            'warranty_text' => '1 Year Official Hardware Warranty + 90 Days Free Service Support',
            'specs' => json_encode([
                'Speed' => '100 Mbps – 250 Mbps Download / 20–40 ms Latency',
                'Mount' => 'Reinforced Galvanized Steel Pipe Roof Mount (1.5m – 3m Height)',
                'Cabling' => 'UV-Protected 75ft Starlink Cable with Drip Loops & Silicone Sealing',
                'Router' => 'Dual-Band Wi-Fi 5 / Wi-Fi 6 with Guest Access & Parental Controls',
            ]),
            'brand' => 'Starlink',
            'image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?q=80&w=800&auto=format&fit=crop',
            'stock_qty' => 12,
            'track_stock' => true,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_inclusions')->insert([
            ['product_id' => $p4, 'item_name' => 'Original Starlink Satellite Dish & Electronic Antenna', 'quantity' => '1 Unit', 'description' => 'Self-orienting phased-array antenna', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p4, 'item_name' => 'Starlink Wi-Fi Base Station Router & Power Supply', 'quantity' => '1 Unit', 'description' => 'High speed dual band coverage', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p4, 'item_name' => 'Fabricated Heavy-Duty Steel Mast & Flange Brackets', 'quantity' => '1 Unit', 'description' => 'Rust-proof zinc primer coated', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p4, 'item_name' => 'Concealed Conduit & Weather-Sealed Cable Entry', 'quantity' => 'Included', 'description' => 'Zero rainwater leakage guarantee', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p4, 'item_name' => 'Account Activation & Wi-Fi Configuration Service', 'quantity' => 'Included', 'description' => 'Testing latency and device connections', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Product E: DSTV HD Decoder Kit
        $p5 = DB::table('products')->insertGetId([
            'category_id' => $dstvProdCat,
            'service_id' => $dstvServiceId,
            'type' => 'dstv_package',
            'name' => 'DSTV HD Decoder Complete Dish Kit with 1 Month Compact & Installation',
            'slug' => 'dstv-hd-decoder-complete-dish-kit-installation',
            'sku' => 'ZAY-DSTV-HD1',
            'summary' => 'Brand new DSTV HD decoder, metal dish, Smart LNB, cable roll, remote, HDMI cable, 1-month Compact subscription, and full installation.',
            'description' => 'Get installed today and enjoy crisp High Definition sports, movies, documentaries, and news. Includes 1-month Compact subscription, complete 90cm metal dish, and professional alignment by certified technicians.',
            'price' => 4500000, // ₦45,000 in kobo
            'compare_at_price' => 5000000,
            'price_label' => 'Complete Package',
            'requires_quote' => false,
            'installation_included' => true,
            'warranty_text' => '1 Year MultiChoice Decoder Warranty',
            'specs' => json_encode([
                'Resolution' => 'Full HD 1080i with Dolby Digital 5.1 Audio',
                'Dish Size' => '90cm Heavy Duty Coated Metal Dish',
                'LNB' => 'Single Output Smart LNB',
                'Subscription' => '1 Month DSTV Compact Bouquet Included',
            ]),
            'brand' => 'DSTV / MultiChoice',
            'image' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?q=80&w=800&auto=format&fit=crop',
            'stock_qty' => 25,
            'track_stock' => true,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_inclusions')->insert([
            ['product_id' => $p5, 'item_name' => 'DSTV HD Decoder (Model 9S / Latest)', 'quantity' => '1 Unit', 'description' => 'HDMI & RCA output', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p5, 'item_name' => 'Original DSTV Remote Control with Batteries', 'quantity' => '1 Unit', 'description' => 'B7 model remote', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p5, 'item_name' => '90cm Satellite Dish & Wall Mounting Bracket', 'quantity' => '1 Set', 'description' => 'Zinc-galvanized rust resistant', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p5, 'item_name' => 'Smart LNB & 25m RG6 Coaxial Cable Roll', 'quantity' => '1 Set', 'description' => 'With high quality F-connectors', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $p5, 'item_name' => 'Professional Signal Meter Alignment & Setup', 'quantity' => 'Included', 'description' => 'Guaranteed 95%+ signal level', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Product F: DSTV Explora Ultra
        $p6 = DB::table('products')->insertGetId([
            'category_id' => $dstvProdCat,
            'service_id' => $dstvServiceId,
            'type' => 'dstv_package',
            'name' => 'DSTV Explora Ultra 4K Decoder + Smart LNB & Installation',
            'slug' => 'dstv-explora-ultra-4k-smart-lnb-installation',
            'sku' => 'ZAY-DSTV-ULTRA',
            'summary' => 'Record up to 110 hours, pause live TV, access Catch Up, BoxOffice, Showmax & Netflix in Ultra 4K HDR.',
            'description' => 'The ultimate television entertainment powerhouse. Features 4K HDR video streaming, built-in Wi-Fi for Showmax and Netflix, massive hard drive recording, and seamless Extra View capability.',
            'price' => 12500000, // ₦125,000 in kobo
            'compare_at_price' => 14000000,
            'price_label' => 'Installed Price',
            'requires_quote' => false,
            'installation_included' => true,
            'warranty_text' => '1 Year MultiChoice Official Warranty',
            'specs' => json_encode([
                'Video' => '4K Ultra High Definition (UHD) with HDR',
                'Hard Drive' => 'Built-in 1TB PVR (Record up to 110 Hours)',
                'Apps' => 'Integrated Netflix, Showmax, YouTube, Prime Video',
                'Connectivity' => 'Dual-Band Wi-Fi + Gigabit Ethernet LAN',
            ]),
            'brand' => 'DSTV / MultiChoice',
            'image' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?q=80&w=800&auto=format&fit=crop',
            'stock_qty' => 6,
            'track_stock' => true,
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Projects (Showcase Proof of Work)
        $lagosStateId = DB::table('states')->where('code', 'LA')->value('id');
        $abujaStateId = DB::table('states')->where('code', 'FC')->value('id');
        $oyoStateId = DB::table('states')->where('code', 'OY')->value('id');

        $proj1 = DB::table('projects')->insertGetId([
            'service_id' => $solarServiceId,
            'state_id' => $lagosStateId,
            'city' => 'Lekki Phase 1',
            'title' => '5kVA 48V Hybrid Solar Installation for 5-Bedroom Duplex',
            'slug' => '5kva-hybrid-solar-installation-lekki-phase-1',
            'summary' => 'Replaced daily 12-hour generator runs with a silent 5kVA Deye inverter and 5.12kWh Felicity LiFePO4 battery array.',
            'body' => 'The homeowner previously spent over ₦280,000 monthly on petrol for a noisy 8kVA generator. We installed an immaculate 5kVA system with 6x 550W Canadian Solar panels mounted on the roof terrace. Now, their inverter air conditioners, refrigerators, and water pumping operate 24/7 silently with zero petrol burned on sunny days.',
            'client_name' => 'Engr. Babatunde A.',
            'completed_on' => '2026-08-14',
            'system_size' => '5kVA Inverter / 5.12kWh Lithium / 3.3kW Solar',
            'featured_image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=1200&auto=format&fit=crop',
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('project_videos')->insert([
            'project_id' => $proj1,
            'title' => 'Lekki 5kVA Solar Installation Walkthrough & Load Test',
            'description' => 'Tour the battery rack, inverter connection, trunking finish, and watch the load test running an inverter AC.',
            'transcript' => 'Here we are at Lekki Phase 1 inspecting the completed 5kVA solar installation. The inverter is drawing 3.2kW from the solar panels while powering the living room air conditioner and deep freezer...',
            'source' => 'upload',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'ready',
            'duration_seconds' => 145,
            'poster_path' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=1200&auto=format&fit=crop',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $proj2 = DB::table('projects')->insertGetId([
            'service_id' => $starlinkServiceId,
            'state_id' => $lagosStateId,
            'city' => 'Victoria Island',
            'title' => 'Starlink Mast Setup & Enterprise Mesh Wi-Fi for Tech Agency',
            'slug' => 'starlink-setup-enterprise-mesh-victoria-island',
            'summary' => 'Custom 3-meter rooftop pipe mast elevation overcoming adjacent high-rise shadows with 220 Mbps speed distribution across 3 floors.',
            'body' => 'A design and fintech office on Victoria Island suffered from unreliable fibre cuts. We deployed Starlink as their primary high-speed trunk with zero obstruction elevation, paired with a 3-node enterprise Wi-Fi 6 mesh supporting over 45 concurrent workstations seamlessly.',
            'client_name' => 'Apex Digital Labs',
            'completed_on' => '2026-09-02',
            'system_size' => 'Starlink Gen 3 + 3-Node Wi-Fi 6 Mesh',
            'featured_image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?q=80&w=1200&auto=format&fit=crop',
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $proj3 = DB::table('projects')->insertGetId([
            'service_id' => $solarServiceId,
            'state_id' => $abujaStateId,
            'city' => 'Maitama, Abuja',
            'title' => '10kVA Commercial Hybrid Solar Setup for Private Medical Clinic',
            'slug' => '10kva-solar-clinic-maitama-abuja',
            'summary' => 'Critical continuous power for medical refrigerators, laboratory equipment, theater lighting, and administrative computers.',
            'body' => 'Medical facilities cannot afford even a 3-second power drop. We installed a robust 10kVA parallel dual-inverter configuration with 10.24kWh high-voltage lithium storage and generator auto-cranking backup logic.',
            'client_name' => 'St. Luke Health Specialists',
            'completed_on' => '2026-07-28',
            'system_size' => '10kVA / 10.24kWh Lithium / 6.6kW Array',
            'featured_image' => 'https://images.unsplash.com/photo-1548337138-e87d889cc369?q=80&w=1200&auto=format&fit=crop',
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $proj4 = DB::table('projects')->insertGetId([
            'service_id' => $dstvServiceId,
            'state_id' => $oyoStateId,
            'city' => 'Bodija, Ibadan',
            'title' => 'DSTV Explora Ultra Extra View & 3-Room Heartbeat Setup',
            'slug' => 'dstv-extra-view-heartbeat-bodija-ibadan',
            'summary' => 'Triple view installation allowing master bedroom, kids lounge, and main parlour to watch separate DSTV channels simultaneously.',
            'body' => 'Resolved a persistent signal loss issue and upgraded old twin LNB to a modern Smart LNB with calibrated heartbeat signal over quality RG6 cabling.',
            'client_name' => 'Alhaji Rasheed K.',
            'completed_on' => '2026-09-18',
            'system_size' => 'DSTV Explora Ultra + 2x HD Decoders',
            'featured_image' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?q=80&w=1200&auto=format&fit=crop',
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Maintenance Plans
        DB::table('maintenance_plans')->insert([
            [
                'name' => 'Residential Solar Essential Care',
                'slug' => 'residential-solar-essential',
                'service_id' => $solarMaintServiceId,
                'description' => '2 scheduled visits per year: panel de-ionized cleaning, terminal torque check, and battery health audit.',
                'price' => 3500000, // ₦35,000 per year
                'billing_period' => 'annual',
                'visits_per_year' => 2,
                'inclusions' => json_encode([
                    '2x Thorough solar panel washes to eliminate dust, grime, and bird soot',
                    'Inverter cooling vent cleaning and thermal camera hotspot check',
                    'Battery terminal cleaning, cable retightening, and cycle audit',
                    'Priority response within 24 hours for emergency breakdown calls',
                ]),
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium Home & Satellite Care',
                'slug' => 'premium-home-satellite-care',
                'service_id' => $solarMaintServiceId,
                'description' => '4 quarterly visits per year covering Solar, Starlink mast alignment, and DSTV signal tuning.',
                'price' => 8500000, // ₦85,000 per year
                'billing_period' => 'annual',
                'visits_per_year' => 4,
                'inclusions' => json_encode([
                    '4x Quarterly preventive maintenance visits',
                    'Complete solar system wattage benchmarking and panel washes',
                    'Starlink dish inspection, cable inspection, and firmware optimization',
                    'DSTV dish realignment and signal peaking with digital meters',
                    'Same-day priority emergency callout support',
                    '10% discount on all spare parts and upgrade equipment',
                ]),
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Commercial Facility Power & Network AMC',
                'slug' => 'commercial-facility-amc',
                'service_id' => $solarMaintServiceId,
                'description' => 'Monthly dedicated maintenance for businesses, clinics, schools, and estate facilities.',
                'price' => 25000000, // ₦250,000 per year
                'billing_period' => 'annual',
                'visits_per_year' => 12,
                'inclusions' => json_encode([
                    '12x Monthly comprehensive maintenance visits',
                    'Full electrical safety check, earthing resistance testing, and infrared scan',
                    'Emergency callout response within 4 hours',
                    'Dedicated assigned senior engineer with direct phone line',
                    'Monthly executive energy generation and savings audit report',
                ]),
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 7. FAQs
        DB::table('faqs')->insert([
            [
                'category' => 'solar',
                'service_id' => $solarServiceId,
                'question' => 'How many hours will my solar inverter system last when NEPA / grid goes off?',
                'answer' => 'Your runtime depends on the battery capacity and what appliances are active. Our 1.5kVA system provides 8–14 hours of continuous power for TVs, lights, decoders, and fans. Our 5kVA system with a 5.12kWh lithium battery will run essential household appliances 24/7, replenishing its charge automatically each day via solar panels.',
                'sort_order' => 1,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'solar',
                'service_id' => $solarServiceId,
                'question' => 'Can a solar system power my Air Conditioner and Pumping Machine?',
                'answer' => 'Yes! Systems rated 3.5kVA or 5kVA and above with adequate lithium battery capacity and solar arrays are specifically designed to power 1HP to 1.5HP Inverter Air Conditioners and 0.75HP–1HP water pumping machines seamlessly.',
                'sort_order' => 2,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'starlink',
                'service_id' => $starlinkServiceId,
                'question' => 'Why do I need professional Starlink installation instead of just putting it on the ground?',
                'answer' => 'Starlink relies on low-earth orbit satellites moving across the sky. Nearby trees, parapet walls, or neighboring buildings cause micro-obstructions that result in dropped video calls and gaming lag. Our professional elevated roof masts guarantee 100% zero obstruction, weather-sealed cabling into your building, and whole-home Wi-Fi mesh distribution.',
                'sort_order' => 3,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'dstv',
                'service_id' => $dstvServiceId,
                'question' => 'What causes the DSTV "E48-32 No Signal" error and how do you fix it?',
                'answer' => 'E48-32 indicates that your decoder is receiving inadequate or no satellite signal. Common causes include a loosened dish shifted by heavy wind, a faulty or water-damaged LNB, or degraded coaxial cabling. Our technicians use digital signal spectrum meters to pinpoint the problem and realign the dish to maximum strength in minutes.',
                'sort_order' => 4,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'general',
                'service_id' => null,
                'question' => 'Do you provide services outside Lagos?',
                'answer' => 'Yes! We operate nationwide across all 36 Nigerian states and the FCT Abuja. Equipment delivery and certified technician deployment are scheduled conveniently to your location with fixed state logistics fees.',
                'sort_order' => 5,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'payment',
                'service_id' => null,
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept secure instant online card and USSD payments via Paystack, as well as direct corporate bank transfers to our official bank accounts with receipt verification. For large solar installations, milestone-based deposit arrangements are also available.',
                'sort_order' => 6,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 8. Testimonials
        DB::table('testimonials')->insert([
            [
                'name' => 'Chief Oladipo Adeleke',
                'occupation' => 'Business Executive, Lekki Phase 1, Lagos',
                'rating' => 5,
                'message' => 'Zayin installed a 5kVA hybrid solar system in my residence 8 months ago. I have completely stopped buying petrol for my generator during the day. The installation was neat, concealed, and finished in a single day.',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mrs. Ngozi Okonjo',
                'occupation' => 'Architect, Maitama, Abuja',
                'rating' => 5,
                'message' => 'Their Starlink rooftop installation was exceptional. They fabricated a clean steel pole that cleared the trees around my compound and set up mesh Wi-Fi. My whole family now streams with zero buffering.',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dr. Femi Adelekan',
                'occupation' => 'Medical Director, Bodija, Ibadan',
                'rating' => 5,
                'message' => 'We called Zayin for DSTV Extra View across three consulting rooms after two previous technicians failed to configure the heartbeat cable. Their technician sorted it in 45 minutes with crystal clear HD signal.',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barrister Chukwuma Obi',
                'occupation' => 'Managing Partner, Port Harcourt',
                'rating' => 5,
                'message' => 'Fast delivery to Port Harcourt and the technician was courteous and knowledgeable. The 3.5kVA system powers our legal chambers without any downtime.',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
