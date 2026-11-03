/* Parallax: headline slide, [data-px] drift and the cursor-follow hero layers.
 * Prototype: the hl/pxEls loops in onScroll(), the heroArt block in onPointer()
 * and the layers loop in frame(). */

export function updateHeadlines(hl, y) {
    hl.forEach((el) => {
        const f = +el.getAttribute('data-hl');
        el.style.transform = 'translate3d(' + (f * Math.min(y, 1200) * 0.22).toFixed(1) + 'px,0,0)';
    });
}

export function updateParallax(pxEls, vh) {
    pxEls.forEach((el) => {
        const r = el.getBoundingClientRect();
        if (r.bottom < -200 || r.top > vh + 200) return;
        const c = r.top + r.height / 2 - vh / 2;
        el.style.transform = 'translate3d(0,' + (c * -(+el.getAttribute('data-px'))).toFixed(1) + 'px,0)';
    });
}

export function trackHeroPointer(heroArt, e, hx, hy) {
    if (!heroArt) return { hx, hy };
    const hr = heroArt.getBoundingClientRect();
    if (e.clientY < hr.bottom + 200) {
        return {
            hx: (e.clientX / window.innerWidth - 0.5) * -1,
            hy: (e.clientY / window.innerHeight - 0.5) * -1,
        };
    }
    return { hx: hx * 0.9, hy: hy * 0.9 };
}

export function applyHeroLayers(layers, hx, hy) {
    layers.forEach((l) => {
        const d = +l.getAttribute('data-depth');
        l.style.transform = 'translate3d(' + (hx * d).toFixed(1) + 'px,' + (hy * d).toFixed(1) + 'px,0)';
    });
}
