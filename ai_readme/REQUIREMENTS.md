# REQUIREMENTS.md

Priority key: **M** = Must, **S** = Should, **C** = Could (MoSCoW).

## 1. Business Context
A Nigerian company providing solar installation & maintenance, networking (Starlink and
related), DSTV/satellite TV installation & maintenance, and other technical services. The
website must build trust, generate leads, rank on Google for Nigerian search terms, and
sell ready-made packages online.

**Goals**
1. Generate quote requests, bookings and WhatsApp enquiries.
2. Sell fixed-price packages (Solar, DSTV, Starlink, accessories) online.
3. Showcase real work through videos and photos (social proof).
4. Rank for local and service keywords (SEO).
5. Let non-technical staff manage all content without a developer.

**Target users**: homeowners, shop/office owners, estate managers, schools, clinics,
churches, small businesses, hospitality; mostly on mobile devices.

**Roles**: Visitor, Customer, Super Admin, Admin, Content Manager, Sales, Technician.

## 2. Functional Requirements

### 2.1 Public Website
| ID | Requirement | Pri |
|---|---|---|
| FR-001 | Home page: hero with primary CTAs (Get Quote, WhatsApp), services overview, featured packages, featured project videos, why-choose-us, process steps, testimonials, service areas, FAQ preview, CTA footer | M |
| FR-002 | About page: company story, team, values, certifications (only if real), CAC details (optional) | M |
| FR-003 | Services index and detail pages (Solar, Networking/Starlink, DSTV, extensible). Each has description, benefits, process, FAQs, related packages, related projects, CTA | M |
| FR-004 | Packages/Shop: listing by category (Solar, DSTV, Starlink, Accessories), search, filters (price, capacity kVA, brand, type), sorting | M |
| FR-005 | Package detail: gallery, price in ₦, what's included (itemised), specs, installation included?, warranty, delivery/installation notes, FAQs, related items, add to cart, request quote, WhatsApp enquiry | M |
| FR-006 | Projects (portfolio): grid of completed jobs with video/photos, location (state/city), service type, date, short story; filter by service and state | M |
| FR-007 | Video gallery with lazy loading, poster images, transcripts/descriptions | M |
| FR-008 | Blog with categories, tags, author, related posts, table of contents | S |
| FR-009 | FAQ page grouped by service | M |
| FR-010 | Contact page: form, phone, WhatsApp, email, address, hours, lazy map | M |
| FR-011 | Service-area (location) pages for key states/cities with unique content | S |
| FR-012 | Testimonials/reviews (admin-curated; verified-customer submissions optional) | S |
| FR-013 | Legal pages: Privacy Policy, Terms, Refund/Returns, Warranty, Cookie notice | M |
| FR-014 | Site search (products, services, posts) | S |
| FR-015 | Custom 404/500 pages with helpful links | M |
| FR-016 | Floating WhatsApp + Call buttons on all pages (mobile sticky bar) | M |
| FR-017 | Newsletter signup (double opt-in) | C |
| FR-018 | Price calculator / solar load calculator that recommends a package (appliances → kVA) | C |

### 2.2 Quotes & Bookings
| ID | Requirement | Pri |
|---|---|---|
| FR-020 | Quote request form: service type, property type, location (state/LGA), appliances/needs, budget range, preferred contact, optional photo upload | M |
| FR-021 | Installation/inspection booking: date, time slot, address, service, notes; admin confirms | M |
| FR-022 | Maintenance plan enquiry/subscription (annual maintenance contract) with scheduled visits | S |
| FR-023 | Admin pipeline for quotes: New → Contacted → Site Visit → Quoted → Won/Lost; assign to staff; notes | M |
| FR-024 | Email + WhatsApp/SMS notification to admin on new lead; confirmation to customer | M |
| FR-025 | Spam protection (honeypot + Turnstile/reCAPTCHA + rate limit) | M |

