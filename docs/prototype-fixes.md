# Deviations from `design/v7/index.html`

The prototype is the source of truth. Every intentional difference between it and the production
build is listed here, with the reason and the approval. Nothing goes in this file without a
decision from the client.

| # | Date | Area | Change | Approved by |
|---|---|---|---|---|
| F1 | 2026-11-02 | SOC incident feed | `.feed div { … }` → `.feed-in > div { … }` (all three rules: the base rule at prototype line 234 and the two `@media (max-width:760px)` rules at lines 396–397) | Client, Phase 0 review |

## F1 — SOC incident feed selector

**Problem.** `.feed div` is a descendant selector, so it matched the `.feed-in` wrapper as well as
the row `div`s inside it. `.feed-in` therefore became a 3-column grid and its 12 rows flowed into
it three per grid row, overlapping each other. Measured in the untouched prototype at 1440px:

```
.feed-in  display: grid
          grid-template-columns: 96px 16.31px 479.69px
          height: 578px
rows 1-3  all at top 6568   <- overlapping
```

**Fix.** Scope the rule to direct children of `.feed-in`:

```css
/* prototype line 234 */
-  .feed div      { display:grid; grid-template-columns:96px minmax(0,1fr) auto; gap:16px; padding:9px 0; font:500 14px/20px var(--text); text-transform:uppercase }
+  .feed-in > div { display:grid; grid-template-columns:96px minmax(0,1fr) auto; gap:16px; padding:9px 0; font:500 14px/20px var(--text); text-transform:uppercase }

/* prototype lines 396-397, inside @media (max-width:760px) */
-  .feed div   { grid-template-columns:72px minmax(0,1fr) }
-  .feed div b { display:none }
+  .feed-in > div   { grid-template-columns:72px minmax(0,1fr) }
+  .feed-in > div b { display:none }
```

**Result, measured after the fix.**

```
.feed-in  display: block
          height: 456px   = 12 rows x 38px exactly
rows      38px each, at 261 / 299 / 337 / 375
```

`@keyframes feed` animates `translateY(-50%)`, which is now exactly −228px — precisely the 6-row
repeat. The 18s loop is therefore seamless. That exactness is the evidence that this was the
intended layout: the duplicated 6 rows and the `-50%` only line up if each row is 38px.

**Reference screenshots.** `docs/reference/proto-*.png` were regenerated after this fix, so the
Phase 1 visual diff targets the corrected feed. Document heights were unchanged at all 5 widths
(the feed is a fixed 150px window), which confirms the fix is contained to that element.

---

## Phase 3 additions beyond the prototype

The prototype covers the homepage only, so the inner pages necessarily add markup the design never
showed. These are additions, not deviations — nothing on the homepage changed, and the visual diff
is still 0 pixels at all five widths. Recorded so the new vocabulary is reviewable in one place.

| # | Addition | Why, and how it stays inside V7 |
|---|---|---|
| A1 | `.phero` page hero | The homepage hero is 224px type over a 760px artwork stage. An inner page needs to be subordinate to it, so the headline steps down to the 96/80 the SOC block already uses, the stats row is dropped, and the artwork sits in the same 5:4 frame the cards use. Same radial glow, same slash line, same chip. |
| A2 | `.crumbs` breadcrumb | A hairline row above the hero, reusing the footer link's cyan underline-grow. Says where the page sits without a second navigation bar. |
| A3 | `.rows` itemised list | Hairline rows like `.soc-list`, with room for a description. Marked with the brand's cyan square, **not** 01 / 02 / 03 — what a practice includes is a list, not a sequence, and numbering would imply an order that is not there. Numbers are kept only for the four engagement phases, which genuinely are one. |
| A4 | `.chips` framework list | The `.chip` shape with an ink outline instead of a cyan fill, so a row of eight does not shout louder than the section title. |
| A5 | `.faq` disclosure | Built on native `<details>`/`<summary>`: it opens with no JavaScript and is keyboard operable for free. The marker is a cyan **square** holding a plus that loses its upright when open — circles stay reserved for arrow buttons, the header action and the title dot. |
| A6 | `.phero + .sec` spacing | The artwork is taller than the text beside it, so a centred hero row leaves dead space under the CTAs. Combined with `.sec`'s 160px that read as a hole, so the first section after a page hero opens at 112px instead. |

