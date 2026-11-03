# UB Shield Website — Claude Code Build Brief (Phase 2)

> **How to use**
> 1. Create the project folder and copy the approved design export into it as `design/v7/` (it contains `index.html`, `README.md` and `assets/`).
> 2. Install the skills listed in section 1.
> 3. Open Claude Code in the project folder and say: **"Read `BUILD.md` and `design/v7/README.md`, then start Phase 0."** Save this file as `BUILD.md` in the project root.
> 4. Claude stops at the end of every phase for your review.

---

## 0. Mission and the one rule that matters

Build the production website for **UB Shield**, a cybersecurity and compliance services company (Ahmedabad, India). The approved homepage design is **`design/v7/index.html`**. It is a working prototype with all HTML, CSS and JS in one file, and **`design/v7/README.md`** is its handoff spec covering tokens, type, spacing, sections, motion and accessibility.

**The prototype is the source of truth.** Your job is to make it **pixel-faithful and production-grade**, not to redesign it.
- Do not "improve", restyle, re-space, re-colour or simplify anything visual unless this brief says so.
- Do not swap the fonts, add rounded corners, add shadows or gradients outside the artwork, or add any colour outside the tokens.
- If something in the prototype is broken or impossible to build as drawn, **stop and ask**. Do not guess.
- Port the prototype's CSS values and JS behaviour exactly. Do not write new versions from memory.

---

## 1. Skills to install first (before Phase 0)

Review every `SKILL.md` before installing. Skills can run code.

```bash
# 1. Anthropic frontend-design: keeps design quality and blocks generic AI-looking UI
git clone https://github.com/anthropics/skills.git /tmp/anthropic-skills
cp -r /tmp/anthropic-skills/skills/frontend-design .claude/skills/

# 2. Vercel web-design-guidelines: 100+ rule UI/UX/accessibility audit (used in the QA phase)
git clone https://github.com/vercel-labs/agent-skills.git /tmp/vercel-skills
cp -r /tmp/vercel-skills/skills/web-design-guidelines .claude/skills/

# 3. UI/UX Pro Max: UX rules and a pre-delivery checklist (do NOT let it pick a new style or palette)
git clone https://github.com/nextlevelbuilder/ui-ux-pro-max-skill.git /tmp/uiux
cp -r /tmp/uiux/.claude/skills/ui-ux-pro-max .claude/skills/

# 4. AccessLint: WCAG contrast and a11y audit (plugin)
#   inside Claude Code:  /plugin marketplace add accesslint/claude-marketplace
#                        /plugin install accesslint@accesslint
```

**How to use the skills on this project**
- `frontend-design`: use it for **new pages that are not in the prototype** (service, industry and article templates), so that they extend the V7 language instead of looking like AI templates. Its aesthetic choices are already made here: V7's tokens and fonts win every time.
- `ui-ux-pro-max`: use **only** its UX rules, stack guidelines (`html-tailwind` / Laravel-Blade equivalents) and pre-delivery checklist. **Never** run `--design-system` to generate a new palette or font set.
- `web-design-guidelines` + AccessLint: run both in the QA phase on every Blade view and fix everything they find.

---

## 2. Stack (fixed)

| Layer | Choice |
|---|---|
| Framework | **Laravel, current stable version** (PHP 8.3+) |
| Views | **Blade components** (server-rendered, for SEO and speed) |
| Assets | **Vite**. CSS is ported from the prototype as plain modern CSS split into partials (no Tailwind, so the values stay exact). JS uses **vanilla ES modules** ported from the prototype script (no React, jQuery or GSAP unless a behaviour cannot be done otherwise; ask first). |
| Admin / CMS | **Filament (current stable)** for Insights articles, Case studies, Leads (form submissions) and site settings (contact details, stats) |
| DB | MySQL 8 |
| Mail | Laravel Mail plus a queued job for lead notifications |
| Fonts | **Self-hosted** WOFF2 (Sofia Sans Extra Condensed 700/900, Big Shoulders Display 400, Instrument Sans 500/600/700, Michroma), subset to Latin, with `font-display: swap` and preloads for the hero font |
| Hosting target | A standard Linux VPS (Nginx + PHP-FPM). Keep it deployable with `composer install && npm run build && php artisan migrate`. |

---

## 3. Project structure

