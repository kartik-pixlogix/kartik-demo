/* Number count-up. Prototype: countUp() — 1400ms, cubic ease-out, one-shot. */
export function countUp(el) {
    if (el._done) return;
    el._done = true;
    const to = +el.getAttribute('data-count');
    const t0 = performance.now();
    const dur = 1400;
    (function step(t) {
        const k = Math.min(1, (t - t0) / dur);
        const e = 1 - Math.pow(1 - k, 3);
        el.textContent = String(Math.round(to * e));
        if (k < 1) requestAnimationFrame(step);
    })(t0);
}
