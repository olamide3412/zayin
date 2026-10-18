<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessBankAccount;
use App\Models\Customer;
use App\Models\InvoiceItemCatalog;
use App\Models\InvoiceTemplate;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Business
        $business = Business::firstOrCreate(
            ['name' => 'Zayin Global Limited'],
            [
                'tagline' => 'Solar, Starlink & DSTV Installations',
                'rc_number' => 'RC-7842910',
                'tin' => 'TIN-28491048-0001',
                'phone' => '+234 803 123 4567',
                'phone_alt' => '+234 812 345 6789',
                'email' => 'invoicing@zayin.com.ng',
                'website' => 'https://zayin.com.ng',
                'address_line1' => 'Plot 14, Commercial Avenue',
                'address_line2' => 'Suite 3B, Tech Park',
                'city' => 'Ikeja',
                'state' => 'Lagos State',
                'vat_rate_bps' => 750, // 7.5%
                'default_vat_mode' => 'exclusive',
                'default_notes' => 'Thank you for choosing Zayin Global Limited. Quality energy & telecom solutions built to last.',
                'default_terms' => "1. 70% commitment deposit required before equipment mobilization; 30% balance payable upon installation commissioning.\n2. Inverter & battery equipment carries a 24-month manufacturer warranty; workmanship guaranteed for 6 months.\n3. Goods once supplied in good condition are not returnable after 7 days.",
                'footer_text' => 'Zayin Global Limited • RC 7842910 • TIN 28491048-0001 • Ikeja, Lagos',
            ]
        );

        // 2. Bank Accounts
        $bankAccounts = [
            [
                'bank_name' => 'Zenith Bank',
                'account_name' => 'ZAYIN GLOBAL LTD',
                'account_number' => '1018492048',
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'bank_name' => 'Guaranty Trust Bank (GTBank)',
                'account_name' => 'ZAYIN GLOBAL LTD',
                'account_number' => '0129481023',
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'bank_name' => 'Moniepoint Microfinance Bank',
                'account_name' => 'ZAYIN GLOBAL LTD',
                'account_number' => '8294019283',
                'is_default' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($bankAccounts as $bank) {
            BusinessBankAccount::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'account_number' => $bank['account_number'],
                ],
                array_merge($bank, ['business_id' => $business->id])
            );
        }

        // 3. Invoice Templates
        $templates = [
            [
                'business_id' => $business->id,
                'name' => 'Classic Professional',
                'layout' => 'classic',
                'accent_color' => '#1e3a8a', // Deep corporate navy
                'font_family' => 'DejaVu Sans',
                'logo_position' => 'left',
                'document_title' => 'INVOICE',
                'show_fields' => [
                    'rc_number' => true,
                    'tin' => true,
                    'bank_accounts' => true,
                    'amount_in_words' => true,
                    'signature_line' => true,
                    'notes' => true,
                    'terms' => true,
                ],
                'show_watermark' => true,
                'number_prefix' => 'ZYN-',
                'number_padding' => 4,
                'is_default' => true,
            ],
            [
                'business_id' => $business->id,
                'name' => 'Modern Emerald',
                'layout' => 'modern',
                'accent_color' => '#0F5132', // Nigerian Emerald Green
                'font_family' => 'DejaVu Sans',
                'logo_position' => 'left',
                'document_title' => 'TAX INVOICE',
                'show_fields' => [
                    'rc_number' => true,
                    'tin' => true,
                    'bank_accounts' => true,
                    'amount_in_words' => true,
                    'signature_line' => true,
                    'notes' => true,
                    'terms' => true,
                ],
                'show_watermark' => true,
                'number_prefix' => 'ZYN-',
                'number_padding' => 4,
                'is_default' => false,
            ],
            [
                'business_id' => $business->id,
                'name' => 'Clean Minimal',
                'layout' => 'minimal',
                'accent_color' => '#111827', // Slate Charcoal
                'font_family' => 'DejaVu Sans',
                'logo_position' => 'center',
                'document_title' => 'INVOICE',
                'show_fields' => [
                    'rc_number' => true,
                    'tin' => true,
                    'bank_accounts' => true,
                    'amount_in_words' => true,
                    'signature_line' => false,
                    'notes' => true,
                    'terms' => true,
                ],
                'show_watermark' => false,
                'number_prefix' => 'ZYN-',
                'number_padding' => 4,
                'is_default' => false,
            ],
        ];

        foreach ($templates as $tmpl) {
            InvoiceTemplate::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'layout' => $tmpl['layout'],
                ],
                $tmpl
            );
        }

        // 4. Starter Catalog Items (§6)
        $catalogItems = [
            [
                'name' => '5kVA Felicity Lithium Solar Inverter (Pure Sine Wave)',
                'description' => '48V Pure Sine Wave Hybrid Solar Inverter with built-in 80A MPPT Charge Controller and Wi-Fi monitoring.',
                'default_price_kobo' => 85000000, // ₦850,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 15,
                'category' => 'Inverters',
            ],
            [
                'name' => '10kVA Felicity Solar Hybrid Inverter',
                'description' => '48V High Frequency Pure Sine Wave Dual MPPT Inverter suitable for 3-phase and heavy residential loads.',
                'default_price_kobo' => 175000000, // ₦1,750,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 8,
                'category' => 'Inverters',
            ],
            [
                'name' => '5.12kWh Felicity LiFePO4 Lithium Battery (48V 100Ah)',
                'description' => 'Deep Cycle Lithium Iron Phosphate Battery with smart BMS, 6,000 cycle life, wall-mount design.',
                'default_price_kobo' => 125000000, // ₦1,250,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 18,
                'category' => 'Batteries',
            ],
            [
                'name' => '10.24kWh Felicity LiFePO4 Lithium Battery (48V 200Ah)',
                'description' => 'High capacity standing floor battery storage unit with high discharge rate & CAN/RS485 communication.',
                'default_price_kobo' => 240000000, // ₦2,400,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 6,
                'category' => 'Batteries',
            ],
            [
                'name' => '550W Jinko Tier-1 Monocrystalline Solar Panel',
                'description' => 'High-efficiency half-cell mono solar PV module with multi-busbar technology and anti-PID certification.',
                'default_price_kobo' => 11500000, // ₦115,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 42,
                'category' => 'Solar Panels',
            ],
            [
                'name' => '600W Canadian Solar Tier-1 Panel',
                'description' => 'Bi-facial high output monocrystalline PV panel designed for optimal Nigerian sun irradiance.',
                'default_price_kobo' => 13500000, // ₦135,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 25,
                'category' => 'Solar Panels',
            ],
            [
                'name' => 'Starlink Standard Kit (Gen 3)',
                'description' => 'Complete high-speed low-latency satellite internet kit including dish, Wi-Fi 6 router, power supply & cables.',
                'default_price_kobo' => 59000000, // ₦590,000.00
                'unit' => 'kit',
                'is_vatable' => true,
                'usage_count' => 30,
                'category' => 'Starlink',
            ],
            [
                'name' => 'Starlink High-Performance Flat Kit',
                'description' => 'Enterprise/in-motion flat high performance kit with wide field of view and enhanced GPS capabilities.',
                'default_price_kobo' => 210000000, // ₦2,100,000.00
                'unit' => 'kit',
                'is_vatable' => true,
                'usage_count' => 4,
                'category' => 'Starlink',
            ],
            [
                'name' => 'Starlink Gen 3 Pipe Adapter Mount',
                'description' => 'Heavy-duty aluminum mast pipe mount adapter for Starlink standard dishes.',
                'default_price_kobo' => 4500000, // ₦45,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 22,
                'category' => 'Starlink Accessories',
            ],
            [
                'name' => 'Starlink Ridge Mount',
                'description' => 'Ballasted roof ridge mount requiring no roof penetration, suitable for sloped shingles and sheet roofs.',
                'default_price_kobo' => 9500000, // ₦95,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 12,
                'category' => 'Starlink Accessories',
            ],
            [
                'name' => 'DSTV Explora 3A Decoder + Smart LNB',
                'description' => 'Ultra HD personal video recorder decoder with smart LNB, remote control, and HDMI cable.',
                'default_price_kobo' => 7800000, // ₦78,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 16,
                'category' => 'DSTV Equipment',
            ],
            [
                'name' => 'DSTV HD Zapper Decoder (Single View)',
                'description' => 'Compact High Definition satellite TV decoder with remote control and RCA cables.',
                'default_price_kobo' => 2200000, // ₦22,000.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 20,
                'category' => 'DSTV Equipment',
            ],
            [
                'name' => '90cm Steel Satellite Dish with LNB Arm & Bracket',
                'description' => 'Galvanized outdoor satellite antenna with elevation adjustment and corrosion-resistant powder coat.',
                'default_price_kobo' => 1850000, // ₦18,500.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 19,
                'category' => 'Satellite Accessories',
            ],
            [
                'name' => 'RG6 Coaxial Cable (100m Roll, High Density Shielding)',
                'description' => 'Solid copper conductor with dual-layer aluminum foil shield for zero signal loss over distance.',
                'default_price_kobo' => 2800000, // ₦28,000.00
                'unit' => 'roll',
                'is_vatable' => true,
                'usage_count' => 14,
                'category' => 'Cables & Wiring',
            ],
            [
                'name' => '16mm² Double-Insulated Copper Battery Cable (per metre)',
                'description' => 'Ultra-flexible oxygen-free pure copper cable rated for heavy inverter DC current transmission.',
                'default_price_kobo' => 450000, // ₦4,500.00
                'unit' => 'm',
                'is_vatable' => true,
                'usage_count' => 38,
                'category' => 'Cables & Wiring',
            ],
            [
                'name' => '4mm² Single-Core PV Solar DC Cable (per metre)',
                'description' => 'TUV/UL certified UV-resistant cross-linked halogen-free solar connection cable (Red/Black).',
                'default_price_kobo' => 140000, // ₦1,400.00
                'unit' => 'm',
                'is_vatable' => true,
                'usage_count' => 44,
                'category' => 'Cables & Wiring',
            ],
            [
                'name' => '63A DC Miniature Circuit Breaker (MCB) 2-Pole',
                'description' => 'High breaking capacity DC breaker for solar PV string and battery isolation protection.',
                'default_price_kobo' => 850000, // ₦8,500.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 27,
                'category' => 'Protection & Switchgear',
            ],
            [
                'name' => '600V DC Surge Protective Device (SPD) 2-Pole',
                'description' => 'Class II lightning and transient overvoltage surge arrester for solar installations.',
                'default_price_kobo' => 1250000, // ₦12,500.00
                'unit' => 'pcs',
                'is_vatable' => true,
                'usage_count' => 24,
                'category' => 'Protection & Switchgear',
            ],
            [
                'name' => 'Professional Solar Inverter System Installation & Testing',
                'description' => 'Complete physical installation, AC/DC distribution board wiring, earth grounding rod sinking, earthing resistance audit, and load balancing test.',
                'default_price_kobo' => 8500000, // ₦85,000.00
                'unit' => 'service',
                'is_vatable' => true,
                'usage_count' => 35,
                'category' => 'Installation Services',
            ],
            [
                'name' => 'Starlink High-Speed Internet Setup & Roof Mount Calibration',
                'description' => 'Precise satellite azimuth line-of-sight alignment, weatherproof conduit routing, Wi-Fi speed test and network configuration.',
                'default_price_kobo' => 3500000, // ₦35,000.00
                'unit' => 'service',
                'is_vatable' => true,
                'usage_count' => 29,
                'category' => 'Installation Services',
            ],
            [
                'name' => 'DSTV Multi-Room Installation & Distribution Cabling',
                'description' => 'Smart LNB signal alignment, heart-beat cable coupling, multi-point TV setup, and signal level optimization.',
                'default_price_kobo' => 2500000, // ₦25,000.00
                'unit' => 'service',
                'is_vatable' => true,
                'usage_count' => 19,
                'category' => 'Installation Services',
            ],
        ];

        foreach ($catalogItems as $item) {
            $normalized = InvoiceItemCatalog::normalizeName($item['name']);
            InvoiceItemCatalog::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'normalized_name' => $normalized,
                ],
                array_merge($item, [
                    'business_id' => $business->id,
                    'normalized_name' => $normalized,
                    'last_price_kobo' => $item['default_price_kobo'],
                    'last_used_at' => now(),
                ])
            );
        }

        // 5. Seed sample customers
        if (Customer::count() === 0) {
            Customer::create([
                'business_id' => $business->id,
                'name' => 'Apex Horizon Estates Ltd',
                'email' => 'facilities@apexhorizon.com.ng',
                'phone' => '+234 802 991 8822',
                'tin' => 'TIN-9812401-0001',
                'address' => "Block 8, Lekki Phase 1\nLagos State, Nigeria",
                'notes' => 'Preferred contact: Engr. Dapo Adeyemi',
            ]);

            Customer::create([
                'business_id' => $business->id,
                'name' => 'Dr. Ngozi Okonjo-Bello',
                'email' => 'dr.ngozi@gmail.com',
                'phone' => '+234 814 555 7890',
                'address' => "24 Victoria Garden City, Ikota\nAjah, Lagos State, Nigeria",
                'notes' => 'Residential solar install client',
            ]);
        }
    }
}
