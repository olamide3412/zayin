<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    // ─── Public Routes ────────────────────────────────────────────────────────

    /**
     * Public blog listing page.
     */
    public function publicIndex(Request $request)
    {
        $query = BlogPost::published()->with('author:id,name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
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

        // ?view=list → full editorial list (from "More →" button)
        if ($request->view === 'list') {
            $layoutStyle = 'editorial_list';
            $perPage     = 12;
        } else {
            $layoutStyle = 'hero_split';   // Blog always uses hero_split as default
            $perPage     = 4;              // 1 featured + 3 side panel
        }

        return Inertia::render('Blog/Index', [
            'posts'       => $query->paginate($perPage),
            'filters'     => $request->only(['search', 'category', 'sort', 'date', 'view']),
            'layoutStyle' => $layoutStyle,
        ])->withViewData([
            'meta' => [
                'title' => 'Engineering Blog & Solar Insights | Zayin Technologies',
                'description' => 'Read our latest engineering articles on solar inverter sizing, Starlink mounting, DSTV signal optimization, and battery storage.'

            ]
        ]);
    }

    /**
     * Public single blog post.
     */
    public function publicShow(string $slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->with('author:id,name')
            ->firstOrFail();

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get(['id', 'title', 'slug', 'cover_image', 'published_at', 'category']);

        return Inertia::render('Blog/Show', [
            'post'    => $post,
            'related' => $related,
        ])->withViewData([
            'meta' => [
                'title' => $post->title . ' | Blog | Zayin Technologies',
                'description' => $post->excerpt ?: Str::limit(strip_tags($post->body), 150),
                'image' => $post->cover_image ? asset($post->cover_image) : asset('images/logo.png'),
                'type' => 'article'
            ]
        ]);
    }

    // ─── Admin Routes ─────────────────────────────────────────────────────────

    /**
     * Admin blog list.
     */
    public function index(Request $request)
    {
        $query = BlogPost::with('author:id,name')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Auth/Blog/Index', [
            'posts'   => $query->paginate(15),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show admin create form.
     */
    public function create()
    {
        return Inertia::render('Auth/Blog/Form', ['post' => null]);
    }

    /**
     * Store a new blog post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'excerpt'     => 'nullable|string|max:500',
            'body'        => 'required|string',
            'cover_image' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'read_time'   => 'nullable|integer|min:1',
            'status'      => 'required|in:draft,published',
        ]);

        $validated['user_id']      = Auth::id();
        $validated['slug']         = Str::slug($validated['title']) . '-' . time();
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')->with('message', 'Blog post created successfully!');
    }

    /**
     * Show admin edit form.
     */
    public function edit(BlogPost $blog)
    {
        return Inertia::render('Auth/Blog/Form', ['post' => $blog]);
    }

    /**
     * Update an existing blog post.
     */
    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'excerpt'     => 'nullable|string|max:500',
            'body'        => 'required|string',
            'cover_image' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'read_time'   => 'nullable|integer|min:1',
            'status'      => 'required|in:draft,published',
        ]);

        if ($validated['status'] === 'published' && !$blog->published_at) {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return redirect()->route('admin.blog.index')->with('message', 'Blog post updated successfully!');
    }

    /**
     * Delete a blog post.
     */
    public function destroy(BlogPost $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blog.index')->with('message', 'Blog post deleted.');
    }
}
