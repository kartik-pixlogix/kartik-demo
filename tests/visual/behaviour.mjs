/* Exercises every motion/interaction behaviour BUILD.md Phase 1.4-1.5 lists.
 * The visual diff proves the resting state; this proves the page actually moves. */
import { chromium } from 'playwright';

import { BASE_URL as BASE } from './_shared.mjs';
const results = [];
const ok = (name, pass, detail = '') => results.push({ name, pass, detail });

const browser = await chromium.launch();

async function page(width = 1440, height = 900, opts = {}) {
    const ctx = await browser.newContext({ viewport: { width, height }, deviceScaleFactor: 1, ...opts });
    const p = await ctx.newPage();
    p.on('pageerror', (e) => ok('NO JS ERRORS', false, String(e)));
    await p.goto(BASE, { waitUntil: 'load' });
    await p.evaluate(() => document.fonts.ready);
    await p.waitForTimeout(600);
    return p;
}
const cls = (p) => p.evaluate(() => document.getElementById('ubs').className);
const scrollTo = async (p, y) => { await p.evaluate((v) => window.scrollTo(0, v), y); await p.waitForTimeout(450); };
const topOf = (p, sel) => p.evaluate((s) => document.querySelector(s).getBoundingClientRect().top + window.scrollY, sel);

