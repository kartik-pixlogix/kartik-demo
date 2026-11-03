# UB Shield website — V7 build handoff

This folder is the approved **V7 homepage** as a working, self-contained prototype. Open `index.html` in a browser (it needs internet only for Google Fonts). Use it as the visual and behavioural source of truth when building the production site.

## Files

| Path | What it is |
|---|---|
| `index.html` | Full homepage: all HTML, CSS (in `<style>`) and vanilla JS (bottom `<script>`). No build step, no dependencies. |
| `assets/ub-monogram.png` | UB monogram, transparent, for dark backgrounds (header, favicon). |
| `assets/ub-wordmark.png` | "UB SHIELD" wordmark, transparent, for dark backgrounds (header). |
| `assets/ub-lockup.png` | Monogram + wordmark + tagline lockup, transparent, dark backgrounds. |

The PNGs were cut from the supplied logo artwork. Replace them with the client's vector logo files when available.

The **shield emblem** in the hero, contact block and footer is an inline SVG drawn for this site (silver left facet, blue right facet, U and B cut-outs). Extract it into one reusable component (`<ShieldEmblem scan />`).

## Brand tokens

| Token | Value | Use |
|---|---|---|
| `--paper` | `#061522` Deep Navy | Page background |
| `--ink` | `#F4F6F8` Silver White | All text, rules, light fills (buttons, arrow circles) |
| `--acid` | `#27B8ED` Cyan | The one accent, always a **fill** (chips, header action, highlighter bars, title dots, CTA block). Text on it is navy. |
| Bright Blue | `#1479D4` | Arrows inside light circles/buttons, brush stroke, artwork |
| Midnight | `#0C2944` | Artwork panels, depth |
| Cool Silver | `#C7D0D8` | Artwork lines |
| `--line` | `rgba(199,208,216,.22)` | Hairlines between stat columns only |

Rules: square corners everywhere; circles only for arrow buttons, the header action and the title dot. No shadows or gradients outside artwork. The SOC section is the one inverted block (Silver White background, navy text).

**Type** (Google Fonts):

- **Sofia Sans Extra Condensed** 900/700 for display headlines (all caps).
  - Hero: 224px with 160px line height.
  - Section titles: 56px with 45px line height.
- **Big Shoulders Display** 400 for the single statement ("We stay until it's fixed.").
- **Instrument Sans** 500/600/700 for everything else.
  - Lead: 22px.
  - Body: 19px with 30px line height.
  - Labels: 18 / 16 / 14px, uppercase.
- **Michroma** for the footer "UB SHIELD" wordmark.

The hero headline only fits its column with Sofia Sans Extra Condensed loaded. Self-host the fonts in production.

Spacing: 1440px design width, 64px side margins, 1312px container, 4px steps (12 / 16 / 24 / 32 / 48 / 64 / 80). Breakpoints: 1180px, 900px (mobile menu), 760px (single column).

## Page sections (in order)

1. **Header**
   - Sticky. Compacts after 40px of scroll and hides on scroll-down after 500px.
   - Scroll progress bar along its bottom edge.
   - Services dropdown with the 5 pillars.
   - "Talk to an expert" with a cyan action circle.
   - Mobile: cyan circular-reveal full-screen menu (Escape closes it).
2. **Hero**
   - Chip, then the stacked headline TESTED. / FIXED. / WATCHED. / PROVEN. (the "O" is a circle holding the arrow).
   - Lead, primary button and a text link.
   - Right side: layered cyber artwork (dot grid, radar rings, WAF/SIEM/EDR/IAM nodes, circuit traces, shield emblem with scan beam).
   - Stats row of 4.
3. **Trust strip**: credentials line plus an infinite marquee of client-logo placeholders.
4. **Services**
   - 5 cards: VAPT, Privacy, GRC, Infra & Cloud, MSS.
   - Desktop: the section pins while the cards scroll sideways, with a 01 | 05 counter and a progress bar.
   - Below 1024px: a plain grid.
