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
