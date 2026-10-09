<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\MaintenancePlan;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Services\Seo\SeoData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function about(): Response
    {
        $seo = new SeoData(
            title: 'About Us | Certified Solar, Starlink & DSTV Technicians',
            description: 'Learn about Zayin Technologies, our experienced electrical engineers, quality standards, and commitment to powering Nigerian homes and businesses.',
            breadcrumbs: [
                'Home' => url('/'),
                'About Us' => url('/about'),
            ]
        );

        return Inertia::render('About', [
            'seo' => $seo->toArray(),
        ]);
    }

    public function contact(): Response
    {
        $seo = new SeoData(
            title: 'Contact Us | Offices, Phone & WhatsApp',
            description: 'Get in touch with Zayin Technologies. Call our support team, chat on WhatsApp, or visit our Lagos, Abuja, and Ibadan offices.',
            breadcrumbs: [
                'Home' => url('/'),
                'Contact Us' => url('/contact'),
            ]
        );

        return Inertia::render('Contact', [
            'seo' => $seo->toArray(),
        ]);
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:120',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'phone' => normalize_nigerian_phone($validated['phone']),
            'email' => $validated['email'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return redirect()->back()->with('success', 'Thank you! Your message has been received. Our team will contact you shortly.');
    }

    public function faq(): Response
    {
        $faqs = Faq::published()->orderBy('sort_order')->get();

        $seo = new SeoData(
            title: 'Frequently Asked Questions (Solar, Starlink & DSTV)',
            description: 'Got questions about solar system runtimes, Starlink speeds, or DSTV signal errors? Find answers to commonly asked questions.',
            breadcrumbs: [
                'Home' => url('/'),
                'FAQs' => url('/faq'),
            ]
        );

        return Inertia::render('FAQ', [
            'faqs' => $faqs,
            'seo' => $seo->toArray(),
        ]);
    }

    public function maintenancePlans(): Response
    {
        $plans = MaintenancePlan::where('is_active', true)->orderBy('sort_order')->get();

        $seo = new SeoData(
            title: 'Annual Maintenance Contracts (AMC) & Service Plans',
            description: 'Protect your energy and entertainment investments with scheduled solar panel washing, inverter diagnostics, and satellite alignments.',
            breadcrumbs: [
                'Home' => url('/'),
                'Maintenance Plans' => url('/maintenance-plans'),
            ]
        );

        return Inertia::render('MaintenancePlans/Index', [
            'plans' => $plans,
            'seo' => $seo->toArray(),
        ]);
    }

    public function legal(string $slug): Response
    {
        $pages = [
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'description' => 'Our data handling practices in compliance with the Nigeria Data Protection Act (NDPA) 2023.',
            ],
            'terms' => [
                'title' => 'Terms of Service',
                'description' => 'Terms and conditions governing equipment supply, warranties, and technician service delivery.',
            ],
            'warranty' => [
                'title' => 'Warranty & Quality Guarantee',
                'description' => 'Details on our multi-year equipment warranties and certified workmanship guarantees.',
            ],
            'refund-policy' => [
                'title' => 'Refund & Cancellation Policy',
                'description' => 'Transparent policies regarding order cancellations, returns, and service refunds.',
            ],
        ];

        $page = $pages[$slug] ?? abort(404);

        $seo = new SeoData(
            title: $page['title'],
            description: $page['description'],
            breadcrumbs: [
                'Home' => url('/'),
                $page['title'] => url("/{$slug}"),
            ]
        );

        return Inertia::render('Legal/Show', [
            'pageSlug' => $slug,
            'pageTitle' => $page['title'],
            'seo' => $seo->toArray(),
        ]);
    }
}
