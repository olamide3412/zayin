# SEO.md — Complete SEO Specification

SEO is a **launch blocker**, not a nice-to-have. Every item below is a requirement.

## 1. Rendering (critical)
- Inertia **SSR is mandatory**. Configure `resources/js/ssr.ts`, `vite.config.ts` (`ssr` entry),
  run `php artisan inertia:start-ssr` under Supervisor, and restart it on every deploy.
- Verify: `curl -s https://site/path | grep -E "<title>|<h1|application/ld\+json|canonical"`
  must show full content. If missing, SSR is broken.
- All meaningful text (descriptions, FAQs, specs) must be present in the server HTML.
  Accordions/tabs must render content in HTML (collapse with CSS/`<details>`), not load via JS.
- Graceful fallback: if the SSR server is down, Laravel still returns the client-side shell;
  add monitoring/alert for the SSR process.

## 2. Per-Page Meta (SeoData DTO)
```php
final class SeoData {
  public string $title;        // 50–60 chars, "Primary Keyword | Brand"
  public string $description;  // 120–155 chars, includes CTA
  public string $canonical;    // absolute, https, no tracking params
  public string $robots = 'index,follow';
  public ?string $ogImage;     // 1200×630
  public string $ogType = 'website';
  public array $jsonLd = [];   // array of schema graphs
  public array $breadcrumbs = [];
}
```
Render in `<SeoHead>`: `<title>`, meta description, canonical, robots, `og:*`
(`og:locale` = `en_NG`), `twitter:card=summary_large_image`, `<link rel="alternate" hreflang="en-NG">`
plus `x-default`, theme-color, JSON-LD `<script type="application/ld+json">`.

Rules: unique title and description per URL; one `<h1>` per page matching search intent;
logical H2/H3; admin can override any field via `seo_meta`; fallback generators from model data.

Title templates:
| Page | Template |
|---|---|
| Home | `{{COMPANY_NAME}} | Solar, Starlink & DSTV Installation in Nigeria` |
| Service | `{Service} in Nigeria | {{COMPANY_NAME}}` |
| Product | `{Name} – ₦{price} | {{COMPANY_NAME}}` |
| Project | `{Title} – {City} {Service} Project | {{COMPANY_NAME}}` |
| Location | `{Service} in {City}, {State} | {{COMPANY_NAME}}` |
| Blog | `{Post title} | {{COMPANY_NAME}} Blog` |

## 3. Structured Data (JSON-LD)
Build with `JsonLdBuilder` as one `@graph` per page, ids linked (`#organization`, `#website`).
| Schema | Where |
|---|---|
| `Organization` (+logo, sameAs socials, contactPoint) | Sitewide |
| `LocalBusiness` (subtype e.g. `Electrician`/`HomeAndConstructionBusiness`), address, geo, openingHours, telephone, areaServed (states/cities), priceRange | Home, Contact, Location pages |
| `WebSite` (+ `SearchAction` if search exists) | Home |
| `BreadcrumbList` | All inner pages |
| `Service` (provider, areaServed, serviceType) | Service pages |
| `Product` + `Offer` (`priceCurrency: NGN`, `price`, `availability`, `itemCondition`, `sku`, `brand`) | Package pages |
| `VideoObject` (name, description, thumbnailUrl, uploadDate, duration ISO8601, contentUrl, embedUrl) | Project/video pages |
| `BlogPosting` (author, datePublished, dateModified, image) | Blog |
| `FAQPage` | Pages with visible FAQs (valid markup; Google may not show rich results for all sites) |
| `Review`/`AggregateRating` | **Only** with genuine, visible, verifiable reviews; never self-serving or fabricated |

Validate with Google Rich Results Test and Schema.org validator in CI (snapshot tests).

## 4. URL Structure
```
/                                  /services/solar-installation
/services/starlink-installation    /services/dstv-installation-maintenance
/packages/solar/5kva-solar-package /packages/dstv/dstv-installation-package
/projects/5kva-solar-install-enugu /areas/lagos/ikeja
/blog/how-much-does-solar-cost-in-nigeria
```
Lowercase, hyphenated, short, keyword-aware, no IDs, no dates in evergreen URLs. Slug change →
automatic 301. Deleted → 410 (or 301 to nearest parent).

## 5. Sitemaps & Robots
- `/sitemap.xml` (index) → `sitemap-pages.xml`, `-services`, `-products`, `-projects`,
  `-posts`, `-locations`, `-videos` (video sitemap), images via `<image:image>`.
- Include only canonical, 200, indexable URLs with accurate `lastmod`.
- Regenerate on publish/unpublish (queued) and nightly via scheduler.
- `robots.txt`:
```
User-agent: *
Disallow: /admin
Disallow: /cart
Disallow: /checkout
Disallow: /account
Disallow: /payment
Disallow: /search
Disallow: /*?*sort=
Disallow: /*?*page=
Allow: /
Sitemap: https://{{DOMAIN}}/sitemap.xml
```
(Staging/preview environments: `Disallow: /` and `X-Robots-Tag: noindex`.)
- Submit in Google Search Console and Bing Webmaster Tools.

