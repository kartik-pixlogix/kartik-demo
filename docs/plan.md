# UB Shield — build plan

Companion to `BUILD.md` (the brief) and `docs/inventory.md` (what the prototype actually contains).
This records how each phase will be executed and what "done" looks like, so there are no surprises
at the review gates.

---

## Phase 0 — status

| Step | State |
|---|---|
| Read `design/v7/README.md` and all 1048 lines of `design/v7/index.html` | done |
| Written inventory → `docs/inventory.md` | done — 16 sections, incl. 15 discrepancies and 6 open questions |
| Reference screenshots at 1440 / 1180 / 900 / 760 / 390 → `docs/reference/` | done — motion off, fonts loaded, full page |
| Self-host the fonts | done — `public/fonts/`, metrics verified pixel-exact (§Q5) |
| Copy the brand PNGs | done — `public/images/brand/` |
| Scaffold Laravel + Vite + Filament | done — Laravel 13.39.0 on PHP 8.4.27, Filament 5.11.0 at `/admin`, MySQL 8.4.9, Vite building in 541ms |

All six Phase 0 questions were answered at the review: PHP 8.4 + Laravel 13 (Q1), MySQL 8 installed
locally (Q2), all 13 SVG defs ported (Q3, default), the 1024–1180px pin band kept as drawn (Q4,
default), fonts self-hosted (Q5), and the SOC feed fix applied (Q6 — see `docs/prototype-fixes.md`).

`design/v7/` was not modified. The motion-off capture ran against a copy in the scratchpad.

### What the reference screenshots are and are not

Captured with the prototype's own `motion = false` flag, as the brief specifies. That flag also
turns off `rvon` and `pinon`, so the references show:

- every reveal in its **resting** state (nothing hidden) — which is what we want to diff against;
- the services section as a **3-column grid**, not the pinned horizontal track.

So the references cover 12 of the 13 sections faithfully, and the pinned services behaviour has no
still to diff against. Phase 1 will add a small motion-on capture set for the pinned track at
several scroll positions and diff that separately.

---

## Phase 1 — homepage, pixel-faithful

### 1. CSS, in cascade order

`resources/css/app.css` imports only, in exactly this order (per inventory §9):

```
tokens → base → type → layout → components → cursor
→ sections/{header,hero,trust,services,statement,process,soc,
            industries,frameworks,cases,insights,strip,footer} → art
→ motion → responsive → v7-overrides
```

`responsive.css` and `v7-overrides.css` stay as separate trailing files. The V7 override block
genuinely overrides earlier rules (`.brand` becomes an image lockup, `.dd-m a:hover` flips
`#1a1a1a` → `#dfe5ea`, arrow strokes get remapped), so folding it into the section partials would
silently change the design. Values are copied from the prototype, never retyped from memory.

### 2. Blade components

Shared: `layout`, `svg-defs`, `shield-emblem` (with a `scan` flag), `ui/{arrow-circle, text-link,
button, chip, section-title, crosshair, cursor, industry-preview}`.
Sections: one component per section in prototype order.
Art: one component per illustration under `components/art/`, SVG kept inline so IDs, gradients and
animation classes survive. The `#sh`/`#embc`/`#dots` references only resolve because the defs are
emitted once per page — `svg-defs` goes at the top of `<body>`, as in the prototype.

### 3. JS modules

`resources/js/app.js` owns the state object, the single rAF loop and the `dirty` flag; behaviour
lives in `modules/*` per the table in inventory §11. Two things that are easy to get wrong and will
be ported literally:

- scroll work is **never** done in the scroll handler — the listener only sets `dirty = true`, and
  the rAF loop calls `onScroll`;
- `layout()` re-runs on `resize`, on `document.fonts.ready` **and** on `load`, because `pinDist`
  depends on text metrics.

### 4. Verification

