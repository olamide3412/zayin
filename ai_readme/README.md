# {{COMPANY_NAME}} — Solar, Networking & DSTV Services Website

A professional, SEO-ready company website and small e-commerce platform for a Nigerian
services company offering:

- **Solar** installation & maintenance (panels, inverters, batteries, solar packages)
- **Networking & Internet** (Starlink, Wi-Fi, structured cabling, CCTV/IP networks, etc.)
- **DSTV / Satellite TV** installation, repair & maintenance (DSTV, GOtv, dish alignment)
- **Other services** added over time (the service catalogue is fully admin-managed)

Customers can browse services, watch videos of completed work, buy packages (Solar, DSTV,
Starlink, accessories), request quotes, book installations and sign up for maintenance plans.
Admins manage everything from a secure dashboard, including uploading work videos.

> **Status:** Greenfield. Start with `docs/ROADMAP.md`. AI agents must read `AGENTS.md` first.

---

## 1. Tech Stack

| Layer | Choice |
|---|---|
| Backend | PHP 8.3+, Laravel (latest stable; verify against official docs) |
| Frontend | Vue 3 (Composition API, `<script setup>`), TypeScript preferred |
| Glue | Inertia.js (v2) with **Server-Side Rendering (SSR) enabled** — mandatory for SEO |
| Styling | Tailwind CSS, headless components, `lucide-vue-next` icons |
| Database | MySQL 8 (or PostgreSQL 15+) |
| Cache/Queue/Session | Redis |
| Build | Vite |
| Auth | Laravel Fortify/Breeze (Vue + SSR) + `spatie/laravel-permission` for roles |
| Media | `spatie/laravel-medialibrary`, FFmpeg (video), Intervention/Imagick (images) |
| Payments | Paystack (primary), Flutterwave (optional), manual bank transfer |
| SEO | Custom `Seo` service + `spatie/laravel-sitemap` + JSON-LD |
| Testing | Pest, Vitest, Playwright, Lighthouse CI |
| Monitoring | Sentry, uptime monitor, Laravel Pulse/Telescope (non-prod) |

Recommended Composer packages: `inertiajs/inertia-laravel`, `tightenco/ziggy`,
`spatie/laravel-permission`, `spatie/laravel-medialibrary`, `spatie/laravel-sluggable`,
`spatie/laravel-sitemap`, `spatie/laravel-activitylog`, `spatie/laravel-backup`,
`barryvdh/laravel-dompdf` (invoices), `pestphp/pest`.

---

## 2. Key Features

**Public site**
- Home, About, Services (list + detail), Packages/Shop, Projects (video & photo gallery),
  Blog, FAQ, Contact, Service-area pages, legal pages
- Package catalogue with filters (category, price in ₦, capacity/kVA, brand)
- Video gallery of completed jobs with lazy loading and low-data playback
- Quote request and installation booking forms
- Cart, checkout, order tracking
- WhatsApp click-to-chat and click-to-call everywhere (primary conversion channel in Nigeria)
- Fully SEO-ready: SSR, meta, JSON-LD, sitemaps, canonical URLs, fast Core Web Vitals

**Customer account**
- Orders, quotes, bookings, maintenance plans, invoices, saved addresses

**Admin dashboard**
- Services, products/packages, orders, quotes, bookings, projects/videos, testimonials,
  blog, FAQ, locations, SEO overrides, redirects, settings, users & roles, activity log

---

## 3. Getting Started

```bash
# 1. Scaffold (use the official Laravel Vue/Inertia starter kit or Breeze with SSR)
composer create-project laravel/laravel company-site
cd company-site
composer require laravel/breeze --dev
php artisan breeze:install vue --ssr --typescript   # verify current official install docs

# 2. Install project packages
composer require spatie/laravel-permission spatie/laravel-medialibrary \
  spatie/laravel-sluggable spatie/laravel-sitemap spatie/laravel-activitylog \
  spatie/laravel-backup barryvdh/laravel-dompdf
composer require pestphp/pest --dev

# 3. Configure
cp .env.example .env && php artisan key:generate
# set DB, REDIS, PAYSTACK_*, MAIL_*, APP_URL, see docs/DEPLOYMENT.md

# 4. Database
php artisan migrate --seed

# 5. Frontend
npm install
npm run dev            # local dev
npm run build && php artisan inertia:start-ssr   # verify SSR output locally

# 6. Storage & queue
php artisan storage:link
php artisan queue:work
```

### Useful commands
```bash
php artisan test                 # Pest
npm run test                     # Vitest
npx playwright test              # E2E
npm run lint && npm run typecheck
php artisan sitemap:generate     # custom command (see docs/SEO.md)
```

---

## 4. Project Structure (target)

```
app/
  Actions/               # single-purpose business actions (CreateOrder, VerifyPayment…)
  Enums/                 # OrderStatus, PaymentStatus, ProductType, QuoteStatus…
  Http/Controllers/
    Public/              # Home, Service, Product, Project, Blog, Contact…
    Account/             # Customer area
    Admin/               # Dashboard CRUD
    Webhooks/            # PaystackWebhookController
  Http/Requests/         # Form Requests (all validation here)
  Jobs/                  # ProcessVideoUpload, SendOrderNotification…
  Models/
  Policies/
  Services/
    Seo/                 # SeoData, JsonLdBuilder, SitemapGenerator
    Payments/            # PaystackGateway, BankTransferGateway
    Cart/
    Media/               # VideoProcessor
resources/js/
  Pages/{Public,Account,Admin,Auth}/
  Components/{ui,layout,seo,product,video,forms}/
  Layouts/               # PublicLayout, AccountLayout, AdminLayout
  Composables/
  lib/                   # formatNaira(), phone helpers
  ssr.ts  app.ts
database/{migrations,seeders,factories}
docs/                    # all project docs
tests/{Feature,Unit,Browser}
```

---

## 5. Documentation Map

| File | Purpose |
|---|---|
| `AGENTS.md` | **Rules and workflow for AI coding agents** (read first) |
| `REQUIREMENTS.md` | Functional & non-functional requirements, acceptance criteria |
| `docs/ARCHITECTURE.md` | System design, conventions, patterns |
| `docs/DATABASE.md` | Schema, relationships, indexes |
| `docs/ROUTES.md` | Public, account, admin and webhook routes |
| `docs/SEO.md` | Complete SEO implementation spec |
| `docs/DESIGN.md` | Brand, UI/UX, Nigerian market considerations |
| `docs/PAYMENTS_AND_ORDERS.md` | Cart, checkout, Paystack, bank transfer, invoices |
| `docs/MEDIA_VIDEO.md` | Image & video upload/processing/delivery |
| `docs/SECURITY.md` | Security, privacy, NDPA 2023 compliance |
| `docs/TESTING.md` | Test strategy and quality gates |
| `docs/DEPLOYMENT.md` | Server setup, env vars, CI/CD, backups |
| `docs/ROADMAP.md` | Phased build plan with task checklists |

---

## 6. Placeholders To Replace Before Launch

`{{COMPANY_NAME}}`, logo, brand colours, phone/WhatsApp numbers, office address & map
coordinates, CAC registration number (only if held), certifications (only if held), social
links, Paystack keys, GA4/Search Console IDs, real photos/videos, real prices.

> Never invent certifications, licences, reviews, awards or prices. Use only real data
> supplied by the client.
