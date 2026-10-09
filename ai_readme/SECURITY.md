# SECURITY.md

## 1. Application Security
- Laravel defaults on: CSRF, XSS escaping, prepared statements, hashed passwords (bcrypt/argon2).
- Validation in Form Requests; `$fillable` explicit; no `unguard`.
- Authorisation via Policies + role middleware; admin routes under `/admin` with `role:` checks.
- **Admin 2FA** (TOTP) required for Super Admin/Admin; login throttling; session regeneration;
  secure, httpOnly, sameSite cookies; short admin session lifetime.
- Rate limits: login, register, contact, quote, booking, checkout, uploads, webhooks.
- Bot protection: honeypot field + Cloudflare Turnstile (or reCAPTCHA) on public forms.
- Security headers: HSTS, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`,
  `X-Frame-Options`/`frame-ancestors`, **CSP** (allow self, Paystack, analytics, YouTube facade).
- Sanitize rich text (HTMLPurifier/`mews/purifier`) before storing/rendering.
- Uploads: validate MIME + extension + size, random filenames, store outside web root or on
  object storage, never execute; optional ClamAV scan for proofs/attachments.
- Secrets only in `.env`; rotate on staff change; separate test/live keys.
- Webhooks: verify signatures; allow-list not required but log source IP.
- Dependency hygiene: `composer audit`, `npm audit`, Dependabot/Renovate weekly.
- Admin and payment endpoints: no caching; `noindex`.
- Logging: no passwords, tokens, full card/bank details, or raw personal data.

## 2. Infrastructure
Firewall (80/443/SSH only), SSH keys, fail2ban, unattended security updates, non-root deploy
user, DB not publicly reachable, Redis bound to localhost/private, Cloudflare WAF + rate
limiting, daily encrypted offsite backups (`spatie/laravel-backup`), tested restores.

## 3. Privacy & NDPA 2023 (Nigeria Data Protection Act)
Obtain legal review before launch. Implementation baseline:
- **Privacy Policy** page: what data is collected, purposes, lawful basis, retention, sharing
  (Paystack, email/SMS providers, analytics), user rights, contact/DPO details.
- **Consent:** cookie/analytics banner (granular: essential vs analytics/marketing); analytics
  and pixels load only after consent. Marketing emails opt-in with unsubscribe.
- **Data minimisation:** collect only what is needed (e.g. no card data ever touches our server).
- **Data subject rights:** process to access, correct, delete, export personal data (admin tool
  + documented SLA).
- **Retention:** define periods (e.g. leads 24 months, orders per tax/accounting rules); scheduled
  anonymisation/deletion jobs.
- **Breach response:** documented plan, log, and notification procedure.
- **Third parties:** list processors; use data-processing agreements where applicable.
- **Customer media:** record consent before publishing videos/photos of customers' premises or people.
- Check whether registration/filing with the Nigeria Data Protection Commission applies to
  the business and keep records up to date.

## 4. Payment Security
Use hosted Paystack checkout/popup (PCI scope stays with Paystack). Never store card numbers,
CVV or PINs. Verify every payment server-side. Store only reference, status, amount, channel.

## 5. Roles & Permissions Matrix (baseline)
| Capability | Super Admin | Admin | Content Mgr | Sales | Technician |
|---|---|---|---|---|---|
| Settings/Users/Roles | ✔ | – | – | – | – |
| Products/Services | ✔ | ✔ | ✔ | view | view |
| Projects/Videos/Blog/FAQ/SEO | ✔ | ✔ | ✔ | – | upload own |
| Orders/Payments | ✔ | ✔ | – | ✔ | view assigned |
| Quotes/Bookings/Leads | ✔ | ✔ | – | ✔ | view assigned |
| Redirects/Activity log | ✔ | ✔ | – | – | – |
