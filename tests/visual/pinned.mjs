/* Compares the pinned horizontal services track against the prototype.
 *
 * The docs/reference/ captures cannot cover this: `motion = false` disables the
 * pin, so they show the cards as a grid. This runs both pages with motion ON and
 * samples five points of pin progress.
 *
 * Reveals are settled and CSS animations frozen on both sides, so the comparison
 * is about layout. The infinite artwork animations (.flow .pl2 .hunt .spin .tick
 * .redact .scan*) are time-based and start at different moments in the two pages;
 * left running they account for a few thousand pixels of pure phase difference.
 *
 * Start the app first, then:
 *   node tests/visual/pinned.mjs
 */
import { chromium } from 'playwright';
import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import path from 'node:path';
import { DIFF, BASE_URL, FIXED_TIME, prototypeUrl } from './_shared.mjs';

const OUT = path.join(DIFF, 'pinned');
mkdirSync(OUT, { recursive: true });
const STOPS = [0, 0.25, 0.5, 0.75, 1];

async function capture(browser, url, tag) {
    const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 1 });
    const p = await ctx.newPage();
    await p.clock.setFixedTime(FIXED_TIME);
    await p.goto(url, { waitUntil: 'load' });
    await p.evaluate(() => document.fonts.ready);
    await p.waitForTimeout(900);

    await p.evaluate(() => {
        document.querySelectorAll('.rv,.rm,.rs,.rl,.rw,.brush,.art.rc,.stats').forEach((e) => e.classList.add('in'));
    });
    await p.waitForTimeout(1400);
    // Freeze every animation. Done with inline style attributes rather than an
    // injected stylesheet, because the site's own CSP blocks inline <style>
    // elements (style-src-elem 'self') — that refusal is the policy working.
    // The prototype animates real elements, never pseudo-elements, so setting
    // the property per element covers all of it.
    await p.evaluate(() => {
        document.querySelectorAll('*').forEach((el) => {
            el.style.animation = 'none';
        });
    });
    await p.waitForTimeout(400);

    const geom = await p.evaluate(() => {
        const pin = document.querySelector('.svc-sec');
        const stick = document.querySelector('.svc-stick');
        return {
            top: pin.getBoundingClientRect().top + window.scrollY,
            travel: pin.offsetHeight - stick.offsetHeight,
            pinned: document.getElementById('ubs').classList.contains('pinon'),
        };
    });

    const shots = [];
    for (const s of STOPS) {
        await p.evaluate((y) => window.scrollTo(0, y), geom.top + geom.travel * s);
        await p.waitForTimeout(500);
        const file = path.join(OUT, `${tag}-${String(s).replace('.', '_')}.png`);
        await p.screenshot({ path: file });
        shots.push({
            s,
            file,
            state: await p.evaluate(() => ({
                track: document.querySelector('.track').style.transform,
                slide: document.querySelector('[data-slide]').textContent,
                pbar: document.querySelector('.pbar i').style.transform,
            })),
        });
    }
    await ctx.close();
    return { geom, shots };
}

const browser = await chromium.launch();
const a = await capture(browser, prototypeUrl('motion'), 'proto');
const b = await capture(browser, BASE_URL, 'build');
await browser.close();

console.log('pin geometry');
console.log(`  prototype  top=${Math.round(a.geom.top)} travel=${a.geom.travel} pinned=${a.geom.pinned}`);
console.log(`  build      top=${Math.round(b.geom.top)} travel=${b.geom.travel} pinned=${b.geom.pinned}`);
const geomOk = a.geom.travel === b.geom.travel && a.geom.pinned && b.geom.pinned;
console.log(`  travel: ${geomOk ? 'EXACT' : 'DIFFERS'}\n`);

console.log('progress  counter  track transform                 pixels');
let worst = 0;
let stateOk = true;
for (let i = 0; i < STOPS.length; i++) {
    const A = PNG.sync.read(readFileSync(a.shots[i].file));
    const B = PNG.sync.read(readFileSync(b.shots[i].file));
    const out = new PNG({ width: A.width, height: A.height });
    const n = pixelmatch(A.data, B.data, out.data, A.width, A.height, { threshold: 0.1 });
    writeFileSync(path.join(OUT, `diff-${String(STOPS[i]).replace('.', '_')}.png`), PNG.sync.write(out));
    worst = Math.max(worst, n);
    const same = a.shots[i].state.track === b.shots[i].state.track && a.shots[i].state.slide === b.shots[i].state.slide;
    if (!same) stateOk = false;
    console.log(
        `${String(STOPS[i]).padEnd(9)} ${a.shots[i].state.slide}/${b.shots[i].state.slide}    ` +
        `${(b.shots[i].state.track || '-').padEnd(32)} ${n}${same ? '' : '  << STATE MISMATCH'}`
    );
}
const clean = worst === 0 && geomOk && stateOk;
console.log(clean ? '\nclean — the pinned track matches the prototype exactly.' : `\nworst frame: ${worst} px`);
if (!clean) process.exitCode = 1;
