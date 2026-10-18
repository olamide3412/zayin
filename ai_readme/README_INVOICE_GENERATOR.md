# Invoice Generator (Nigerian A5 Invoice) — AI Agent Build Guide

> Read this file fully before writing code. It defines scope, the invoice layout, calculation rules, the self-learning item catalog, data model, routes, and tests. Anything marked **configurable** must live in settings, never in business logic.

---

## 1. What We're Building

An in-platform **invoice generator** where an admin:

1. Fills in a simple form: who the invoice is for, and what it is for (line items),
2. Gets a **standard Nigerian-style invoice** (company name, logo, address, RC/TIN, bank details, VAT, amount in words) on a clean **A5** page,
3. Can **download it as PDF**, **edit it later**, and **customize** how it looks,
4. Gets **smart item suggestions** while typing, and anything new typed is **saved to an item catalog** so it's suggested next time.

It can ship as a **standalone module** or inside the Hotel Management System (e.g., generate an invoice from a guest folio or event booking). Keep the invoice logic independent of the hotel modules so it works in both.

---

## 2. Tech Stack

| Layer | Choice |
|---|---|
| Backend | Laravel 11+ (PHP 8.3+) |
| Frontend | Vue 3 (`<script setup>`) + Inertia.js + Tailwind CSS |
| Database | MySQL 8+ |
| PDF | `barryvdh/laravel-dompdf` (works on cheap shared hosting/VPS, no headless Chrome needed) |
| Permissions | `spatie/laravel-permission` |
| Tests | Pest (backend), Vitest (frontend logic) |

**Alternative PDF engine:** if DomPDF's CSS limits hurt the design, `spatie/laravel-pdf` with Browsershot gives better CSS but requires Chrome on the server. Keep PDF generation behind an `InvoicePdfRenderer` interface so the engine can be swapped without touching business logic.

**Key rules**
- Money is stored as **integer kobo** (₦1 = 100 kobo). No floats, ever.
- All totals are computed by a pure PHP class, `App\Services\Invoice\InvoiceCalculator` (no DB, no HTTP), so it's trivially testable.
- The **live preview and the PDF use the same Blade template** (preview is that HTML shown in an iframe). This guarantees "what you see is what you download."

---

## 3. User Flow

**A. Create invoice (single page form)**
1. **Bill To:** pick an existing customer (autosuggest) or type a new one: name, address, phone, email, optional TIN. New customers are saved automatically.
2. **Invoice details:** issue date (default today), due date or payment terms (Due on receipt / 7 / 14 / 30 days), optional PO/reference, optional "Subject / Description of work."
3. **Line items:** add rows. Each row: description, quantity, unit (pcs, hrs, nights, day, set…), unit price (₦), optional discount, VAT toggle.
   - Typing in the description field shows **suggestions** from the item catalog (§6). Selecting one fills description, unit and default price (all still editable).
   - If the typed item isn't in the catalog, the user just keeps typing; it is **saved to the catalog on save** (with a "Save to item list" checkbox, on by default).
4. **Totals panel (live):** subtotal, discount, VAT, total, amount paid, balance due, and **amount in words**.
5. **Notes & terms:** prefilled from defaults, editable per invoice.
6. **Live A5 preview** beside (desktop) or below/tab (mobile) the form.
7. Actions: **Save Draft**, **Issue Invoice**, **Download PDF**, **Print**, **Share link**.

**B. Invoice list**
- Search, filter by status/date/customer, totals per filter, quick actions: view, edit, duplicate, download, record payment, void.

**C. Edit**
- Draft: fully editable.
- Issued: editable with a **revision log** (§8). Voiding replaces deleting.

---

## 4. The Invoice Layout (A5, Standard Nigerian)

**Page:** A5 portrait, **148 × 210 mm**, margins ~10 mm. In DomPDF set paper to `a5`, portrait. Base font size ~8.5–9 pt for body, 7.5 pt for fine print.

**Font warning:** DomPDF's default Helvetica lacks the Naira glyph (₦). Bundle and register **DejaVu Sans** or a font that includes U+20A6 (e.g., Noto Sans), and verify ₦ renders in the PDF. Fallback: show "NGN" instead of the symbol if the glyph can't render.