## 6. Technical SEO Checklist
- [ ] HTTPS everywhere, HSTS, single canonical host (301 www ↔ non-www)
- [ ] Canonical on every page; filtered/sorted/paginated listing URLs canonicalise to base (or noindex)
- [ ] `lang="en-NG"` on `<html>`
- [ ] Custom 404 returns real 404 status (no soft 404s)
- [ ] No duplicate titles/descriptions/H1s (CI check against crawl)
- [ ] Breadcrumbs (visible + JSON-LD)
- [ ] Internal linking: services ↔ packages ↔ projects ↔ blog ↔ locations (descriptive anchors)
- [ ] Pagination with crawlable `<a href>` links
- [ ] Redirect map maintained; no chains/loops
- [ ] Gzip/Brotli, HTTP/2 or 3, long-cache hashed assets
- [ ] Open Graph images exist and render (1200×630)
- [ ] `noindex` on cart, checkout, account, admin, search, thank-you pages
- [ ] Favicon set, web manifest, theme colour
- [ ] No render-blocking third-party scripts; analytics deferred and consent-aware

## 7. Performance (ranking factor & conversion)
Targets (mobile, Slow 4G): **LCP < 2.5s, INP < 200ms, CLS < 0.1**.
- Preload hero image and critical font; self-host WOFF2 fonts, `font-display: swap`, subset Latin.
- Images: WebP/AVIF, `srcset`/`sizes`, explicit `width`/`height`, `loading="lazy"` below fold,
  `fetchpriority="high"` for LCP image only.
- Video: `preload="none"` + poster; never autoplay on load; lazy mount player.
- Code-split admin and heavy widgets; avoid large UI libraries; tree-shake icons.
- Lighthouse CI budgets enforced in pipeline (see `docs/TESTING.md`).
- Use Cloudflare CDN, Brotli, image optimisation, cache anonymous HTML where safe.

## 8. Content & Keyword Strategy (Nigeria)
Do real keyword research (Google Keyword Planner, Search Console, Ahrefs/Semrush). Seed ideas:
- **Solar:** solar installation in Nigeria, 5kva solar system price in Nigeria, inverter and
  battery installation, solar panel maintenance, solar for homes/shops/estates/churches/
  hospitals, solar vs generator cost
- **Starlink/Networking:** Starlink installation Nigeria, Starlink Nigeria price/plans,
  Starlink mounting & cabling, Wi-Fi installation for offices, CCTV + network setup
- **DSTV:** DSTV installation near me, DSTV dish alignment, DSTV repair, DSTV extra view
  installation, GOtv installation, DSTV installation cost in Nigeria
- Modifiers: city/state names, "price", "cost", "near me", "installer", "company", "how to"

Rules:
- Search-intent mapping: one primary keyword/intent per URL; avoid cannibalisation.
- Money pages (services, packages, locations) need genuinely useful, original copy 400–900+
  words with specs, process, pricing guidance, FAQs, real project proof.
- **Location pages** only where the company truly serves; unique content per page (local
  projects, local FAQs, travel/response times, testimonials). No copy-paste doorway pages.
- Blog cadence 2–4 posts/month: pricing guides, buying guides, troubleshooting (e.g. "DSTV
  no signal E48-32"), maintenance tips, comparisons, case studies. Update old posts; show
  author and `dateModified`.
- E-E-A-T: real team, real projects, real addresses, certificates (if held), clear contact,
  refund & warranty policy, transparent pricing.
- Every image has descriptive `alt`; filenames descriptive (`5kva-solar-installation-enugu.webp`).

## 9. Local SEO
- Create/verify **Google Business Profile** (categories: Solar energy contractor, Satellite
  communications service, Internet service provider/installer, etc.). Consistent **NAP**
  (name, address, phone) on site, GBP, directories.
- Embed map lazily (click-to-load) to protect performance.
- Encourage genuine Google reviews; link GBP review URL on site.
- Add `areaServed` and service-area pages; cite local directories (Nigerian business listings).
- Add WhatsApp Business link and Click-to-Call tracking events.

## 10. Video SEO
- Each video lives on a crawlable page with title, description, transcript/summary, and
  `VideoObject` schema; included in `sitemap-videos.xml`.
- Thumbnails 1280×720 at stable URLs. Optionally mirror on YouTube (with link back) for reach.

## 11. Analytics & Measurement
GA4 + Google Search Console + (optional) Meta Pixel, loaded after consent. Track events:
`whatsapp_click`, `call_click`, `quote_submit`, `booking_submit`, `add_to_cart`,
`begin_checkout`, `purchase`, `video_play`. Monthly report: impressions, clicks, positions,
leads, orders.

## 12. Pre-Launch SEO Checklist
- [ ] SSR verified on all templates
- [ ] Titles/descriptions unique; JSON-LD valid
- [ ] Sitemap submitted; robots correct (not blocking prod)
- [ ] Staging not indexed; production indexable
- [ ] Redirects from any old site mapped
- [ ] Lighthouse mobile targets met
- [ ] GBP verified; NAP consistent
- [ ] Search Console + GA4 connected
- [ ] 404/410 behaviour tested
- [ ] Crawl with Screaming Frog (or similar): no 4xx/5xx, no duplicate meta, no orphan pages
