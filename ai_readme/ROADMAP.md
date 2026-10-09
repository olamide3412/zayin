# ROADMAP.md — Phased Build Plan

Tick items as completed. Each phase ends with tests green and a demo on staging.

## Phase 0 — Foundation
- [ ] Scaffold Laravel + Inertia + Vue 3 + TS + Tailwind with **SSR working**
- [ ] Lint/format/type tooling (Pint, Larastan, ESLint, Prettier, vue-tsc), CI pipeline
- [ ] Base layouts, UI kit, `formatNaira`, phone helpers, error pages
- [ ] Settings system + seeders (states, roles, settings, admin)
- [ ] Auth (login/register/reset/verify) + roles & permissions + admin 2FA

## Phase 1 — Catalogue & Content Admin
- [ ] Services + categories CRUD
- [ ] Product categories, products/packages, inclusions, specs, images
- [ ] Pages, FAQs, testimonials, locations CRUD
- [ ] Rich text editor + media library with alt text
- [ ] SEO fields (`seo_meta`) + redirect manager (auto 301 on slug change)

## Phase 2 — Public Site + Core SEO
- [ ] Home, About, Services, Packages (list/category/detail), FAQ, Contact, legal pages
- [ ] `SeoData`, `SeoHead`, `JsonLdBuilder` (Organization, LocalBusiness, WebSite, Breadcrumb, Service, Product)
- [ ] Sitemap index + robots + canonical rules, 404/410
- [ ] Sticky WhatsApp/Call bar, header/footer, breadcrumbs
- [ ] Lighthouse CI budgets passing

## Phase 3 — Portfolio & Video
- [ ] Projects CRUD with photo gallery
- [ ] Resumable video upload + FFmpeg job + poster + status polling
- [ ] `VideoPlayer` (low-data aware), YouTube facade option
- [ ] `VideoObject` schema + video sitemap
- [ ] Public project pages + filters

## Phase 4 — Leads
- [ ] Quote request, booking, contact forms with spam protection
- [ ] Admin pipeline (status, assignment, notes), notifications (mail + SMS/WhatsApp)
- [ ] Lead export (CSV)

## Phase 5 — E-commerce
- [ ] Cart (guest + user merge), checkout, fees per state, VAT
- [ ] Paystack init/callback/webhook (idempotent), tests
- [ ] Bank transfer with proof upload/approval
- [ ] Orders admin, status history, invoices PDF, customer order tracking
- [ ] Optional: deposits, coupons, stock tracking

## Phase 6 — Customer Account & Maintenance Plans
- [ ] Account dashboard (orders, quotes, bookings, addresses)
- [ ] Maintenance plans + subscriptions + visit scheduling reminders

## Phase 7 — Blog & Growth
- [ ] Blog (categories, tags, TOC, related posts, BlogPosting schema)
- [ ] Location pages with unique content workflow
- [ ] Site search, newsletter (double opt-in), solar load calculator (optional)
- [ ] Analytics events + consent banner

## Phase 8 — Hardening & Launch
- [ ] Security review (headers/CSP, rate limits, audit), NDPA checklist, legal review
- [ ] Performance tuning (CDN, caching, image/video audit)
- [ ] Full SEO audit/crawl, Rich Results validation
- [ ] Backups, monitoring, load test, UAT with client
- [ ] Go-live checklist (docs/DEPLOYMENT.md §9) and post-launch SEO plan (GBP, Search Console)

## Post-Launch (continuous)
Content calendar (2–4 posts/month), new project videos weekly, review requests, monthly SEO/
conversion report, dependency updates, quarterly security + backup restore test.
