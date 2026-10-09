<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(15)->through(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'sku' => $p->sku,
                'category' => $p->category ? $p->category->name : 'Uncategorized',
                'category_id' => $p->category_id,
                'price_naira' => $p->price ? round($p->price / 100, 2) : 0,
                'compare_at_naira' => $p->compare_at_price ? round($p->compare_at_price / 100, 2) : null,
                'formatted_price' => $p->formatted_price,
                'formatted_compare' => $p->formatted_compare_price,
                'stock_qty' => $p->stock_qty,
                'is_published' => $p->is_published,
                'is_featured' => $p->is_featured,
                'image' => $p->image,
                'specs' => $p->specs ?? [],
            ];
        });

        $categories = ProductCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Auth/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    public function create(): Response
    {
        $categories = ProductCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Auth/Products/Form', [
            'product' => null,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:product_categories,id',
            'sku' => 'nullable|string|max:60|unique:products,sku',
            'price_naira' => 'required|numeric|min:0',
            'compare_at_naira' => 'nullable|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'warranty_text' => 'nullable|string|max:200',
            'image' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'specs' => 'nullable|array',
        ]);

        $priceKobo = (int) round(((float) $validated['price_naira']) * 100);
        $compareKobo = !empty($validated['compare_at_naira']) 
            ? (int) round(((float) $validated['compare_at_naira']) * 100) 
            : null;

        $slug = Str::slug($validated['name']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-" . $counter++;
        }

        Product::create([
            'name' => $validated['name'],
            'slug' => $uniqueSlug,
            'category_id' => $validated['category_id'],
            'sku' => $validated['sku'] ?? 'ZAY-' . strtoupper(Str::random(6)),
            'price' => $priceKobo,
            'compare_at_price' => $compareKobo,
            'stock_qty' => $validated['stock_qty'],
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'warranty_text' => $validated['warranty_text'] ?? '1 Year Manufacturer Warranty',
            'image' => $validated['image'] ?? null,
            'is_published' => $validated['is_published'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'specs' => $validated['specs'] ?? [],
            'published_at' => now(),
        ]);

        return redirect()->route('admin.products.index')->with('message', 'Product / Package created successfully!');
    }

    public function edit(Product $product): Response
    {
        $categories = ProductCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Auth/Products/Form', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category_id' => $product->category_id,
                'sku' => $product->sku,
                'price_naira' => $product->price ? round($product->price / 100, 2) : 0,
                'compare_at_naira' => $product->compare_at_price ? round($product->compare_at_price / 100, 2) : null,
                'stock_qty' => $product->stock_qty,
                'summary' => $product->summary,
                'description' => $product->description,
                'warranty_text' => $product->warranty_text,
                'image' => $product->image,
                'is_published' => (bool) $product->is_published,
                'is_featured' => (bool) $product->is_featured,
                'specs' => $product->specs ?? [],
            ],
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:product_categories,id',
            'sku' => 'nullable|string|max:60|unique:products,sku,' . $product->id,
            'price_naira' => 'required|numeric|min:0',
            'compare_at_naira' => 'nullable|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'warranty_text' => 'nullable|string|max:200',
            'image' => 'nullable|string',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'specs' => 'nullable|array',
        ]);

        $priceKobo = (int) round(((float) $validated['price_naira']) * 100);
        $compareKobo = !empty($validated['compare_at_naira']) 
            ? (int) round(((float) $validated['compare_at_naira']) * 100) 
            : null;

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'sku' => $validated['sku'] ?? $product->sku,
            'price' => $priceKobo,
            'compare_at_price' => $compareKobo,
            'stock_qty' => $validated['stock_qty'],
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'warranty_text' => $validated['warranty_text'] ?? $product->warranty_text,
            'image' => $validated['image'] ?? $product->image,
            'is_published' => $validated['is_published'] ?? $product->is_published,
            'is_featured' => $validated['is_featured'] ?? $product->is_featured,
            'specs' => $validated['specs'] ?? $product->specs,
        ]);

        return redirect()->route('admin.products.index')->with('message', 'Product / Package updated successfully!');
    }

    /**
     * Quick update for price, compare price, stock, or published status directly from table.
     */
    public function quickUpdate(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'price_naira' => 'nullable|numeric|min:0',
            'compare_at_naira' => 'nullable|numeric|min:0',
            'stock_qty' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $updates = [];
        if (isset($validated['price_naira'])) {
            $updates['price'] = (int) round(((float) $validated['price_naira']) * 100);
        }
        if (array_key_exists('compare_at_naira', $validated)) {
            $updates['compare_at_price'] = $validated['compare_at_naira'] !== null 
                ? (int) round(((float) $validated['compare_at_naira']) * 100) 
                : null;
        }
        if (isset($validated['stock_qty'])) {
            $updates['stock_qty'] = $validated['stock_qty'];
        }
        if (isset($validated['is_published'])) {
            $updates['is_published'] = $validated['is_published'];
        }
        if (isset($validated['is_featured'])) {
            $updates['is_featured'] = $validated['is_featured'];
        }

        $product->update($updates);

        return redirect()->back()->with('message', "{$product->name} updated successfully!");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('message', 'Product deleted successfully!');
    }
}
