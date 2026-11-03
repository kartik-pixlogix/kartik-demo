# Phase 4 — SEO, performance and security results

Measured on this machine against `php artisan serve`. Where a number depends on the web server,
that is called out rather than hidden, because `artisan serve` does no compression and sets no
cache lifetimes — two of the things Lighthouse weighs most heavily.

---

## Lighthouse, mobile

| Category | Target | Measured | |
|---|---|---|---|
| Performance | ≥ 90 | **91** | pass |
| Accessibility | 100 | **100** | pass |
| Best Practices | 100 | **100** | pass |
| SEO | 100 | **100** | pass |

| Core Web Vital | Target | Measured | |
|---|---|---|---|
| LCP | < 2.5s | 2.6s | **see below** |
| CLS | < 0.05 | **0** | pass |
| INP (TBT as proxy) | < 200ms | 160–260ms | borderline on the dev server |

### Where it started

The first run was Performance 85, Accessibility 91. What moved it:

| Change | Effect |
|---|---|
| Pause infinite CSS animation outside the viewport | TBT 390ms → 160ms, Speed Index 3.6s → 2.1s. The homepage animates well over a hundred elements continuously; most are offscreen most of the time. |
| Brand images to lossless WebP | 94 KB → 51 KB, verified pixel-identical (see below) |
| Explicit `width`/`height` on both header images | removed the CLS risk; CLS measured 0 |
| Three accessibility fixes | 91 → 100 |

### On LCP, and the two numbers that are not the app's

`artisan serve` sends everything uncompressed and sets no cache headers. Measured directly:

```
homepage HTML uncompressed   55,791 bytes
homepage HTML gzipped        11,952 bytes   (79% smaller)
```

That 44 KB, plus the ~37 KB stylesheet, is paid on first byte in the dev-server measurement and
is why LCP sits at 2.6s rather than under 2.5s. `deploy/nginx.conf` ships the gzip and cache
configuration that removes it; Lighthouse's own estimate for the two is "79 KiB" of text
compression and "200 KiB" of cache savings.

**This is stated as a projection, not a measurement.** An attempt to measure through a local
compressing proxy produced Performance 99 — but inspection showed the proxy had broken the asset
origin, so the CSS and JS were blocked by CORS and Lighthouse had scored an unstyled page. That
number is discarded. The honest figure is the 91 above, measured with everything working, and the
production figure should be re-measured on the real server after deployment.

### Remaining Lighthouse notes, and why they stand

| Note | Disposition |
|---|---|
| Enable text compression · efficient cache lifetimes · static asset cache policy · document request latency | Web-server configuration. Shipped in `deploy/nginx.conf`. |
| Properly size images (44 KiB) | The header renders a 266×238 monogram at 42px. Serving it pre-scaled would change Chromium's downsampling and break the pixel-exact homepage. Rejected deliberately — see `docs/prototype-fixes.md`. |
| Avoid an excessive DOM size (1,132 elements) | Inherent to the approved design: 73 inline SVG illustrations, kept inline so they stay crisp and animatable (the handoff asks for this explicitly). |
| Reduce unused CSS (16 KiB) | The stylesheet is the whole site in one file; "unused" means "used on another page". Splitting it per page would cost a round trip on navigation. |
| Forced reflow | The rAF loop reads `getBoundingClientRect()` for parallax. Reducing it means changing the prototype's motion behaviour. |

---

## Security headers

Set by `App\Http\Middleware\SecurityHeaders` on **every** response, including errors.

