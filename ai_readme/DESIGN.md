# DESIGN.md — Brand, UI/UX & Nigerian Market Guidance

## 1. Design Principles
1. **Trust first** — real photos/videos, visible phone/WhatsApp, real address, clear warranty.
2. **Mobile first** — design at 360px, enhance upward.
3. **Clarity** — plain English, itemised packages, prices in ₦, no jargon without explanation.
4. **Speed** — light pages; the site should feel instant on mobile data.
5. **Action everywhere** — Get Quote / Buy / WhatsApp always one tap away.

## 2. Brand (placeholders — confirm with client)
- Personality: reliable, professional, modern, energetic.
- Suggested palette: Deep navy `#0B1F3A` (trust), Solar amber `#F59E0B` (energy / CTA accent),
  Electric green `#16A34A` (success/eco), Neutral slate scale, white backgrounds.
  Contrast must meet WCAG AA (check amber on white; use navy text on amber buttons).
- Typography: a clean sans (e.g. Inter or Plus Jakarta Sans), self-hosted WOFF2, 2 weights
  (400/600–700). Base 16px, line-height 1.6.
- Logo: SVG, plus favicon set and OG default image.
- Iconography: `lucide-vue-next`, consistent stroke.
- Imagery: authentic installs (Nigerian homes/shops/estates), team in branded uniform,
  before/after. Avoid generic foreign stock photos where possible.

## 3. Layout & Components
Header: logo · Services (mega-menu) · Packages · Projects · Blog · Contact · **Get Quote**
button · cart icon · phone. Sticky on scroll (compact).
Mobile: hamburger + **sticky bottom bar** with `Call` · `WhatsApp` · `Get Quote`.
Footer: services, packages, areas served, company links, contact (NAP), socials, payment
badges, legal links, newsletter.

Reusable components: Button, Card, PackageCard, ServiceCard, ProjectCard, VideoPlayer,
Gallery/Lightbox, Breadcrumbs, FAQ accordion, TestimonialSlider, StatsStrip, StepProcess,
PriceTag (`₦`), Badge, Toast, Modal, Pagination, FormField set, Skeletons, EmptyState,
CookieBanner, WhatsAppButton, StateSelect/LgaSelect.

## 4. Key Pages (content blocks)
**Home:** Hero (headline + subheading + 2 CTAs + trust badges) → 3 service tiles → Featured
packages (Solar / DSTV / Starlink tabs) → "See our work" video grid → How it works (4 steps:
Enquire → Site survey → Install → Support) → Why choose us → Testimonials → Areas served →
FAQ preview → Final CTA.
**Service page:** Hero → overview → benefits → packages → process → projects/videos →
FAQs → CTA.
**Package page:** Gallery · title · price · what's included · specs · warranty · delivery &
install info · Buy / Quote / WhatsApp · FAQs · related.
**Project page:** Video (hero) · summary (location, system size, date) · gallery · story ·
related package CTA.
**Checkout:** single page, progress steps, guest-friendly, trust badges, order summary
sticky on desktop.

## 5. Nigerian Market UX Notes
- Prefer **WhatsApp** and **calls** for conversion; track both.
- Show **prices** (or "from ₦X") — hidden prices lose trust; allow "Request exact quote".
- Show **state coverage**, installation timeframes, and whether installation is included.
- Payment reassurance: Paystack badge, bank transfer details, receipt/invoice promise.
- Address entry: State → City/LGA → Street + nearest landmark.
- Dates/time: `Africa/Lagos`; 12-hour display ok.
- Explain solar terms simply (kVA, inverter, battery, panels, autonomy hours).
- Light/low-data: lazy images, click-to-play video, optional "Data saver" notice.
- Offer maintenance plans and warranty prominently — a major trust driver.

## 6. Accessibility
WCAG 2.1 AA: colour contrast, focus rings, keyboard nav, skip link, alt text, form labels,
`aria-*` on accordion/menus, reduced-motion support, min tap target 44×44px.

## 7. Motion
Subtle only (150–250ms). Respect `prefers-reduced-motion`. No heavy parallax/animations.

## 8. Copy Tone
Friendly, confident, professional Nigerian English. Short sentences. Benefits before specs.
Examples: "Power your home 24/7 — no more fuel costs." "Fast, neat DSTV installation by certified
technicians." (Use "certified" only if true.)
