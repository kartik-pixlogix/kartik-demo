/* Renders sample cover images for the field notes and case studies.
 *
 * WHY THESE ARE DRAWN AND NOT PHOTOGRAPHED
 *
 * BUILD.md rules out stock imagery, and a security consultancy illustrated
 * with hooded-figure photographs would be the exact cliche the design avoids.
 * These are built from the same vocabulary as the rest of the site — Deep
 * Navy, the dot grid, cyan hairlines, the display face, one diagonal — so a
 * card carrying one still belongs to the page it sits on.
 *
 * They are review scaffolding, so the client can see how the cards read with
 * images instead of the hand-drawn SVGs. Clearing the Cover image field in
 * the admin puts the SVG back.
 *
 *   php artisan serve      # the fonts are served from /fonts
 *   node scripts/build-sample-covers.mjs
 */
import { chromium } from 'playwright';
import { readFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const OUT = join(ROOT, 'storage', 'app', 'public', 'covers');
mkdirSync(OUT, { recursive: true });

const b64 = (p, mime) => `data:${mime};base64,` + readFileSync(join(ROOT, p)).toString('base64');
const DISP = b64('public/fonts/sofia-sans-extra-condensed-var.woff2', 'font/woff2');
const TEXT = b64('public/fonts/instrument-sans-var.woff2', 'font/woff2');

/* 5:4, matching the .art frame, at 2x for retina. */
const W = 1000;
const H = 800;

/** [slug, kicker, headline, accent, motif] */
const COVERS = [
    ['dpdpa-consent', 'Advisory', 'Consent\nis a log,\nnot a flag', 'acid', 'ledger'],
    ['sebi-cscrf', 'Guide', 'Control\nby\ncontrol', 'blue', 'matrix'],
    ['cloud-misconfig', 'Field note', 'Public\nby\ndefault', 'acid', 'buckets'],
    ['case-exchange', 'Case file', 'Trading\nfloor\nhardened', 'blue', 'waves'],
    ['case-health', 'Case file', 'Patient\ndata,\nmapped', 'acid', 'nodes'],
];

/** Motifs drawn in SVG, on a 1000x800 canvas. */
const MOTIFS = {
    ledger: `
    <g stroke="var(--a)" stroke-width="3" fill="none" opacity=".85">
      ${[0, 1, 2, 3].map((i) => `<rect x="${620}" y="${150 + i * 110}" width="260" height="70"/>`).join('')}
    </g>
    <g fill="var(--a)">
      ${[0, 1, 3].map((i) => `<rect x="${838}" y="${168 + i * 110}" width="24" height="34"/>`).join('')}
    </g>`,
    matrix: `
    <g stroke="var(--a)" stroke-width="3" fill="none">
      ${Array.from({ length: 12 }, (_, i) => {
          const c = i % 4, r = (i / 4) | 0;
          return `<rect x="${620 + c * 72}" y="${190 + r * 72}" width="54" height="54"/>`;
      }).join('')}
    </g>
    <g stroke="var(--a)" stroke-width="5" fill="none" stroke-linecap="square">
      ${[0, 1, 2, 4, 5, 8].map((i) => {
          const c = i % 4, r = (i / 4) | 0;
          return `<path d="M${632 + c * 72} ${218 + r * 72}l10 11 20-24"/>`;
      }).join('')}
    </g>`,
    buckets: `
    <g fill="none" stroke="var(--a)" stroke-width="3">
      <ellipse cx="760" cy="200" rx="130" ry="36"/>
      <path d="M630 200v240c0 20 58 36 130 36s130-16 130-36V200"/>
      <ellipse cx="760" cy="440" rx="130" ry="36" opacity=".5"/>
    </g>
    <path d="M700 530h120l-60 90Z" fill="var(--a)"/>`,
    waves: `
    <g fill="none" stroke="var(--a)" stroke-width="3">
      ${[0, 1, 2, 3, 4].map((i) =>
          `<path d="M600 ${220 + i * 70}h70l40 -40 50 80 40 -40h100" opacity="${1 - i * 0.16}"/>`).join('')}
    </g>`,
    nodes: `
    <g stroke="var(--a)" stroke-width="2.5" fill="none" opacity=".7">
      <path d="M660 250 790 330 660 480 880 400 790 330"/>
    </g>
    <g fill="var(--a)">
      ${[[660, 250], [790, 330], [660, 480], [880, 400]].map(([x, y]) =>
          `<rect x="${x - 11}" y="${y - 11}" width="22" height="22"/>`).join('')}
    </g>
    <circle cx="770" cy="365" r="165" fill="none" stroke="var(--a)" stroke-width="1.5" opacity=".35"/>`,
};

const page = (kicker, headline, accent, motif) => `<!doctype html><meta charset="utf-8"><style>
@font-face{font-family:'Sofia Sans Extra Condensed';src:url('${DISP}') format('woff2');font-weight:700 900}
@font-face{font-family:'Instrument Sans';src:url('${TEXT}') format('woff2');font-weight:500 700}
*{margin:0;box-sizing:border-box}
:root{--a:${accent === 'acid' ? '#27B8ED' : '#1479D4'}}
body{width:${W}px;height:${H}px;overflow:hidden;position:relative;background:#061522;
     font-family:'Instrument Sans',sans-serif;color:#F4F6F8}
.dots{position:absolute;inset:0;background-image:radial-gradient(#27B8ED 2px,transparent 2px);
      background-size:34px 34px;opacity:.16}
.glow{position:absolute;inset:0;background:radial-gradient(ellipse 60% 70% at 76% 46%,
      rgba(20,121,212,.30),transparent 68%)}
svg{position:absolute;inset:0}
.t{position:absolute;left:64px;top:64px;right:64px}
.kick{display:inline-block;height:44px;line-height:44px;padding:0 20px;background:var(--a);color:#061522;
      font-weight:600;font-size:21px;letter-spacing:.1em;text-transform:uppercase}
h1{font-family:'Sofia Sans Extra Condensed',sans-serif;font-weight:900;font-size:124px;line-height:98px;
   text-transform:uppercase;letter-spacing:-.005em;margin-top:40px;white-space:pre-line}
.rule{position:absolute;left:64px;bottom:64px;width:150px;height:5px;background:var(--a)}
.slash{position:absolute;right:-120px;top:-180px;width:3px;height:900px;background:
       linear-gradient(180deg,transparent,#C7D0D8,transparent);transform:rotate(26deg);opacity:.5}
</style>
<div class="dots"></div><div class="glow"></div>
<svg viewBox="0 0 ${W} ${H}" aria-hidden="true">${MOTIFS[motif]}</svg>
<div class="slash"></div>
<div class="t"><span class="kick">${kicker}</span><h1>${headline}</h1></div>
<div class="rule"></div>`;

const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport: { width: W, height: H }, deviceScaleFactor: 1 });
const p = await ctx.newPage();

for (const [slug, kicker, headline, accent, motif] of COVERS) {
    await p.setContent(page(kicker, headline, accent, motif));
    await p.evaluate(() => document.fonts.ready);
    await p.screenshot({ path: join(OUT, `${slug}.png`) });
    console.log(`  ${slug.padEnd(20)} ${W}x${H}  ${kicker} / ${motif}`);
}

await browser.close();
console.log(`\n${COVERS.length} sample covers written to storage/app/public/covers/`);
console.log('Attached by CoverImageSeeder. Clear the field in the admin to get the SVG back.');