### Section order (top to bottom)

1. **Header band**
   - Left: **logo** (max ~14 mm tall), company name (bold), tagline (optional)
   - Right: the word **INVOICE** (large, accent color), invoice number, issue date, due date
2. **Seller details** (small text under the header): full address, phone numbers, email, website, **RC Number** (CAC), **TIN** (tax ID)
3. **Two-column block**
   - **Bill To:** customer name, address, phone/email, TIN (if provided)
   - **Invoice Info:** PO/reference, payment terms, status badge (DRAFT / UNPAID / PAID)
4. **Items table:** `S/N | Description | Qty | Unit Price | Amount` (optional columns: Unit, Discount, VAT). Light zebra rows, thin dividers, accent-colored header row.
5. **Totals block** (right-aligned): Subtotal, Discount, VAT (7.5%), **Total** (bold, accent), Amount Paid, **Balance Due**
6. **Amount in words** (italic): e.g., *"Two Hundred and Fifty Thousand Naira, Fifty Kobo Only."*
7. **Payment details box:** Bank name, Account name, Account number (support multiple accounts), optional payment note
8. **Notes / Terms** (small text)
9. **Footer:** "Thank you for your business", signature line + "Authorized Signature" (optional signature image), optional stamp image, page number ("Page 1 of 1")

### Design guidance ("fine design")
- Clean, lots of whitespace, one **accent color** (default deep green or navy, user-customizable), neutral greys for everything else.
- Thin 0.5 pt dividers rather than heavy boxes. Rounded accent header only on the title band.
- Right-align all numbers. Use tabular, aligned columns.
- Optional faint **watermark**: "PAID" (green) or "DRAFT"/"VOID" (grey/red), low opacity, rotated.
- Must look good in **greyscale** (many offices print in black and white).

### Multi-page handling (A5 is small)
- Repeat the table header on page breaks (`thead { display: table-header-group }`), avoid splitting a row across pages (`page-break-inside: avoid`).
- Totals, amount in words, bank details, and signature appear **only on the last page**; earlier pages show "Continued…".
- Roughly 10–12 line items fit on page 1 with a full header; extra rows flow to page 2.

---

## 5. Calculation Rules (authoritative spec)

All values in **kobo**. Quantity may be decimal (e.g., 2.5 hours), stored as a decimal with 2–3 places.

```
line_gross   = round_half_up(quantity × unit_price)
line_discount = fixed amount OR (line_gross × percent)    // per line, optional
line_net     = line_gross − line_discount

invoice_discount (optional, applied on subtotal)
subtotal     = Σ line_net
taxable_base = Σ line_net of lines flagged VAT-applicable − proportional share of invoice_discount
vat          = round_half_up(taxable_base × vat_rate)      // default 7.5%, configurable
total        = subtotal − invoice_discount + vat
balance_due  = total − Σ payments
```

- **VAT mode (configurable per invoice):**
  - *Exclusive* (default): prices entered exclude VAT, VAT added on top.
  - *Inclusive*: prices already include VAT; VAT is extracted: `vat = taxable_total − taxable_total / (1 + rate)`.
- **VAT rate** is stored in settings (`vat_rate_bps`, 750 = 7.5%); do not hardcode it. Lines can be flagged non-VATable (e.g., VAT-exempt goods and services).
- **Rounding:** round each line to the nearest kobo (half-up), then sum. Never round only at the end.
- **Optional WHT line (P2):** many corporate customers deduct **withholding tax** when paying. Offer an optional, configurable "WHT deducted by customer (x%)" line that reduces the expected payment but does not change the invoice total. Rates vary by transaction type and change; **flag "verify current rates" in the UI** rather than seeding a hardcoded rate as truth.
- **Amount in words:** convert Naira and kobo: `250,000.50 → "Two Hundred and Fifty Thousand Naira, Fifty Kobo Only"`. Implement as a tested helper (`NairaInWords`) handling thousands/millions/billions, zero kobo ("… Naira Only"), and zero naira ("Fifty Kobo Only").