All of the above live in `resources/css/inner-pages.css`, imported last alongside `form-states.css`
and clearly marked as not coming from the prototype.

---

## Phase 4 deviations

Two more changes to the prototype's own values, both driven by BUILD.md Phase 4's
**Accessibility = 100** target. Both are one line and both are listed in the FIXES array of
`scripts/split-prototype-css.mjs` and `tests/visual/_shared.mjs`, so regenerating the CSS or the
reference captures keeps them. **Revert either line to restore the prototype exactly.**

| # | Change | Why | What it costs |
|---|---|---|---|
| F2 | `--faint` `rgba(199,208,216,.38)` → `.44` | At .38 the token composites to `#4f5c67` on the navy page, which is **2.69:1** — below the 3:1 WCAG 1.4.3 requires for large text. `.44` gives **3.19:1**. | One thing on screen: the de-emphasised total in the `01 | 04` and `01 | 05` counters is very slightly lighter. The reference captures were regenerated. |
| F3 | footer `<h4>` → `<h3>`, and `.foot h4` → `.foot h3` | The footer headings followed an `<h2>` with no `<h3>` between, so the document outline skipped a level. | Nothing visible — the declarations are untouched and the markup and selector moved together. |
| F4 | `.rvon .rw .w` `opacity:.12` → `.38` | The statement's word-fade reveal rests at 1.37:1 before it animates, so the text is effectively invisible to a low-vision reader who has not scrolled it into view. `.38` gives 3.31:1. | The fade-in is less extreme. **Invisible to the visual diff**, which is captured with motion off and so never shows the resting state. |

F3 is purely semantic. F2 and F4 are judgement calls where two parts of the brief pull against each
other — "do not re-colour anything" versus "Accessibility = 100" — and they were resolved in favour
of the explicit Phase 4 target, on the grounds that both are genuine WCAG failures on text a
visitor is expected to read. **If the designer would rather keep the original values, say so and
both revert in one line each**; the measured cost is Lighthouse Accessibility 100 → 96.

### Deliberately NOT done

`content-visibility: auto` on long sections is in BUILD.md's list of performance techniques and was
**not applied**. It changes the document height while a section is offscreen, because the browser
substitutes `contain-intrinsic-size` for the real height — which directly conflicts with the hard
requirement that the homepage match the prototype pixel for pixel, and risks layout shift against
the very CLS budget it is meant to protect. Pausing offscreen animation is the bigger win on this
page and costs nothing; CLS measured **0**.

---

## Client brand deviations (post-review)

The client reviewed the Phase 5 build and supplied their logo files: a shield mark, a stacked
lockup and a circular badge. Two notes on the site and one on the admin panel followed.

The instruction was *"the shield you have made is not as per logo, use same"* — the prototype
draws its own approximation of the mark, and the real artwork should be used instead. That is a
change to the approved design, so it is logged here with everything else.

| # | Change | Why | What it costs |
|---|---|---|---|
| **A7** | The hand-drawn shield replaced by the client's artwork, in all four places it appears — header lockup, hero, closing strip, footer | The prototype's five `<path>`s are a sketch of the logo, not the logo. The client asked for their own mark. | The visual diff was **89,751 px (0.56%) at 1440** before the reference was updated, and a diff map confirmed those pixels fell **only** in the four brand bands — `y 0–100`, `y 300–700`, `y 9,800–10,200`, `y 10,800–11,000` — on an 11,078px page. Nothing else moved. |
| **A8** | The header **wordmark** stays as the prototype ships it | **Not** a change — a decision not to make one, recorded because it looks like an omission. See below. |

### A7 — how it is kept honest

The mark is raster artwork, so three things had to change around it:

1. **Placement.** The prototype's shield filled 192 × 266 of the emblem's 240 × 280 viewBox. The
   real mark is 692 × 726 — far squarer — so matching its height makes it 253 wide and it spills
   out of the box (which clipped the footer emblem). It is fitted to the same **area** instead:
   220.6 × 231.5, centred. That keeps the emblem's visual weight, stays inside the viewBox at
   every size, and still clears the `r=190` circle it sits inside on the closing strip.
