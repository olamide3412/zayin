<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\State;
use App\Services\Seo\SeoData;
use App\Services\Seo\JsonLdBuilder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $services = Service::published()->get(['id', 'name', 'slug']);
        $states = State::where('is_active', true)->whereHas('projects')->get(['id', 'name']);

        $query = Project::published()->with(['service', 'state', 'videos']);

        if ($request->filled('service')) {
            $query->whereHas('service', fn($q) => $q->where('slug', $request->input('service')));
        }

        if ($request->filled('state')) {
            $query->where('state_id', $request->input('state'));
        }

        $projects = $query->latest('completed_on')->paginate(9)->withQueryString();

        $seo = new SeoData(
            title: 'Our Completed Projects & Video Proof',
            description: 'Watch video walkthroughs and view real photos of our completed solar installations, Starlink setups, and DSTV deployments across Nigeria.',
            breadcrumbs: [
                'Home' => url('/'),
                'Projects' => url('/projects'),
            ]
        );

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'services' => $services,
            'states' => $states,
            'filters' => $request->only(['service', 'state']),
            'seo' => $seo->toArray(),
        ]);
    }

    public function show(string $slug): Response
    {
        $project = Project::published()
            ->where('slug', $slug)
            ->with(['service', 'state', 'videos', 'products'])
            ->firstOrFail();

        $relatedProjects = Project::published()
            ->where('service_id', $project->service_id)
            ->where('id', '!=', $project->id)
            ->with(['state'])
            ->take(3)
            ->get();

        $breadcrumbs = [
            'Home' => url('/'),
            'Projects' => url('/projects'),
            $project->title => url()->current(),
        ];

        $seo = new SeoData(
            title: "{$project->title} – {$project->city}",
            description: $project->summary,
            ogImage: $project->featured_image,
            breadcrumbs: $breadcrumbs
        );

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
            'seo' => $seo->toArray(),
        ]);
    }
}