### Worked example (primary test fixture)

| Description | Qty | Unit price | VAT? | Line net |
|---|---|---|---|---|
| Website design | 1 | ₦150,000.00 | Yes | ₦150,000.00 |
| Monthly hosting (12 months) | 12 | ₦2,500.00 | Yes | ₦30,000.00 |
| Domain registration | 1 | ₦15,000.00 | No | ₦15,000.00 |

- Subtotal = ₦195,000.00
- VAT base = 150,000 + 30,000 = ₦180,000.00 → VAT 7.5% = **₦13,500.00**
- Total = **₦208,500.00**
- Words: *"Two Hundred and Eight Thousand, Five Hundred Naira Only."*
- With ₦100,000 paid → Balance due **₦108,500.00**

Add a second fixture with a percentage line discount and invoice discount, a third using inclusive VAT, and a fourth with odd-kobo rounding (e.g., qty 3 × ₦333.33).

---

## 6. Smart Item Catalog (suggest + self-learning)

### Behavior
1. As the admin types in the description field (after 1–2 characters), call a lightweight search endpoint and show up to ~8 suggestions.
2. **Ranking:** exact/prefix match first, then contains/fuzzy match; ties broken by `usage_count` (most-used first), then `last_used_at`. Frequently used items rise to the top over time.
3. Selecting a suggestion fills **description, unit, default price, VAT flag**. All remain editable for that invoice.
4. If the admin types something not in the catalog, it appears in the row as a "**New item**" chip; on saving the invoice it is **inserted into the catalog** (unless "Save to item list" is unticked).
5. If the item **exists but the price differs**, do **not** silently overwrite the catalog price. Store it as `last_price_kobo` and show a small hint next time ("Last used at ₦X"). Offer a one-click "Update default price" on the catalog screen.
6. **Dedupe:** compare on a normalized name (`lowercase`, trim, collapse spaces, strip punctuation). Typing "Website Design " must match "website design", not create a duplicate.
7. Each use increments `usage_count` and updates `last_used_at`.
8. Debounce requests (~250 ms), cache the catalog per business, and keep the endpoint fast (indexed prefix search; add a MySQL FULLTEXT index on `name` for fuzzy matches).

### Catalog management screen (admin)
Search, edit (name, unit, default price, VAT flag), **merge duplicates**, archive/unarchive (never hard-delete items used on past invoices), bulk import/export CSV.

### Starter seed items (names and units only, **no hardcoded prices**; admin sets prices)

| Category | Items |
|---|---|
| General services | Consultancy fee, Professional service fee, Project management fee, Training / workshop, Maintenance & repairs, Installation charge, Labour charge, Transport / logistics, Delivery charge |
| Tech & creative | Website design, Website maintenance, Hosting (monthly), Domain registration, Software development, Graphic design, Branding / logo design, Social media management |
| Supply & trade | Supply of goods, Equipment supply, Materials, Printing, Packaging |
| Hospitality | Room accommodation (per night), Restaurant & bar, Laundry service, Event hall rental, Catering service, Airport pickup, Conference / meeting room |
| Solar & electrical | Solar panel supply, Inverter supply, Battery supply, Solar installation, Electrical wiring, System maintenance |
| Misc | Deposit, Service charge, Miscellaneous |

Seeds are just a head start; the catalog grows from real usage.

---

## 7. Customization

Stored in `invoice_templates` (per business; allow multiple saved templates, one default).

| Setting | Options |
|---|---|
| Logo | Upload (PNG/JPG/SVG, ≤ 2 MB), position left / center, size |
| Accent color | Color picker with presets (green, navy, maroon, charcoal, teal), contrast-checked |
| Layout style | **Classic** (bordered table), **Modern** (clean, color header), **Minimal** (text-only, print-friendly) — each is a Blade view sharing the same data |
| Typography | Choice of 2–3 bundled fonts that include ₦ |
| Visible fields | Toggle RC no., TIN, website, PO ref, unit column, discount column, VAT column, amount in words, bank details, signature, stamp |
| Bank accounts | Multiple accounts; choose which to show on each invoice |
| Document title | "Invoice", "Tax Invoice", "Proforma Invoice" |
| Watermark | Auto (PAID/DRAFT/VOID) on/off |
| Default notes & terms | Per template; overridable per invoice |
| Footer message | Custom text (e.g., "Thank you for your patronage") |
| Number format | Prefix, year, padding (e.g., `INV-2026-0001`) |

