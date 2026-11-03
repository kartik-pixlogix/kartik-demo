/* Floating industry artwork that follows the cursor.
 * Prototype: the prev block in onPointer() and the lerp + rotate in frame(). */

export function updatePreview(prev, t) {
    if (!prev || !t) return;
    const row = t.closest('.ind-row');
    if (row) {
        prev.dataset.show = '1';
        prev.dataset.v = row.dataset.v;
    } else {
        prev.dataset.show = '';
    }
}

export function movePreview(prev, px, py, mx) {
    if (!prev) return;
    const rot = Math.max(-12, Math.min(12, (mx - px) * 0.08));
    prev.style.transform =
        'translate3d(' + px.toFixed(1) + 'px,' + py.toFixed(1) + 'px,0) rotate(' + rot.toFixed(2) + 'deg)';
}
