# Phase 5 — QA results

Everything below is reproducible: `npm run qa` runs the suites, and each number says how it was
measured. Where a result depends on the machine or the web server, that is stated rather than
hidden.

---

## 1. Web Interface Guidelines review

Reviewed every Blade view against the Vercel Web Interface Guidelines. Findings and what was done:

### Fixed

| Finding | Fix |
|---|---|
| Decorative arrow glyphs were exposed to assistive tech (44 SVGs on the homepage) | `aria-hidden="true"` on the arrow-circle glyphs and the header action |
| In-page anchors landed under the sticky header | `scroll-margin-top: 120px` (100px on mobile) on every `[id]` |
| 300ms double-tap delay on touch | `touch-action: manipulation` on interactive elements |
| Default grey tap flash fought the cyan fills | `-webkit-tap-highlight-color` set to a cyan tint |
| Mobile menu scroll chained through to the page, and ignored notches | `overscroll-behavior: contain` plus `env(safe-area-inset-*)` padding |
| `<select>` options rendered dark-on-dark in Windows dark mode | explicit `background-color` / `color` on `option` (popup only — the closed control is untouched) |
| Email field invited spellcheck and autocapitalisation | `inputmode="email" spellcheck="false" autocapitalize="off"` |
| Scrollbars and native chrome rendered light on a dark page | `<meta name="color-scheme" content="dark">` |
| IST clock hard-coded a +5:30 offset | `Intl.DateTimeFormat` with `timeZone: 'Asia/Kolkata'`, so it reads the browser's tz database. Identical output |
| Footer and breadcrumb links were 20px tall | hit area grown to 28px with an absolutely positioned `::after` — no layout change |
| Dead `welcome.blade.php` from the Laravel scaffold | deleted |

### Considered and rejected, with the reason

| Finding | Why not |
|---|---|
| `env(safe-area-inset-*)` on `.wrap` | `.wrap` is overridden by sections with their own shorthand — `.trust-in` uses `padding:24px 0` to sit flush. A longhand `.wrap` rule loaded last silently wins and shifts the row; **measured at 7,917 differing pixels.** Landscape notch handling for the full-bleed sections belongs in the section partials, which means changing the prototype's own values. |
| Expanding the hit area on the closing-strip contact links | Making those links positioned repaints them into their own box and shifts text antialiasing — **measured at 313 differing pixels.** They already clear WCAG 2.5.8 on the spacing exception. |
| Placeholders ending in `…`, curly quotes, `text-wrap: balance` | All change rendered text or line breaking in the approved design. |
| `transition` on properties other than transform/opacity | The prototype animates `clip-path`, `stroke-dashoffset`, `background-size` and `height` by design. |

---

## 2. Accessibility

### Lighthouse (axe) — mobile

**Accessibility 100 on every page type**: homepage, service, industry, insights index, article,
contact, about, legal.

### Automated suite — `tests/visual/qa.mjs`

108 checks per engine, on eight page types. **Chromium 108/108 · Firefox 108/108 · WebKit 107/107.**

Covers, per page: every SVG hidden or labelled · images have `alt` and dimensions · buttons have an
accessible name · form fields have labels · exactly one `h1` · heading order never skips a level ·
`main`/`header`/`footer` landmarks · every `nav` labelled · skip link present · marquees have a
static equivalent · the live SOC feed is not announced · all text meets WCAG AA contrast ·
no horizontal overflow at 390 and 320px · tap targets meet WCAG 2.5.8.

### Keyboard

- First Tab reaches the skip link, which moves on-screen when focused.
- **Every one of the 53 focusable elements on the homepage has a visible focus indicator.** The
  form fields swap the outline for a 4px underline plus an 8px inset, which is the prototype's own
  treatment.
- The services dropdown opens on focus, not only on hover (`:focus-within`).
- Industry rows show the cyan fill on focus, not only on hover.
- Mobile menu: opens, moves focus inside, traps Tab and Shift+Tab, closes on Escape, returns focus
  to the toggle.
- The pinned services section is scroll-driven, so it works with keyboard scrolling; it is not a
  drag-only interaction.

> On WebKit the first Tab does **not** reach the skip link. That is Safari's default — Tab visits
> only form controls unless "Press Tab to highlight each item on a webpage" / Full Keyboard Access
> is on, which any keyboard-only user has. Verified: WebKit's tab order is input, input, input,
> select, button. A platform setting, not a site defect; the suite skips the ordering assertion on
> WebKit and still asserts the skip link works.

### Screen-reader semantics

Checked by assertion rather than by ear:

- **Decorative SVGs**: all 73 on the homepage are either `aria-hidden`, inside an `aria-hidden`
  parent, or carry `role="img"` with a label.
- **Marquees**: the client-logo strip duplicates 8 boxes and marks the duplicates `aria-hidden`;
  the framework marquees carry `role="img"` with the full list as `aria-label`.
- **The live SOC feed is `aria-hidden="true"`** — stronger than the `aria-live="off"` the brief
  asks for, since it is removed from the accessibility tree entirely rather than merely not
  announced.
- Landmarks, labelled navs, one `h1` per page, and a heading order that never skips.

### Contrast

Every text node on all eight page types was sampled and composited against its painted background.
**No failures.**

Three categories the sampler cannot model are excluded and were checked by eye and by axe instead:
text painted by a stroke (`.mq .o`) or a clipped gradient (`.fwm b`), and text over SVG artwork
(the card numbers). The brand rules hold: Bright Blue is never body text on navy, and text on cyan
is navy.

Two contrast failures found in Phase 4 were fixed by changing prototype values — `--faint` and the
word-fade reveal. Both are logged in `docs/prototype-fixes.md` as F2 and F4 and revert in one line
each.