The settings page includes a **live A5 preview** with sample data.

---

## 8. Statuses, Numbering, Editing & Audit

**Status flow:** `Draft → Issued → Partially Paid → Paid`, plus `Overdue` (computed when past due date and unpaid) and `Void`.

**Invoice numbering**
- Numbers are assigned **when the invoice is issued**, not when a draft is created, so issued invoices form a **gap-free sequence** (important for tax and audit trails).
- Use a sequence table with a row lock (`SELECT … FOR UPDATE` inside a transaction) to avoid duplicate numbers under concurrent issuing.
- Voided invoices keep their number (never reuse numbers).

**Editing rules**
- Draft: free editing, can be deleted.
- Issued: editable, but every save creates an `invoice_revisions` row (who, when, JSON snapshot before/after, optional reason). Show a small "Edited" marker with revision history. Optionally lock editing once any payment is recorded (configurable), requiring a credit note or void instead.
- Void: requires a reason, keeps all data, shows a VOID watermark. Never hard-delete an issued invoice.
- **Duplicate** action: copies an invoice into a new draft (new customer/date), useful for recurring billing.

**Payments:** record partial/full payments (method: cash, bank transfer, POS, Paystack/Flutterwave reference; amount; date; reference). Status updates automatically from the balance.

**Overdue:** a scheduled daily command (`invoices:mark-overdue`) updates overdue status and can optionally send reminders.

---

## 9. Data Model

```
businesses                                (company profile; hotel_id/tenant_id optional if used inside the HMS)
  id, name, tagline, logo_path, address_line1, address_line2, city, state,
  phone, phone_alt, email, website, rc_number, tin,
  signature_path, stamp_path, vat_rate_bps (default 750),
  default_vat_mode (exclusive|inclusive), default_terms, default_notes, footer_text

business_bank_accounts
  id, business_id, bank_name, account_name, account_number, is_default, sort_order

invoice_templates
  id, business_id, name, layout (classic|modern|minimal), accent_color,
  font_family, logo_position, document_title, show_fields (json),
  show_watermark, number_prefix, number_padding, is_default

customers
  id, business_id, name, address, phone, email, tin, notes,
  created_at, updated_at, deleted_at

invoice_items_catalog
  id, business_id, name, normalized_name (indexed, unique per business),
  description, unit, default_price_kobo, last_price_kobo, is_vatable,
  category, usage_count, last_used_at, is_archived, created_at, updated_at
  FULLTEXT(name)

invoices
  id, business_id, customer_id, template_id,
  invoice_number (nullable until issued, unique per business),
  status, issue_date, due_date, payment_terms, reference, subject,
  vat_mode, vat_rate_bps, invoice_discount_kobo,
  subtotal_kobo, discount_kobo, vat_kobo, total_kobo, paid_kobo, balance_kobo,
  amount_in_words, notes, terms, bank_account_ids (json),
  issued_at, voided_at, void_reason, created_by, created_at, updated_at, deleted_at

invoice_items
  id, invoice_id, catalog_item_id (nullable), sort_order,
  description, quantity, unit, unit_price_kobo,
  discount_type (none|fixed|percent), discount_value,
  is_vatable, line_net_kobo

invoice_payments
  id, invoice_id, amount_kobo, method, reference, paid_on, recorded_by, created_at

invoice_revisions
  id, invoice_id, user_id, before (json), after (json), reason, created_at

invoice_number_sequences
  id, business_id, year (nullable if not yearly reset), last_number
```

Store **computed totals on the invoice** for fast listing and reporting, and **recompute server-side on every save** (never trust client-sent totals).

---

## 10. Code Structure