### 2.3 E-commerce
| ID | Requirement | Pri |
|---|---|---|
| FR-030 | Cart (guest via session, merged to account on login); update qty, remove | M |
| FR-031 | Checkout: contact, state/LGA/address, delivery vs installation option, notes | M |
| FR-032 | Delivery/installation fee computed per state (admin-configurable) | M |
| FR-033 | Payments: Paystack (card, bank, USSD, transfer) | M |
| FR-034 | Manual bank transfer with proof-of-payment upload and admin approval | M |
| FR-035 | Optional deposit payment (e.g. 50–70%) for large solar packages; balance on installation | S |
| FR-036 | Server-side payment verification + signed webhook handling, idempotent | M |
| FR-037 | Order statuses and timeline; customer order tracking page | M |
| FR-038 | Invoice/receipt PDF; order confirmation emails | M |
| FR-039 | Inventory/stock tracking per product (optional "made to order") | S |
| FR-040 | Coupons/discounts | C |
| FR-041 | "Request installation after purchase" scheduling | S |
| FR-042 | Flutterwave as secondary gateway | C |

### 2.4 Customer Account
| ID | Requirement | Pri |
|---|---|---|
| FR-050 | Register/login (email or phone), password reset, email verification | M |
| FR-051 | Dashboard: orders, quotes, bookings, maintenance plans, invoices | M |
| FR-052 | Saved addresses, profile, change password | S |
| FR-053 | Guest checkout allowed | M |

### 2.5 Admin Dashboard
| ID | Requirement | Pri |
|---|---|---|
| FR-060 | Role-based access (Super Admin, Admin, Content Manager, Sales, Technician) | M |
| FR-061 | CRUD: services, categories, products/packages (with included items, images, specs, SEO fields) | M |
| FR-062 | **Video upload** for projects (resumable/chunked, progress bar, processing status, poster selection) plus option to attach YouTube/Vimeo URL | M |
| FR-063 | Photo galleries with drag-to-reorder, alt text required | M |
| FR-064 | CRUD: projects, testimonials, FAQs, blog posts, locations, static pages | M |
| FR-065 | Orders management: view, change status, record manual payment, refund note, print invoice | M |
| FR-066 | Quotes/bookings/leads management with assignment and notes | M |
| FR-067 | Per-page SEO override (title, description, OG image, canonical, noindex) | M |
| FR-068 | Redirect manager (301/302), auto-created on slug change | M |
| FR-069 | Site settings: company info, phones, WhatsApp, address, hours, social links, delivery fees, bank details, VAT %, analytics IDs | M |
| FR-070 | Dashboard stats: leads, orders, revenue, top products, pending items | S |
| FR-071 | Activity log (who changed what) | S |
| FR-072 | Rich text editor with image upload and heading structure enforcement | S |
| FR-073 | CSV export of orders/leads | S |

### 2.6 SEO (see `docs/SEO.md` for full spec)
| ID | Requirement | Pri |
|---|---|---|
| FR-080 | Inertia SSR for all public routes | M |
| FR-081 | Unique title/meta description/canonical/OG/Twitter on every indexable page | M |
| FR-082 | JSON-LD: Organization, LocalBusiness, WebSite, BreadcrumbList, Service, Product/Offer, VideoObject, Article, FAQPage | M |
| FR-083 | XML sitemap index (+ video & image sitemaps), robots.txt | M |
| FR-084 | Clean, stable, keyword-aware URLs; 301 on changes; 410 for removed | M |
| FR-085 | Core Web Vitals pass on mobile | M |
| FR-086 | Open Graph images auto-generated or admin-set | S |
| FR-087 | Google Search Console + GA4 integration, consent-aware | M |
| FR-088 | Breadcrumbs on all inner pages | M |

