/* UB Shield — script entry.
 *
 * A faithful port of the prototype's single IIFE (design/v7/index.html lines
 * 851-1046) split into the modules under ./modules. The control flow,
 * thresholds and easing constants are the prototype's; see docs/inventory.md
 * §11 for the mapping.
 *
 * Two things that are easy to get wrong and are deliberately preserved:
 *   1. the scroll listener only sets `dirty = true` — every scroll-driven
 *      update happens inside the rAF loop;
 *   2. layout() re-runs on resize, on document.fonts.ready AND on load,
 *      because the pinned-services distance depends on text metrics.
 */
import { createRevealObserver } from './modules/reveal.js';
import { updateHeader } from './modules/header.js';
import { updateHeadlines, updateParallax, trackHeroPointer, applyHeroLayers } from './modules/parallax.js';
import { updateSteps } from './modules/process-steps.js';
import { measurePin, updatePin } from './modules/pinned-services.js';
import { updateCursor, moveCursor } from './modules/cursor.js';
import { updatePreview, movePreview } from './modules/industries-preview.js';
import { applyMagnetic, resetMagnetic } from './modules/magnetic.js';
import { stepMarquees } from './modules/marquee.js';
import { startClock } from './modules/soc-clock.js';
import { initMobileMenu } from './modules/mobile-menu.js';
import { initReviewForm } from './modules/form.js';

const root = document.getElementById('ubs');
if (root) boot(root);

function boot(root) {
    const $ = (s, c) => (c || root).querySelector(s);
    const $$ = (s, c) => Array.prototype.slice.call((c || root).querySelectorAll(s));
    const mm = (q) => (window.matchMedia ? window.matchMedia(q).matches : false);

    const reduced = mm('(prefers-reduced-motion: reduce)');
    const fine = mm('(hover: hover) and (pointer: fine)');
    const motion = !reduced;
    const customCursor = true;

    const hdr = $('.hdr'), prog = $('.prog'), cur = $('.cur'), prev = $('.prev');
    const hl = $$('[data-hl]'), pxEls = $$('[data-px]'), layers = $$('[data-depth]');
    const pin = $('.svc-sec'), stick = $('.svc-stick'), track = $('.track'), pbar = $('.pbar i');
    const mq = $$('[data-mq]'), clock = $('[data-clock]'), heroArt = $('.hero-art');
    const steps = $$('[data-step]'), slideEl = $('[data-slide]'), stepEl = $('[data-stepno]');

    const st = { compact: false, up: false, slide: 1, step: 0, menu: false, scrollable: false, wide: false, pinDist: 0 };
    let mx = -200, my = -200, cx = -200, cy = -200, px = -200, py = -200, hx = 0, hy = 0;
    let lastY = window.scrollY || 0, vel = 0, dir = 1;
    const mqx = mq.map(() => 0);
    let dirty = true, io = null;

    const setClass = (el, c, on) => { if (el) el.classList.toggle(c, !!on); };
    const isPin = () => motion && st.scrollable && st.wide;

    function layout() {
        st.scrollable = document.documentElement.scrollHeight > window.innerHeight + 300;
        st.wide = window.innerWidth >= 1024;
        setClass(root, 'mo', motion);
        setClass(root, 'has-cur', motion && fine && customCursor);
        setClass(root, 'rvon', motion && st.scrollable);
        setClass(root, 'pinon', isPin());
        measurePin({ pin, stick, track, st, pinned: isPin() });
        if (io) { io.disconnect(); io = null; }
        if (motion && st.scrollable && 'IntersectionObserver' in window) {
            io = createRevealObserver($$);
        }
        dirty = true;
    }

    function onScroll() {
        const y = window.scrollY || 0, vh = window.innerHeight, dy = y - lastY;
        vel += dy * 0.25;
        if (dy !== 0) dir = dy > 0 ? 1 : -1;
        updateHeader({ hdr, prog, st, y, dy, vh, setClass });
        lastY = y;
        if (motion && st.scrollable) {
            updateHeadlines(hl, y);
            updateParallax(pxEls, vh);
            updateSteps({ steps, st, vh, stepEl, setClass });
        }
        if (isPin()) {
            updatePin({ pin, stick, track, pbar, st, slideEl });
        }
    }

    function onPointer(e) {
        mx = e.clientX; my = e.clientY;
        const t = e.target && e.target.closest ? e.target : null;
        updateCursor(cur, t);
        updatePreview(prev, t);
        if (!motion || !t) { resetMagnetic(); return; }
        applyMagnetic(t, e);
        const h = trackHeroPointer(heroArt, e, hx, hy);
        hx = h.hx; hy = h.hy;
    }

    function frame() {
        cx += (mx - cx) * 0.22; cy += (my - cy) * 0.22;
        moveCursor(cur, cx, cy);
        px += (mx - px) * 0.12; py += (my - py) * 0.12;
        movePreview(prev, px, py, mx);
        if (motion) {
            applyHeroLayers(layers, hx, hy);
            vel *= 0.92;
            const sp = 0.6 + Math.min(Math.abs(vel) * 0.35, 14);
            stepMarquees(mq, mqx, sp, dir);
        }
        if (dirty) { dirty = false; onScroll(); }
        requestAnimationFrame(frame);
    }

    initMobileMenu({ root, hdr, st, setClass, $, $$ });
    initReviewForm($);

    window.addEventListener('resize', layout);
    window.addEventListener('scroll', () => { dirty = true; }, { passive: true });
    window.addEventListener('mousemove', onPointer, { passive: true });
    window.addEventListener('mousedown', () => { if (cur) cur.dataset.down = '1'; });
    window.addEventListener('mouseup', () => { if (cur) cur.dataset.down = ''; });
    document.documentElement.addEventListener('mouseleave', () => {
        if (cur) cur.dataset.hidden = '1';
        if (prev) prev.dataset.show = '';
        resetMagnetic();
    });

    startClock(clock);
    layout();
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(layout);
    window.addEventListener('load', layout);
    requestAnimationFrame(frame);
}
