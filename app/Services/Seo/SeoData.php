<?php

namespace App\Services\Seo;

class SeoData
{
    public string $title;
    public string $description;
    public string $canonical;
    public string $robots;
    public ?string $ogImage;
    public string $ogType;
    public array $jsonLd;
    public array $breadcrumbs;

    public function __construct(
        string $title = '',
        string $description = '',
        string $canonical = '',
        string $robots = 'index,follow',
        ?string $ogImage = null,
        string $ogType = 'website',
        array $jsonLd = [],
        array $breadcrumbs = []
    ) {
        $company = config('app.name', 'Zayin Technologies');
        $this->title = $title ? "{$title} | {$company}" : "{$company} | Solar, Starlink & DSTV Installation Nigeria";
        $this->description = $description ?: "Zayin Technologies is Nigeria's trusted installer for engineered solar systems, zero-obstruction Starlink internet setups, and certified DSTV satellite solutions.";
        $this->canonical = $canonical ?: url()->current();
        $this->robots = $robots;
        $this->ogImage = $ogImage ?: url('/images/og-default.jpg');
        $this->ogType = $ogType;
        $this->jsonLd = $jsonLd;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'canonical' => $this->canonical,
            'robots' => $this->robots,
            'ogImage' => $this->ogImage,
            'ogType' => $this->ogType,
            'jsonLd' => $this->jsonLd,
            'breadcrumbs' => $this->breadcrumbs,
        ];
    }
}