2. **The scan beam.** It was clipped to the two hand-drawn facet paths (`clipPath #embc`). There
   are no paths now, so it is masked by the artwork's own alpha instead (`mask #embm`, in
   `<x-brand-defs />`, with `mask-type: alpha` set from `resources/css/inner-pages.css`). If a
   browser does not support `mask-type`, the mask falls back to luminance and the beam shows only
   over the bright parts of the mark — dimmer, still inside the shield. Cosmetic, not broken.
3. **The reference.** `design/v7/index.html` is read-only, so the swap is applied to the copy the
   harness materialises, through `MARKUP_FIXES` in `tests/visual/_shared.mjs` — the same mechanism
   F1–F4 use. **The diff is back to 0 px at all five widths**, so the guarantee still has teeth:
   any future change outside the brand marks still shows up.

Four numbers (`x="9.7" y="24.3" width="220.6" height="231.5"`) are repeated in
`shield-emblem.blade.php`, `brand-defs.blade.php`, `_shared.mjs` and `build-og-image.mjs`.
`HomepageTest` asserts all four occurrences on the rendered page, so if they drift apart the suite
says so rather than the beam quietly sliding off the mark.

### A8 — why the wordmark did not change

The client's stacked lockup is drawn for **white** backgrounds. Measured against Deep Navy:

| | "UB" | contrast on `#061522` |
|---|---|---|
| Prototype wordmark | gradient, mean `#3AA4E1` | **4.97 : 1** |
| Client's new lockup | flat `#06538C` | **2.31 : 1** |

`#06538C` is also not in the brand palette — the client's own guidelines give Bright Blue as
`#1479D4` — so adopting it would break the "no new colours" rule in `CLAUDE.md` *and* halve the
logotype's contrast on every page. It is the same logotype in the same typeface either way.

The client's artwork is **not discarded**: it ships as `ub-wordmark-light.*` and
`ub-lockup-light.*` for light surfaces (documents, print, anything on white). **If the client
wants their flat wordmark on the dark site regardless, it is a one-line change** in
`scripts/split-prototype-html.mjs` and `tests/visual/_shared.mjs` — worth asking them for a
dark-background version of the lockup first, since they evidently have one (it is inside the
circular badge they supplied).

### A9 — semantic colour in the admin panel

The admin's chrome is entirely on-palette: `primary` and `info` are generated from `#1479D4` and
`#27B8ED`, and Filament's default Zinc grey is replaced by a navy ramp anchored on `#061522`,
`#0C2944` and `#C7D0D8` (see `App\Support\BrandPalette`).

`danger` and `success` are **left as Filament ships them**. They are not brand colour — they are
the difference between "saved" and "this will delete three records" — and a destructive
confirmation rendered in Bright Blue would be a worse admin. They appear only in notifications and
confirmation modals, never in the panel's chrome. Amber was removed entirely: the two places that
used it for "needs attention" now use cyan.

---

## A10 — the pixel diff no longer depends on the database

Until now `npm run visual:diff` worked because the database held exactly the prototype's
placeholders: `[email address]`, `[XX]%`, sixteen dashed "Client logo" boxes. The moment any real
content went in, the homepage legitimately stopped matching `design/v7/index.html` — a real phone
number is a different width from `[phone number]`, and six client logos are not eight dashed boxes.

Left alone, the guarantee would have decayed into "the homepage differs for reasons nobody
checked", which is worse than no guarantee: the next real regression would hide inside the noise.

So the harness now captures the build with `?design-preview=1`. In that mode settings resolve to
the seeder's values — which are the prototype's, by construction — and the client marquee, the
footer's social column and the card covers fall back to what the design draws. `visual:diff` is
back to measuring only CSS and markup, which is the one thing it was ever able to catch.

- `App\Support\DesignPreview` — the mode, and where the placeholder values come from
- `App\Http\Middleware\DesignPreviewMode` — turns it on, **local and testing only**
- `designUrl()` in `tests/visual/_shared.mjs` — used by `diff.mjs` and `pinned.mjs`

