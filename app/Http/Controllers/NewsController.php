<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewsController extends Controller
{
    // ─── Public Routes ────────────────────────────────────────────────────────

    /**
     * Public news listing page.
     */
    public function publicIndex(Request $request)
    {
        $query = News::published()->with('author:id,name');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('published_at', $request->date);
        }

        // Sort
        if ($request->sort === 'oldest') {
            $query->oldest('published_at');
        } else {
            $query->latest('published_at');
        }

        // If ?view=list is passed (e.g. from "More →" button), always show full list
        if ($request->view === 'list') {
            $layoutStyle = 'editorial_list';
            $perPage     = 12;
        } else {
            $layoutStyle = \App\Models\Setting::get('news_layout_style', 'hero_split');
            // Hero split only shows 4 articles (1 featured + 3 side), so limit to 4
            $perPage     = ($layoutStyle === 'hero_split') ? 4 : 12;
        }

        return Inertia::render('News/Index', [
            'articles'    => $query->paginate($perPage),
            'filters'     => $request->only(['search', 'category', 'sort', 'date', 'view']),
            'layoutStyle' => $layoutStyle,
        ])->withViewData([
            'meta' => [
                'title' => 'News, Deployments & Field Bulletins | Zayin Technologies',
                'description' => 'Stay updated with regional solar projects, Starlink field milestones, and corporate announcements from Zayin Technologies.'
            ]
        ]);
    }


    /**
     * Public single news article.
     */
    public function publicShow(string $slug)
    {
        $article = News::where('slug', $slug)
            ->where('status', 'published')
            ->with('author:id,name')
            ->firstOrFail();

        $related = News::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->limit(3)
            ->get(['id', 'title', 'slug', 'cover_image', 'published_at', 'category']);

        return Inertia::render('News/Show', [
            'article' => $article,
            'related' => $related,
        ])->withViewData([
            'meta' => [
                'title' => $article->title . ' | News | Zayin Technologies',
                'description' => $article->excerpt ?: Str::limit(strip_tags($article->body), 150),
                'image' => $article->cover_image ? asset($article->cover_image) : asset('images/logo.png'),
                'type' => 'article'
            ]
        ]);
    }

    // ─── Admin Routes ─────────────────────────────────────────────────────────

    /**
     * Admin news list.
     */
    public function index(Request $request)
    {
        $query = News::with('author:id,name')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return Inertia::render('Auth/News/Index', [
            'articles' => $query->paginate(15),
            'filters'  => $request->only(['search', 'status', 'category']),
        ]);
    }

    /**
     * Show admin create form.
     */
    public function create()
    {
        return Inertia::render('Auth/News/Form', ['article' => null]);
    }

    /**
     * Store a new news article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'excerpt'       => 'nullable|string|max:500',
            'body'          => 'required|string',
            'cover_image'   => 'nullable|string',
            'media_gallery' => 'nullable|array',
            'category'      => 'required|in:news,event,announcement,media',
            'status'        => 'required|in:draft,published',
        ]);

        $validated['user_id']      = Auth::id();
        $validated['slug']         = Str::slug($validated['title']) . '-' . time();
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        News::create($validated);

        return redirect()->route('admin.news.index')->with('message', 'News article created successfully!');
    }

    /**
     * Show admin edit form.
     */
    public function edit(News $news)
    {
        return Inertia::render('Auth/News/Form', ['article' => $news]);
    }

    /**
     * Update an existing news article.
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'excerpt'       => 'nullable|string|max:500',
            'body'          => 'required|string',
            'cover_image'   => 'nullable|string',
            'media_gallery' => 'nullable|array',
            'category'      => 'required|in:news,event,announcement,media',
            'status'        => 'required|in:draft,published',
        ]);

        if ($validated['status'] === 'published' && !$news->published_at) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')->with('message', 'News article updated successfully!');
    }

    /**
     * Delete a news article.
     */
    public function destroy(News $news)
    {
        $news->delete();
        return redirect()->route('admin.news.index')->with('message', 'News article deleted.');
    }
}
