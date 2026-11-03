# How to review the site

The site is running now at **http://localhost:8000**

If it is not responding, restart it:

```powershell
cd C:\Users\HP\Documents\ubshield-v7
.\scripts\mysql-start.ps1              # MySQL is not a Windows service; start it after a reboot
.\bin\php.cmd artisan serve            # the site
.\bin\php.cmd artisan queue:work       # second terminal — needed for the form's emails
```

> `artisan serve` is single-threaded. It is fine for one person clicking around; it just means the
> Lighthouse numbers measured against it understate production. See `docs/phase-4-results.md`.

---

## Every page

| | |
|---|---|
| Homepage | http://localhost:8000 |
| **Services** | [Offensive Security / VAPT](http://localhost:8000/services/offensive-security) · [Data Privacy](http://localhost:8000/services/data-privacy) · [GRC](http://localhost:8000/services/governance-risk-compliance) · [Infrastructure & Cloud](http://localhost:8000/services/infrastructure-cloud) · [Managed Security Services](http://localhost:8000/services/managed-security-services) |
| **Industries** | [Banking & finance](http://localhost:8000/industries/banking-finance) · [Healthcare](http://localhost:8000/industries/healthcare) · [SaaS & IT](http://localhost:8000/industries/saas-it) · [Manufacturing](http://localhost:8000/industries/manufacturing) · [Education](http://localhost:8000/industries/education) · [E-commerce](http://localhost:8000/industries/e-commerce) |
| **Field notes** | [Index](http://localhost:8000/insights) · [DPDPA 2023](http://localhost:8000/insights/dpdpa-2023-consent-flows) · [SEBI CSCRF](http://localhost:8000/insights/sebi-cscrf-control-by-control) · [Cloud misconfigurations](http://localhost:8000/insights/five-cloud-misconfigurations-aws) |
| **Other** | [Contact](http://localhost:8000/contact) · [About](http://localhost:8000/about) |
| **Legal** | [Privacy](http://localhost:8000/privacy) · [Terms](http://localhost:8000/terms) · [Responsible disclosure](http://localhost:8000/responsible-disclosure) |
| **Machine-readable** | [sitemap.xml](http://localhost:8000/sitemap.xml) · [robots.txt](http://localhost:8000/robots.txt) |
| **Admin** | http://localhost:8000/admin |

---

## 1. The homepage against the prototype (10 min)

This is the thing to check hardest, because it is the one hard guarantee.

Open the prototype and the build side by side:

- prototype: `design/v7/index.html` — double-click it, it needs no server
- build: http://localhost:8000

They should be indistinguishable. Resize both to **1440, 1180, 900, 760 and 390** and compare.

Things worth exercising on the build:
- Scroll into **Services** — the section pins and the cards track sideways, counter `01 → 05`.
- Keep scrolling — the four **How we work** steps light up one at a time.
- Hover the **Industries** rows — the row fills cyan and artwork follows the cursor.
- Scroll the **Frameworks** marquees up and down — they change speed and reverse.
- Narrow below 900px — the nav becomes the cyan circular-reveal menu.

**Two deliberate differences from the prototype**, both logged in `docs/prototype-fixes.md`:
1. The SOC incident feed. In the prototype its rows overlap into unreadable text — a CSS selector
   bug. Fixed with your approval in Phase 0.
2. Two contrast values nudged for the Accessibility 100 target: the faint `04` in the `01 | 04`
   counter, and how dim the statement words start before they fade in. **Both revert in one line
   if you prefer the original** — see §5 below.

## 2. The form (5 min)

On [Contact](http://localhost:8000/contact) or the bottom of any page.

- Submit it empty → the browser blocks it (that is the client-side layer).
- Enter `a` / a valid email / `b` → the server rejects it and errors appear inline without a reload.
- Fill it properly and submit → the button label becomes "Request received…".
- **Without JavaScript**: disable JS in devtools and submit again. It still works — the page comes
  back with the form replaced by the success message.
- Check the mail: `storage\logs\laravel.log` holds both messages (an admin notification with
  Reply-To set to the visitor, and the visitor's auto-reply). Mail is queued, so the
  `queue:work` terminal must be running.
- The lead appears in the admin under **Leads**.

## 3. The admin (10 min)

http://localhost:8000/admin — `admin@ubshield.test` / `ChangeMe!2026`

**Two-factor is mandatory**, so the first login sends you to setup. Scan the QR with any
authenticator (Google Authenticator, 1Password, Authy) and enter the code. Save the recovery codes.

> If you would rather skip 2FA while reviewing:
> `.\bin\php.cmd artisan tinker --execute="App\Models\User::first()->update(['app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);"`
> then open `/admin` — you will be asked for a code; use that secret in your authenticator, or set
> `isRequired: false` in `app/Providers/Filament/AdminPanelProvider.php` temporarily.

Then look at:
- **Leads** — read-only by design (no create, no edit; a lead is a record of what someone sent).
  Try **Export to CSV**.
- **Insights** and **Case studies** — editable, reorderable by dragging. Cover artwork is a
  dropdown of the hand-drawn SVGs; there is no image upload anywhere, deliberately.
- **Site settings** — the ⚠ marks every value still a placeholder. Change **Assessments
  delivered** to something real, save, and reload the homepage: the hero updates. Put a real email
  in and the footer turns it into a `mailto:` link.

## 4. Accessibility and keyboard (5 min)

- Press **Tab** from the top of the homepage. First stop is a cyan "Skip to content".
- Keep tabbing — every stop should have a visible focus ring, including the Services dropdown,
  which opens on focus.
- Narrow to 390px, Tab to the menu button, Enter. Focus moves into the menu, Tab cycles inside it,
  Escape closes it and returns focus to the button.
- Turn on **Settings → Accessibility → Reduce motion** (or devtools → Rendering →
  `prefers-reduced-motion`) and reload. All motion stops and the custom cursor disappears.

## 5. If you want anything changed

The two contrast tweaks revert independently. In `scripts/split-prototype-css.mjs`, delete the
relevant line from the `FIXES` array and do the same in `tests/visual/_shared.mjs`, then:

```powershell
node scripts/split-prototype-css.mjs
npm run build
npm run visual:reference    # re-capture the baseline
```

- **F2** = `--faint` (the de-emphasised counter total)
- **F4** = the word-fade reveal's starting opacity

---

## Running the checks yourself

```powershell
.\bin\php.cmd artisan test   # 140 tests
npm run qa                   # all browser suites: a11y, keyboard, cross-browser, pixel diff
npm run visual:diff          # just the pixel diff — must stay at 0
```

## What is still open

`docs/placeholders.md` is the full list — 26 placeholders, and seven claims that need a
conversation rather than a text field (certification statuses, the CERT-In six-hour window, MSS
response times).

Two accessibility findings need a design decision, in `docs/phase-5-qa.md` §5:
1. No in-page pause control for the continuous animation. `prefers-reduced-motion` turns it all
   off, but WCAG 2.2.2 wants a control on the page.
2. The three footer legal links sit about 20px apart at mobile widths, under the 24px guideline.

Three things cannot be checked from this machine: **securityheaders.com** and **Mozilla
Observatory** need a public URL, a real **iPhone** should confirm the sticky and pinned sections,
and a pass with a real **screen reader** is worth the hour before launch.
