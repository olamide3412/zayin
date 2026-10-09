# DEPLOYMENT.md

## 1. Hosting Recommendation
- VPS (2 vCPU / 4GB RAM minimum) on Ubuntu LTS: Nginx, PHP 8.3-FPM, MySQL 8 or PostgreSQL,
  Redis, Node 20+ (SSR server), FFmpeg, Supervisor, Certbot.
- Put **Cloudflare** in front (DNS, CDN, WAF, caching) — good performance for Nigerian users.
- Choose a region with good latency to Nigeria (e.g. Europe/Africa regions) and test from
  Lagos/Abuja/Port Harcourt connections.
- Object storage (R2/Spaces/B2/S3) for videos and images, served via CDN.

## 2. Environment Variables (`.env.example`)
```
APP_NAME="{{COMPANY_NAME}}"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://{{DOMAIN}}
APP_TIMEZONE=Africa/Lagos
APP_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=
AWS_ENDPOINT=
AWS_URL=https://media.{{DOMAIN}}

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=hello@{{DOMAIN}}
MAIL_FROM_NAME="${APP_NAME}"

PAYSTACK_PUBLIC_KEY=
PAYSTACK_SECRET_KEY=
SMS_PROVIDER_KEY=
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=
SENTRY_LARAVEL_DSN=
GA4_MEASUREMENT_ID=
INERTIA_SSR_URL=http://127.0.0.1:13714
FFMPEG_BINARIES=/usr/bin/ffmpeg
FFPROBE_BINARIES=/usr/bin/ffprobe
ADMIN_EMAIL=
ADMIN_PASSWORD=
```

## 3. Nginx Essentials
- Redirect HTTP → HTTPS, www ↔ non-www (301), strip trailing slashes (301).
- `client_max_body_size 20M` for normal routes (chunked video uploads stay small per request).
- Brotli/Gzip, HTTP/2, `Cache-Control: public, max-age=31536000, immutable` for `/build/*`.
- Optional `fastcgi_cache` for anonymous GETs (bypass for cart/checkout/account/admin/cookies).
- Security headers set here or in Laravel middleware.

## 4. Supervisor Programs
```
[program:queue-default]  command=php artisan queue:work redis --queue=default,notifications --sleep=3 --tries=3
[program:queue-media]    command=nice php artisan queue:work redis --queue=media --timeout=3600 --tries=2
[program:inertia-ssr]    command=php artisan inertia:start-ssr
```
Scheduler cron: `* * * * * php /var/www/app/artisan schedule:run`
Schedule: sitemap nightly, `backup:run` daily, `backup:clean`, expire abandoned carts/orders,
prune failed jobs, `queue:prune-batches`.

## 5. Deploy Script (zero-downtime preferred: Deployer/Envoyer/GitHub Actions)
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build            # includes SSR build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache
php artisan storage:link
php artisan queue:restart
php artisan inertia:stop-ssr && sudo supervisorctl restart inertia-ssr
php artisan sitemap:generate
```
Post-deploy smoke: `curl -I` home, view-source check for `<title>`, test checkout in Paystack
test mode on staging before going live.

## 6. Environments
| Env | Purpose | Notes |
|---|---|---|
| local | dev | demo seeder, Mailpit, Paystack test keys |
| staging | QA/client review | HTTP auth + `noindex` + `Disallow: /` |
| production | live | indexable, live keys, backups on |

## 7. Backups & Recovery
Daily DB + media backup to offsite bucket (encrypted), 30-day retention, monthly restore test.
Document RTO/RPO (target: RPO 24h, RTO 4h).

## 8. Monitoring
Sentry (errors), uptime monitor (home, checkout, SSR health endpoint), disk/CPU alerts,
queue length alerts, Search Console coverage alerts, log rotation.

## 9. Go-Live Checklist
- [ ] Domain + SSL + Cloudflare configured; canonical host set
- [ ] Production `.env` reviewed, `APP_DEBUG=false`
- [ ] Paystack live keys + webhook URL registered and tested
- [ ] Mail/SMS deliverability (SPF, DKIM, DMARC)
- [ ] Real content, prices, legal pages, contact details in place
- [ ] robots/sitemap correct; Search Console & GA4 verified
- [ ] Backups running; monitoring alerts tested
- [ ] Admin accounts with 2FA; default credentials removed
- [ ] SEO pre-launch checklist (docs/SEO.md §12) complete