It is not a feature flag. In any other environment the query parameter is ignored outright.

**Measured:** homepage carrying real contact details, six client logos and five cover images —
`visual:diff` **0 px at all five widths**, `visual:pinned` **0 px**, behaviour **45/45**, QA
**108 / 108 / 107**.

## A11 — the trust strip, cover art and footer socials became editable

| Was | Is | Falls back to |
|---|---|---|
| 16 hard-coded `Client logo` boxes | `clients` table, CRUD at **Client logos**, logo upload | the dashed boxes, when no client has a logo |
| cover artwork fixed to a hand-drawn SVG | optional **Cover image** upload per field note / case study | the SVG, when the field is empty |
| footer "Follow" column, two links to `#top` | **Site settings → Social profiles** | the prototype's two placeholder links, when none is set |

The fallbacks are the point: every one of these renders the approved design until somebody
deliberately replaces it, so an empty database still produces the page that was signed off.

The cover-image upload is a deliberate relaxation of "there is no image upload in this admin"
(`App\Support\CoverArt`). That rule existed to stop stock photography landing in a design that has
none, and it still holds by default — the SVG is what a record gets unless someone chooses
otherwise. Client logos had to be uploadable regardless: a client's mark is theirs, not ours.

---

## A12 — the pinned services section clipped its own heading on short viewports

Reported from a screenshot: the word **SERVICES** sliced in half horizontally.

**It is the prototype's bug, reproduced exactly** — the same measurements come out of
`design/v7/index.html`. It survived five phases of QA because every harness viewport is 900px or
844px tall and the bug needs under 780.

### What was happening

`.pinon .svc-stick` is `height: min(100vh, 860px)` with `overflow: hidden` and its content
centred. That content is a fixed **772px** tall — measured constant at 1024, 1100, 1280, 1440,
1600, 1920 and 2560px wide. Below a 772px viewport it overflows top and bottom, and the first
thing off the top is the section heading.

The second half is worse than cosmetic. Once an ancestor has clipped the heading away,
IntersectionObserver computes an empty intersection rect, so the reveal observer never fires. The
heading keeps its `.rm` mask but never gets `.in`, so the inner span stays parked at
`translateY(47.25px)` — **permanently** cut, not just for the 900ms of the animation.

Measured at 1920x640 before the fix: `dl rm` with no `in`, `transform: matrix(1, 0, 0, 1, 0,
47.25)`, 1 of 41 reveals never fired, heading 66px above its clipping ancestor.

### The fix

`isPin()` gains a height condition, alongside the width one it already had:

```js
const PIN_MIN_HEIGHT = 780;          // the 772px the content measures, plus slack
st.tall = window.innerHeight >= PIN_MIN_HEIGHT;
const isPin = () => motion && st.scrollable && st.wide && st.tall && !!(pin && stick && track);
```

Below the threshold the section falls back to the unpinned layout — **the same path every
viewport under 1024px already takes**, so nothing new had to be designed. All the pinned CSS is
gated behind `.pinon`, so dropping the class is enough: `overflow` goes back to `visible`, the
heading is whole, and all 41 reveals fire.

The threshold is a constant rather than a measurement, deliberately. Measuring the content at
runtime would read the *unpinned* height once unpinned, flip the decision back, and oscillate.
The pinned content height does not depend on the viewport, so a constant is both correct and
stable.

### Guarded against recurrence

`tests/visual/qa.mjs` now checks, at **1366x640** (a real laptop: 1366x768 less browser chrome)
and **1920x560**:

- the services section is not pinned
- every reveal fired
- the services heading is not sitting above its mask

That is +6 checks per engine. **Chromium 114/114 · Firefox 114/114 · WebKit 113/113.**

### One check was also wrong

`images have dimensions` read `img.width`, the *rendered* width, which is 0 for a
`loading="lazy"` image that has not scrolled into view. It reported the client-logo marquee as
undimensioned purely for being below the fold. It now tests the `width`/`height` **attributes**,
which is what "reserves its space so it cannot shift the layout" actually means.
