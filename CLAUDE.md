# UB Shield website

Production website for UB Shield, a cybersecurity and compliance company in Ahmedabad, India.

**Read `BUILD.md` first — it is the authoritative brief.** Then `design/v7/README.md` (the design
handoff), `docs/inventory.md` (what the prototype actually contains) and `docs/plan.md`.

## The one rule

`design/v7/index.html` is the **source of truth** and is **read-only**. The job is to make it
pixel-faithful and production-grade, not to redesign it. Do not restyle, re-space, re-colour or
simplify anything visual. Port the prototype's CSS values and JS behaviour exactly rather than
writing new versions from memory. If something is broken or impossible as drawn, stop and ask —
every approved deviation is logged in `docs/prototype-fixes.md`.

## Hard rules

- **Colours:** only `#061522`, `#0C2944`, `#1479D4`, `#27B8ED`, `#F4F6F8`, `#C7D0D8` and the
  `--line` hairline. No new colours.
- **Shapes:** square corners everywhere. Circles only for arrow buttons, the header action and the
  title dot. No shadows or gradients outside the artwork.
- **Fonts:** only the four V7 families, self-hosted from `public/fonts/`. No Inter, Roboto, Poppins
  or visible system-ui fallback on the public site. (Filament ships its own Inter for `/admin`;
  that is fine and does not apply to the site.)
- **No content invention.** Every `[placeholder]` stays exactly as drawn — no invented client
  names, statistics, certifications, awards, testimonials, addresses or phone numbers.
- No stock imagery, no emoji, no new icon libraries — reuse the prototype's SVG arrows and glyphs.
- Never commit `.env`.

## This machine

PHP 8.2 (XAMPP) is first on `PATH`, but this project needs **PHP 8.4**. Use the shims:

```sh
bin/php.cmd       # PHP 8.4.27 NTS
bin/composer.cmd  # Composer running on 8.4
```

Prepend `bin/` to `PATH` for a session, or call the shims directly. Note that `.cmd` shims pass
arguments through `cmd.exe`, which **eats `^`** — for Composer constraints like `^5.0`, call
`php.exe` and `composer.phar` directly instead.

MySQL 8.4 runs as a plain process, not a service (registering one needs an elevated shell).
Start it with `scripts/mysql-start.ps1` after a reboot.

Composer downloads must run with `COMPOSER_MAX_PARALLEL_HTTP=1` on this machine — Windows Defender
real-time scanning races Composer's parallel temp zips and causes spurious "Permission denied"
failures.

## Stack

Laravel 13 · Blade components (server-rendered) · Vite with plain modern CSS in partials (no
Tailwind, so the prototype's values stay exact) · vanilla ES module JS · Filament 5 at `/admin` ·
MySQL 8.4.

## Layout

```
design/v7/                 read-only reference — never edit
docs/inventory.md          every section, token, keyframe, JS behaviour and SVG def
docs/plan.md               phase-by-phase build plan
docs/prototype-fixes.md    approved deviations from the prototype
docs/reference/            prototype screenshots at 1440/1180/900/760/390 (motion off)
docs/diff/                 visual diffs + how to reproduce them (docs/diff/README.md)
scripts/                   one-time porting scripts, kept as provenance
tests/visual/              Playwright visual + behaviour regression harness
```

## Verifying a change

```sh
php artisan serve                 # the harness needs the app running
npm run visual:diff               # must stay at 0 differing pixels, all five widths
npm run visual:pinned             # the pinned services track, motion on
npm run visual:behaviour          # 35 interaction checks
php artisan test
```

The homepage is currently **pixel-identical** to the prototype at all five widths. Treat any
non-zero `visual:diff` as a regression and find the cause before moving on. The CSS and markup
were generated from the prototype by `scripts/split-prototype-*.mjs`, so prefer fixing the source
of a difference over hand-editing the output.

## CSS cascade

`resources/css/app.css` imports partials in a fixed order, with `responsive.css` and
`v7-overrides.css` **last**. The prototype's trailing V7 block genuinely overrides earlier rules,
so folding it into the section partials would silently change the design. See `docs/inventory.md`
§9 before touching the import order.
