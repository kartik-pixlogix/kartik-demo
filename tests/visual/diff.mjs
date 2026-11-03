/* Full-page visual diff of the running build against docs/reference/.
 *
 * Start the app first (php artisan serve), then:
 *   node tests/visual/diff.mjs
 *
 * Exits non-zero if any width differs, so it can gate a commit or CI run.
 */
import { chromium } from 'playwright';
import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import path from 'node:path';
import { REF, DIFF, WIDTHS, BASE_URL, FIXED_TIME, forceMotionOff } from './_shared.mjs';

const BUILD = path.join(DIFF, 'build');
mkdirSync(BUILD, { recursive: true });

const browser = await chromium.launch();
const rows = [];

for (const w of WIDTHS) {
    const ctx = await browser.newContext({
        viewport: { width: w, height: w <= 390 ? 844 : 900 },
        deviceScaleFactor: 1,
        reducedMotion: 'no-preference',
    });
    const page = await ctx.newPage();
    const errs = [];
    page.on('pageerror', (e) => errs.push(String(e)));
    page.on('console', (m) => { if (m.type() === 'error') errs.push(m.text()); });
    await page.addInitScript(forceMotionOff);

    await page.clock.setFixedTime(FIXED_TIME);
    await page.goto(BASE_URL, { waitUntil: 'load' });
    await page.evaluate(() => document.fonts.ready);
    await page.waitForTimeout(1200);
    const shot = path.join(BUILD, `build-${w}.png`);
    await page.screenshot({ path: shot, fullPage: true });

    const a = PNG.sync.read(readFileSync(path.join(REF, `proto-${w}.png`)));
    const b = PNG.sync.read(readFileSync(shot));

    let mismatch = null;
    let note;
    if (a.width !== b.width || a.height !== b.height) {
        note = `SIZE MISMATCH ${a.width}x${a.height} ref vs ${b.width}x${b.height} build`;
    } else {
        const out = new PNG({ width: a.width, height: a.height });
        mismatch = pixelmatch(a.data, b.data, out.data, a.width, a.height, { threshold: 0.1 });
        writeFileSync(path.join(DIFF, `diff-${w}.png`), PNG.sync.write(out));
        note = `${mismatch} px (${((mismatch / (a.width * a.height)) * 100).toFixed(4)}%)`;
    }
    rows.push({ w, note, errs, mismatch, h: b.height });
    await ctx.close();
}

await browser.close();

console.log('width  height  result');
for (const r of rows) {
    console.log(`${String(r.w).padEnd(6)} ${String(r.h).padEnd(7)} ${r.note}`);
    if (r.errs.length) console.log(`       JS ERRORS: ${r.errs.slice(0, 3).join(' | ')}`);
}
const clean = rows.every((r) => r.mismatch === 0 && !r.errs.length);
console.log(clean ? '\nclean — every width matches the prototype exactly.' : '\nDIFFERENCES FOUND');
if (!clean) process.exitCode = 1;