## 3. Non-Functional Requirements
| ID | Requirement |
|---|---|
| NFR-001 | **Performance:** LCP < 2.5s, INP < 200ms, CLS < 0.1 on Slow 4G mobile; TTFB < 600ms |
| NFR-002 | **Lighthouse (mobile):** Perf ≥ 85, SEO 100, A11y ≥ 95, Best Practices ≥ 95 |
| NFR-003 | **Accessibility:** WCAG 2.1 AA |
| NFR-004 | **Responsive:** 320px → 1920px, mobile-first |
| NFR-005 | **Browser support:** last 2 versions of Chrome, Safari, Edge, Firefox, Samsung Internet; Android Chrome priority |
| NFR-006 | **Security:** OWASP Top 10 mitigated; HTTPS only; HSTS; CSP; 2FA for admins; rate limiting |
| NFR-007 | **Privacy:** NDPA 2023 compliant: consent, privacy policy, data-subject requests process |
| NFR-008 | **Reliability:** daily DB + media backups, offsite; restore tested; uptime ≥ 99.5% |
| NFR-009 | **Scalability:** queue-driven heavy work; CDN for media; Redis cache |
| NFR-010 | **Maintainability:** typed code, tests for critical paths, documented conventions |
| NFR-011 | **Localisation:** en-NG, ₦, WAT timezone (`Africa/Lagos`), Nigerian phone/address formats |
| NFR-012 | **Observability:** error tracking, uptime checks, structured logs |
| NFR-013 | **Low-data mode:** video defaults to click-to-play; images served as WebP/AVIF with srcset |

## 4. Core User Stories & Acceptance Criteria

**US-1 Visitor finds a solar package**
Given I search "5kva solar system price" and land on a package page,
when the page loads, then I see price in ₦, itemised inclusions, warranty, installation
info, a Buy button, a Get Quote button and a WhatsApp button, and the page is fully
rendered in HTML source (SSR) with Product JSON-LD.

**US-2 Visitor watches proof of work**
Given I open a project page, when I tap play, then a compressed video streams within 3s on a
4G connection, a poster shows before playback, and a text description is present for SEO.

**US-3 Customer buys a DSTV package**
Given I add a DSTV package to cart, choose my state and pay via Paystack, then the order
becomes **Paid** only after server-side verification, I receive an email + invoice, and the
admin is notified.

**US-4 Customer pays by bank transfer**
Given I choose bank transfer, then I see the company's account details and order
reference, can upload proof, and the order stays **Awaiting Payment Confirmation** until an
admin approves.

**US-5 Admin uploads a work video**
Given I am a Content Manager, when I upload a 300 MB MP4 on a flaky connection, then the
upload resumes after interruption, the video is transcoded in the background to web-optimised
renditions with a poster, and the project becomes publishable when status = Ready.

**US-6 Lead submits a quote**
Given I submit the quote form, then spam checks pass, the lead is stored, an email +
WhatsApp/SMS alert goes to sales, and I see a confirmation with a reference number.

**US-7 Admin edits SEO**
Given I edit a product's slug, then a 301 redirect from the old URL is created and the
sitemap updates.

## 5. Out of Scope (v1)
Native mobile apps, multi-vendor marketplace, multi-currency, live chat with agents (WhatsApp
is used instead), full ERP/inventory/accounting, loan/financing integration (enquiry form only).

## 6. Assumptions (agent may append)
- Single company, single currency (NGN), English language.
- Company will supply real photos, videos, prices, legal text, and brand assets.
- Hosting: VPS + Cloudflare; object storage (S3-compatible) for videos.
- VAT is configurable (default 7.5%) and may be shown inclusive or exclusive.
- Starlink hardware sales depend on the company's authorised reseller/installer status; the
  site must not claim official partnership unless the client confirms it.

## 7. Open Questions For The Client
1. Company name, logo, brand colours, tagline?
2. States/cities served? Is installation nationwide or limited?
3. Which brands/products are sold? Real package list and prices?
4. Are prices public, or "from ₦X" with quote for large systems?
5. Delivery/installation fee table per state?
6. Payment: Paystack account ready? Bank details for transfers?
7. Do you want deposits/instalments for solar packages?
8. Warranty and after-sales terms? Maintenance plan pricing?
9. Official authorisations (e.g. Starlink, MultiChoice) to display?
10. WhatsApp Business number and notification preferences?
