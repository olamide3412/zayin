# ROUTES.md

## Public (indexable, SSR)
| Method | URL | Name | Notes |
|---|---|---|---|
| GET | `/` | home | |
| GET | `/about` | about | |
| GET | `/services` | services.index | |
| GET | `/services/{slug}` | services.show | e.g. `/services/solar-installation` |
| GET | `/packages` | products.index | All packages; filters via query (noindex filter combos) |
| GET | `/packages/{category}` | products.category | `/packages/solar`, `/packages/dstv`, `/packages/starlink` |
| GET | `/packages/{category}/{slug}` | products.show | |
| GET | `/projects` | projects.index | |
| GET | `/projects/{slug}` | projects.show | Video + photos |
| GET | `/blog` · `/blog/{slug}` · `/blog/category/{slug}` | blog.* | |
| GET | `/faq` | faq | |
| GET | `/areas` · `/areas/{state}/{city?}` | locations.* | Service-area pages |
| GET | `/contact` · POST `/contact` | contact.* | throttle + captcha |
| GET | `/request-quote` · POST | quotes.* | |
| GET | `/book-installation` · POST | bookings.* | |
| GET | `/maintenance-plans` | plans.index | |
| GET | `/search` | search | `noindex` |
| GET | `/privacy-policy` `/terms` `/refund-policy` `/warranty` | pages.show | |
| GET | `/sitemap.xml` + child sitemaps | sitemap.* | |
| GET | `/robots.txt` | robots | dynamic by env |

## Commerce (noindex)
| `/cart` | cart.show |
| POST `/cart/items` · PATCH/DELETE `/cart/items/{id}` | cart.* |
| `/checkout` · POST `/checkout` | checkout.* |
| `/checkout/pay/{order}` | initialise Paystack |
| GET `/payment/callback` | verify server-side then redirect |
| `/orders/{number}/track` | guest tracking (signed URL or number+phone) |
| `/orders/{number}/bank-transfer` | show details + upload proof |

## Webhooks (no CSRF, signature-verified)
`POST /webhooks/paystack` — verify `x-paystack-signature` (HMAC SHA-512 of raw body with secret
key), idempotent by event id/reference.

## Auth & Account (noindex, auth middleware)
`/login /register /forgot-password /reset-password /verify-email`
`/account` · `/account/orders` · `/account/orders/{number}` · `/account/quotes` ·
`/account/bookings` · `/account/maintenance` · `/account/addresses` · `/account/profile`

## Admin (`/admin`, auth + role middleware, `noindex`, throttled)
`/admin` dashboard · `/admin/services` · `/admin/products` · `/admin/product-categories` ·
`/admin/projects` (+ video upload endpoints) · `/admin/orders` · `/admin/payments` ·
`/admin/quotes` · `/admin/bookings` · `/admin/contacts` · `/admin/testimonials` ·
`/admin/faqs` · `/admin/posts` · `/admin/pages` · `/admin/locations` · `/admin/redirects` ·
`/admin/seo` · `/admin/settings` · `/admin/users` · `/admin/roles` · `/admin/activity`

## Video upload endpoints (admin)
`POST /admin/uploads/init` → returns upload id · `POST /admin/uploads/{id}/chunk` ·
`POST /admin/uploads/{id}/complete` → dispatches `ProcessVideoUpload` ·
`GET /admin/videos/{id}/status` (polling).

## URL Rules
Lowercase, hyphenated, no trailing slash (301 to canonical), no IDs in public URLs, HTTPS
and non-www (or www) enforced with 301, query strings never indexable unless whitelisted.
