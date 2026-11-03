# UB Shield

Production website for UB Shield, a cybersecurity and compliance company in Ahmedabad, India.

The approved design is `design/v7/index.html`, a self-contained prototype that is the **source of
truth** for the homepage and is read-only. The build is checked against it with a pixel diff that
currently reports **0 differing pixels at all five breakpoints**.

| | |
|---|---|
| Framework | Laravel 13 (PHP 8.3+) |
| Views | Blade components, server-rendered |
| Assets | Vite, plain modern CSS in partials (no Tailwind), vanilla ES modules |
| Admin | Filament 5 at `/admin` |
| Database | MySQL 8 |

Further reading: [`BUILD.md`](BUILD.md) (the brief), [`CLAUDE.md`](CLAUDE.md) (working rules),
[`docs/inventory.md`](docs/inventory.md) (what the prototype contains),
[`docs/plan.md`](docs/plan.md) (phase plan and status),
[`docs/prototype-fixes.md`](docs/prototype-fixes.md) (approved deviations),
[`docs/diff/README.md`](docs/diff/README.md) (how fidelity is verified).

---

## Setup

Requires PHP 8.3+, Composer, Node 20+, and MySQL 8.

```sh
composer install
npm install
npx playwright install chromium     # only needed to run the visual tests

cp .env.example .env
php artisan key:generate
```

Create the two databases and point `.env` at them:

```sql
CREATE DATABASE ubshield       CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE DATABASE ubshield_test  CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
CREATE USER 'ubshield'@'127.0.0.1' IDENTIFIED BY 'a-strong-password';
GRANT ALL PRIVILEGES ON ubshield.*      TO 'ubshield'@'127.0.0.1';
GRANT ALL PRIVILEGES ON ubshield_test.* TO 'ubshield'@'127.0.0.1';
```

Then:

```sh
php artisan migrate --seed
npm run build
php artisan serve
```

The seeders load the prototype's placeholder content **verbatim** — `[XX]`, `[status]`,
`Client logo` and the rest. Nothing in square brackets is real data; see
[`docs/inventory.md`](docs/inventory.md) §15 for the full list.

### Creating an admin user

There is no public registration. Accounts are created deliberately:

```sh
php artisan make:filament-user
```

Anyone with an account can reach `/admin`. Phase 4 adds 2FA, login throttling and a password policy.

### Running the queue

Lead notifications and auto-replies are **queued**, so a worker must be running or no mail is sent:

```sh
php artisan queue:work
```

In production run it under supervisor or systemd. `MAIL_MAILER=log` in local development writes
messages to `storage/logs/laravel.log` instead of sending them.

---

## Environment variables

Beyond Laravel's defaults:

| Variable | Default | What it does |
|---|---|---|
| `MAIL_ADMIN_ADDRESS` | — | Where new leads are announced. The **Lead notification inbox** setting in the admin overrides it. |
| `REVIEW_MIN_FILL_SECONDS` | `3` | Submissions completed faster than this are rejected as automated. |
| `REVIEW_MAX_FORM_AGE_SECONDS` | `43200` | Rejects a replayed or scraped form older than this. |
| `REVIEW_RATE_LIMIT` | `5` | Submissions allowed per IP per window. |
| `REVIEW_RATE_DECAY_MINUTES` | `60` | The window. |
| `REVIEW_VALIDATE_EMAIL_DNS` | `true` | Live DNS lookup on the submitted email domain. Turn off if your host blocks outbound DNS. |
| `TURNSTILE_ENABLED` | `false` | Optional third spam layer. The honeypot and fill timer work without it. |
| `TURNSTILE_SITE_KEY` / `TURNSTILE_SECRET_KEY` | — | Cloudflare Turnstile credentials. |

---

## Tests

```sh
php artisan test                  # 41 tests — routing, form, spam defences, admin, placeholders

# the visual suite needs the app running (php artisan serve)
npm run visual:diff               # full page at 1440/1180/900/760/390 — must stay at 0 pixels
npm run visual:pinned             # the pinned services track, motion on
npm run visual:behaviour          # 45 interaction checks
npm run visual:side-by-side       # review images into docs/diff/
npm run visual:reference          # re-capture the baseline (only when the prototype changes)
```

`visual:diff` and `visual:pinned` exit non-zero on any difference. **Treat a non-zero diff as a
regression** — the homepage is currently pixel-identical to the prototype and should stay that way.

The PHP suite runs against `ubshield_test` on MySQL rather than SQLite, so migrations and queries
are exercised on the same engine as production.

---

## Deployment

Target is a standard Linux VPS with Nginx and PHP-FPM.

```sh
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Checklist:

- `APP_ENV=production`, `APP_DEBUG=false`, a real `APP_KEY`.
- Point the web root at `public/`.
- A queue worker under supervisor, or lead mail never goes out.
- `.env` is never committed.
- `storage/` and `bootstrap/cache/` writable by the web user.

### Security

All headers are set by the application (`App\Http\Middleware\SecurityHeaders`), including a
nonce-based CSP with no `unsafe-inline` or `unsafe-eval` for scripts. **Do not duplicate them in
Nginx** — two sources for one header is how they drift apart. `deploy/nginx.conf` is responsible
only for TLS, compression and cache lifetimes.

HSTS is sent only over TLS, so it appears automatically once the site is served over https.
Behind a proxy or Cloudflare, make sure `X-Forwarded-Proto` reaches Laravel or it will never
send HSTS and will generate http:// canonicals.

After deploying, re-run **securityheaders.com** and **Mozilla Observatory** — both need a public
URL and cannot be graded locally.

Admin accounts require two-factor authentication: a user without an authenticator is redirected to
setup and reaches no part of the panel.

---

## Project layout

```
app/
  Filament/              admin: Leads (read-only + CSV), Insights, Case studies, Site settings
  Http/Controllers/      HomeController, ReviewController
  Http/Requests/         ReviewRequest — validation and the spam checks
  Mail/                  queued admin notification + visitor auto-reply
  Models/                Lead, Insight, CaseStudy, Setting, User
  Support/               LeadCsv, CoverArt, Turnstile
design/v7/               the approved prototype — READ ONLY, never edit
docs/                    inventory, plan, approved fixes, reference captures, diffs
resources/css/           partials imported in the prototype's cascade order
resources/js/modules/    the prototype's behaviours, one module each
resources/views/         components/{sections,ui,art}, pages/
scripts/                 one-time porting scripts, kept as provenance
tests/visual/            Playwright visual + behaviour harness
```

### A note on the CSS

`resources/css/app.css` imports partials in a fixed order with `responsive.css` and
`v7-overrides.css` **last**, mirroring the prototype's single `<style>` block. The trailing V7
block genuinely overrides earlier rules, so reordering the imports silently changes the design.
The partials were generated from the prototype by `scripts/split-prototype-css.mjs` rather than
retyped — prefer fixing the source of a difference over hand-editing the output.

`form-states.css` is the one stylesheet not from the prototype: the prototype's form has no
validation, so it has no error or success states to copy.