```
app/
  Http/Controllers/{HomeController,ServiceController,IndustryController,InsightController,ContactController}.php
  Http/Requests/ReviewRequest.php
  Models/{Lead,Insight,CaseStudy,Setting}.php
  Filament/Resources/...
resources/
  css/
    app.css                 # imports only
    tokens.css              # :root tokens copied exactly from the prototype
    base.css  type.css  layout.css  motion.css  cursor.css
    sections/{header,hero,trust,services,statement,process,soc,industries,frameworks,cases,insights,strip,footer}.css
  js/
    app.js                  # boot: checks reduced motion, imports modules
    modules/{reveal,parallax,pinned-services,cursor,magnetic,count-up,header,mobile-menu,marquee,process-steps,soc-clock,industries-preview,statement,form}.js
  views/
    components/
      layout.blade.php
      ui/{arrow-circle,text-link,button,chip,section-title}.blade.php
      shield-emblem.blade.php        # <x-shield-emblem scan />
      svg-defs.blade.php             # the shared hidden <svg> defs (gSl, gBl, gN, #dots, #sh, #embc ...)
      art/{hero,soc-radar,industry-*,circuit,...}.blade.php   # one per illustration, SVG kept inline
      sections/{header,hero,trust,services,statement,process,soc,industries,frameworks,cases,insights,strip,footer}.blade.php
    pages/{home,service,industry,insights-index,insight,contact,privacy,terms}.blade.php
public/fonts/  public/images/brand/
design/v7/                 # untouched reference; never edit
```

The section, component and hook breakdown follows the "Build notes" in `design/v7/README.md`, translated to Blade components and JS modules.

---

## 4. Phases (stop after each phase and give me a short report with screenshots)

