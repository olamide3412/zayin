<?php

namespace App\Services\Seo;

class JsonLdBuilder
{
    public static function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('/') . '#organization',
            'name' => config('app.name', 'Zayin Technologies'),
            'url' => url('/'),
            'logo' => url('/images/logo.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+234-803-123-4567',
                'contactType' => 'customer service',
                'areaServed' => 'NG',
                'availableLanguage' => 'English',
            ],
            'sameAs' => [
                'https://facebook.com/zayinng',
                'https://instagram.com/zayinng',
                'https://twitter.com/zayinng',
            ],
        ];
    }

    public static function localBusiness(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => url('/') . '#localbusiness',
            'name' => config('app.name', 'Zayin Technologies'),
            'image' => url('/images/og-default.jpg'),
            'telephone' => '+234-803-123-4567',
            'priceRange' => '₦₦₦',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Abraka',
                'addressLocality' => 'Ethiope East',
                'addressRegion' => 'Delta State',
                'addressCountry' => 'NG',
            ],
            'areaServed' => 'Nigeria (Nationwide across 36 States + FCT)',
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 5.7915,
                'longitude' => 6.1017,
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens' => '08:00',
                    'closes' => '18:00',
                ],
            ],
        ];
    }

    public static function service($service): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->name,
            'description' => $service->summary,
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => config('app.name', 'Zayin Technologies'),
            ],
            'areaServed' => 'NG',
            'url' => url("/services/{$service->slug}"),
        ];
    }

    public static function product($product): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->summary,
            'sku' => $product->sku,
            'image' => $product->image ?: url('/images/og-default.jpg'),
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand ?: config('app.name', 'Zayin Technologies'),
            ],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'NGN',
                'price' => number_format($product->price / 100, 2, '.', ''),
                'priceValidUntil' => date('Y-12-31'),
                'availability' => 'https://schema.org/InStock',
                'url' => url("/packages/{$product->category?->slug}/{$product->slug}"),
            ],
        ];
    }

    public static function breadcrumbs(array $items): array
    {
        $itemList = [];
        $position = 1;

        foreach ($items as $name => $url) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $name,
                'item' => $url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList,
        ];
    }
}
