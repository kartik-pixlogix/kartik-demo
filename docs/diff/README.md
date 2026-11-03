# Phase 1 visual diff — Laravel build vs the V7 prototype

All figures produced on Chromium at `deviceScaleFactor: 1`. To reproduce, start the app
(`php artisan serve`) and then:

```sh
npm run visual:diff           # full page, five widths  -> §1
npm run visual:pinned         # the pinned services track -> §2
npm run visual:behaviour      # the 35 interaction checks -> §3
npm run visual:side-by-side   # the review images
npm run visual:reference      # re-capture docs/reference/ (only when the prototype changes)
```

`visual:diff` and `visual:pinned` exit non-zero on any difference, so they can gate a commit.
The harness materialises its own copies of the prototype under `storage/app/prototype/`
(gitignored) — `design/v7/` is never written to.

## 1. Full page, five widths, resting state

The build is compared against `docs/reference/proto-*.png`, which were captured from the prototype
with its own `motion = false` flag. To reproduce exactly those conditions, the harness overrides
`matchMedia('(prefers-reduced-motion: reduce)')` to return `true` — so `app.js` computes
`motion = false` and skips the `mo` / `rvon` / `pinon` / `has-cur` classes — **without** enabling
Playwright's reduced-motion emulation, which would additionally fire the
`@media (prefers-reduced-motion: reduce)` block that the references never saw.

`Date` is frozen with `page.clock.setFixedTime('2026-01-01T12:34:56Z')` in both captures, so the
live IST clock in the SOC section renders the same string on both sides. `setFixedTime` fakes
`Date` only and leaves timers and `requestAnimationFrame` running normally.

| Width | Page height (ref = build) | Pixels differing |
|---|---|---|
| 1440 | 11078 | **0** (0.0000%) |
| 1180 | 13356 | **0** (0.0000%) |
| 900 | 13350 | **0** (0.0000%) |
| 760 | 18037 | **0** (0.0000%) |
| 390 | 16262 | **0** (0.0000%) |

Files: `diff-<width>.png` (pixelmatch output, entirely black at 0 differences),
`build/build-<width>.png` (the build's capture), `side-by-side-1440.png` and
`side-by-side-390.png` (review images).

> Before the clock was frozen the only difference at any width was `span.clock` — 302 to 666 pixels
> in a single ~100×27px band, because the reference had been captured at a different time of day.
> Nothing else differed, at any width, at any point in Phase 1.

## 2. Pinned horizontal services, motion on

The resting-state references cannot cover this section: `motion = false` also disables the pin, so
the references show the services cards as a 3-column grid. This second pass compares the build
against a motion-on copy of the prototype at five points of pin progress.

Scroll reveals are forced to `.in` on both sides first, so the comparison is about layout rather
than where each reveal happens to be in its 1.1s transition.

Pin geometry is identical: section top 1487px, travel **1236px**, `pinon` active on both.

| Progress | Counter (proto/build) | Track transform | Pixels differing |
|---|---|---|---|
| 0.00 | 01 / 01 | `translate3d(0px, 0px, 0px)` | 0 |
| 0.25 | 02 / 02 | `translate3d(-336.6px, 0px, 0px)` | 0 |
| 0.50 | 03 / 03 | `translate3d(-645.6px, 0px, 0px)` | 0 |
| 0.75 | 04 / 04 | `translate3d(-954.6px, 0px, 0px)` | 0 |
| 1.00 | 05 / 05 | `translate3d(-1236px, 0px, 0px)` | 0 |

Files: `pinned/`.

> One caveat on this pass: the very first run after the harness was moved into the repo reported 7
> pixels at the three fractional stops, and three consecutive runs since have reported 0. Treat a
> handful of pixels at `-336.6px` / `-645.6px` / `-954.6px` as cold-start rasterisation noise at
> sub-pixel transform offsets, not as a regression — but re-run before concluding anything from it.

> With the artwork animations left running (`pinned/`) the same frames differ by 2,109 to 11,570
> pixels. That is animation **phase**, not layout: `.flow`, `.pl2`, `.hunt`, `.spin`, `.tick` and
> `.redact` are infinite, time-based, and start at different moments in the two pages. Injecting
> `*{animation:none !important}` into both takes every frame to exactly 0, which is what
> `pinned-frozen/` records.

## 3. Behaviour

A separate Playwright suite exercises every behaviour listed in BUILD.md Phase 1.4–1.5 — load
classes, reveal arming, count-ups, header compact/hide/progress, all three parallax kinds, the
pinned track and its counter and progress bar, the grid fallback below 1024px, the sticky process
counter, the IST clock, the marquees, the industries hover preview, all four cursor modes, magnetic
buttons, the mobile menu (open, `aria-expanded`, focus in, focus trapped, Escape, focus restored)
and reduced motion. **35 of 35 pass**, with no page errors.

One defect was found and fixed by that suite: the mobile-menu focus trap leaked, because the toggle
button precedes the menu in document order and the focusable list had not been sorted, so the
wrap-around compared against the wrong element.
