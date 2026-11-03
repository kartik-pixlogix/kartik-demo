# A staging URL for the team

You need a link that works when your machine is off. That means hosting it somewhere.

## Use Laravel Cloud

[cloud.laravel.com](https://cloud.laravel.com) — it is Laravel's own host, and for this app it is
the only option that needs **no changes to the repo at all**.

That matters more than it sounds. Everywhere else, three things have to be engineered around:

| | Laravel Cloud | Vercel | Most shared hosting |
|---|---|---|---|
| **PHP 8.4** — required, the locked Symfony 8 packages need `>= 8.4.1` | yes, selectable | pinned community runtime | often stuck on 8.2/8.3 |
| **File uploads** — client logos, cover images | local disk, works | needs an S3 bucket | works |
| **Queue worker** — the lead notification emails | real worker | no long-running processes | usually no worker |
| **MySQL 8** | managed, built in | external service | usually included |

It also scales to zero when nobody is looking at it, which is what a review site does most of the
time.

### Steps

1. Connect the GitHub repo at [cloud.laravel.com](https://cloud.laravel.com).
2. Add a **MySQL** database from its dashboard. The connection variables are injected for you.
3. In **General Settings → Runtime**, set PHP to **8.4**.

   Do not skip this. New environments default to 8.5, and this project is developed and tested
   on 8.4.27 — 8.4 is the version the lock file is resolved against. (8.3 would fail outright:
   the Symfony 8 components require `>= 8.4.1`.)
4. Set three environment variables:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=<paste the output of: php artisan key:generate --show>
   ```
5. Check the **deploy command** includes the asset build and the storage link. Neither
   `public/build` nor `public/storage` is in the repo, so without these the site deploys
   unstyled and with broken images:
   ```sh
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan storage:link
   php artisan migrate --force
   ```
6. Then, from its console, seed the content:
   ```sh
   php artisan db:seed --class=SettingSeeder --force
   php artisan make:filament-user
   ```

   **On a review site, also seed the placeholder content** so the team sees the design carrying
   artwork rather than dashed boxes:
   ```sh
   php artisan db:seed --class=ClientSeeder --force
   php artisan db:seed --class=CoverImageSeeder --force
   ```
   Both are flagged in the admin as placeholders. Do **not** run them on the real production
   site — see `docs/placeholders.md` §7.
7. Share the `*.laravel.cloud` URL it gives you.

> Run **only** `SettingSeeder`. A bare `db:seed` also seeds the placeholder client logos and
> sample cover images — see `docs/placeholders.md` §7.

### Cost

No monthly fee on the Starter plan — usage only, and a new organisation gets a small credit to
start. An idle review site costs very little because it sleeps. Check the current
[pricing page](https://cloud.laravel.com) rather than trusting this paragraph; the terms move.

---

## If you would rather not use Laravel Cloud

- **Railway** — [railway.app](https://railway.app). Managed MySQL, deploys from the repo, gives a
  `*.up.railway.app` URL. Closest equivalent. Pin PHP 8.4 explicitly; its auto-detection reads
  `composer.json`, which says `^8.3`, and 8.3 will not install this dependency tree.
- **A small VPS** — Hetzner or DigitalOcean, a few euros a month. More setup, but `deploy/nginx.conf`
  is already in this repo for exactly this, nothing sleeps, and it is the same box you can later
  point the real domain at.
- **Render** — has a genuinely free tier, but free services sleep after ~15 minutes and take
  30–60 seconds to wake. A client clicking your link and waiting a minute on a blank page is a bad
  first impression. It also has no managed MySQL, so you would need an external database anyway.
- **Vercel** — `docs/deploy-vercel.md`. Works, but it is the most setup of any option here.

---

## Before you send the link

- **`APP_DEBUG=false`.** With it on, any error page prints your database password and `APP_KEY`
  to whoever opens the link.
- **Say it is a preview.** The site still carries placeholders — `[Street address]`, the
  certification statuses, the `[XX]` figures, six placeholder client logos. `docs/placeholders.md`
  is the full list, and the admin dashboard counts what is outstanding on its first screen so
  reviewers can see it themselves rather than reporting it as a bug.
- **`/admin` needs an authenticator app.** Two-factor is mandatory, so whoever signs in first has
  to set it up.

---

## Appendix: a throwaway URL in one minute

For "look at this right now" on a call, `scripts/share-preview.ps1` opens a Cloudflare tunnel to
your local server and prints a public HTTPS address. It turns `APP_DEBUG` off for the life of the
tunnel and restores your `.env` on Ctrl-C.

It only works while your machine is awake and the window is open, and the address changes every
run — which is why it is an appendix and not the recommendation.