---

## 3. Cross-browser

`tests/visual/cross-browser.mjs` — three engines × three page types × five widths.

Compared on **measurements, not pixels**: the engines rasterise type differently, so a pixel diff
between them would be noise. What must agree is the layout.

| | Result |
|---|---|
| Document height vs Chromium | identical at 13 of 15 combinations; Firefox differs by 3px at 760 and 17px at 390, both inside tolerance |
| Column counts (`.stats`, `.grid4`, `.foot-g`) | identical everywhere |
| Horizontal overflow | none, any engine, any width |
| JS errors | none, any engine, any page |

### Sticky and pinned — the iOS Safari risk the brief names

| Engine | Sticky header | Pinned | Track moves | Compacts | Travel |
|---|---|---|---|---|---|
| Chromium | yes | yes | yes | yes | 1236px |
| Firefox | yes | yes | yes | yes | 1236px |
| **WebKit** | **yes** | **yes** | **yes** | **yes** | **1236px** |

WebKit is Safari's engine, so this is the strongest signal available without a physical device.
**Still worth a pass on a real iPhone before launch** — WebKit on desktop does not reproduce iOS
Safari's dynamic toolbar, which is what usually breaks `100vh` sticky layouts. The pinned section
uses `min(100vh, 860px)`, so it is exposed to that specific difference.

---

## 4. UI/UX Pro Max pre-delivery checklist

The skill's own scope notice says its canonical checklist targets native/mobile app UI and does not
apply 1:1 to web. Applying the items that do transfer:

| Item | Result |
|---|---|
| Decorative icons hidden from the accessibility tree | pass (fixed this phase) |
| Meaningful icons have a text alternative | pass |
| Icon controls have an accessible name and announce state | pass — the menu button carries `aria-label`, `aria-expanded`, `aria-controls` |
| Form fields have labels, hints and clear errors | pass |
| Colour is not the only indicator | pass — errors pair a navy square marker with text |
| Reduced motion supported without layout breakage | pass |
| Sticky UI does not obscure keyboard focus | pass (`scroll-margin-top` added) |
| Swipe-only interactions have alternatives | pass — the pinned track is scroll-driven |
| Paste and password managers allowed | pass — nothing blocks paste |
| Failed forms keep inline errors and focus the first one | pass |
| Vector-only assets, no emoji icons | pass |
| 4/8px spacing rhythm | pass — the prototype is built on 4px steps |
| Readable text measure | pass — capped at 62–68 characters |
| **Auto-rotating content has pause/stop controls** | **see below** |

---

## 5. Open findings

### A. No pause control for continuous motion (WCAG 2.2.2, Level A)

The marquees, radar sweep, incident feed and the animations inside every illustration start
automatically and run indefinitely alongside other content. WCAG 2.2.2 asks for a mechanism to
pause, stop or hide them.

**Mitigated but not closed.** `prefers-reduced-motion` disables all of it, and the client-logo
marquee pauses on hover — but a user who has not set that OS preference has no in-page control,
and WCAG expects the mechanism to be on the page.

Closing it means adding a visible motion toggle, which is a design addition the prototype does not
have. **Flagged for a decision.** A small toggle in the footer, in the existing V7 vocabulary,
would do it and would persist in `localStorage`.

### B. Footer legal links at mobile widths

The three legal links in the footer bottom bar wrap at narrow widths and end up roughly 20px apart
vertically, under the 24px WCAG 2.5.8 (AA) wants. Closing it means changing the prototype's footer
line spacing, which would break the pixel-exact homepage. The suite documents it as a known
exception rather than hiding it.

### C. Not verifiable from this machine

- **securityheaders.com** and **Mozilla Observatory** need a public URL. Every header they grade
  is present and correct; the only one withheld locally is HSTS, deliberately, because it is only
  sent over TLS. Re-run both after deployment.
- **A real iOS Safari device**, for the reason in §3.
- **A real screen reader.** Semantics are asserted programmatically; a pass with NVDA or VoiceOver
  before launch is still worth the hour.

---

## 6. Final numbers

### Lighthouse, mobile

| Page | Perf | A11y | Best practices | SEO | LCP | CLS |
|---|---|---|---|---|---|---|
| Homepage | 91–96 | **100** | **100** | **100** | 2.6s | **0** |
| Service | 97 | **100** | **100** | **100** | 2.4s | 0.008 |
| Article | 92 | **100** | **100** | **100** | 2.3s | **0** |
| Contact | 94 | **100** | **100** | **100** | 2.4s | **0** |

Performance varies by a few points run to run on this machine — it is measured against
`php artisan serve`, which is single-threaded and sends everything uncompressed. The gzip and
cache configuration that closes the remaining LCP gap ships in `deploy/nginx.conf`; measured, the
homepage HTML is 55,791 bytes raw and 11,952 gzipped. **Re-measure on the real server after
deploy.**

> One article-page run reported CLS 0.143. Instrumenting layout-shift directly under the same
> throttling measured **0.0000**, and the figure has not recurred across subsequent runs — it was
> dev-server contention delaying the stylesheet. The body font is now preloaded alongside the
> display font, which removes the swap window that would have caused it.

### Test suites

| Suite | Result |
|---|---|
| PHP | **140 passed**, 699 assertions |
| Behaviour (Playwright) | **45/45** |
| QA — Chromium / Firefox / WebKit | **108 / 108 / 107**, all passing |
| Cross-browser layout | all three engines agree |
| Visual diff vs prototype | **0 pixels** at 1440 / 1180 / 900 / 760 / 390 |
| Pinned services | **0 pixels** at five scroll positions |
| `composer audit` / `npm audit` | **0 vulnerabilities** |
