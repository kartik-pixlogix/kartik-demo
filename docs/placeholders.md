# Placeholders — everything the client must supply before launch

Nothing in square brackets on this site is real data. This is the complete list, extracted from the
rendered pages rather than from memory, so it does not drift from what is actually published.

**Nothing here was invented.** Where a fact about UB Shield was needed and had not been confirmed —
a certification status, a response time, a client name — it was left in brackets rather than filled
in with something plausible. That is deliberate: a security company's website asserting an
unconfirmed certification is a real problem, not a cosmetic one.

Last extracted against all 21 pages.

---

## 1. Blocking — the site should not go live with these

| Placeholder | Where | What is needed |
|---|---|---|
| `[email address]` | every page (footer, closing strip) | The public contact address. Until it is set, the footer and strip render it as plain text rather than a `mailto:` link. |
| `[phone number]` | every page | The public phone number. Same — no `tel:` link until it is real. |
| `[Street address]` | every page | The Ahmedabad street address. The city line "Ahmedabad, Gujarat, India" is already fixed below it. |
| `[status]` ×2 | homepage trust strip, About | **ISO/IEC 27001** and **SOC 2** status. These are claims about certification — see §4. |
| `[if applicable]` | homepage trust strip, About | **CERT-In empanelment** status. See §4. |
| `[legal review]` | Privacy, Terms, Responsible disclosure | Every section of all three documents is placeholder text and must be replaced by the client's counsel. |
| `[date]` | Privacy, Terms, Responsible disclosure | "Last updated" date for each document. |

All of the first five are editable in the admin at **Site settings** without a developer.

## 2. Statistics

All editable at **Site settings → Statistics**. Type them exactly as they should appear, including
any `+` or `%`.

| Setting | Current | Where |
|---|---|---|
| Assessments delivered | `[XX]+` | homepage hero |
| Critical findings closed in 30 days | `[XX]%` | homepage hero |
| Audits supported to certification | `[XX]` | homepage case files |
| Mean time to respond | `[XX] min` | homepage case files **and** the Managed SOC list |
| Endpoints monitored | `[XX]+` | homepage case files |

Real figures already on the page and **not** placeholders: `24/7`, `8` frameworks, `5` practices,
`© 2026`, "Ahmedabad, Gujarat, India", "Within one business day".

## 3. Case studies

Editable in the admin under **Case studies**. The section's own standfirst says these are
placeholders, so the page is honest as it stands — but it cannot stay this way at launch.

| Placeholder | Needed |
|---|---|
| `[Case study: BFSI client]`, `[mid-size lender]`, `Outcome: [to confirm]` | The real client (or an agreed anonymisation), and the outcome. |
| `[Case study: SaaS client]`, `[B2B SaaS company]`, `Outcome: [to confirm]` | As above. |

Also needed: **client logos**. The trust strip renders 16 dashed "Client logo" boxes (8 real plus 8
`aria-hidden` duplicates that make the marquee loop). Real logos need permission as well as files.

## 4. Claims that need confirming, not just filling

These are the ones worth a conversation rather than a text field. Flagged in the original handoff
and still open.

1. **ISO/IEC 27001 status** — held, in progress, or not pursued? Say which, plainly.
2. **SOC 2 status** — same, and which Type.
3. **CERT-In empanelment** — currently reads "[if applicable]", which is the designer's way of
   saying nobody has confirmed whether UB Shield is empanelled.
4. **The CERT-In six-hour reporting window** — asserted in the Managed SOC list and on the MSS
   service page. Confirm the obligation as it applies to UB Shield's clients before publishing it
   as a service commitment.
5. **MSS response times** (`[Target times to confirm with the client.]`) — these become contractual
   once published.
6. **Engagement durations** — VAPT (`[Typical durations to confirm.]`) and ISO 27001
   (`[Typical timelines to confirm.]`).
7. **DPDPA Rules status** (`[Current status to confirm with the client.]`) on the privacy service
   page. Deliberately not dated, because the date moves.

## 5. Legal pages

Placeholder throughout and marked with a cyan **Legal review required** flag at the top of each.
Specific gaps counsel must close:

| Document | Open items |
|---|---|
| Privacy policy | Lawful basis per purpose · retention period · list of processors · cookie statement (the site currently sets only session and CSRF cookies — confirm before publishing) · Grievance Officer name, contact and response timeline |
| Terms of use | Liability position and cap · ownership of the brand artwork · governing jurisdiction |
| Responsible disclosure | Systems in scope · security contact address · whether a PGP key is published · acknowledgement time · out-of-scope list · whether any bounty is offered |

## 6. Editorial

| Item | Status |
|---|---|
| Three field-note articles | **Draft copy.** The prototype supplied titles only; the bodies were written to exercise the long-form typography and give the client something to edit. They describe general practice and assert nothing unconfirmed about UB Shield, but they need an editorial pass and a byline decision. |
| Article dates | All three show `[DD MMM 2026]`. Set real dates in the admin; `date_label` prints exactly as typed. |
| Service and industry page copy | Written in the prototype's voice from what each practice involves. Worth a read-through by whoever owns positioning. |
| About page | Deliberately short on biography — no founding date, headcount, leadership names or team credentials, because none were confirmed. Add them if wanted. |

## 7. Still unresolved, needs a decision

| # | Item |
|---|---|
| 1 | **LinkedIn and X URLs.** Footer links are placeholders pointing at `#top`. |
| 2 | **Careers.** The footer links it; there is no careers page. Currently points at About. Build one, or drop the link? |
| 3 | **Case study detail pages.** `CaseStudy` has a slug and a body but no route, so "related case file" links back to the homepage section. Not in BUILD.md's page list — do you want `/case-studies/{slug}`? |
| 4 | **Responsible disclosure** was built because the approved footer links to it and a security company is expected to publish one. It is not in BUILD.md's page list — remove the page and the footer link together if it is not wanted. |
| 5 | **Service and industry content** lives in `config/practices.php` and `config/industries.php`, not the admin. Move it into Filament if the client wants to edit it without a developer. |

---

## How to check this list is still accurate

The placeholders are asserted by the test suite, so removing one by accident fails a test:

```sh
php artisan test --filter="placeholder|legal|credential"
```

To re-extract the list from the running site, request every route and match `\[[^\]\[<>]{2,60}\]`.