```
app/
  Services/Invoice/
    InvoiceCalculator.php           // pure money math (§5)
    NairaInWords.php                // number-to-words (§5)
    InvoiceNumberGenerator.php      // locked, gap-free sequence
    ItemCatalogService.php          // search, upsert, dedupe, ranking
    InvoicePdfRenderer.php          // interface
    DomPdfInvoiceRenderer.php
  Actions/Invoice/
    SaveInvoiceDraftAction.php
    IssueInvoiceAction.php
    UpdateIssuedInvoiceAction.php   // writes revision
    VoidInvoiceAction.php
    RecordPaymentAction.php
    DuplicateInvoiceAction.php
  Http/Controllers/Invoice/
    InvoiceController.php
    InvoicePdfController.php
    InvoicePreviewController.php    // returns Blade HTML for the iframe
    InvoiceItemSuggestionController.php
    CustomerController.php
    CatalogController.php
    TemplateController.php
    BusinessProfileController.php
  Http/Requests/Invoice/
  Policies/InvoicePolicy.php
  Console/Commands/MarkOverdueInvoices.php

resources/views/invoices/
  layouts/classic.blade.php
  layouts/modern.blade.php
  layouts/minimal.blade.php
  partials/ (header, items-table, totals, bank-box, footer)

resources/js/
  Pages/Invoices/
    Index.vue, Create.vue, Edit.vue, Show.vue
    Customers/Index.vue
    Catalog/Index.vue
    Settings/BusinessProfile.vue, Settings/Templates.vue
  Components/Invoice/
    ItemRow.vue
    ItemSuggestInput.vue           // debounced autosuggest
    CustomerPicker.vue
    TotalsPanel.vue
    InvoicePreviewFrame.vue        // iframe of preview endpoint
    StatusBadge.vue
  Composables/
    useInvoiceTotals.ts            // mirrors PHP math for live totals (PHP stays authoritative)
    useNaira.ts
```

### Routes

```
GET    /invoices                          invoices.index
GET    /invoices/create                   invoices.create
POST   /invoices                          invoices.store          (save draft)
GET    /invoices/{invoice}                invoices.show
GET    /invoices/{invoice}/edit           invoices.edit
PUT    /invoices/{invoice}                invoices.update
POST   /invoices/{invoice}/issue          invoices.issue
POST   /invoices/{invoice}/void           invoices.void
POST   /invoices/{invoice}/duplicate      invoices.duplicate
POST   /invoices/{invoice}/payments       invoices.payments.store
GET    /invoices/{invoice}/pdf            invoices.pdf            (?download=1 or inline)
POST   /invoices/preview                  invoices.preview        (renders unsaved form data as HTML for the iframe)
GET    /invoices/{invoice}/share          invoices.share          (creates signed public link)
GET    /i/{token}                         invoices.public         (public read-only view + PDF download, signed/expiring)

GET    /invoice-items/suggest?q=          invoices.items.suggest  (JSON, debounced)
GET    /customers/suggest?q=              customers.suggest       (JSON)

CRUD   /customers, /catalog, /invoice-templates
GET/PUT /settings/business                business.profile
```

---

## 11. Conventions

- **Thin controllers**, logic in Actions/Services, validation in Form Requests, authorization in Policies (`spatie/laravel-permission`: e.g., `invoices.create`, `invoices.issue`, `invoices.void`, `invoices.manage-catalog`).
- **Server recomputes everything** on save; client totals are for preview only.
- **Money in kobo end-to-end**, formatted only at display via `useNaira`/Blade helper.
- **Preview = PDF source.** One Blade template per layout; never duplicate layout logic in Vue.
- **Issued invoices are never hard-deleted**; void instead. Log every edit, void, and payment to the audit/revision tables.
- **Public share links** use signed, expiring tokens and show only that invoice (no navigation into the admin app).
- **Validation:** quantity > 0 (max 3 decimals), unit price ≥ 0, discount ≤ line amount, due date ≥ issue date, max ~100 line items, Nigerian phone numbers normalized to `+234…` on save.
- **Mobile-friendly form**: the item rows stack neatly on phones; the preview appears in a tab. Keep the JS bundle small (data costs matter).
- **Never trust uploads:** validate image type/size, store logos on the public disk with sanitized names, strip SVG scripts.

