/* Magnetic buttons. Prototype: the mag block in onPointer() and resetMag().
 * The module keeps the single "currently leaning" element the prototype held
 * in its magEl closure variable. */
let magEl = null;

export function resetMagnetic() {
    if (magEl) {
        magEl.style.transform = '';
        magEl = null;
    }
}

export function applyMagnetic(t, e) {
    const mag = t.closest('.mag');
    if (magEl && magEl !== mag) resetMagnetic();
    if (!mag) return;
    const r = mag.getBoundingClientRect();
    mag.style.transform =
        'translate(' +
        ((e.clientX - r.left - r.width / 2) * 0.25).toFixed(1) +
        'px,' +
        ((e.clientY - r.top - r.height / 2) * 0.35).toFixed(1) +
        'px)';
    magEl = mag;
}
