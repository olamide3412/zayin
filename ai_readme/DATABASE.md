# DATABASE.md

Conventions: `id` bigint PK, `created_at/updated_at`, soft deletes where noted (`sd`).
Money = unsigned bigint **kobo**. Enums stored as strings. Indexes on FK, slug (unique),
status, published_at. Timezone stored UTC; displayed `Africa/Lagos`.

## Users & Access
- **users**: name, email (unique, nullable), phone (E.164, unique nullable), password,
  email_verified_at, marketing_consent bool, last_login_at, 2fa fields
- Roles/permissions tables from `spatie/laravel-permission`
- **addresses**: user_id, label, recipient, phone, state_id, lga/city, street, landmark, is_default

## Geography
- **states**: name, slug, code (36 + FCT), delivery_fee (kobo), install_fee (kobo), is_active
- **locations** (service-area SEO pages): state_id, city, slug, headline, body, services_offered, lat, lng, is_published

## Catalog
- **service_categories**: name, slug, description, icon, sort_order
- **services** (sd): category_id, name, slug, summary, body, hero_image, benefits json, process json, is_featured, is_published, sort_order
- **product_categories**: parent_id, name, slug, description, sort_order  (Solar, DSTV, Starlink, Accessories…)
- **products** (sd): category_id, service_id nullable, type enum(solar_package, dstv_package, starlink_kit, accessory, service_plan), name, slug, sku, summary, description, price (kobo), compare_at_price nullable, price_label (e.g. "From"), requires_quote bool, installation_included bool, warranty_text, specs json (e.g. capacity_kva, battery_kwh, panels_watts), brand, stock_qty nullable, track_stock bool, is_featured, is_published, published_at
- **product_inclusions**: product_id, item_name, quantity, description, sort_order  (what's in the package)
- **product_variants** (optional): product_id, name, sku, price, stock_qty
- **brands**: name, slug, logo
- Media via `media` table (spatie): product images, gallery

## Portfolio
- **projects** (sd): service_id, state_id, city, title, slug, summary, body, client_name nullable, completed_on, system_size (e.g. "5kVA"), is_featured, is_published, published_at
- **project_videos**: project_id, title, description, transcript, source enum(upload, youtube, vimeo), external_url, status enum(uploading, processing, ready, failed), duration_seconds, poster_path, renditions json (480p/720p paths), file_size, sort_order
- **project_product**: project_id, product_id (pivot; shows related packages)
- Photos via `media`

## Content
- **posts** (sd): author_id, category_id, title, slug, excerpt, body, featured_image, reading_minutes, published_at, status
- **post_categories**, **tags**, **post_tag**
- **pages**: title, slug, body, template, is_published (about, privacy, terms…)
- **faqs**: faqable (morph: service/product/location/global), question, answer, sort_order, is_published
- **testimonials**: name, role/location, rating (1–5), body, service_id nullable, project_id nullable, photo, is_verified, is_published

## Leads
- **contact_messages**: name, email, phone, subject, message, status, ip_hash, handled_by
- **quote_requests**: reference (unique), user_id nullable, name, email, phone, service_id, product_id nullable, property_type, state_id, city, address, budget_range, details text, status enum(new, contacted, site_visit, quoted, won, lost), assigned_to, quoted_amount nullable, notes
- **quote_attachments** via media
- **bookings**: reference, user_id nullable, service_id, name, phone, address, state_id, preferred_date, preferred_slot, status enum(pending, confirmed, completed, cancelled), technician_id nullable, notes
- **maintenance_plans**: name, slug, service_id, description, price (kobo), visits_per_year, inclusions json
- **maintenance_subscriptions**: user_id, plan_id, start_date, end_date, status, next_visit_date
- **newsletter_subscribers**: email, confirmed_at, token

## Commerce
- **carts**: id (uuid), user_id nullable, session_id, expires_at; **cart_items**: cart_id, product_id, variant_id nullable, qty, unit_price (snapshot)
- **orders**: number (unique, e.g. ORD-2026-000123), user_id nullable, guest email/phone, status enum(pending, awaiting_payment, paid, processing, scheduled, installed/delivered, completed, cancelled, refunded), payment_status enum(unpaid, partial, paid, failed, refunded), fulfilment_type enum(delivery, installation, pickup), subtotal, delivery_fee, install_fee, discount, tax, total (all kobo), amount_paid, currency 'NGN', billing/shipping address json, notes, placed_at
- **order_items**: order_id, product_id, name_snapshot, sku_snapshot, qty, unit_price, total
- **order_status_histories**: order_id, from, to, note, user_id
- **payments**: order_id, gateway enum(paystack, flutterwave, bank_transfer, cash), reference (unique), amount (kobo), status enum(initiated, pending, success, failed, reversed), gateway_response json, paid_at, verified_at, proof (media for transfers), approved_by
- **webhook_events**: provider, event_id (unique for idempotency), payload json, processed_at
- **coupons** (optional): code, type, value, starts_at, ends_at, max_uses, used_count
- **invoices**: order_id, number, issued_at, pdf_path

## SEO & System
- **seo_meta** (morph): seoable, title, description, canonical, og_image, robots (index/noindex), json_ld_extra json
- **redirects**: from_path (unique), to_path, status_code (301/302), hits, is_active
- **settings**: key (unique), value json, group
- **activity_log** (spatie)
- **media** (spatie), **jobs**, **failed_jobs**, **cache**

## Key Relationships
Service 1–N Product · Service 1–N Project · Project 1–N ProjectVideo · Project N–N Product ·
Order 1–N OrderItem · Order 1–N Payment · User 1–N Order/Quote/Booking/Address ·
State 1–N Location/Address/Project.

## Seeders
`StatesSeeder` (36 states + FCT with default fees), `RolesSeeder`, `SettingsSeeder`,
`DemoContentSeeder` (clearly labelled demo; local/staging only), `AdminSeeder` (from env).