Playwright screenshots of the build at the same 5 widths with motion off, compared to
`docs/reference/` with pixelmatch; diff images and a per-width mismatch count land in `docs/diff/`.
Target: differences only at the sub-pixel / font-rasterisation level. Plus a manual pass on each
motion behaviour and a reduced-motion pass.

**Gate: side-by-side at 1440 and 390, plus the diff numbers.**

---

## Phase 2 — backend and content

Posture-review form: FormRequest validation server-side and matching client-side; honeypot +
minimum fill time + `RateLimiter` (5/hour/IP), with Turnstile behind an env flag; `Lead` records
name, work email, company, service interest, source page, UTM params, hashed IP and user agent;
queued admin notification and visitor auto-reply; inline success/error in the prototype's style
(the button label swaps) **and** a working no-JS POST-and-redirect path.

Filament resources for Leads (read-only + CSV export), Insights, Case studies and Settings.
Homepage stats, credentials, case files and field notes move to the database, with seeders that
reproduce the placeholder strings in inventory §15 **verbatim** — `[XX]`, `[status]`, `Client logo`
and the rest stay exactly as drawn.

**Gate: form flow demo and the admin.**

---

## Phase 3 — inner pages

Service template ×5, industry template ×6, Insights index and article, Contact, About, Privacy and
Terms. Built from existing components and tokens only; the `frontend-design` skill is used for
composition decisions on patterns the prototype does not contain, never for a new palette or type
scale. On-page anchors become real routes while the homepage anchors keep working.

**Gate: one service page before the other four are built.**

---

## Phase 4 — SEO, performance, security

Per-page meta, canonical, OG/Twitter, generated `sitemap.xml`, `robots.txt`, and JSON-LD for
Organization, WebSite, Service, Article, FAQPage and BreadcrumbList.
Lighthouse mobile targets: Performance ≥ 90, the other three at 100; LCP < 2.5s, CLS < 0.05,
INP < 200ms. Levers: preload the hero font, inline critical CSS, lazy-init offscreen modules,
`content-visibility` on long sections, AVIF/WebP, and pause infinite animations when offscreen.
Security headers via middleware (CSP with nonces and no `unsafe-inline` for scripts, HSTS,
`X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `frame-ancestors 'none'`),
secure session cookies, CSRF everywhere, Filament behind `/admin` with 2FA, login throttling and a
password policy. Targets: securityheaders.com A+ and a clean Mozilla Observatory run.

> One thing to flag early: a nonce-based CSP with no `unsafe-inline` for **scripts** is
> straightforward here because all JS is in modules. The prototype also carries ~40 inline `style`
> attributes on artwork and layout elements, which a strict `style-src` would block. Inline
> `style` attributes need `style-src-attr 'unsafe-inline'` (or moving each one into a class).
> I will move them into classes where it is mechanical and raise anything that is not.

---

## Phase 5 — QA

`/web-design-guidelines` across every Blade view, the AccessLint reviewer and contrast checks
(`#1479D4` never body text on navy; text on cyan is navy), keyboard-only passes through the header,
dropdown, mobile menu, pinned services, form and footer, a screen-reader pass, cross-browser at the
5 widths including iOS Safari for the sticky and pinned sections, the UI/UX Pro Max pre-delivery
checklist, and a repeat of the homepage visual diff.

**Gate: Lighthouse, securityheaders and a11y results, plus `docs/placeholders.md`.**

---

## Standing rules being applied

Tokens only, square corners, the four V7 fonts, no stock imagery, no emoji, no new icon libraries,
no invented content — every `[placeholder]` stays as drawn. `design/v7/` is read-only. A commit at
the end of each phase; `.env` is never committed. When the prototype is broken or ambiguous, I stop
and ask rather than guess — which is why Phase 0 ends with six questions rather than a scaffold.

Note: this directory is **not a git repository yet**. `git init` plus the initial commit happens
with the scaffold, so Phase 0's artefacts land in the first commit.
