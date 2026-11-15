<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectVideo;
use App\Models\Service;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Project::with(['service', 'state'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('system_size', 'like', "%{$search}%");
            });
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            }
        }

        $perPage = max(25, (int) $request->input('per_page', 25));

        $projects = $query->paginate($perPage)->withQueryString()->through(function ($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'service' => $p->service?->name ?? 'General',
                'service_id' => $p->service_id,
                'state' => $p->state?->name,
                'city' => $p->city,
                'client_name' => $p->client_name,
                'system_size' => $p->system_size,
                'completed_on' => $p->completed_on?->format('Y-m-d'),
                'featured_image' => $p->featured_image,
                'is_featured' => (bool) $p->is_featured,
                'is_published' => (bool) $p->is_published,
                'published_at' => $p->published_at?->format('d M Y'),
            ];
        });

        $counts = [
            'all' => Project::count(),
            'published' => Project::where('is_published', true)->count(),
            'draft' => Project::where('is_published', false)->count(),
            'featured' => Project::where('is_featured', true)->count(),
        ];

        $services = Service::orderBy('name')->get(['id', 'name']);
        $states = State::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Auth/Projects/Index', [
            'projects' => $projects,
            'counts' => $counts,
            'services' => $services,
            'states' => $states,
            'filters' => $request->only(['search', 'service_id', 'status']),
        ]);
    }

    public function create(): Response
    {
        $services = Service::orderBy('name')->get(['id', 'name']);
        $states = State::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Auth/Projects/Form', [
            'project' => null,
            'services' => $services,
            'states' => $states,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'state_id' => 'nullable|exists:states,id',
            'city' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:150',
            'system_size' => 'nullable|string|max:150',
            'completed_on' => 'nullable|date',
            'summary' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array|max:10',
            'gallery.*' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
            'video_title' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . $counter++;
        }

        $project = Project::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'service_id' => $validated['service_id'] ?? null,
            'state_id' => $validated['state_id'] ?? null,
            'city' => $validated['city'] ?? null,
            'client_name' => $validated['client_name'] ?? null,
            'system_size' => $validated['system_size'] ?? null,
            'completed_on' => $validated['completed_on'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'body' => $validated['body'] ?? null,
            'featured_image' => $validated['featured_image'] ?? null,
            'gallery' => $validated['gallery'] ?? [],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_published' => $validated['is_published'] ?? true,
            'published_at' => !empty($validated['is_published']) ? now() : null,
        ]);

        if (!empty($validated['video_url'])) {
            ProjectVideo::create([
                'project_id' => $project->id,
                'title' => $validated['video_title'] ?: ($project->title . ' Walkthrough'),
                'source' => 'youtube',
                'external_url' => $validated['video_url'],
                'poster_path' => $project->featured_image,
                'status' => 'ready',
            ]);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project): Response
    {
        $project->load(['service', 'state', 'videos']);

        $services = Service::orderBy('name')->get(['id', 'name']);
        $states = State::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        $firstVideo = $project->videos->first();

        return Inertia::render('Auth/Projects/Form', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'service_id' => $project->service_id,
                'state_id' => $project->state_id,
                'city' => $project->city,
                'client_name' => $project->client_name,
                'system_size' => $project->system_size,
                'completed_on' => $project->completed_on?->format('Y-m-d'),
                'summary' => $project->summary,
                'body' => $project->body,
                'featured_image' => $project->featured_image,
                'gallery' => $project->gallery ?? [],
                'video_url' => $firstVideo?->external_url ?? '',
                'video_title' => $firstVideo?->title ?? '',
                'is_featured' => (bool) $project->is_featured,
                'is_published' => (bool) $project->is_published,
            ],
            'services' => $services,
            'states' => $states,
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'state_id' => 'nullable|exists:states,id',
            'city' => 'nullable|string|max:100',
            'client_name' => 'nullable|string|max:150',
            'system_size' => 'nullable|string|max:150',
            'completed_on' => 'nullable|date',
            'summary' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'gallery' => 'nullable|array|max:10',
            'gallery.*' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
            'video_title' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($validated['title'] !== $project->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Project::where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
                $slug = "{$originalSlug}-" . $counter++;
            }
            $validated['slug'] = $slug;
        }

        if (!empty($validated['is_published']) && !$project->is_published) {
            $validated['published_at'] = now();
        }

        $videoUrl = $validated['video_url'] ?? null;
        $videoTitle = $validated['video_title'] ?? null;
        unset($validated['video_url'], $validated['video_title']);

        $project->update($validated);

        if (!empty($videoUrl)) {
            $video = $project->videos()->first();
            if ($video) {
                $video->update([
                    'title' => $videoTitle ?: ($project->title . ' Walkthrough'),
                    'external_url' => $videoUrl,
                    'poster_path' => $project->featured_image,
                ]);
            } else {
                ProjectVideo::create([
                    'project_id' => $project->id,
                    'title' => $videoTitle ?: ($project->title . ' Walkthrough'),
                    'source' => 'youtube',
                    'external_url' => $videoUrl,
                    'poster_path' => $project->featured_image,
                    'status' => 'ready',
                ]);
            }
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}

