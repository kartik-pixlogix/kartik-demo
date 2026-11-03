# UB Shield V7 — prototype inventory

Source of truth: `design/v7/index.html` (1048 lines, self-contained) and `design/v7/README.md`.
This document records **what exists in the prototype**, so the Laravel port can be checked against it
line by line. Nothing here is a design proposal — where the prototype and its README disagree, the
prototype wins and the disagreement is recorded in §14.

Read with: `design/v7/README.md` (the designer's handoff) and `BUILD.md` (the build brief).

---

## 1. File skeleton

| Block | Lines | Contents |
|---|---|---|
| `<head>` | 1–10 | charset, viewport, title, meta description, favicon (`assets/ub-monogram.png`), Google Fonts link |
| `<style>` | 11–492 | all CSS, in 6 cascade blocks (see §9) |
| hidden `<svg><defs>` | 497–510 | 13 shared defs (§8) |
| `.cur` cursor node | 512 | custom cursor follower |
| `.prev` preview node | 514–524 | 6 industry preview SVGs |
| `<header class="hdr">` | 526–552 | sticky header + dropdown + progress bar |
| `<nav class="mmenu">` | 554–563 | mobile circular-reveal menu |
| 12 `<section>` / `<footer>` | 565–849 | page content in order (§7) |
| `<script>` | 851–1046 | one IIFE, all behaviour (§11) |

Root wrapper: `<div class="ds6" id="ubs">` — **every** style is scoped to `.ds6`, and the JS
root is `document.getElementById('ubs')`. The port must keep a single root element carrying
`.ds6` plus the JS-toggled state classes `mo`, `has-cur`, `rvon`, `pinon`, `menu-open`.

---

## 2. Design tokens — exact values

Declared on `.ds6` (line 13). **These are the only colours allowed anywhere.**

| Custom property | Value | Role |
|---|---|---|
| `--paper` | `#061522` | Deep Navy — page background |
| `--ink` | `#F4F6F8` | Silver White — all text, rules, light fills |
| `--acid` | `#27B8ED` | Cyan — the single accent, always used as a **fill**; text on it is navy |
| `--faint` | `rgba(199,208,216,.38)` | counter total (`.counter .tot`) only |
| `--line` | `rgba(199,208,216,.22)` | hairline — stat column dividers and `.pbar` only |
| `--blue` | `#1479D4` | Bright Blue — arrows in light circles, brush stroke, artwork |
| `--mid` | `#0C2944` | Midnight — artwork panels, depth |
| `--cool` | `#C7D0D8` | Cool Silver — artwork lines |
| `--ease` | `cubic-bezier(.16,.84,.24,1)` | the one easing curve, used by every transition |
| `--disp` | `'Sofia Sans Extra Condensed','Arial Narrow',sans-serif` | display headlines |
| `--stmt` | `'Big Shoulders Display','Arial Narrow',sans-serif` | the one statement |
| `--text` | `'Instrument Sans','Helvetica Neue',Arial,sans-serif` | everything else |

Base on `.ds6`: `font-weight:500; font-size:19px; line-height:30px; color:var(--ink);
background:var(--paper); overflow-x:clip; position:relative`.

**Literal hex values that appear outside the token list** (all inside the V7 override block or
artwork, all intentional — they are tints of the brand palette, not new brand colours):

| Hex | Where |
|---|---|
| `#8fdcfa`, `#a8e6fb`, `#8fcdf6` | gradient highlight stops (`gBl`, `gM`, `gV`), `.fwm b` gradient |
| `#7f8d9b`, `#5d6b79`, `#9aa9b7`, `#7b8a99`, `#22374c`, `#e6ebf0`, `#3b77ad`, `#062a52`, `#08506e`, `#0b4f99` | gradient mid/shadow stops in `defs` |
| `#ffffff` | gradient top stops + the `.emb` bevel highlight at `opacity:.35` |
| `#dfe5ea` | `.dd-m a:hover` background |
| `#0E5CA8` | `.dd-m span.k` (the kicker inside the light dropdown — darkened for contrast on `#F4F6F8`) |
| `#1a1a1a` | **dead rule** — `.dd-m a:hover` is overridden to `#dfe5ea` later (§14 D4) |

Non-token rgba values in use: `rgba(6,21,34,.9)` (header backdrop),
`rgba(20,121,212,.20/.35/.4)` and `rgba(0,0,0,.5/.55)` (hero glow and emblem drop-shadows),
plus the `--line`/`--faint` tokens themselves.

---

## 3. Typography

Four families, loaded from Google Fonts in the prototype, **to be self-hosted as WOFF2**:

| Family | Weights used | Used by |
|---|---|---|
| Sofia Sans Extra Condensed | 900, 700 | `.dl`, `.h-xl`, `.counter`, `.step h3`, `.ind-row h3`, `.mq`, `.giant`, `.dd-m b`, `.mmenu a`, `.card .num`, `.step .n` |
| Big Shoulders Display | 400 | `.stm p` only |
| Instrument Sans | 500, 600, 700 | body, labels, `.stat`, `.brand` (pre-override), form fields, all SVG `<text>` |
| Michroma | 400 | `.fwm` (footer wordmark) only |

Scale, as written in the CSS:

| Class | `font` shorthand |
|---|---|
| `.ds6` (body) | `500 19px/30px var(--text)` |
| `.lead` | `500 22px/26px var(--text)` |
| `.lab-l` | `600 18px/24px var(--text)`, uppercase |
| `.lab-m` | `500 16px/20px var(--text)`, uppercase |
| `.lab-s` | `500 14px/20px var(--text)`, uppercase |
| `.stat` | `700 38px/38px var(--text)` |
| `.dl` (section title) | `900 56px/45px var(--disp)`, uppercase, `letter-spacing:-.005em` |
| `.dl .alt` | weight `700` |
| `.h-xl` (hero) | `900 224px/160px var(--disp)`, uppercase, `letter-spacing:-.01em` |
| `.counter` | `700 56px/45px var(--disp)` |
| `.proc-l .counter` | `120px/96px` |
| `.step h3` | `900 96px/80px var(--disp)` |
| `.step .n`, `.card .num` | `900 56px/45px var(--disp)` |
| `.ind-row h3` | `900 72px/60px var(--disp)` |
| `.mq` | `900 160px/140px var(--disp)` |
| `.giant` | `900 min(25vw,360px)/.8 var(--disp)`, `letter-spacing:-.02em` |
| `.fwm` | `400 min(8.4vw,122px)/1 'Michroma'`, `letter-spacing:.02em` |
| `.stm p` | `400 120px/120px var(--stmt)`, uppercase, `letter-spacing:-.005em` |
| `.mmenu a` | `900 64px/56px var(--disp)` |
| `.dd-m b` | `900 28px/28px var(--disp)` |
| `.brand` | `700 38px/38px var(--text)` — **superseded**: the V7 block turns `.brand` into a flex image lockup |
| `.chip` | `600 18px/24px var(--text)`, uppercase |
| `.btn` | `500 16px/20px var(--text)`, uppercase |
| `.tl .t` | `600 18px/24px var(--text)`, uppercase |
| `.cur:after` | `600 12px/1 var(--text)`, `letter-spacing:.06em` |
| SVG `<text>` | `Instrument Sans, sans-serif` weight 600, sizes 13 / 14 / 15 / 18 / 28 / 88 |

Section-title overrides set inline in the markup: the three SOC headings at
`font-size:96px;line-height:80px`; `.cta-b .dl` at `72px/60px`; `.all .dl` at `56px`.

---

## 4. Layout and spacing

- Design width **1440px**; `.wrap{max-width:1440px;margin:0 auto;padding:0 64px}` → **1312px** content column.
- Section rhythm: `.sec{padding:160px 0 0}` · `.soc{margin-top:160px;padding:120px 0}` ·
  `.strip{margin-top:160px}` · `.stm{padding:200px 0 40px}` · `.foot{padding:96px 0 32px}` ·
  `.hero{padding:80px 0 0}` · `.sec-t{margin-bottom:32px}`.
- 4px steps in use: 4, 6, 8, 10, 12, 14, 16, 20, 24, 28, 32, 36, 40, 44, 48, 56, 64, 72, 80, 96, 104, 120, 140, 150, 160, 200.
- Grids: `.hero-g` 1fr 1fr · `.stats` 4× · `.track` 3× (grid mode) · `.proc` 5fr/7fr gap 64 ·
  `.soc-g` 1fr 1fr gap 64 · `.grid4` 4× gap 12 (`.case` spans 2) · `.fw-notes` 4× gap 12 ·
  `.strip` 1.25fr/1fr/.8fr · `.foot-g` 4× gap 12 · `.ind-row` 80px 1fr 1fr 42px ·
  `.step` 80px 1fr · `.dd-m a` 72px 1fr 42px · `.feed div` 96px 1fr auto · `.form` 2×.
- Breakpoints: **1180**, **900** (mobile menu), **760** (single column). The JS adds a fourth,
  JS-only threshold at **1024px** (`st.wide`) that governs pinned services.

Per-breakpoint deltas are in §12.

---

## 5. Shared UI components

| Target Blade component | Prototype hook | Notes |
|---|---|---|
| `ui/arrow-circle` | `.ac` + two stacked `svg.a1`/`svg.a2` | 42×42 circle, `--ink` fill, `--blue` stroke. Variant `.ac.on-acid` (cyan bg, navy stroke). The diagonal-arrow path `M7 17 17 7M9 7h8v8` appears **38×** in the file — one component. |
| `ui/text-link` | `.tl` > `.t` + `.ac` | cyan highlighter bar grows on hover; the `.ink-sec` variant starts at 3px |
| `ui/button` | `.btn` | 48px, ink fill, cyan wipe `:before`, arrow rotates 45° on hover; `.cta-b .btn` is the inverted (navy) variant |
| `ui/chip` | `.chip` | 36px cyan box, navy text. Used at `font-size:14px;height:30px` in the process steps |
| `ui/section-title` | `.dl.rm` > `span` + `i.dot` | `.dot` = 20px cyan circle (12px in footer `h4`) |
| `shield-emblem` | `svg.emb` / `svg.femb` / inline in `.strip .img` | 240×280 viewBox, 5 paths + an `#embc`-clipped `.scanE` beam. Appears **3×**: hero (scan), closing strip (scan), footer (no scan) → `<x-shield-emblem scan />` |
| `ui/crosshair` | `.ch` | 32×32 CSS crosshair from `:before`/`:after` hairlines; inverts inside `.ink-sec`. Used 7× as absolute decoration |
| `svg-defs` | the hidden `<svg>` | §8 |
| `ui/cursor` | `.cur` | one node; state via `data-mode` / `data-label` / `data-dark` / `data-hidden` / `data-down` |
| `ui/industry-preview` | `.prev` > `.pv` > `svg.p1…p6` | one node holding all 6 artworks; visibility by `data-v` |

The `.mag` magnetic modifier is on 5 elements: header `.act`, hero `.btn`, hero `.tl`,
SOC `.tl`, form submit `.btn`.

---

## 6. Artwork inventory (73 inline `<svg>` elements)

| # | Where | viewBox | Animation classes |
|---|---|---|---|
| 1 | hidden defs | 0×0 | — |
| 2–7 | `.prev` p1…p6 (industry previews) | `0 0 500 400` | `.flow` (p2, p4), `.spin` (p4) |
| 8–45 | arrow glyphs in `.ac`, `.btn`, `.oring`, `.act i`, `.cur`, plus the dropdown chevron | `0 0 24 24` | — |
| 46 | hero layer `data-depth="14"` — dot grid, radar rings, corner traces | `0 0 640 760` | `.spin`, `.flow`×4 |
| 47 | hero layer `data-depth="30"` — WAF / SIEM / EDR / IAM nodes | `0 0 640 760` | `.flow`×4, `.pl2`×4 (delays 0/.6/1.2/1.8s) |
| 48 | hero layer `data-depth="22"` — `svg.emb` shield emblem | `0 0 240 280` | `.scanE` |
| 49 | hero layer `data-depth="52"` — MFA / TLS badges + 2 small shields | `0 0 640 760` | `.pl2`×2 |
| 50 | service card 01 — VAPT: browser window, SQLi/XSS/IDOR tags, crosshair | `0 0 500 400` | `.pl2`×3, `.hunt` |
| 51 | service card 02 — Privacy: fingerprint, redaction bars | `0 0 500 400` | `.scanY`, `.redact`×4 (delays 0/.4/.8/1.2s) |
| 52 | service card 03 — GRC: clipboard, ticks, AUDIT READY dial | `0 0 500 400` | `.tick`×3 (delays 0/.4/.8s), `.spin` |
| 53 | service card 04 — Infra & Cloud: cloud, 3 racks | `0 0 500 400` | `.flow`×3, `.pl2`×3 |
| 54 | service card 05 — MSS: monitor, EKG trace, radar | `0 0 500 400` | `.flow`, `.spin`, `.pl2`×2 |
| 55 | `.stm` brush stroke | `0 0 400 80`, `preserveAspectRatio="none"` | painted by `.brush.in` clip-path |
| 56 | case file 01 — RBI CSF | `0 0 500 400` | `.tick` |
| 57 | case file 02 — SOC 2 | `0 0 500 400` | `.redact`×3 |
| 58 | field note 01 — consent toggles | `0 0 500 400` | `.knob` |
| 59 | field note 02 — control checklist grid | `0 0 500 400` | `.scanX` |
| 60 | field note 03 — cloud bucket public→private | `0 0 500 400` | `.pl2` |
| 61 | `.strip .img` — circuit + shield panel | `0 0 500 700`, `preserveAspectRatio="xMidYMid slice"` | `.flow`×6, `.scanE` |
| 62 | footer `svg.femb` | `0 0 240 280` | — (no scan beam) |

All artwork `<svg>` carry `aria-hidden="true"`; the SOC `.radar` is CSS-only and carries
`role="img"` with a descriptive `aria-label`. `[data-px]` parallax is applied to the SVG itself on
service / case / post cards (`0.06`–`0.08`) and to the `.strip .img` svg (`0.1`).

---

## 7. Section inventory, in DOM order

### 7.1 Header — `header.hdr`
- Sticky, `z-index:50`, height 104px → **76px** when `.compact`, 84px at ≤760px.
- Background `rgba(6,21,34,.9)` + `backdrop-filter:blur(12px)` (from the V7 block).
- `.brand` — flex lockup: `img.m` monogram 42px (32px compact, 34px ≤760px) + `img.w` wordmark 17px (13px ≤760px). `aria-label="UB Shield home"`, monogram `alt=""`, wordmark `alt="UB Shield"`.
- `.nav` — the `.dd` Services dropdown (5 pillars) plus Industries, Compliance, Resources, About. A cyan highlighter `:after` wipes in on hover and `:focus-within`.
- `.dd-m` — 560px light panel (`--ink` bg, navy text), rows `72px 1fr 42px`: kicker `.k`, `<b>` title, `.ac` (cyan variant). Pillars: Secure / Privacy / Compliance / Infra / MSS.
- `.act.mag` — "Talk to an expert" + a 60px cyan circle; the label is hidden ≤1180px (`.lab-hide`) and the whole element ≤900px.
- `.menu-btn` — 60px cyan circle, two bars → X when `.menu-open`; shown only ≤900px. `aria-expanded`, `aria-controls="mmenu"`, `data-menu-toggle`.
- `i.prog` — 3px scroll-progress bar, `linear-gradient(90deg,#1479D4,#27B8ED)`, `transform-origin:left`.
- `.hdr.up{transform:translateY(-100%)}` — hide-on-scroll-down.

### 7.2 Mobile menu — `nav.mmenu#mmenu`
Fixed, cyan, `clip-path:circle(0 at calc(100% - 46px) 46px)` → `circle(150% …)` over `.8s`.
6 links (`.mmenu a`, 64/56 display type, navy) staggered 0/.06/.12/.18/.24/.3s, plus a
pillars line pinned with `margin-top:auto`. Closed by Escape or by any link click.

### 7.3 Hero — `section.hero#top`
- `background: radial-gradient(ellipse 55% 60% at 78% 38%, rgba(20,121,212,.20), transparent 70%)`.
- Two `.hsl` slash lines (`a` left/top 120, `b` right/top 760), hidden ≤760px.
- `.hero-g` two columns; **at ≤760px the artwork takes `order:-1`** (moving above the headline) and bleeds `margin:0 -16px`.
- `.chip` "Cybersecurity & compliance".
- `h1.h-xl` — 4 `.ln` mask rows, each `.ln > span > span[data-hl]`: Tested. (`-1`) / Fixed. (`1`) / Watched. (`-1`) / PR**O**VEN. (`1`). The "O" is `.oring`: a `.62em` ink circle holding the arrow, `aria-hidden`, with `aria-label="Proven."` on the parent span.
- `.lead` (max 520px), then `.cta-row`: `.btn.mag` "Book a security assessment" + `.tl.mag` "Explore services".
- `.hero-art` — 760px tall (560 at ≤1180, 360 at ≤760), wrapped in `[data-px="-0.12"]`, holding 4 `.lay[data-depth]` layers (14 / 30 / 22 / 52), 2 `.bsl` slashes and 3 `.ch` crosshairs.
- `.stats.h` — 4 columns with hairline dividers: `[XX]+` Assessments delivered · `24`/7 SOC coverage from Ahmedabad · `[XX]%` Critical findings closed in 30 days · `8` Frameworks covered. `data-count` on 24 and 8.

### 7.4 Trust strip — `section.trust`
Rules top and bottom. `.trust-in` = `.chip` "Credentials" + the `.creds` line
`ISO/IEC 27001 [status] / SOC 2 [status] / CERT-In empanelment [if applicable]`.
`.lmq` marquee: 16 `.lg` 200×72 dashed "Client logo" boxes — **8 real + 8 `aria-hidden` duplicates**
(the accessible static equivalent). `animation:lmq 38s linear infinite` (`translateX(-50%)`),
paused on hover. 150×60 at ≤760px.

### 7.5 Services — `section.sec.svc-sec#services`
- `.svc-stick` becomes `position:sticky;top:0;height:min(100vh,860px)` **only** under `.pinon`.
- `.svc-head`: `h2.dl.rm` "Services" + dot, a `.lab-m.rv` pillar line, and `.counter.v-hide` `01 | 05` (`[data-slide]`; `.v-hide` is `display:none` unless `.pinon`).
- `.track.rs` — grid `repeat(3,1fr)` gap `48px 12px` by default; under `.pinon` it becomes `display:flex;width:max-content` with `.card{width:500px}`.
- 5 `.card` anchors, each: `.art.rc` > `.zoom` > `svg[data-px="0.06"]` + `.num`; `.meta` (`.lab-l` title + `.lab-s` pillar + `.ac`); `.body`; a `<ul>` of `/`-separated tags.
  1. Offensive Security / VAPT — Secure — 5 tags — `art`
  2. Data Privacy — Privacy — 4 tags — `art`
  3. Governance, Risk & Compliance — Compliance — 5 tags — `art k`
  4. Infrastructure & Cloud — Infrastructure — 4 tags — `art`
  5. Managed Security Services — MSS — 4 tags — `art k`
- `.pbar.v-hide` — a 1px `--line` rail with a 3px ink fill scaled by pin progress.
- `.art{aspect-ratio:5/4;overflow:hidden}` · `.art.k{background:var(--ink)}` · the inner svg is `top:-8%;height:116%` so parallax has headroom · `.zoom` scales to `1.06` on card hover.

### 7.6 Statement — `section.stm`
Two `.ch` crosshairs. `p.rw` with 5 `.w` words: We / stay / until / it's / fixed. — the last
wrapped in `.brush`, which carries the 400×80 stroke SVG behind it (`fill:#1479D4`, `z-index:-1`).
Then a `.rv` paragraph at an inline `500 19px/30px`, max 520px.

### 7.7 How we work — `section.sec` > `.proc`
- `.proc-l` sticky at `top:140px` (static ≤1180px): `h2.dl.rm` "How we work" + dot, `.counter` `01 | 04` (`[data-stepno]`) at 120/96, and a `.rv` paragraph max 420px.
- `.steps` — 4 `.step[data-step]` rows, `min-height:340px`, `grid 80px 1fr`, with a 3px top bar scaled in by `.on` and a `.dot` that scales in on `.on`:
  01 Assess — "Risk register / test report" · 02 Remediate — "Closure evidence" ·
  03 Monitor — "Monthly posture report" · 04 Certify — "Audit-ready evidence pack".
  Each `.out` row = a 30px `.chip` "Output" + a `.lab-s`. The first step ships with `.on` in the HTML.

### 7.8 Managed SOC — `section.soc.ink-sec#soc`
**The one inverted block**: `background:#F4F6F8;color:#061522`; focus rings and `.ch` invert; `.sweep`/`.blip` switch from cyan to `#1479D4`.
- Left: `.chip`, three 96/80 `.dl.rm` lines ("We never" / "clock off" `.alt` / "at night." + dot), `.body.rv`, `ul.soc-list.rs` of 4 rows (SIEM monitoring · Detection & response · CERT-In reporting *inside the six-hour window* · Mean time to respond `[XX] min`), and a `.tl.mag` "Scope a managed SOC" with `.ac.on-acid`.
- Right: `.radar[data-px="-0.06"]` — CSS-only, `aspect-ratio:1`, max 560px: 3 `.ring` (100/70/40%), `.x`/`.y` crosshairs, `.sweep` (4s rotation), 3 `.blip` (delays 0/1.4/2.6s), and `.soc-hud` with a `.lab-s` "Ahmedabad / IST" and `.clock[data-clock="1"]` (38px tabular-nums, initial `--:--:--`).
- `.feed` — a 150px window, `aria-hidden="true"`, 6 incident rows **duplicated** for the `feed 18s linear infinite` `translateY(-50%)` loop. The status `<b>` is a cyan tag (hidden ≤760px).

### 7.9 Industries — `section.sec#industries`
`.sec-t` title + `.rv` blurb. `.inds.rs` = 6 `.ind-row[data-v="1…6"]` anchors,
`grid 80px 1fr 1fr 42px`, with a cyan `:before` that scales up from the bottom on hover **and
`:focus-visible`**, bleeding `inset:0 -64px`. `h3` slides `translateX(24px)` on hover (disabled ≤760px).
01 Banking & finance · 02 Healthcare · 03 SaaS & IT · 04 Manufacturing · 05 Education · 06 E-commerce.

### 7.10 Frameworks — `section.sec#compliance`
`.sec-t` title + `.rv` blurb, then **two** full-bleed `.mq` rows at 160/140:
- row 1 `[data-mq="-1"]` is `aria-hidden`, and its parent `<p>` carries
  `aria-label="DPDPA, ISO 27001, SOC 2, RBI, SEBI CSCRF, PCI DSS, GDPR, HIPAA"` — **the accessible
  equivalent for both rows**;
- row 2 `[data-mq="1"]` has the whole `<p>` `aria-hidden`.
Alternate words use `.o` (transparent fill, `-webkit-text-stroke:2px var(--ink)`); separators are 28px cyan `i` dots with a 1px inset ink ring. Then `.fw-notes.rs`, 4 cells with a top rule.

### 7.11 Case files — `section.sec#about`
`.sec-t` title + the `.rv` note "Names, figures and outcomes are placeholders until confirmed."
`.grid4.rs` with 2 `.case` (each `grid-column:span 2`). Then `.stats.p.rs`:
`[XX]` Audits supported to certification · `[XX] min` Mean time to respond ·
`[XX]+` Endpoints monitored · `5` Practices, one team (`data-count="5"`).

### 7.12 Field notes — `section.sec#insights`
`.sec-t` title only. `.grid4.rs`: 3 `.post` cards (`.art.rc` > `.zoom` > svg, `.meta` with a `.lab-l`
headline and a `.lab-s` "Category / [DD MMM 2026]") plus one `.all` cyan tile
("All / resources" at `.dl` 56px + `.ac`), which inverts to ink on hover.

### 7.13 Closing strip — `section.strip#contact`
Three columns `1.25fr 1fr .8fr` (→ 2 columns ≤1180 with `.cta-b` spanning, → 1 column ≤760):
- `.cta-b` — cyan block, 64px padding (40/16 ≤760). `h2.dl` 72/60 in 3 `.rm` lines: "Let's run" / "your free" (`.alt`) / "posture review." Then `.body.rv`, then `form.form[data-review-form]`.
  **Fields as drawn:** Name `#v6n` text/`autocomplete="name"`, Work email `#v6e` email/`email`,
  Company `#v6c` text/`organization`, Service interest `#v6s` `<select>` with 6 options
  (the 5 pillars + "Not sure yet"), then a full-width `.btn.mag` submit carrying
  `[data-submit-label]` "Request my review".
  *No `name` attributes, no `required`, no honeypot, no CSRF — all added in Phase 2 (§14 D10).*
- `.img` — min-height 640px (320 ≤760), the 500×700 circuit/shield panel at `data-px="0.1"`.
- `.contact` — 4 stacked pairs: Email `[email address]`, Phone `[phone number]`,
  Office `[Street address]` / Ahmedabad, Gujarat, India, Response "Within one business day".
  Email and phone are `<a href="#contact">` placeholders.

### 7.14 Footer — `footer.foot`
`.foot-g.rs` 4 columns, each an `h4` + a cyan 12px dot + a `<ul>`; links carry a cyan
`linear-gradient` underline that grows to `100% 8px` on hover.
Columns: Services (5) · Company (4: About, Industries, Resources, Careers) · Contact (3) · Follow (2: LinkedIn, X).
`.flogo` — `role="img" aria-label="UB Shield"`, `[data-px="0.06"]`: a 120px `svg.femb` + `.fwm`
`<b>UB</b> SHIELD`, where `<b>` takes a `180deg #8fdcfa → #27B8ED 45% → #1479D4` clipped text gradient.
`.fb.lab-s` — the pillars line, plus `© 2026 UB Shield` and Privacy policy / Terms / Responsible disclosure.

> Note: `.giant` (the `min(25vw,360px)` wordmark) is defined in CSS and partially overridden by the
> V7 block but is **not used** in the markup — `.flogo`/`.fwm` replaced it. See §14 D5.

---

## 8. SVG defs (the hidden `<svg>`)

| id | Type | Used by markup? |
|---|---|---|
| `gSl` | linearGradient — white → `#C7D0D8` → `#7f8d9b` | **yes** — shield left facet (×3) |
| `gBl` | linearGradient — `#8fdcfa` → `#27B8ED` → `#1479D4` → `#0b4f99` | **yes** — shield right facet (×3) |
| `dots` | pattern 20×20, `r=1.2` cyan circle at `.22` | **yes** — 18 artwork backgrounds |
| `sh` | path — the 80×100 small shield glyph | **yes** — 8 × `<use href="#sh">` |
| `embc` | clipPath — the two emblem facets | **yes** — ×2, clips the `.scanE` beam |
| `gV` | radialGradient white→`#8fcdf6`→`#1479D4`→`#062a52` | no |
| `gN` | radialGradient `#3b77ad`→`#0C2944`→`#061522`→`#1479D4` | no |
| `gM` | radialGradient white→`#a8e6fb`→`#27B8ED`→`#08506e` | no |
| `gA` | radialGradient white→`#F4F6F8`→`#C7D0D8`→`#5d6b79` | no |
| `gC` | radialGradient white→`#e6ebf0`→`#7b8a99`→`#061522`→`#9aa9b7`→`#22374c` | no |
| `gT` | linearGradient white→`#27B8ED`→`#1479D4`→`#C7D0D8` | no |
| `goo` | filter — gaussian blur + colour matrix (gooey threshold) | no |
| `soft` | filter — `feGaussianBlur stdDeviation="6"` | no |

**Decision needed (§16 Q3):** the 8 unused defs are inert (the host `<svg>` is `width="0" height="0"`,
~1.8 KB uncompressed). Porting them verbatim is the faithful choice and costs nothing visually;
dropping them is the lean choice. Default assumption unless told otherwise: **port all 13**, so the
defs component matches the prototype and future artwork can use them.

---

## 9. CSS cascade map → target partials

The prototype's `<style>` runs in six blocks, **and the last two override the first four**.
Order must be preserved exactly.

| Prototype block | Lines | Target file(s) |
|---|---|---|
| reset + tokens + type + `.wrap` + generic helpers (`.lab-*`, `.stat`, `.dl`, `.dot`, `.chip`, `.sec`, `.sec-t`, `.ch`) | 12–36 | `tokens.css`, `base.css`, `type.css`, `layout.css` |
| shared components: `.ac`, `.tl`, `.btn`, `.cur` | 38–83 | `components.css`, `cursor.css` |
| sections, in page order: header → footer, plus `.art`/`.spin`/`.blob` | 84–308 | `sections/{header,hero,trust,services,statement,process,soc,industries,frameworks,cases,insights,strip,footer}.css` + `art.css` |
| motion: keyframes, the `.mo` load sequence, `.rvon` reveals, reduced-motion | 310–347 | `motion.css` |
| **responsive** — `@media 1180 / 900 / 760` | 349–425 | `responsive.css` |
| **V7 palette + logo + cyber-art overrides**, including its own two `@media 760` blocks | 426–492 | `v7-overrides.css` |

**Porting rule:** `app.css` imports in exactly this order, with `responsive.css` and
`v7-overrides.css` last. Folding the override block back into its section partials would change the
cascade (`.brand` goes from a 38px wordmark to an image lockup; `.dd-m a:hover` goes
`#1a1a1a` → `#dfe5ea`; the arrow strokes get remapped), so the override file stays separate.

---

## 10. Keyframes and animation classes

### 10.1 `@keyframes` (18)

| Name | Driven by | Spec |
|---|---|---|
| `up` | `.mo .h-xl .ln>span` | `translateY(105%)` → none, `1.1s var(--ease)`; lines 2–4 delayed .08/.16/.24s |
| `fade` | `.mo .hero .lead` / `.cta-row`, `.mo .stats.h>div` | opacity 0→1; lead .55s, cta .7s, stats .8/.9/1/1.1s |
| `artIn` | `.mo .hero-art` | `scale(.85) rotate(-8deg)` + opacity → none, `1.6s .2s` |
| `chipIn` | `.mo .hero .chip`, `.mo .bsl`/`.hsl` | `clip-path:inset(0 100% 0 0)` → `inset(0)` |
| `hmIn` | `.mo .hm`, `.mo .emb` | opacity 0 + `blur(10px)` → opacity 1, `1.6s .5s` |
| `lmq` | `.lmq-in` | `translateX(-50%)`, `38s linear infinite` |
| `drift` | `.blob` | **unused in markup** (§14 D1) |
| `rot` | `.spin` (26s), `.sweep` (4s) | `rotate(360deg)`, linear infinite |
| `blip` | `.blip` | scale/opacity pulse, `4s ease-out infinite` |
| `feed` | `.feed-in` | `translateY(-50%)`, `18s linear infinite` |
| `flow` | `.flow` | `stroke-dashoffset:-64`, `2.2s linear infinite` (`stroke-dasharray:6 10`) |
| `pl2` | `.pl2` | opacity `.55` → `1` → `.55`, `2.4s ease-in-out infinite` |
| `scanE` | `.scanE` | `translateY(-120px → 124px)`, `2.8s alternate` |
| `scanY` | `.scanY` | `translateY(-80px → 80px)`, `2.4s alternate` |
| `scanX` | `.scanX` | `translateX(0 → 290px)`, `3.6s alternate` |
| `redact` | `.redact` | `scaleX(.08 → 1)`, `3s alternate`, `transform-origin:left` |
| `tick` | `.tick` | `stroke-dashoffset 40 → 0`, fades out at 100%, `3.2s ease-out infinite` |
| `hunt` | `.hunt` | 4-stop `translate()` tour `(329,160) → (174,220) → (359,280) → (329,160)`, `6s cubic-bezier(.65,0,.35,1)` |
| `knob` | `.knob` | `translateX(0 → 16px)`, `2.6s alternate` |

`.blob`, `.spin`, `.redact` and `.tick` rely on `transform-box:fill-box` with an explicit
`transform-origin` — easy to lose in a port, and the artwork breaks without it.

### 10.2 Motion kill-switches (both must be ported)

- `.ds6:not(.mo)` zeroes `.blob .spin .sweep .blip .feed-in .lmq-in` (line 335) and
  `.flow .pl2 .scanE .scanY .scanX .redact .tick .knob` (line 484), pins `.tick{stroke-dashoffset:0}`
  and parks `.hunt` at `translate(329px,160px)`.
- `@media (prefers-reduced-motion: reduce)` sets
  `.ds6 *,:before,:after{animation:none!important;transition:none!important}`
  **and** `.cur,.prev{display:none!important}`.

### 10.3 Scroll-reveal classes (all gated on `.rvon`)

| Class | From → to |
|---|---|
| `.rv` | `opacity:0; translateY(48px)` → none (`1s` / `1.1s`) |
| `.rm` | child `span` `translateY(105%)` → none, parent `overflow:hidden` |
| `.rs` | children `opacity:0; translateY(56px)` → none, staggered 0/.08/.16/.24/.32/.4s (`nth-child` 2–6) |
| `.rw` | `.w` children `opacity:.12` → 1, same 6-step stagger |
| `.rl` | `scaleX(0)` → none — **unused in markup** (§14 D2) |
| `.brush` | svg `clip-path:inset(0 100% 0 0)` → `inset(0)`, `1.2s .5s` |
| `.art.rc` | `clip-path:inset(18% 0 18% 0)` → `inset(0)`, `1.3s` |

Observed by the IntersectionObserver: `.rv, .rm, .rs, .rl, .rw, .brush, .art.rc, .stats`
at `threshold:0.12, rootMargin:'0px 0px -6% 0px'`, unobserved after firing.

---

## 11. JS behaviour → module map

One IIFE scoped to `#ubs`. Flags at the top: `reduced` (`prefers-reduced-motion`),
`fine` (`(hover:hover) and (pointer:fine)`), `motion = !reduced`, `customCursor = true`.
State object `st = {compact, up, slide, step, menu, scrollable, wide, pinDist}`.

| Prototype function | Target module | Behaviour to port exactly |
|---|---|---|
| `layout()` | the `app.js` orchestrator | Sets `st.scrollable = scrollHeight > innerHeight + 300` and `st.wide = innerWidth >= 1024`; toggles `mo`, `has-cur` (`motion && fine && customCursor`), `rvon` (`motion && scrollable`), `pinon` (`motion && scrollable && wide`); recomputes `st.pinDist = max(0, track.scrollWidth - stick's .wrap clientWidth + 128)` and sets `pin.style.height = stick.offsetHeight + pinDist`; rebuilds the IntersectionObserver. Re-runs on `resize`, on `document.fonts.ready`, and on `load`. |
| IntersectionObserver in `layout()` | `reveal.js` | adds `.in`, fires nested `[data-count]`, then `unobserve` |
| `countUp(el)` | `count-up.js` | 1400ms, `1-(1-k)³` ease, `Math.round`, one-shot via `el._done` |
| `onScroll()` → header | `header.js` | `.compact` at `y>40`; `.up` when `y>500 && dy>2 && !menu`, cleared when `dy<-2` or `y<=500`; `.prog` `scaleX(min(1, y/max))` to 4dp |
| `onScroll()` → parallax | `parallax.js` | `[data-hl]`: `translate3d(hl × min(y,1200) × 0.22 px,0,0)`. `[data-px]`: skip when `bottom < -200 \|\| top > vh+200`, else `translate3d(0, centreOffset × -factor px, 0)` at 1dp |
| `onScroll()` → steps | `process-steps.js` | the `.step` nearest `vh*0.5` (measured as `r.top + min(r.height,300)/2`) wins `.on`; `[data-stepno]` ← `'0'+(i+1)` |
| `onScroll()` → pin | `pinned-services.js` | `p = clamp(-r.top / (pin.offsetHeight - stick.offsetHeight))`; `track` `translate3d(-p × pinDist,0,0)`; `.pbar i` `scaleX(p)`; slide `1 + min(4, round(p*4))` → `[data-slide]` |
| `onPointer(e)` → cursor | `cursor.js` | `data-mode`: `text` over `input,select,textarea`; `view` + label `VIEW` over `.ind-row`; `view` + label `OPEN` over `.card,.case,.post`; `go` over any `a,button`; `data-dark=1` over `.ink-sec,.dd-m` when the mode is empty |
| `onPointer(e)` → preview | `industries-preview.js` | `.prev` gets `data-show=1` and `data-v` from the hovered `.ind-row` |
| `onPointer(e)` → magnetic | `magnetic.js` | `.mag` offset `(x − centreX) × 0.25`, `(y − centreY) × 0.35`; reset on leave |
| `onPointer(e)` → hero depth | `parallax.js` | `hx/hy = -(clientX/innerWidth − .5)` and the Y equivalent, **only while `clientY < heroArt.bottom + 200`**, otherwise decayed `*= 0.9` |
| `frame()` | the `app.js` rAF loop | cursor lerp `0.22`; preview lerp `0.12` + `rotate(clamp(±12, (mx−px) × 0.08))`; layers `translate3d(hx × depth, hy × depth, 0)`; marquee velocity; `if (dirty) onScroll()` |
| `frame()` → marquee | `marquee.js` | `vel *= 0.92` each frame, `sp = 0.6 + min(abs(vel) × 0.35, 14)`, per-row `mqx += sp × dir × data-mq` with wraparound at `scrollWidth/2`. `vel += dy × 0.25` and `dir` are set in `onScroll` |
| `tickClock()` | `soc-clock.js` | IST = `Date.now() + (330 + getTimezoneOffset()) × 60000`, zero-padded `HH:MM:SS`, `setInterval 1000` |
| `setMenu(open)` | `mobile-menu.js` | toggles `menu-open` on the root, syncs `aria-expanded` and `aria-label` (Open/Close menu), force-clears `.up`; closed by Escape and by any `.mmenu a` click |
| form submit handler | `form.js` | the prototype only calls `preventDefault()` and swaps `[data-submit-label]` to "Request received. We reply within one business day" — replaced by the real endpoint in Phase 2 |

Listeners: `resize` → `layout`; `scroll` → `dirty = true` (passive); `mousemove` → `onPointer`
(passive); `mousedown`/`mouseup` → `.cur[data-down]`; `documentElement.mouseleave` → hide the cursor,
clear the preview, reset magnetic. Scroll work is **always** deferred to the rAF loop via the `dirty`
flag — the port must keep that, not call `onScroll` from the scroll event.

---

## 12. Responsive deltas

### ≤1180px
`.nav .lab-hide` hidden · `.h-xl` 160/118 · `.hero-g` 1.2fr/.8fr · `.hero-art` 560px ·
`.track` 2 columns · `.proc` 1 column and `.proc-l` static · `.soc-g` 1 column · `.radar` centred ·
`.strip` 2 columns with `.cta-b` full width · `.mq` 120/110.

### ≤900px
`.nav` and `.act` hidden · `.menu-btn` shown.

### ≤760px
`.wrap` padding 16px · body 17/26 · `.hdr-in` 84px · `.brand` 30px (with `img.m` 34px / `img.w` 13px
from the V7 block) · `.hero` padding-top 24px · `.hero-g` 1 column · `.h-xl` 96/74 with
`.ln` padding 6/−6 · **`.hero-art` 360px, `order:-1`, `margin:0 -16px`** · `.lead` 19/24 ·
`.cta-row` gap 20 · `.stats` 2 columns with `nth-child(3)` losing its rule and a 32px row gap ·
`.trust-in` 1 column · `.lg` 150×60 · `.sec` padding-top 104 · `.dl` 44/38 · `.track` 1 column
gap 40 · `.counter` 44 · `.stm` padding-top 120 and `p` 60/62 · `.proc-l .counter` 72/60 ·
`.step` 1 column, min-height 0 · `.step h3` 64/54 · `.soc` margin 104 / padding 80 ·
`.soc-list li` stacked · `.feed div` 2 columns with `b` hidden · `.ind-row` `40px 1fr 42px` with
`p` on row 2 and `.ac` moved to column 3 row 1, `h3` 44/38, hover slide disabled · `.mq` 72/72
with 16px dots · `.fw-notes` 1 column · `.grid4` 1 column gap 40 · `.case` span reset ·
`.all` min-height 200 · `.strip` 1 column, margin 104 · `.cta-b` 40/16 padding and `.dl` 52/44 ·
`.form` 1 column · `.strip .img` 320px · `.contact` 40/16 · `.foot-g` 2 columns ·
`.giant` 26vw · `.fb a` margin-right 16 · `.hm`/`.emb` 170–180px · `.hsl` hidden ·
`.flogo` margin 56 · `.femb` 52px · `.flogo>span` gap 14 · `.fwm` 7.4vw.

### JS-only, 1024px
`st.wide` gates the pinned services section. Between 1024 and 1180px the cards are a
**2-column grid inside a pinned sticky block** — confirm this is intended (§16 Q4).

---

## 13. Accessibility inventory (as built)

Present in the prototype:
- `:focus-visible{outline:2px solid var(--ink);outline-offset:4px}`, inverted to `--acid` inside `.ink-sec` and to navy inside `.cta-b`.
- Every decorative/artwork `<svg>` has `aria-hidden="true"`; arrow glyphs inside `.ac` sit inside `aria-hidden` spans or carry it themselves.
- `.cur` and `.prev` are `aria-hidden`.
- Landmarks: `header`, `nav[aria-label="Main"]`, `nav[aria-label="Mobile"]`, `footer`; `section[aria-label]` on the trust strip ("Credentials and clients") and the statement ("Our principle").
- Hero "O" ring: `aria-hidden` on the glyph, `aria-label="Proven."` on the text span.
- Marquees: `.lmq` duplicates are `aria-hidden`; `.mq` row 1's parent `<p>` carries the full framework list as `aria-label`, and row 2 is entirely `aria-hidden`.
- `.feed` is `aria-hidden="true"` — stronger than the `aria-live="off"` the brief asks for; keep as is.
- `.radar` is `role="img"` with a descriptive label.
- `.menu-btn` has `aria-label`, `aria-expanded` and `aria-controls`; Escape closes the menu.
- `.ind-row:focus-visible` triggers the same cyan fill as hover.
- All 4 form fields have a `<label for>`.
- `prefers-reduced-motion` kills animation, transitions, the cursor and the preview.

**Gaps to close in the port** (additions the brief asks for, beyond the prototype):
1. No skip link.
2. The mobile menu does **not** trap focus. *(Correction: an earlier draft of this
   note also said the links stay tabbable while closed — they do not. `.mmenu{visibility:hidden}`
   already removes them from the tab order, so no `inert` handling is needed and only the trap had
   to be added.)* Done in Phase 1 — `resources/js/modules/mobile-menu.js`. One subtlety: the toggle
   button is earlier in the document than the menu, so the focusable list must be sorted into
   document order or the wrap-around fires on the wrong element.
3. The services dropdown is hover/`:focus-within` only — keyboard users can tab to the links, but there is no Escape-to-close and no `aria-expanded`.
4. The `.lg` "Client logo" boxes are bare text, which is fine for now; real logos will need `alt`.
5. `.dd-m span.k` is `#0E5CA8` on `#F4F6F8` — verify ≥4.5:1 in Phase 5.
6. The 14px `.lab-s` on cyan and on `#F4F6F8` needs a contrast pass.
7. `.contact` email and phone are `href="#contact"` — they must become `mailto:`/`tel:` once confirmed.

---

## 14. Dead code and prototype/README discrepancies

Recorded so the port is a deliberate copy, not an accidental one.

| # | Finding | Proposed handling |
|---|---|---|
| D1 | `.blob` + `@keyframes drift` + `--dur/--dx/--dy/--s` — defined, never used in markup | **default: drop**, recorded here |
| D2 | `.rl` reveal class — defined and observed by the IO, never used in markup | port the CSS (the IO selector lists it) |
| D3 | 8 unused SVG defs: `gV gN gM gA gC gT goo soft` | **default: port all** (§8) |
| D4 | `.dd-m a:hover{background:#1a1a1a}` is overridden by `#dfe5ea` in the V7 block | port both, keep the cascade order |
| D5 | `.giant` (+ `.giant span{display:block}`, `.giant img`) is fully styled but unused — `.flogo`/`.fwm` replaced it | **default: drop**; revisit if `.giant` is wanted on inner pages |
| D6 | `.hm` (hero monogram PNG) is styled with its own `hmIn` animation and a 760px rule, but the markup uses `svg.emb` instead | drop the `.hm` rules; keep `hmIn` (used by `.emb`) |
| D7 | `.art.k .num{color:var(--paper)}` then `.art.k .num{color:#F4F6F8}` in the V7 block — the later wins, so the number is Silver White on `.art.k`. Correct, because every `.art.k` SVG paints a dark `#061522`/`#0C2944` rect over the light `.art.k` background | port as is |
| D8 | The README says the mobile menu traps focus; the prototype does not | implement the trap (BUILD.md requires it) |
| D9 | The README lists `#sh` and `#embc` but does not mention the 8 unused defs | §8 is the authoritative list |
| D10 | The form has no `name`, `required`, honeypot or CSRF, and the handler only swaps the label | Phase 2 adds all of it; the field **ids** `v6n/v6e/v6c/v6s` keep their `for`/`id` pairing while gaining readable `name`s (`name`, `email`, `company`, `service`) |
| D11 | Nav, card, industry and footer links are on-page anchors (`#services`, `#contact`, `#soc`, `#insights`, `#about`, `#compliance`, `#top`) | Phase 3 swaps them for real routes and keeps the homepage anchors working |
| D12 | `.creds` says `CERT-In empanelment [if applicable]` while the README and BUILD.md call it `[status]` | keep the prototype's text verbatim; both go in `docs/placeholders.md` |
| D13 | The favicon is a PNG monogram; there is no apple-touch or SVG icon | Phase 4 adds the full icon set from the same artwork |
| D14 | `title` and `meta description` exist only for the homepage | Phase 4 |
| D15 | **Live bug — the SOC incident feed is mis-laid-out.** `.feed div{display:grid;grid-template-columns:96px minmax(0,1fr) auto}` is a *descendant* selector, so it also matches `.feed-in` itself. `.feed-in` becomes a 3-column grid and its 12 row `div`s flow into it 3-per-row, overlapping. Measured in the untouched prototype at 1440px: `.feed-in` computes to `display:grid`, columns `96px 16.31px 479.69px`, height 578px, and rows 1–3 all sit at the same `top`. Visible as the jumbled text in `docs/reference/proto-*.png`. | **Blocking — §16 Q6.** One-character-class fix: scope the rule to `.feed-in > div`. Verified: `.feed-in` then becomes 474px with 38px rows at 6570/6608/6646/6684, and with the stray `padding:9px 0` also removed it is exactly **456px = 12 × 38px**, so the `translateY(-50%)` of `@keyframes feed` lands precisely on the 6-row repeat and the 18s loop is seamless. That exactness is strong evidence this is the intended design. |

---

## 15. Placeholders (seed data must reproduce these verbatim)

| Where | Placeholder |
|---|---|
| Hero stats | `[XX]+` assessments delivered · `[XX]%` critical findings closed in 30 days |
| Trust strip | `ISO/IEC 27001 [status]`, `SOC 2 [status]`, `CERT-In empanelment [if applicable]` |
| Trust strip | 8 × `Client logo` (+8 `aria-hidden` duplicates) |
| SOC list | `Mean time to respond` → `[XX] min` |
| SOC list | `CERT-In reporting` → `Inside the six-hour window` ← **client must confirm** |
| Case files | `[Case study: BFSI client]`, `[mid-size lender]`, `Outcome: [to confirm]` |
| Case files | `[Case study: SaaS client]`, `[B2B SaaS company]`, `Outcome: [to confirm]` |
| Case stats | `[XX]` audits supported · `[XX] min` MTTR · `[XX]+` endpoints monitored |
| Field notes | 3 × `[DD MMM 2026]` |
| Contact block | `[email address]`, `[phone number]`, `[Street address]` |
| Footer | `[email address]`, `[phone number]` |

Real (non-placeholder) numbers in the prototype: `24`/7, `8` frameworks, `5` practices,
`© 2026`, "Ahmedabad, Gujarat, India", "Within one business day".

To confirm with the client, per the README: **the CERT-In six-hour reporting window** and
**each credential's actual status**. Both carried into `docs/placeholders.md`.

---

## 16. Open questions

Numbered so the answers can be dropped straight back in. Q1 and Q2 block the Laravel scaffold.

- **Q1 — PHP / Laravel version.** BUILD.md asks for "Laravel, current stable (PHP 8.3+)". Current
  stable is **Laravel 13**, which requires PHP `^8.3`. This machine has **PHP 8.2.12** (XAMPP).
  Either upgrade PHP to 8.3/8.4 and build on Laravel 13, or build on **Laravel 12** (PHP `^8.2`,
  supported into 2027). Filament 5 runs on both.
- **Q2 — Database.** BUILD.md asks for MySQL 8. This machine has XAMPP **MariaDB 10.4.32**
  (currently stopped, and past end of life). Install MySQL 8 for local dev, use the XAMPP MariaDB
  locally while targeting MySQL 8 in production, or run SQLite locally?
- **Q3 — Unused SVG defs** (§8, D3): port all 13 for fidelity, or only the 5 that are used?
- **Q4 — Pinned services between 1024 and 1180px.** The JS pins the section at ≥1024px while the CSS
  puts the cards in a 2-column grid at ≤1180px, so in that 156px band the grid sits inside a pinned
  sticky block with the counter and progress bar visible but nothing moving sideways. Keep it exactly
  as drawn, or raise the JS threshold to 1180px so the pin and the flex track switch together?
- **Q5 — Fonts. Resolved during Phase 0, flagging for the record.** The four families were fetched
  as Latin-subset WOFF2 into `build-assets/fonts/` (103 KB total). Instrument Sans and Sofia Sans
  Extra Condensed are served by Google as *variable* files — one file covers every weight — so they
  are stored once each and declared with weight ranges (`font-weight:500 700` / `700 900`) rather
  than one file per weight. Swapping the prototype onto these files reproduces its text metrics
  **exactly** (hero lines 589.48 / 467.44 / 760.84 / 685.42px, statement 240px, footer wordmark
  930.11px — zero delta), so self-hosting carries no fidelity risk. Say the word if the client would
  rather licence and supply the files.
- **Q6 — The SOC feed bug (D15).** BUILD.md says to stop and ask when the prototype is broken, so:
  may I apply the one-line fix (scope `.feed div` to `.feed-in > div`) and rebuild the affected
  reference screenshots? The alternative is to port the overlap faithfully, which I do not think
  anyone wants. **This is the one finding that changes what the homepage looks like**, so it needs a
  decision rather than an assumption.
