/* Captures docs/reference/ from the prototype: full page, five widths, motion off.
 *
 * Only re-run this when the prototype itself changes or an approved deviation is
 * added to docs/prototype-fixes.md — the references are the baseline the build is
 * judged against.
 *
 *   node tests/visual/capture-reference.mjs
 */
import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';
import path from 'node:path';
import { REF, WIDTHS, FIXED_TIME, prototypeUrl } from './_shared.mjs';

mkdirSync(REF, { recursive: true });
const URL = prototypeUrl('resting');
const browser = await chromium.launch();

for (const w of WIDTHS) {
    const ctx = await browser.newContext({
        viewport: { width: w, height: w <= 390 ? 844 : 900 },
        deviceScaleFactor: 1,
        reducedMotion: 'no-preference',
    });
    const page = await ctx.newPage();
    const errs = [];
    page.on('pageerror', (e) => errs.push(String(e)));

    await page.clock.setFixedTime(FIXED_TIME);
    await page.goto(URL, { waitUntil: 'load' });
    await page.evaluate(() => document.fonts.ready);
    await page.waitForTimeout(1200);
    await page.screenshot({ path: path.join(REF, `proto-${w}.png`), fullPage: true });

    const info = await page.evaluate(() => ({
        rootClasses: document.getElementById('ubs').className,
        height: document.documentElement.scrollHeight,
        fonts: [...document.fonts].filter((f) => f.status === 'loaded').map((f) => f.family),
    }));
    const missing = ['Sofia Sans Extra Condensed', 'Big Shoulders Display', 'Instrument Sans', 'Michroma']
        .filter((f) => !info.fonts.includes(f));

    console.log(`${String(w).padEnd(5)} -> proto-${w}.png  height ${info.height}  root "${info.rootClasses}"`);
    if (missing.length) console.log(`      !! fonts not loaded: ${missing.join(', ')}`);
    if (errs.length) console.log(`      !! errors: ${errs.join(' | ')}`);
    await ctx.close();
}

await browser.close();
console.log('\nreferences captured.');