### Phase 0: Read, plan and set up
1. Read `design/v7/README.md` and **all** of `design/v7/index.html`.
2. Make a written inventory: every section, every CSS custom property, every keyframe and animation class, every JS behaviour, and every SVG def. Save it as `docs/inventory.md`.
3. Scaffold Laravel, Vite and Filament, self-host the fonts, and copy the brand PNGs to `public/images/brand/`.
4. Capture **reference screenshots** of the prototype with Playwright at 1440, 1180, 900, 760 and 390px widths (full page, with motion off via the prototype's `motion = false` flag). Save them to `docs/reference/`.
**Stop. Show me the inventory and the plan.**

### Phase 1: Homepage, pixel-faithful
1. Port the tokens, base styles, type and layout exactly.
2. Build each section as a Blade component in prototype order. Port the CSS section by section.
3. Extract the shared SVG defs and every illustration into Blade SVG components, keeping all IDs, gradients and animation classes intact.
4. Port every JS behaviour into modules: load sequence, IntersectionObserver reveals (`.rv` `.rm` `.rs` `.rw` `.brush` `.art.rc` with the `rvon` arming), parallax (`data-px`, `data-hl`, `data-depth`), pinned horizontal services with the counter and progress bar (grid below 1024px), sticky process with the live counter, SOC radar with the IST clock and feed, industries hover preview, framework marquees with scroll-velocity reversal, count-ups, the custom cursor and magnetic buttons (fine pointers only), the header compact/hide/progress, and the mobile circular-reveal menu (Escape closes it, focus is trapped).
5. `prefers-reduced-motion` must disable all motion and the custom cursor, exactly as in the prototype.
6. **Visual diff:** take Playwright screenshots of the build at the same 5 widths and compare them with `docs/reference/` (use pixelmatch or a side-by-side). Fix differences until they are at the sub-pixel or font-rendering level only. Put the diffs in `docs/diff/`.
**Stop. Show me side-by-side screenshots at 1440 and 390.**

### Phase 2: Backend and content
1. **Posture-review form** (`[data-review-form]`):
   - Validation is server-side (FormRequest) and client-side.
   - Spam protection: a honeypot field, a minimum fill time, and `RateLimiter` (5 per hour per IP). Optionally add Cloudflare Turnstile behind an env flag.
   - Store each submission as a `Lead` (name, work email, company, service interest, source page, UTM params, IP hash, user agent).
   - Queue an email notification to the admin address in settings, and send an auto-reply to the visitor.
   - Show inline success and error states in the prototype's style (the button label swaps). The form must also work without JS through a normal POST and redirect.
2. Set up Filament resources for Leads (read-only and export to CSV), Insights (title, slug, excerpt, body, cover art key, category, published_at, SEO fields), Case studies, and Settings (contact email, phone, address, stats `[XX]` values, credential statuses).
3. Homepage stats, credentials, case files and field notes read from the database and settings, with seeders that reproduce the **current placeholder text exactly** (`[XX]`, `[status]`, `Client logo`, and so on).
**Stop. Demo the form flow and the admin.**

### Phase 3: Inner pages (extend V7; use the `frontend-design` skill here)
- **Service page template** (×5: VAPT, Privacy, GRC, Infrastructure & Cloud, MSS): a hero in the V7 style with a condensed all-caps title, then what's included, the process, deliverables, frameworks covered, related case file, FAQ (with accordion and FAQPage schema) and the closing strip.
- **Industry page template** (×6), the **Insights index and article** pages (good long-form typography using Instrument Sans at 19/30), **Contact**, **About**, **Privacy policy** and **Terms** (with placeholder legal text marked `[legal review]`).
- Reuse only existing components and tokens. Any new pattern must use the same squares, hairlines, cyan fills and condensed headlines. **Show me one service page before building the rest.**
- Replace the prototype's on-page anchor links with real routes, and keep the anchors working on the homepage.

### Phase 4: SEO, performance and security hardening
- **SEO:**
  - Per-page title, meta description, canonical, Open Graph and Twitter tags, plus an OG image built from the brand art.
  - `sitemap.xml` (generated), `robots.txt`.
  - JSON-LD: Organization, WebSite, Service (per service page), Article, FAQPage and BreadcrumbList.
  - One `h1` per page and a logical heading order.
- **Performance targets** (Lighthouse on mobile):
  - Performance ≥ 90; Accessibility, Best Practices and SEO = 100.
  - LCP < 2.5s, CLS < 0.05, INP < 200ms.
  - How: preload the hero font, inline critical CSS for above-the-fold content if needed, lazy-init offscreen JS modules, `content-visibility` on long sections, compressed and correctly sized images (AVIF/WebP), and pause infinite animations when offscreen.
- **Security** (this is a security company, so the site must pass its own audit):
  - Strict security headers via middleware:
    - CSP with nonces, no `unsafe-inline` for scripts.
    - HSTS.
    - `X-Content-Type-Options`.
    - `Referrer-Policy: strict-origin-when-cross-origin`.
    - `Permissions-Policy`.
    - `frame-ancestors 'none'`.
  - Secure session cookies and CSRF on every form.
  - Filament: behind `/admin`, with 2FA, login rate limiting and a strong password policy.
  - Production config: `APP_DEBUG=false` in prod, no stack traces, `.env` never committed, `composer audit` and `npm audit` clean.
  - Target an **A+ on securityheaders.com** and a clean Mozilla Observatory scan.

### Phase 5: QA (use the skills here)
1. Run `/web-design-guidelines` on `resources/views/**/*.blade.php` and fix every finding.
2. Run the AccessLint reviewer and contrast checks. Keep to the brand contrast rules: Bright Blue `#1479D4` is never body text on navy, and text on cyan is navy.
3. Check keyboard-only navigation through the header, dropdown, mobile menu, pinned services, form and footer. Focus rings must be visible everywhere.
4. Check with a screen reader: decorative SVGs are `aria-hidden`, marquees have an accessible static equivalent, and the live SOC feed is not announced (`aria-live="off"`).
5. Test cross-browser in Chrome, Safari (including iOS Safari for the sticky and pinned sections) and Firefox, at 1440, 1180, 900, 760 and 390px.
6. Run the UI/UX Pro Max pre-delivery checklist.
7. Repeat the visual diff of the homepage against `docs/reference/`.
**Stop. Give me the Lighthouse, securityheaders and a11y results, plus the list of remaining placeholders.**

---

## 5. Hard rules (apply in every phase)

- **Tokens only:** `#061522`, `#0C2944`, `#1479D4`, `#27B8ED`, `#F4F6F8` and `#C7D0D8`, plus the `--line` hairline. No new colours.
- **Shapes:** square corners everywhere. Circles only for arrow buttons, the header action and the title dot. No shadows or gradients outside the artwork.
- **Fonts:** only the four V7 fonts. No Inter, Roboto, Poppins or system-ui fallbacks visible on screen.
- **No content invention:** keep every `[placeholder]` as is. Never invent client names, statistics, certifications, awards, testimonials, addresses or phone numbers. Flag for client confirmation the CERT-In 6-hour reporting window and each credential's actual status.
- **No stock imagery.** No emoji. No new icon libraries; reuse the prototype's SVG arrows and glyphs.
- **Keep `design/v7/` read-only.**
- **Commits:** commit at the end of each phase with a clear message. Never commit `.env`.
- **When unsure, ask.** A short question beats a wrong guess on a client site.

## 6. Definition of done

- [ ] The homepage matches the V7 prototype at all 5 widths (diffs in `docs/diff/`), with every motion behaviour working and reduced-motion respected.
- [ ] The 5 service pages, 6 industry pages, Insights, Contact, About and the legal pages are built in the V7 language.
- [ ] The form stores leads, emails the admin, auto-replies to the visitor, is protected against spam, and works without JS.
- [ ] Filament admin covers Leads, Insights, Case studies and Settings, with 2FA.
- [ ] Lighthouse mobile scores: Performance ≥ 90 and the other three at 100. securityheaders.com A+.
- [ ] `docs/placeholders.md` lists every remaining placeholder for the client.
- [ ] `README.md` has setup, environment variables, deployment and admin-user creation steps.
