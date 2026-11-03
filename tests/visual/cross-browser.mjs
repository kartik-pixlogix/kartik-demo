/* Cross-browser layout check at all five widths (BUILD.md Phase 5.5).
 *
 * Chromium is the reference, because that is what docs/reference/ was captured
 * with. Firefox and WebKit are compared against it on measurements rather than
 * pixels: the three engines rasterise type differently, so a pixel diff between
 * them would be noise. What matters is that the layout is the same — document
 * height, section positions, column counts, no horizontal overflow — and that
 * the sticky and pinned sections behave, which is the specific iOS Safari risk
 * the brief calls out.
 *
 *   php artisan serve
 *   node tests/visual/cross-browser.mjs
 */
import { chromium, firefox, webkit } from 'playwright';
import { mkdirSync } from 'node:fs';
import path from 'node:path';
import { BASE_URL, DIFF, WIDTHS, FIXED_TIME, forceMotionOff } from './_shared.mjs';

const OUT = path.join(DIFF, 'cross-browser');
mkdirSync(OUT, { recursive: true });

const ENGINES = [['chromium', chromium], ['firefox', firefox], ['webkit', webkit]];
const PAGES = [['home', '/'], ['service', '/services/offensive-security'], ['article', '/insights/dpdpa-2023-consent-flows']];

/** Layout facts that must agree across engines. */
const measure = () => {
    const box = (sel) => {
        const el = document.querySelector(sel);
        if (!el) return null;
        const r = el.getBoundingClientRect();
        return { top: Math.round(r.top + window.scrollY), h: Math.round(r.height), w: Math.round(r.width) };
    };
    const cols = (sel) => {
        const el = document.querySelector(sel);
        return el ? getComputedStyle(el).gridTemplateColumns.split(' ').length : null;
    };
    return {
        docHeight: document.documentElement.scrollHeight,
        overflow: document.documentElement.scrollWidth > window.innerWidth,
        header: box('.hdr'),
        headerSticky: document.querySelector('.hdr') ? getComputedStyle(document.querySelector('.hdr')).position : null,
        hero: box('.hero') || box('.phero'),
        strip: box('.strip'),
        footer: box('.foot'),
        statsCols: cols('.stats'),
        trackCols: cols('.track'),
        gridCols: cols('.grid4'),
        footCols: cols('.foot-g'),
    };
};

const results = [];
const data = {};

for (const [name, engine] of ENGINES) {
    const browser = await engine.launch();
    data[name] = {};

    for (const [label, url] of PAGES) {
        data[name][label] = {};
        for (const w of WIDTHS) {
            const ctx = await browser.newContext({
                viewport: { width: w, height: w <= 390 ? 844 : 900 },
                deviceScaleFactor: 1,
            });
            const p = await ctx.newPage();
            const errs = [];
            p.on('pageerror', (e) => errs.push(String(e)));
            await p.addInitScript(forceMotionOff);
            await p.clock.setFixedTime(FIXED_TIME);
            await p.goto(BASE_URL.replace(/\/$/, '') + url, { waitUntil: 'load' });
            await p.evaluate(() => document.fonts.ready);
            await p.waitForTimeout(900);

            data[name][label][w] = { ...(await p.evaluate(measure)), errs: errs.length };

            if (label === 'home' && (w === 1440 || w === 390)) {
                await p.screenshot({ path: path.join(OUT, `${name}-${w}.png`), fullPage: true });
            }
            await ctx.close();
        }
    }

    // sticky header and the pinned services track, with motion ON — the
    // specific iOS Safari risk BUILD.md names
    const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
    const p = await ctx.newPage();
    await p.goto(BASE_URL, { waitUntil: 'load' });
    await p.evaluate(() => document.fonts.ready);
    await p.waitForTimeout(900);

    const sticky = await p.evaluate(async () => {
        const hdr = document.querySelector('.hdr');
        window.scrollTo(0, 1500);
        await new Promise((r) => setTimeout(r, 600));
        const stuckTop = Math.round(hdr.getBoundingClientRect().top);
        const compact = hdr.classList.contains('compact');

        const pin = document.querySelector('.svc-sec');
        const stick = document.querySelector('.svc-stick');
        const top = pin.getBoundingClientRect().top + window.scrollY;
        const travel = pin.offsetHeight - stick.offsetHeight;
        window.scrollTo(0, top + travel * 0.5);
        await new Promise((r) => setTimeout(r, 600));
        return {
            headerStaysAtTop: stuckTop === 0 || hdr.classList.contains('up'),
            headerCompacts: compact,
            pinned: document.getElementById('ubs').classList.contains('pinon'),
            travel,
            trackMoved: /translate3d\(-\d/.test(document.querySelector('.track').style.transform),
            stickyWorks: Math.abs(Math.round(stick.getBoundingClientRect().top)) < 4,
            counter: document.querySelector('[data-slide]').textContent,
        };
    });
    results.push({ name, sticky });
    await ctx.close();
    await browser.close();
}

// ---- compare -----------------------------------------------------------
const ref = data.chromium;
let problems = 0;

console.log('layout agreement (chromium is the reference)\n');
for (const [label] of PAGES) {
    for (const w of WIDTHS) {
        const r = ref[label][w];
        const row = [`${label} @ ${w}`.padEnd(22)];
        for (const [name] of ENGINES) {
            const d = data[name][label][w];
            const sameCols = d.statsCols === r.statsCols && d.gridCols === r.gridCols && d.footCols === r.footCols;
            // type rasterises differently, so allow a small height tolerance
            const dh = Math.abs(d.docHeight - r.docHeight);
            const close = dh <= Math.max(24, r.docHeight * 0.02);
            const good = sameCols && close && !d.overflow && d.errs === 0;
            if (!good && name !== 'chromium') problems++;
            row.push(`${name}:${good ? 'ok' : 'DIFF'}${name === 'chromium' ? '' : ` (Δh ${d.docHeight - r.docHeight}${d.overflow ? ', OVERFLOW' : ''}${d.errs ? ', JS ERR' : ''})`}`);
        }
        console.log('  ' + row.join('  '));
    }
}

console.log('\nsticky header and pinned services, motion on\n');
for (const { name, sticky } of results) {
    const good = sticky.headerCompacts && sticky.pinned && sticky.trackMoved && sticky.stickyWorks;
    if (!good) problems++;
    console.log(`  ${name.padEnd(9)} ${good ? 'ok  ' : 'FAIL'} sticky=${sticky.stickyWorks} pinned=${sticky.pinned} trackMoved=${sticky.trackMoved} compact=${sticky.headerCompacts} counter=${sticky.counter} travel=${sticky.travel}`);
}

console.log(problems ? `\n${problems} cross-browser problem(s)` : '\nall three engines agree on layout and behaviour');
if (problems) process.exitCode = 1;
