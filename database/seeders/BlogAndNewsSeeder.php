<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogAndNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $userId = $admin ? $admin->id : 1;

        BlogPost::truncate();
        News::truncate();

        $posts = [
            [
                'title' => 'How to Accurately Size a Solar Inverter System for Nigerian Homes',
                'category' => 'Solar & Inverters',
                'cover_image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=1200&auto=format&fit=crop',
                'excerpt' => 'Avoid blown inverters and premature battery death by calculating true inrush currents for inverter air conditioners, deep freezers, and pumping machines.',
                'body' => '<h3>Understanding True Electrical Load</h3><p>Many Nigerian homeowners fall victim to inadequate solar sizing because installers count only nominal running wattage while ignoring high starting surge currents (inrush inductive loads). An inverter AC rated at 800W can pull up to 2,400W for a split second upon compressor startup.</p><h3>Key Appliance Sizing Rules</h3><ul><li><strong>Inverter Air Conditioners (1HP / 1.5HP):</strong> Require at least a 3.5kVA or 5kVA pure sine wave inverter and a minimum 48V battery bank.</li><li><strong>Water Pumping Machines (0.75HP–1HP):</strong> Best powered during peak solar hours (11am–2pm) with dedicated solar priority programming to save battery cycle life.</li><li><strong>Deep Freezers:</strong> Modern inverter chest freezers consume under 120W running, making them ideal for 24/7 battery operation.</li></ul><h3>Lithium vs Lead-Acid</h3><p>LiFePO4 lithium batteries allow 80%–90% Depth of Discharge (DoD) without degradation, whereas lead-acid and tubular batteries suffer permanent plate sulfation if discharged below 50%.</p>',
            ],
            [
                'title' => 'SpaceX Starlink in Nigeria: Rooftop Obstructions & Lightning Earthing',
                'category' => 'Starlink Broadband',
                'cover_image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?q=80&w=1200&auto=format&fit=crop',
                'excerpt' => 'Everything you need to know about mounting Starlink standard and mini terminals securely against tropical thunderstorms, gusts, and tree canopies.',
                'body' => '<h3>The 100° Clear Sky Requirement</h3><p>Starlink dishes communicate with low-earth-orbit (LEO) satellites moving across the Nigerian sky at 27,000 km/h. Even a single mango tree branch or neighboring roof parapet cutting into the scan zone causes micro-disconnects during Zoom calls and streaming.</p><h3>Why Custom Galvanized Poles are Essential</h3><p>Standard roof kickstands included in retail boxes are not built for torrential West African rainy season storms. We engineer 2-inch galvanized steel poles anchored with heavy concrete expansion bolts and guy wires where elevation is required.</p><h3>Lightning & Surge Protection (SPD)</h3><p>In lightning-prone zones like Lagos, Abuja, and Port Harcourt, running Cat6 cables from the roof straight to indoor switches without an in-line Ethernet surge protector is a high electrical risk. Every Zayin installation includes dedicated copper grounding rods and shielded Cat6 cables with drip loops.</p>',
            ],
            [
                'title' => 'Troubleshooting DSTV E48-32 "No Signal" Errors & Extra View Tuning',
                'category' => 'DSTV & Satellite',
                'cover_image' => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?q=80&w=1200&auto=format&fit=crop',
                'excerpt' => 'Stop rain fade and frequent signal loss with proper digital satellite meter dish realignment, Smart LNB calibration, and multi-room heartbeat cabling.',
                'body' => '<h3>What Triggers the Dreaded E48-32 Error?</h3><p>The E48-32 message means your DSTV decoder cannot lock onto the IS-20 satellite transponder frequencies (68.5° East). The most frequent culprits are loose dish brackets buffeted by winds, corroded F-connectors, water seepage into low-grade coaxial cable, or skewed LNB tilt.</p><h3>Optimizing Signal Quality vs Signal Strength</h3><p>Signal Strength measures the power of the incoming wave, but <strong>Signal Quality</strong> determines picture clarity and rain immunity. Our technicians peak both metrics above 95% using digital spectrum analyzers.</p><h3>Setting Up Clean DSTV Extra View</h3><p>Watching separate channels on 2 or 3 decoders requires stable heartbeat signal cabling and properly assigned decoder user bands (UBs). Using a DStv Smart LNB eliminates the need for bulky external multi-switches in residential homes.</p>',
            ],
            [
                'title' => 'Harmattan Season Maintenance: Why Dusty Solar Panels Lose 35% Capacity',
                'category' => 'Battery Storage',
                'cover_image' => 'https://images.unsplash.com/photo-1508873696983-2df57046475a?q=80&w=1200&auto=format&fit=crop',
                'excerpt' => 'Dust, soot, and harmattan haze severely diminish solar irradiance. Learn the safe way to clean panels and check inverter cooling fans.',
                'body' => '<h3>The Impact of Saharan Dust Layers</h3><p>Between November and February, fine airborne dust settles on photovoltaic glass surfaces across Nigeria. Because cells are wired in series strings, shading or soiling on just two panels can bottleneck an entire array output by over a third.</p><h3>Safe Cleaning Rules</h3><ul><li>Never wash hot panels under blazing noon sunlight; thermal shock can crack tempered glass. Clean early morning or after sunset.</li><li>Use deionized or clean soft water with soft microfiber brushes. Avoid harsh detergents or abrasive sponges that scratch anti-reflective coatings.</li><li>Inspect inverter intake fan filters to prevent thermal throttling and component overheating.</li></ul>',
            ],
        ];

        foreach ($posts as $idx => $p) {
            BlogPost::create([
                'user_id' => $userId,
                'title' => $p['title'],
                'slug' => Str::slug($p['title']),
                'category' => $p['category'],
                'cover_image' => $p['cover_image'],
                'excerpt' => $p['excerpt'],
                'body' => $p['body'],
                'status' => 'published',
                'published_at' => now()->subDays(10 * ($idx + 1)),
                'created_at' => now()->subDays(10 * ($idx + 1)),
            ]);
        }

        // News updates
        $newsItems = [
            [
                'title' => 'Zayin Technologies Expands Rapid Field Dispatch to Port Harcourt & Asaba',
                'category' => 'announcement',
                'cover_image' => 'https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?q=80&w=1200&auto=format&fit=crop',
                'excerpt' => 'Expanding our South-South operations with fully equipped mobile technician vans and emergency solar battery inventory.',
                'body' => '<p>To meet surging commercial demand in Rivers and Delta States, Zayin Technologies has established dedicated logistics depots in Port Harcourt and Asaba. Field engineers are now available for 24-hour dispatch across the Niger Delta corridor.</p>',
            ],
            [
                'title' => 'Successful Commissioning of 50kVA Hybrid Solar Mini-Grid in Lekki Industrial Zone',
                'category' => 'news',
                'cover_image' => 'https://images.unsplash.com/photo-1497440001374-f26997328c1b?q=80&w=1200&auto=format&fit=crop',
                'excerpt' => 'Zero downtime achieved for a premier logistics hub using dual Deye high-voltage inverters and 80kWh Tier-1 lithium storage.',
                'body' => '<p>Our engineering team successfully signed off on a 50kVA three-phase solar plant powering 4 cold-storage units and 60 office workstations. The facility has reduced diesel generator run hours from 16 hours daily to zero.</p>',
            ],
        ];

        foreach ($newsItems as $idx => $n) {
            News::create([
                'user_id' => $userId,
                'title' => $n['title'],
                'slug' => Str::slug($n['title']),
                'category' => $n['category'],
                'cover_image' => $n['cover_image'],
                'excerpt' => $n['excerpt'],
                'body' => $n['body'],
                'status' => 'published',
                'published_at' => now()->subDays(5 * ($idx + 1)),
                'created_at' => now()->subDays(5 * ($idx + 1)),
            ]);
        }
    }
}
