/* Pinned horizontal services. Prototype: the pin block in layout() and the
 * isPin() block in onScroll(). Section height = sticky height + overflow. */

export function measurePin({ pin, stick, track, st, pinned }) {
    if (!pin || !stick || !track) return;
    if (pinned) {
        st.pinDist = Math.max(0, track.scrollWidth - stick.querySelector('.wrap').clientWidth + 128);
        pin.style.height = stick.offsetHeight + st.pinDist + 'px';
    } else {
        pin.style.height = '';
        track.style.transform = '';
    }
}

export function updatePin({ pin, stick, track, pbar, st, slideEl }) {
    const r = pin.getBoundingClientRect();
    const total = Math.max(1, pin.offsetHeight - stick.offsetHeight);
    const p = Math.min(1, Math.max(0, -r.top / total));
    track.style.transform = 'translate3d(' + (-p * st.pinDist).toFixed(1) + 'px,0,0)';
    if (pbar) pbar.style.transform = 'scaleX(' + p.toFixed(4) + ')';
    // One step per card. The prototype hardcoded 4 (five cards); the launch
    // catalogue has six, so count them rather than assume.
    const last = Math.max(1, track.children.length - 1);
    const s = 1 + Math.min(last, Math.round(p * last));
    if (s !== st.slide) {
        st.slide = s;
        if (slideEl) slideEl.textContent = '0' + s;
    }
}