---

## 12. Edge Cases & Out of Scope

**Handle:** zero-priced items (e.g., free gift) allowed; very long descriptions (wrap, don't overflow, optionally support a 2-line sub-description); missing logo (show company name only); customer without address; concurrent issuing (no duplicate numbers); invoices spanning multiple A5 pages; deleted/archived catalog items still rendering on old invoices (copy text into `invoice_items`, don't rely on the catalog join).

**Out of scope for now (design hooks only):**
- **Government e-invoicing / tax authority integration.** Nigeria's tax administration and e-invoicing requirements are evolving (including the move toward the Nigeria Revenue Service and its e-invoicing programme). Add **nullable fields** on `invoices` for a government invoice reference number (IRN) and QR payload so integration can be added later, but **verify current requirements with an accountant/tax authority before claiming compliance**. Do not present this tool as tax-authority-certified.
- Credit/debit notes, recurring invoices with auto-send, multi-currency, quotes-to-invoice conversion, online payment links inside the PDF. All reasonable follow-ups; build only after the core is solid.

---

## 13. Testing

**Backend (Pest)**
- `InvoiceCalculator`: all four fixtures from §5 (including inclusive VAT and odd-kobo rounding), per-line vs invoice discounts, mixed VATable/non-VATable lines.
- `NairaInWords`: zero, one, teens, hundreds, thousands, millions, with and without kobo.
- `InvoiceNumberGenerator`: sequential, gap-free, no duplicates under parallel issuing, drafts have no number, voided numbers aren't reused.
- `ItemCatalogService`: normalization/dedupe ("Website Design " == "website design"), ranking by usage, no silent price overwrite, archive behavior, `usage_count` increments, new typed item gets inserted on save and not inserted when "Save to item list" is off.
- Feature tests: create draft, issue, edit issued (creates revision), void (requires reason), record payments (status transitions to partially paid/paid), role permissions (403), tenant isolation (can't view another business's invoices, customers, catalog), public signed link works and expires, tampered token rejected.
- PDF: endpoint returns `application/pdf`; smoke test that the A5 page size is set; snapshot/regression test of the Blade HTML for each layout.
- Overdue command marks the right invoices only.

**Frontend (Vitest)**
- `useInvoiceTotals.ts` matches the PHP fixtures.
- `ItemSuggestInput` debounces and handles selecting vs typing a new item.
- `useNaira` edge cases.

**Manual QA**
- Download the PDF and print it on a real A5 sheet (and on A4 "fit to A5") to check margins.
- Verify ₦ renders, greyscale legibility, and a 25-item invoice paginating correctly.

---

## 14. Build Order

1. Migrations, models, seeders (business profile, default template, seed catalog items)
2. `InvoiceCalculator`, `NairaInWords`, number generator, all with unit tests
3. Business profile + bank accounts + template settings screens
4. Invoice create/edit form with customer picker, item rows, live totals
5. Item suggestion endpoint + auto-save-to-catalog behavior
6. Blade A5 layouts (Classic first) + preview iframe + DomPDF download
7. Issue/void/duplicate/payments + revisions + status handling
8. Modern and Minimal layouts, customization options (colors, fields, watermark)
9. Catalog management (merge/archive/CSV), share links, overdue command
10. Polish: mobile pass, pagination of long invoices, permissions audit, performance

---

## 15. Definition of Done

- [ ] A5 PDF looks clean, prints correctly, shows ₦ properly, and paginates long invoices sensibly
- [ ] Worked example in §5 reproduces exactly in PHP and the TS preview
- [ ] Typing an item suggests from the catalog; a new item is saved and suggested next time; no duplicates; no silent price overwrites
- [ ] Issued invoices get gap-free numbers, are editable with a revision trail, and are voided (not deleted)
- [ ] Logo, accent color, layout, visible fields, bank accounts, and terms are customizable with live preview
- [ ] Preview and PDF are rendered from the same template
- [ ] Permissions, tenant isolation, and public-link security tested
- [ ] All tests in §13 pass
