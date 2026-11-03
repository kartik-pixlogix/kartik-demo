/* Renders the default Open Graph card (1200x630) from the brand artwork.
 *
 * Built rather than drawn: it uses the same shield emblem, the same tokens and
 * the same self-hosted fonts as the site, so it cannot drift from the design.
 * Re-run after a brand change.
 *
 *   php artisan serve          # the fonts are served from /fonts
 *   node scripts/build-og-image.mjs
 */
import { chromium } from 'playwright';
import { readFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const OUT = join(ROOT, 'public', 'images', 'brand');
mkdirSync(OUT, { recursive: true });

const b64 = (p, mime) => `data:${mime};base64,` + readFileSync(join(ROOT, p)).toString('base64');

const DISP = b64('public/fonts/sofia-sans-extra-condensed-var.woff2', 'font/woff2');
const TEXT = b64('public/fonts/instrument-sans-var.woff2', 'font/woff2');

// The shield emblem, identical to resources/views/components/shield-emblem.blade.php
const EMBLEM = `
<svg viewBox="0 0 240 280" width="300" height="350" aria-hidden="true">
  <defs>
    <linearGradient id="gSl" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffffff"/><stop offset=".5" stop-color="#C7D0D8"/><stop offset="1" stop-color="#7f8d9b"/></linearGradient>
    <linearGradient id="gBl" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8fdcfa"/><stop offset=".35" stop-color="#27B8ED"/><stop offset=".75" stop-color="#1479D4"/><stop offset="1" stop-color="#0b4f99"/></linearGradient>
  </defs>
  <path d="M112 10 24 40v106c0 50 38 88 88 120Z" fill="url(#gSl)"/>
  <path d="M128 10l88 30v106c0 50-38 88-88 120Z" fill="url(#gBl)"/>
  <path d="M62 72v76c0 28 18 48 38 56" fill="none" stroke="#061522" stroke-width="15"/>
  <path d="M128 82l52 18-52 24Zm0 58 56 20-56 28Z" fill="#061522"/>
  <path d="M112 10 24 40v34l88-32Z" fill="#ffffff" opacity=".35"/>
</svg>`;

const html = `<!doctype html><meta charset="utf-8"><style>
@font-face{font-family:'Sofia Sans Extra Condensed';src:url('${DISP}') format('woff2');font-weight:700 900}
@font-face{font-family:'Instrument Sans';src:url('${TEXT}') format('woff2');font-weight:500 700}
*{box-sizing:border-box;margin:0}
body{width:1200px;height:630px;overflow:hidden;background:#061522;
     background-image:radial-gradient(ellipse 55% 70% at 78% 45%, rgba(20,121,212,.28), transparent 70%);
     font-family:'Instrument Sans',sans-serif;color:#F4F6F8;
     display:grid;grid-template-columns:1fr auto;align-items:center;gap:48px;padding:64px}
.dots{position:absolute;inset:0;background-image:radial-gradient(#27B8ED .9px, transparent .9px);
      background-size:20px 20px;opacity:.22}
.l{position:relative}
.chip{display:inline-block;height:40px;line-height:40px;padding:0 18px;background:#27B8ED;color:#061522;
      font-weight:600;font-size:20px;text-transform:uppercase}
h1{font-family:'Sofia Sans Extra Condensed',sans-serif;font-weight:900;font-size:128px;line-height:96px;
   text-transform:uppercase;letter-spacing:-.01em;margin:28px 0 0}
.dot{display:inline-block;width:18px;height:18px;border-radius:50%;background:#27B8ED;margin-left:8px}
p{font-size:24px;line-height:32px;max-width:36ch;margin-top:28px;color:#C7D0D8}
.r{position:relative;filter:drop-shadow(0 24px 40px rgba(0,0,0,.5)) drop-shadow(0 0 40px rgba(20,121,212,.35))}
.bar{position:absolute;left:0;right:0;bottom:0;height:8px;background:linear-gradient(90deg,#1479D4,#27B8ED)}
</style>
<div class="dots"></div>
<div class="l">
  <span class="chip">Cybersecurity &amp; compliance</span>
  <h1>Tested.<br>Fixed.<br>Watched.<br>Proven.<span class="dot"></span></h1>
  <p>UB Shield &mdash; Ahmedabad, India</p>
</div>
<div class="r">${EMBLEM}</div>
<div class="bar"></div>`;

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1200, height: 630 }, deviceScaleFactor: 1 });
await page.setContent(html);
await page.evaluate(() => document.fonts.ready);
await page.waitForTimeout(500);
await page.screenshot({ path: join(OUT, 'og-default.png') });
await browser.close();

console.log('public/images/brand/og-default.png written (1200x630)');
