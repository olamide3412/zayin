<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $services = Service::published()->select('slug', 'updated_at')->get();
        $products = Product::published()->with('category:id,slug')->select('id', 'category_id', 'slug', 'updated_at')->get();
        $projects = Project::published()->select('slug', 'updated_at')->get();

        $content = view('sitemap', [
            'services' => $services,
            'products' => $products,
            'projects' => $projects,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function robots(): Response
    {
        $domain = request()->getSchemeAndHttpHost();
        $isProd = app()->isProduction();

        if (!$isProd) {
            $robots = "User-agent: *\nDisallow: /\n";
        } else {
            $robots = "User-agent: *\n" .
                      "Disallow: /admin\n" .
                      "Disallow: /cart\n" .
                      "Disallow: /checkout\n" .
                      "Disallow: /account\n" .
                      "Disallow: /solar-calculator/r/\n" .
                      "Disallow: /orders/*/bank-transfer\n" .
                      "Disallow: /*?*sort=\n" .
                      "Disallow: /*?*search=\n" .
                      "Allow: /\n\n" .
                      "Sitemap: {$domain}/sitemap.xml\n";
        }

        return response($robots, 200)
            ->header('Content-Type', 'text/plain');
    }
}
