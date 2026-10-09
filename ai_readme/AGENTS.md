# AGENTS.md — Instructions for AI Coding Agents

You are building a **Nigerian solar / networking / DSTV services company website with a small
e-commerce store** using **Laravel + Inertia + Vue 3 (SSR)**. Read this file fully, then
`REQUIREMENTS.md`, then the relevant file in `docs/` before each task.

## 1. Golden Rules
1. **SEO is a hard requirement.** Every public page must render complete HTML on the server
   (Inertia SSR): title, meta description, canonical, H1, Open Graph, JSON-LD. Never ship a
   public page that is client-render-only. See `docs/SEO.md`.
2. **Mobile-first, low-bandwidth-first.** Most visitors are on phones with unstable data.
   Optimise images/videos, lazy-load, keep JS small. Budget: LCP < 2.5s on Slow 4G mobile.
3. **Money is stored as integers in kobo** (₦1 = 100 kobo). Never use floats for money.
   Format with a single helper (`formatNaira`) on the frontend and a `Money` value object
   or accessor on the backend.
4. **Validation lives in Form Requests.** Authorisation lives in Policies/Gates.
   Controllers stay thin; business logic goes in `Actions/` or `Services/`.
5. **Never trust the client for prices, totals, stock, or payment status.** Recompute on the
   server; confirm payments only through server-side verification / signed webhooks.
6. **No invented content.** Use placeholders (`{{COMPANY_NAME}}`) and clearly mark seed data
   as demo. Do not fabricate certifications, reviews, or statistics.
7. **Do not add dependencies casually.** Prefer what is in `README.md`. If you add a package,
   justify it in the PR description and `docs/ARCHITECTURE.md`.
8. **Verify current docs.** Laravel, Inertia, Vue, Paystack and Vite APIs change. Check the
   official documentation instead of relying on memory.

## 2. Workflow For Every Task
1. Read the relevant requirement IDs (`FR-*`, `NFR-*`) in `REQUIREMENTS.md`.
2. Read the relevant `docs/*.md`.
3. Plan briefly (files to create/change) before writing code.
4. Implement with tests (Pest feature tests for backend, Vitest for non-trivial components).
5. Run: `php artisan test`, `npm run lint`, `npm run typecheck`, `npm run build`.
6. For public pages: confirm SSR output (`curl -s URL | grep -i "<title>\|application/ld+json"`)
   and that meta/JSON-LD validate.
7. Update docs and tick the item in `docs/ROADMAP.md`.
8. Commit with Conventional Commits (`feat:`, `fix:`, `docs:`, `refactor:`, `test:`, `chore:`).

## 3. Backend Conventions
- PHP 8.3+, `declare(strict_types=1);`, typed properties/returns, Laravel Pint formatting.
- Use **Enums** for statuses/types (backed enums, cast on models).
- Use **API Resources / DTO arrays** to shape Inertia props. Never pass whole Eloquent models
  with hidden/sensitive fields to the frontend.
- Prevent N+1: eager load, and enable `Model::preventLazyLoading(! app()->isProduction())`.
- Use **database transactions** for order creation and payment confirmation.
- Use **queues** for: video processing, emails, SMS/WhatsApp notifications, sitemap rebuilds.
- Slugs via `spatie/laravel-sluggable`; slugs are unique and **stable** (changing a slug creates
  a 301 redirect record automatically).
- Soft deletes for products, services, posts, projects. Deleted public URLs return 410 or 301.
- Log with context; never log personal data, tokens or full card/bank details.

## 4. Frontend Conventions
- Vue 3 `<script setup lang="ts">`, Composition API only.
- Pages in `resources/js/Pages`, reusable parts in `Components`. One component per file,
  PascalCase.
- Use Inertia `<Link>` for internal navigation, `useForm` for forms, Ziggy `route()` helper.
- **Always** render SEO via the shared `<SeoHead :seo="seo" />` component driven by the
  server-provided `seo` prop. Do not hand-write `<Head>` tags in individual pages.
- Images: use the `<ResponsiveImage>` component (srcset, width/height, lazy, alt required).
- Video: use the `<VideoPlayer>` component (poster, `preload="none"`, click-to-play,
  no autoplay with sound, quality fallback). See `docs/MEDIA_VIDEO.md`.
- Accessibility: semantic HTML, one `<h1>` per page, labelled inputs, focus states, WCAG 2.1 AA.
- Keep client bundles small: lazy-load admin pages and heavy widgets (maps, charts, editors).
- Never access `window`/`document` outside `onMounted` (SSR safety).

## 5. Nigerian Market Rules
- Currency ₦ (NGN); format `₦1,250,000`. Locale `en-NG`. `<html lang="en-NG">`.
- Phones: store E.164 (`+2348012345678`); accept `0801…`, `+234801…`, `234801…` on input and
  normalise. WhatsApp link: `https://wa.me/2348012345678?text=…` (URL-encoded).
- Addresses use **State → LGA/City → Street/Landmark**; landmarks matter. Seed all 36 states + FCT.
- Payment methods: card, bank transfer, USSD (via Paystack), manual transfer with proof.
- Delivery/installation fees are configurable **per state**.
- Provide WhatsApp + call buttons sitewide (sticky on mobile).
- Power/connection reality: design for slow networks, avoid heavy autoplay media.
- Comply with the **Nigeria Data Protection Act (NDPA) 2023** — see `docs/SECURITY.md`.

## 6. Definition of Done
- [ ] Meets acceptance criteria in `REQUIREMENTS.md`
- [ ] Tests written and passing; no lint/type errors
- [ ] Policies/validation in place; no mass-assignment holes
- [ ] SSR verified; SEO tags + JSON-LD present (public pages)
- [ ] Mobile tested (360px width) and keyboard accessible
- [ ] Lighthouse mobile: Performance ≥ 85, SEO = 100, Accessibility ≥ 95, Best Practices ≥ 95
- [ ] Docs/ROADMAP updated

## 7. Do NOT
- Do not render public content client-side only or behind JS-only tabs/accordions without
  server-rendered text.
- Do not store prices as floats or trust cart totals from the browser.
- Do not commit secrets or `.env`.
- Do not hotlink third-party images/fonts; self-host fonts (WOFF2, `font-display: swap`).
- Do not create thin, duplicated location pages (doorway pages). Each must have unique,
  useful content.
- Do not auto-play heavy video on page load.
- Do not skip tests for payment, order, auth, or webhook code.

## 8. When Requirements Are Unclear
Make the smallest reasonable assumption, record it under "Assumptions" in
`REQUIREMENTS.md`, and continue. Ask the human only for blocking items (real prices, legal
text, brand assets, API keys).
