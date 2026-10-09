<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Seo\SeoData;
use App\Services\Seo\JsonLdBuilder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = ProductCategory::withCount('products')->orderBy('sort_order')->get();

        $query = Product::published()->with(['category', 'inclusions']);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->input('sort') === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->input('sort') === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('is_featured', 'desc')->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        $seo = new SeoData(
            title: 'Solar Packages, Starlink Kits & DSTV Deals',
            description: 'Browse all-in-one solar packages, genuine Starlink satellite hardware, and DSTV decoders with turnkey installation in Nigeria.',
            breadcrumbs: [
                'Home' => url('/'),
                'Packages' => url('/packages'),
            ]
        );

        return Inertia::render('Packages/Index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => null,
            'filters' => $request->only(['search', 'type', 'sort']),
            'seo' => $seo->toArray(),
        ]);
    }

    public function category(string $categorySlug, Request $request): Response
    {
        $category = ProductCategory::where('slug', $categorySlug)->firstOrFail();
        $categories = ProductCategory::withCount('products')->orderBy('sort_order')->get();

        $query = Product::published()
            ->where('category_id', $category->id)
            ->with(['category', 'inclusions']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($request->input('sort') === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->input('sort') === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('is_featured', 'desc')->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        $breadcrumbs = [
            'Home' => url('/'),
            'Packages' => url('/packages'),
            $category->name => url("/packages/{$category->slug}"),
        ];

        $seo = new SeoData(
            title: "{$category->name} in Nigeria",
            description: $category->description ?: "Find the best deals and professional installations for {$category->name} in Nigeria.",
            breadcrumbs: $breadcrumbs
        );

        return Inertia::render('Packages/Index', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
            'filters' => $request->only(['search', 'sort']),
            'seo' => $seo->toArray(),
        ]);
    }

    public function show(string $categorySlug, string $slug): Response
    {
        $product = Product::published()
            ->where('slug', $slug)
            ->with(['category', 'service', 'inclusions'])
            ->firstOrFail();

        $relatedProducts = Product::published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['inclusions'])
            ->take(3)
            ->get();

        $breadcrumbs = [
            'Home' => url('/'),
            'Packages' => url('/packages'),
            $product->category?->name ?? 'Packages' => url("/packages/" . ($product->category?->slug ?? '')),
            $product->name => url()->current(),
        ];

        $seo = new SeoData(
            title: "{$product->name} – " . format_naira($product->price),
            description: $product->summary,
            ogImage: $product->image,
            jsonLd: [
                JsonLdBuilder::product($product),
                JsonLdBuilder::breadcrumbs($breadcrumbs),
            ],
            breadcrumbs: $breadcrumbs
        );

        return Inertia::render('Packages/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'seo' => $seo->toArray(),
        ]);
    }
}
