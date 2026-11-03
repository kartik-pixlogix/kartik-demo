# Deploying to Vercel

`vercel.json`, `api/index.php` and the `/tmp` handling in `bootstrap/app.php` are already in the
repo. What is left is signing up for three services and pasting environment variables.

**Before you start, get three things:**

| | Why |
|---|---|
| Managed **MySQL 8** — PlanetScale, Aiven, Railway, RDS | Vercel has no database. Not Vercel Postgres: the migrations and test suite target MySQL. |
| **S3-compatible bucket** — Cloudflare R2, AWS S3, Backblaze B2 | Vercel wipes its disk between requests. Without this, every client logo and cover image uploaded in the admin disappears. |
| **SMTP** — Postmark, Resend, SES | For the lead notification emails. |

---

## 1. Add the S3 adapter

```sh
composer require league/flysystem-aws-s3-v3 "^3.0"
git add composer.json composer.lock && git commit -m "Add the S3 filesystem adapter"
```

No code changes — the admin already uploads to `Storage::disk('public')`, and the disk is chosen
by the `FILESYSTEM_DISK` variable in step 3.

## 2. Import the repo

In Vercel: **Add New → Project → Import** your Git repository.

Set **Build Command** to:

```sh
composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Leave **Output Directory** empty. Everything else can stay on its default.

## 3. Paste the environment variables

Copy `.env.vercel.example` into **Settings → Environment Variables** and fill in every `<...>`.

Get the app key with:

```sh
php artisan key:generate --show
```

## 4. Deploy, then set up the database

Deploy. Then, from your machine with the production database credentials in `.env`:

```sh
php artisan migrate --force
php artisan db:seed --class=SettingSeeder --force
php artisan make:filament-user
```

Run migrations yourself rather than from the build — Vercel builds run in parallel and can race.

> **Only `SettingSeeder`.** Plain `db:seed` also runs `ClientSeeder` and `CoverImageSeeder`, which
> seed the placeholder client logos and sample covers from `docs/placeholders.md` §7.
> `SettingSeeder` uses `firstOrCreate`, so re-running it never overwrites anything the client has
> edited.

Two-factor is mandatory, so the new admin account goes to authenticator setup on first sign-in.

## 5. Check four things

1. **`/admin` is styled.** Unstyled means the `/css` and `/js` routes in `vercel.json` are wrong.
2. **Upload a client logo, then redeploy, then reload.** If it is still there, S3 is wired up
   correctly. If it vanished, `FILESYSTEM_DISK` is not `s3`.
3. **Submit the posture-review form** and confirm the mail arrives.
4. **Run securityheaders.com and Mozilla Observatory.** These could not be checked locally. Every
   header is already set in `App\Http\Middleware\SecurityHeaders`; HSTS appears automatically now
   the site is HTTPS.

---

## Things worth knowing

**Email sends inside the request.** Vercel cannot run `queue:work`, so `QUEUE_CONNECTION=sync`
sends the two lead mails during the form submission — a second or two, well inside the 30s limit.
If you would rather queue them, set `QUEUE_CONNECTION=database`, add a route that calls
`queue:work --stop-when-empty --max-time=25`, and point a Vercel Cron at it every five minutes.

Either way the admin dashboard counts leads with no `notified_at`, so a broken mail path shows up
on the first screen instead of in a complaint.

**PHP 8.4 is required, and the version is pinned.** The locked Symfony 8 components need
`php >= 8.4.1`, so the widely-cited `vercel-php@0.7.x` (PHP 8.3) will not install this project at
all. `vercel.json` pins `vercel-php@0.8.0`. Do not float it. It is a
[community runtime](https://github.com/vercel-community/php), not a Vercel product.

**Sample content is not in the repo.** `storage/app/public` is gitignored, so a fresh deploy has
no client logos or cover images — and that degrades correctly: the trust strip falls back to the
design's dashed "Client logo" boxes and each card to its hand-drawn SVG. To put the samples live,
run `node scripts/build-sample-client-logos.mjs` and `node scripts/build-sample-covers.mjs`, then
upload `storage/app/public/` to the bucket.

**Test before you deploy, not after.** `npm run visual:diff` renders with `?design-preview=1`,
which the middleware ignores outside local and testing. Run the suite against a local server:

```sh
php artisan serve
php artisan test
npm run qa
```

## If Vercel turns out to be more trouble than it is worth

This repo ships `deploy/nginx.conf` because a conventional PHP host was the original target. On
Laravel Cloud, Forge, Ploi, Railway or a plain VPS you skip steps 1 and 3 almost entirely: uploads
work on local disk, `queue:work` runs as a real worker, and the gzip and caching rules in that
file are the ones the Phase 4 performance numbers were measured against.

## What I could not verify

I have not watched this deploy succeed. The runtime version and the `vercel.json` routing come
from the community runtime's documentation. Static asset routing is where these setups usually
fail first, and the symptom is an unstyled page rather than an error — if that happens, step 5.1
is the thing to look at.
