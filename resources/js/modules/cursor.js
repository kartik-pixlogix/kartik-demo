/* Custom cursor follower. Prototype: the cur block in onPointer() and the
 * lerp in frame(). Only visible when the root carries .has-cur, which
 * layout() sets from (motion && fine pointer). */

export function updateCursor(cur, t) {
    if (!cur || !t) return;
    cur.dataset.hidden = '';
    let mode = '';
    let label = '';
    if (t.closest('input,select,textarea')) mode = 'text';
    else if (t.closest('a.ind-row')) { // only linked rows; the launch rows are plain
        mode = 'view';
        label = 'VIEW';
    } else if (t.closest('.card,.case,.post')) {
        mode = 'view';
        label = 'OPEN';
    } else if (t.closest('a,button')) mode = 'go';
    cur.dataset.mode = mode;
    cur.dataset.label = label;
    cur.dataset.dark = t.closest('.ink-sec,.dd-m') && mode === '' ? '1' : '';
}

export function moveCursor(cur, cx, cy) {
    if (cur) cur.style.transform = 'translate3d(' + cx.toFixed(1) + 'px,' + cy.toFixed(1) + 'px,0)';
}