// ---------- desktop ----------
{
    const p = await page();
    const c = await cls(p);
    ok('load: root has mo + rvon + pinon + has-cur', ['mo', 'rvon', 'pinon', 'has-cur'].every((k) => c.includes(k)), c);

    // reveals arm on scroll
    const before = await p.evaluate(() => document.querySelectorAll('.rv.in,.rm.in,.rs.in').length);
    await scrollTo(p, await topOf(p, '.stm'));
    const after = await p.evaluate(() => document.querySelectorAll('.rv.in,.rm.in,.rs.in').length);
    ok('reveals: .in added as sections enter view', after > before, `${before} -> ${after}`);

    // count-up reaches its target
    await scrollTo(p, 0);
    await p.waitForTimeout(1800);
    const counts = await p.evaluate(() => Array.from(document.querySelectorAll('[data-count]')).map((e) => [e.getAttribute('data-count'), e.textContent]));
    ok('count-up: animates to the target value', counts.every(([a, b]) => a === b), JSON.stringify(counts));

    // header compact / hide / progress
    await scrollTo(p, 200);
    ok('header: .compact past 40px', (await p.evaluate(() => document.querySelector('.hdr').classList.contains('compact'))));
    await scrollTo(p, 900);
    await scrollTo(p, 1600);
    ok('header: .up hides it on scroll-down past 500', (await p.evaluate(() => document.querySelector('.hdr').classList.contains('up'))));
    await scrollTo(p, 1200);
    ok('header: .up cleared on scroll-up', !(await p.evaluate(() => document.querySelector('.hdr').classList.contains('up'))));
    const prog = await p.evaluate(() => document.querySelector('.prog').style.transform);
    ok('header: progress bar scales with scroll', /scaleX\(0\.\d+\)/.test(prog), prog);

    // parallax
    const hl = await p.evaluate(() => document.querySelector('[data-hl]').style.transform);
    ok('parallax: headline lines slide', /translate3d\(-?\d+(\.\d+)?px/.test(hl) && !/translate3d\(0px/.test(hl), hl);
    const px = await p.evaluate(() => { const e = [...document.querySelectorAll('[data-px]')].find((x) => x.style.transform); return e ? e.style.transform : ''; });
    ok('parallax: [data-px] drifts', px.includes('translate3d'), px);

    // hero depth follows the pointer
    await scrollTo(p, 0);
    await p.mouse.move(1100, 400);
    await p.waitForTimeout(300);
    const depth = await p.evaluate(() => document.querySelector('[data-depth]').style.transform);
    ok('parallax: hero layers follow the cursor', depth.includes('translate3d') && !/translate3d\(0px, 0px/.test(depth), depth);

    // pinned services
    const svcTop = await topOf(p, '.svc-sec');
    const svcH = await p.evaluate(() => document.querySelector('.svc-sec').offsetHeight);
    const stickH = await p.evaluate(() => document.querySelector('.svc-stick').offsetHeight);
    ok('services: section is taller than the sticky block (pin distance reserved)', svcH > stickH + 100, `${svcH} vs ${stickH}`);
    await scrollTo(p, svcTop + (svcH - stickH) * 0.75);
    const pin = await p.evaluate(() => ({
        track: document.querySelector('.track').style.transform,
        slide: document.querySelector('[data-slide]').textContent,
        pbar: document.querySelector('.pbar i').style.transform,
    }));
    ok('services: track translates sideways while pinned', /translate3d\(-\d+/.test(pin.track), pin.track);
    ok('services: counter advances past 01', pin.slide !== '01', pin.slide);
    ok('services: progress bar fills', /scaleX\(0\.[5-9]|scaleX\(1\)/.test(pin.pbar), pin.pbar);

    // sticky process
    const stepsTop = await topOf(p, '.steps');
    await scrollTo(p, stepsTop + 900);
    const step = await p.evaluate(() => ({ no: document.querySelector('[data-stepno]').textContent, on: [...document.querySelectorAll('.step')].findIndex((s) => s.classList.contains('on')) }));
    ok('process: live counter follows the active step', step.no !== '01' && step.on > 0, JSON.stringify(step));

    // SOC clock
    const clock = await p.evaluate(() => document.querySelector('[data-clock]').textContent);
    ok('soc: IST clock is running', /^\d{2}:\d{2}:\d{2}$/.test(clock), clock);

    // marquee moves and reverses with scroll direction
    await scrollTo(p, await topOf(p, '.mq'));
    const m1 = await p.evaluate(() => document.querySelector('[data-mq]').style.transform);
    await p.waitForTimeout(400);
    const m2 = await p.evaluate(() => document.querySelector('[data-mq]').style.transform);
    ok('frameworks: marquee is moving', m1 !== m2, `${m1} -> ${m2}`);

    // industries hover preview + cursor modes
    await p.hover('.ind-row[data-v="3"]');
    await p.waitForTimeout(300);
    const prev = await p.evaluate(() => { const e = document.querySelector('.prev'); return { show: e.dataset.show, v: e.dataset.v }; });
    ok('industries: preview follows the hovered row', prev.show === '1' && prev.v === '3', JSON.stringify(prev));
    const curRow = await p.evaluate(() => { const c = document.querySelector('.cur'); return c.dataset.mode + '/' + c.dataset.label; });
    ok('cursor: VIEW over an industry row', curRow === 'view/VIEW', curRow);

    // back to pin progress 0 so the first card sits inside the viewport again
    await scrollTo(p, svcTop);
    const cardBox = await p.evaluate(() => { const r = document.querySelector('.card').getBoundingClientRect(); return [r.left + r.width / 2, r.top + 80]; });
    await p.mouse.move(cardBox[0], cardBox[1]);
    await p.waitForTimeout(250);
    const curCard = await p.evaluate(() => { const c = document.querySelector('.cur'); return c.dataset.mode + '/' + c.dataset.label; });
    ok('cursor: OPEN over a service card', curCard === 'view/OPEN', curCard + ' at ' + cardBox.join(','));

    await scrollTo(p, await topOf(p, '.strip'));
    const inBox = await p.evaluate(() => { const r = document.querySelector('input#v6n').getBoundingClientRect(); return [r.left + r.width / 2, r.top + r.height / 2]; });
    await p.mouse.move(inBox[0], inBox[1]);
    await p.waitForTimeout(250);
    ok('cursor: shrinks over form fields', (await p.evaluate(() => document.querySelector('.cur').dataset.mode)) === 'text');

    // magnetic
    const mag = await p.evaluate(() => { const b = document.querySelector('.cta-b .btn.mag'); const r = b.getBoundingClientRect(); return [r.left + r.width / 2 + 20, r.top + r.height / 2 + 6]; });
    await p.mouse.move(mag[0], mag[1]);
    await p.waitForTimeout(250);
    const magT = await p.evaluate(() => document.querySelector('.cta-b .btn.mag').style.transform);
    ok('magnetic: button leans toward the pointer', magT.includes('translate('), magT);
    await p.mouse.move(5, 5);
    await p.waitForTimeout(250);
    ok('magnetic: resets when the pointer leaves', (await p.evaluate(() => document.querySelector('.cta-b .btn.mag').style.transform)) === '', '');

    await p.context().close();
}

// ---------- below the 1024px pin threshold ----------
{
    const p = await page(900, 900);
    const c = await cls(p);
    ok('900px: not pinned', !c.includes('pinon'), c);
    const disp = await p.evaluate(() => getComputedStyle(document.querySelector('.track')).display);
    ok('900px: services fall back to a grid', disp === 'grid', disp);
    const counterShown = await p.evaluate(() => getComputedStyle(document.querySelector('.counter.v-hide')).display);
    ok('900px: pinned-only counter is hidden', counterShown === 'none', counterShown);
    await p.context().close();
}

// ---------- mobile menu ----------
{
    const p = await page(390, 844);
    await p.click('[data-menu-toggle]');
    await p.waitForTimeout(900);
    ok('menu: opens', (await cls(p)).includes('menu-open'));
    ok('menu: aria-expanded true', (await p.getAttribute('[data-menu-toggle]', 'aria-expanded')) === 'true');
    const focusInMenu = await p.evaluate(() => !!document.activeElement.closest('.mmenu'));
    ok('menu: focus moves into the menu', focusInMenu);
    // Tab from the last item should wrap back inside the trap
    await p.evaluate(() => { const i = document.querySelectorAll('.mmenu a'); i[i.length - 1].focus(); });
    await p.keyboard.press('Tab');
    await p.keyboard.press('Tab');
    const trapped = await p.evaluate(() => !!(document.activeElement.closest('.mmenu') || document.activeElement.matches('[data-menu-toggle]')));
    ok('menu: focus is trapped', trapped, await p.evaluate(() => document.activeElement.outerHTML.slice(0, 60)));
    await p.keyboard.press('Escape');
    await p.waitForTimeout(500);
    ok('menu: Escape closes it', !(await cls(p)).includes('menu-open'));
    ok('menu: focus returns to the toggle', await p.evaluate(() => document.activeElement.matches('[data-menu-toggle]')));
    await p.context().close();
}

// ---------- reduced motion ----------
{
    const p = await page(1440, 900, { reducedMotion: 'reduce' });
    const c = await cls(p);
    ok('reduced-motion: no mo / rvon / pinon / has-cur', !['mo', 'rvon', 'pinon', 'has-cur'].some((k) => c.includes(k)), `"${c}"`);
    const hidden = await p.evaluate(() => [getComputedStyle(document.querySelector('.cur')).display, getComputedStyle(document.querySelector('.prev')).display]);
    ok('reduced-motion: cursor and preview are display:none', hidden.every((d) => d === 'none'), hidden.join(','));
    const anim = await p.evaluate(() => getComputedStyle(document.querySelector('.sweep')).animationName);
    ok('reduced-motion: infinite animations are off', anim === 'none', anim);
    await p.context().close();
}

await browser.close();

const pass = results.filter((r) => r.pass).length;
for (const r of results) console.log(`${r.pass ? 'PASS' : 'FAIL'}  ${r.name}${r.pass ? '' : '   << ' + r.detail}`);
console.log(`\n${pass}/${results.length} passed`);
if (pass !== results.length) process.exitCode = 1;
