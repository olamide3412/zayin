<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $meta['title'] ?? config('app.name', 'Zayin Technologies') . ' | Solar, Starlink & DSTV Installations' }}</title>

    <meta name="description"
        content="{{ $meta['description'] ?? 'Certified solar power systems, high-speed Starlink installations, enterprise mesh Wi-Fi, and precision DSTV satellite setups across Nigeria.' }}">
    <meta name="keywords"
        content="solar installation Nigeria, inverter battery Lagos, Starlink installer Abuja, DSTV installation Lekki, solar panels Nigeria, Wi-Fi networking, AMC solar maintenance, Zayin Technologies">
    <meta name="robots" content="index, follow">

    <!-- ── SEO: Open Graph (Facebook / LinkedIn) ──────────────────────── -->
    @if(isset($meta))
        <meta property="og:title" content="{{ $meta['title'] }}">
        <meta property="og:description" content="{{ $meta['description'] }}">
        <meta property="og:image" content="{{ $meta['image'] ?? asset('images/logo.png') }}">
        <meta property="og:type" content="{{ $meta['type'] ?? 'website' }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="Zayin Technologies">
        <meta property="og:locale" content="en_NG">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">

        <!-- ── SEO: Twitter / X Card ──────────────────────────────────── -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $meta['title'] }}">
        <meta name="twitter:description" content="{{ $meta['description'] }}">
        <meta name="twitter:image" content="{{ $meta['image'] ?? asset('images/logo.png') }}">
        <meta name="twitter:site" content="@ZayinTechNG">
    @else
        <meta property="og:title" content="Zayin Technologies | Solar, Starlink & DSTV Engineering">
        <meta property="og:description"
            content="Certified solar power systems, Starlink satellite broadband, enterprise Wi-Fi networking, and DSTV distribution across 36 Nigerian states.">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Zayin Technologies">
        <meta property="og:locale" content="en_NG">
        <meta property="og:image" content="{{ asset('images/logo.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:url" content="{{ url()->current() }}">

        <!-- ── SEO: Twitter / X Card ──────────────────────────────────── -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Zayin Technologies | Solar, Starlink & DSTV Engineering">
        <meta name="twitter:description"
            content="Certified solar power systems, Starlink satellite broadband, enterprise Wi-Fi networking, and DSTV distribution across 36 Nigerian states.">
        <meta name="twitter:image" content="{{ asset('images/logo.png') }}">
        <meta name="twitter:site" content="@ZayinTechNG">
    @endif

    <!-- ── SEO: JSON-LD Structured Data (Organization) ───────────────── -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "LocalBusiness",
        "name": "Zayin Technologies",
        "alternateName": "Zayin Tech",
        "description": "Certified solar installations, Starlink satellite internet mounting, and professional DSTV multi-room systems in Nigeria.",
        "url": "{{ config('app.url') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "telephone": "+2348000000000",
        "email": "support@zayin.ng",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "Abraka",
            "addressLocality": "Ethiope East",
            "addressRegion": "Delta State",
            "addressCountry": "NG"
        },
        "areaServed": "Nigeria (Nationwide across 36 States + FCT)",
        "hasOfferCatalog": {
            "@@type": "OfferCatalog",
            "name": "Zayin Technologies Engineering Services",
            "itemListElement": [
                { "@@type": "Offer", "itemOffered": { "@@type": "Service", "name": "Solar & Inverter Power Solutions" } },
                { "@@type": "Offer", "itemOffered": { "@@type": "Service", "name": "Starlink Satellite Installation & Enterprise Wi-Fi" } },
                { "@@type": "Offer", "itemOffered": { "@@type": "Service", "name": "DSTV Extra View & Satellite Realignment" } },
                { "@@type": "Offer", "itemOffered": { "@@type": "Service", "name": "Annual Maintenance Contracts (AMC)" } }
            ]
        }
    }
    </script>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @inertiaHead
    @routes
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900">
    @inertia
</body>

</html>