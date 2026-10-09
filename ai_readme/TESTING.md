# TESTING.md

## 1. Strategy
| Level | Tool | Scope |
|---|---|---|
| Unit | Pest | Money helpers, SeoBuilder, JsonLdBuilder, phone normaliser, order state machine |
| Feature | Pest + Inertia testing | Every controller/route, policies, validation, checkout, webhooks, uploads |
| Component | Vitest + Vue Test Utils | PackageCard, VideoPlayer, CartSummary, forms |
| E2E | Playwright | Browse → add to cart → pay (Paystack test/mocked) → confirmation; quote; admin video upload |
| SEO | Pest + crawler script | Meta/JSON-LD/canonical presence, sitemap validity, noindex rules |
| Performance/A11y | Lighthouse CI, axe | Budgets per key page |
| Security | `composer audit`, `npm audit`, ZAP baseline (staging) | |

## 2. Must-Have Tests
- Auth, roles & policies (each admin area denies unauthorised roles)
- Price tamper attempt rejected; totals/fees/VAT correct
- Paystack: verify success/fail/amount mismatch, webhook signature, replay idempotency
- Bank-transfer proof upload + approval
- Video: chunk assembly, processing job (fake FFmpeg in unit; real in a CI integration job)
- Slug change creates redirect; redirect manager works
- Each public route returns 200, `<title>`, meta description, canonical, one `<h1>`, JSON-LD
- Sitemap contains only indexable canonical URLs; robots env behaviour
- Form spam: honeypot and throttling
- 404 and 410 behaviour

## 3. Lighthouse CI Budgets (mobile, Slow 4G)
Performance ≥ 85 · SEO = 100 · Accessibility ≥ 95 · Best Practices ≥ 95 · LCP ≤ 2.5s ·
CLS ≤ 0.1 · TBT ≤ 200ms · JS ≤ 200KB gz on public pages (excluding admin).
Pages: `/`, `/services/solar-installation`, `/packages/solar`, a product, a project, `/blog/*`, `/contact`.

## 4. CI Pipeline (GitHub Actions or similar)
`composer install` → Pint → PHPStan/Larastan (level 6+) → Pest → `npm ci` → ESLint → `vue-tsc`
→ Vitest → `npm run build` (+ SSR build) → Playwright (smoke) → Lighthouse CI. Block merges on failure.

## 5. Manual QA Checklist (pre-release)
- Test on real Android phones (low-end), Safari iOS, Chrome desktop; throttle to Slow 4G
- Test checkout with Paystack test cards, USSD/transfer simulation, failed/abandoned payments
- View-source check on key pages (SSR content present)
- Check emails/SMS render and links work
- Run Rich Results Test on product, video, FAQ, local business pages
