<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Models\Project;
use App\Models\Faq;
use App\Services\Seo\SeoData;
use App\Services\Seo\JsonLdBuilder;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $categories = ServiceCategory::with(['services' => function ($q) {
            $q->published()->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        $seo = new SeoData(
            title: 'Our Engineering Services',
            description: 'Explore our specialized services in Solar & Inverter installations, Starlink high-speed satellite setups, and DSTV / Satellite TV in Nigeria.',
            breadcrumbs: [
                'Home' => url('/'),
                'Services' => url('/services'),
            ]
        );

        return Inertia::render('Services/Index', [
            'categories' => $categories,
            'seo' => $seo->toArray(),
        ]);
    }

    public function show(string $slug): Response
    {
        $service = Service::published()
            ->where('slug', $slug)
            ->with(['category'])
            ->firstOrFail();

        $relatedProducts = Product::published()
            ->where('service_id', $service->id)
            ->with(['inclusions'])
            ->take(4)
            ->get();

        $relatedProjects = Project::published()
            ->where('service_id', $service->id)
            ->with(['state', 'videos'])
            ->take(3)
            ->get();

        $faqs = Faq::published()
            ->where(function ($q) use ($service) {
                $q->where('service_id', $service->id)
                  ->orWhere('category', $service->category?->slug);
            })
            ->take(6)
            ->get();

        $breadcrumbs = [
            'Home' => url('/'),
            'Services' => url('/services'),
            $service->name => url("/services/{$service->slug}"),
        ];

        $seo = new SeoData(
            title: $service->name,
            description: $service->summary,
            jsonLd: [
                JsonLdBuilder::service($service),
                JsonLdBuilder::breadcrumbs($breadcrumbs),
            ],
            breadcrumbs: $breadcrumbs
        );

        return Inertia::render('Services/Show', [
            'service' => $service,
            'relatedProducts' => $relatedProducts,
            'relatedProjects' => $relatedProjects,
            'faqs' => $faqs,
            'seo' => $seo->toArray(),
        ]);
    }
}
