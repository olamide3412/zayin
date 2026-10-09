# ARCHITECTURE.md

## 1. Overview
A monolithic Laravel application serving Vue 3 pages through Inertia.js with SSR.

```
Browser ──► Cloudflare (CDN/WAF) ──► Nginx ──► PHP-FPM (Laravel + Inertia)
                                        │            │
                                        │            ├─► MySQL/PostgreSQL
                                        │            ├─► Redis (cache/session/queue)
                                        │            ├─► Object storage (videos/images)
                                        │            └─► Paystack API / SMS / Mail
                                        └─► Node SSR server (inertia:start-ssr, port 13714)
Queue workers (Supervisor): video processing, emails, notifications, sitemap
```

## 2. Request Lifecycle (public page)
1. Laravel route → thin controller → Action/Query builds data.
2. Controller returns `Inertia::render('Public/Services/Show', [...props, 'seo' => $seo])`.
3. Inertia SSR server renders Vue to HTML (full HTML including `<head>`).
4. Browser receives complete HTML, then hydrates; subsequent navigation is SPA-like.

## 3. Layers
| Layer | Responsibility |
|---|---|
| Controllers | HTTP only: validate (Form Request), authorise, call action, return Inertia/redirect |
| Actions (`app/Actions`) | One business operation each (`PlaceOrder`, `ConfirmPayment`, `SubmitQuote`) |
| Services | Reusable domain services (`SeoBuilder`, `PaystackGateway`, `VideoProcessor`, `CartService`) |
| Models | Relationships, scopes, casts, accessors. No heavy logic |
| Resources | Shape props for Vue (never leak internal fields) |
| Policies | Authorisation per model |
| Jobs/Listeners | Async work |
| Enums | Status/type constants |

## 4. Shared Inertia Props (HandleInertiaRequests)
`app.name`, `site` (phones, WhatsApp, address, socials, hours — from settings cache),
`auth.user` (id, name, roles), `cart.count`, `flash`, `nav` (menu with services/categories),
`locale`. Keep lean: cached and minimal.

## 5. SEO Architecture
- `App\Services\Seo\SeoData` DTO → every public controller supplies it.
- `SeoBuilder::for($model)` auto-fills from model + admin override (`seo_meta` polymorphic).
- `JsonLdBuilder` generates schema graphs.
- Vue `<SeoHead>` renders everything with Inertia `<Head>` (SSR'd).
- `SitemapGenerator` + scheduled job.
- Details: `docs/SEO.md`.

## 6. Caching
- Settings, nav, and published listing queries cached in Redis with tags; busted on save.
- Full-page HTTP cache for anonymous GETs via Cloudflare/Nginx `fastcgi_cache`
  (bypass for cart/checkout/account/admin and when cookies indicate auth).
- ETag/`Cache-Control: public, max-age` for static build assets (hashed by Vite), 1 year immutable.

## 7. Frontend Architecture
- Layouts: `PublicLayout` (header, mega-menu, footer, sticky WhatsApp bar), `AccountLayout`, `AdminLayout`.
- State: server-driven via Inertia props; local UI state with refs/composables. Pinia only if
  cross-page client state becomes necessary (cart is server-side).
- Components library under `Components/ui` (Button, Input, Select, Modal, Badge, Card,
  Pagination, Breadcrumbs, Tabs, Accordion with server-rendered content).
- Admin pages code-split and not indexed (`noindex`, `Disallow` in robots).
- Forms: Inertia `useForm`; errors displayed inline; progress for uploads.

## 8. Module Map
`Catalog` (services, products, categories) · `Content` (pages, posts, FAQs, testimonials) ·
`Portfolio` (projects, media) · `Leads` (contact, quotes, bookings) · `Commerce` (cart,
orders, payments, invoices) · `Customers` (accounts, addresses) · `Admin` · `Seo` · `Settings`.

## 9. Error Handling
- Custom 404/419/429/500 Inertia pages. 410 for permanently removed content.
- Payment errors never expose gateway internals; log with reference.
- Sentry for exceptions; log channel `daily` + structured context.

## 10. Coding Standards
Laravel Pint (PSR-12 + Laravel preset), ESLint + Prettier, `vue-tsc`. CI runs all gates.