5. **Statement**: "We stay until it's fixed." Words light up in turn and a blue brush stroke paints in.
6. **How we work**
   - Sticky left column with a live 01 | 04 counter.
   - 4 steps (Assess, Remediate, Monitor, Certify); the step nearest the viewport centre becomes active.
7. **Managed SOC**
   - Inverted block: radar sweep, blips and a live IST clock.
   - Scrolling incident feed and a 4-row facts list.
8. **Industries**
   - 6 rows. Hover fills the row cyan, and a floating artwork preview follows the cursor.
9. **Frameworks**: two marquee rows of framework names that speed up and reverse with scroll velocity, plus 4 notes.
10. **Case files**: 2 case cards and a second stats row.
11. **Field notes**: 3 article cards and a cyan "All resources" tile.
12. **Closing strip**: cyan CTA block with the posture-review form, a circuit/shield artwork panel, and contact lines.
13. **Footer**: 4 link columns, the shield emblem with the large Michroma wordmark, and the tagline line.

## Motion and interaction spec (all in the bottom `<script>`)

**Load**
- Headline lines rise from masks, staggered by 80ms.
- The artwork scales in.
- The chip wipes in.

**Scroll reveals**
- An IntersectionObserver adds `.in` to these elements:
  - `.rv`: fade up.
  - `.rm`: heading mask-up.
  - `.rs`: staggered children.
  - `.rw`: word fade.
  - `.brush`: stroke paint.
  - `.art.rc`: image opens from a slit.
- Reveals only arm when the page actually scrolls (the `rvon` class on the root).

**Parallax**
- `[data-px]` elements move by (distance from viewport centre) × the factor.
- Hero headline lines (`[data-hl]`) slide apart horizontally.
- Hero layers (`[data-depth]`) follow the cursor at different depths.

**Pinned services**: section height = sticky height + horizontal overflow; the track's translateX follows scroll progress.

**Cursor** (fine pointers only; the native cursor stays visible)
- A follower ring turns into a cyan "go" circle on links.
- It shows OPEN or VIEW on cards and industry rows, and shrinks over form fields.
- Elements with `.mag` (buttons) lean toward the pointer.

**Number count-up**: `[data-count]` animates when in view.

**SVG illustrations** use these CSS animation classes:
- `.flow`: dashes moving along a path.
- `.pl2`: pulse.
- `.scanE`, `.scanY`, `.scanX`: scan lines.
- `.redact`: data-masking bars.
- `.tick`: checkmarks drawing in.
- `.hunt`: the pentest crosshair moving between targets.
- `.spin`, `.knob`: rotation and the toggle knob.

**Accessibility**
- `prefers-reduced-motion` turns off all motion and the custom cursor.
- Focus rings are visible.
- Decorative SVGs are `aria-hidden`.
- Form fields have labels.

To turn motion off for testing, set `motion = false` at the top of the script.

## Build notes

- Suggested split if building in React: one component per section, plus shared `ArrowCircle`, `TextLink`, `Button`, `Chip`, `SectionTitle`, `ShieldEmblem`, `CyberArt/*` (one per illustration), and hooks such as `useScrollReveal`, `useParallax`, `usePinnedHorizontal`, `useCursor`.
- The illustrations are hand-written inline SVG sharing defs (gradients `gSl`/`gBl`/`gN`, `#dots` pattern, `#sh` shield path, `#embc` clip path) in the hidden `<svg>` at the top of `<body>`. Keep them as SVG components so they stay crisp and animatable.
- **Form**: `[data-review-form]` only swaps the button label. Wire it to a real endpoint (e.g. a Laravel route) with validation and spam protection.
- **Links**: nav and card links point to on-page anchors. Replace them with real service, industry and article URLs.

### Placeholders to fill before launch

Everything in square brackets is a placeholder, not real data:
- `[XX]` statistics.
- `[status]` for ISO 27001, SOC 2 and CERT-In.
- `Client logo` boxes.
- Case-study names and outcomes.
- Article dates.
- `[email address]`, `[phone number]` and `[Street address]`.

Confirm these two facts with the client:
- the CERT-In six-hour incident-reporting window;
- each credential's actual status.