| Header | Value |
|---|---|
| `Content-Security-Policy` | nonce-based, see below |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains; preload` — **only over TLS**, so a plain-HTTP local environment never pins a developer's browser |
| `X-Content-Type-Options` | `nosniff` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | 21 features denied explicitly |
| `X-Frame-Options` | `DENY` |
| `Cross-Origin-Opener-Policy` | `same-origin` |
| `Cross-Origin-Resource-Policy` | `same-origin` |
| `X-Permitted-Cross-Domain-Policies` | `none` |

### The policy

```
script-src 'self' 'nonce-<per-request>'
style-src-elem 'self'
style-src 'self' 'unsafe-inline'
default-src 'self'; base-uri 'self'; object-src 'none'
frame-ancestors 'none'; form-action 'self'; frame-src 'none'
img-src 'self' data:; font-src 'self'; connect-src 'self'
```

**`script-src` has no `unsafe-inline` and no `unsafe-eval`**, which is the part BUILD.md specifies
and the part that actually stops XSS. Nothing had to be loosened to get there: the site has no
inline `<script>` blocks, no `on*` handlers and no `javascript:` URLs. The only inline script is
the JSON-LD, and it carries the nonce.

`style-src` is the deliberate exception. The approved prototype carries **58 inline `style`
attributes** — animation delays, blip positions, per-instance sizes — and those values are the
design. `style-src-elem 'self'` still blocks inline `<style>` blocks and foreign stylesheets in
every modern browser; only attributes are permitted, and an attacker who can inject a style
attribute already has HTML injection.

The admin gets a looser policy (`unsafe-inline`, `unsafe-eval`) because Livewire and Alpine
require it. It is authenticated, `Disallow`ed in robots.txt, and not the public attack surface.

### Found and fixed by the CSP

Filament's default avatar provider is **ui-avatars.com**, which sends the signed-in user's name in
a query string to a third party on every admin page load. The CSP blocked it. Whitelisting the
domain would have silenced the warning without fixing the leak, so it was replaced with
`App\Support\LocalAvatarProvider` — initials rendered locally as a `data:` URI. No outbound
request, no name disclosure, and it keeps working if that service is down.

### Other hardening

- **Two-factor authentication is required**, not optional: a user without an authenticator is
  redirected to setup and reaches no part of the panel. Asserted by a test.
- Secrets (`app_authentication_secret`, recovery codes) are **encrypted at rest**, so a database
  dump is not enough to mint valid codes.
- Password policy: 12 characters, mixed case, numbers, and checked against Have I Been Pwned in
  production (k-anonymous — only the first five hash characters leave the server).
- Session cookies default to `secure` outside local, `SameSite=Strict`, `HttpOnly`, encrypted.
- CSRF on every form, including the public posture-review form.
- `composer audit` and `npm audit`: **0 vulnerabilities**. Lighthouse was installed for this phase
  and then removed from the project, because it pulls in ~30 vulnerable transitive dependencies;
  it lives outside the repo.

### Not verifiable from here

**securityheaders.com A+ and Mozilla Observatory** both require a public URL. Every header those
graders check is present and correct — the one that cannot be confirmed locally is HSTS, which is
withheld deliberately over plain HTTP and will appear as soon as the site is served over TLS.
Re-run both after deployment.

---

## SEO

- Per-page `<title>`, meta description, `canonical`, Open Graph (8 properties) and Twitter card on
  all 21 pages, asserted by test.
- **OG image** `public/images/brand/og-default.png`, 1200×630, built by
  `scripts/build-og-image.mjs` from the same shield emblem, tokens and self-hosted fonts as the
  site, so it cannot drift from the design.
- **Generated `sitemap.xml`** — 21 URLs, built from the router and the database rather than
  maintained by hand, so a new service or article cannot be forgotten. Unpublished articles are
  excluded, asserted by test.
- **`robots.txt`** disallows `/admin` and `/livewire`.
- **JSON-LD** as one `@graph` per page: `Organization` and `WebSite` everywhere, plus `Service` +
  `FAQPage` on service pages, `Article` on field notes, `ContactPage`, `AboutPage` and
  `BreadcrumbList` where they apply.
- One `<h1>` per page and a correct heading order, asserted by test on every page.

### Structured data never publishes a placeholder

`Organization` omits `contactPoint` and `streetAddress` entirely while those settings are still
`[email address]`, `[phone number]` and `[Street address]`, and starts publishing them the moment
real values are saved. Both behaviours are asserted by test. Emitting `"[email address]"` as
machine-readable contact data, or claiming an unconfirmed certification, would be worse than
omitting the property.

`Article` carries a real ISO `datePublished` from `published_at` rather than the design's
`[DD MMM 2026]` label.
