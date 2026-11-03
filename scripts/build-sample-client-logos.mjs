/* Renders placeholder client logos for the trust strip.
 *
 * WHY THESE EXIST
 *
 * The prototype draws sixteen dashed `Client logo` boxes. They are
 * placeholders, and CLAUDE.md forbids inventing client names — so the real
 * marquee stays empty until the client supplies their own logos through the
 * admin. These files are review scaffolding: they show what the strip looks
 * like carrying actual artwork, and every row that uses one is flagged
 * `is_sample` so it cannot quietly reach production.
 *
 * The names are the placeholder set already on unboundsolutions.in, the
 * reference the client pointed at, rather than anything made up here.
 *
 * Drawn, not photographed: a mark plus a wordmark in the site's own body
 * face, Cool Silver on transparent, sized for the 200x72 .lg box at 2x.
 *
 *   npm i --no-save playwright   # already a devDependency
 *   php artisan serve            # fonts are served from /fonts
 *   node scripts/build-sample-client-logos.mjs
 */
import { chromium } from 'playwright';
import { readFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const OUT = join(ROOT, 'storage', 'app', 'public', 'clients');
mkdirSync(OUT, { recursive: true });

const TEXT = 'data:font/woff2;base64,'
    + readFileSync(join(ROOT, 'public/fonts/instrument-sans-var.woff2')).toString('base64');

/** Each mark is a square SVG glyph built from the brand's own vocabulary:
 *  straight edges, one diagonal, no curves except the one ring. */
const MARKS = {
    bars: '<rect x="2" y="12" width="6" height="18"/><rect x="11" y="4" width="6" height="26"/><rect x="20" y="17" width="6" height="13"/>',
    chevron: '<path d="M4 6h9l11 10-11 10H4l11-10Z"/>',
    ring: '<path d="M16 3a13 13 0 1 0 13 13h-6a7 7 0 1 1-7-7Z"/>',
    grid: '<rect x="3" y="3" width="10" height="10"/><rect x="17" y="3" width="10" height="10"/><rect x="3" y="17" width="10" height="10"/><rect x="17" y="17" width="10" height="10"/>',
    arrow: '<path d="M3 26 17 5l3 9 9 12h-9l-3-7-5 7Z"/>',
    stack: '<path d="M16 3 29 10 16 17 3 10Z"/><path d="m3 16 13 7 13-7v5l-13 7-13-7Z"/>',
};

/** [slug, display name, industry, mark] — the reference site's own sample set. */
const CLIENTS = [
    ['national-exports', 'National Exports', 'Seafood export', 'stack'],
    ['meridian-health', 'Meridian Health', 'Healthcare', 'ring'],
    ['verde-realty', 'Verde Realty', 'Real estate', 'chevron'],
    ['nimbus-cart', 'Nimbus Cart', 'D2C e-commerce', 'arrow'],
    ['brightpath-academy', 'BrightPath Academy', 'Education', 'bars'],
    ['skyline-logistics', 'Skyline Logistics', 'Logistics', 'grid'],
];

const SCALE = 2;
const W = 200;
const H = 72;

const page = (name, mark) => `<!doctype html><meta charset="utf-8"><style>
@font-face{font-family:'Instrument Sans';src:url('${TEXT}') format('woff2');font-weight:500 700}
*{margin:0;box-sizing:border-box}
html,body{background:transparent}
body{width:${W}px;height:${H}px;display:flex;align-items:center;justify-content:center;gap:10px;
     font-family:'Instrument Sans',sans-serif;color:#C7D0D8;padding:0 14px}
svg{flex:none;fill:#C7D0D8}
span{font-weight:600;font-size:15px;line-height:18px;letter-spacing:.01em;text-transform:uppercase;
     white-space:normal;max-width:126px}
</style>
<svg width="26" height="26" viewBox="0 0 32 32" aria-hidden="true">${MARKS[mark]}</svg>
<span>${name}</span>`;

const browser = await chromium.launch();
const ctx = await browser.newContext({
    viewport: { width: W, height: H },
    deviceScaleFactor: SCALE,
});
const p = await ctx.newPage();

for (const [slug, name, , mark] of CLIENTS) {
    await p.setContent(page(name, mark));
    await p.evaluate(() => document.fonts.ready);
    await p.screenshot({ path: join(OUT, `${slug}.png`), omitBackground: true });
    console.log(`  ${slug.padEnd(20)} ${W * SCALE}x${H * SCALE}  "${name}"`);
}

await browser.close();
console.log(`\n${CLIENTS.length} sample logos written to storage/app/public/clients/`);
console.log('Seeded by ClientSeeder, every row flagged is_sample.');
