/* Header compact / hide-on-scroll-down / scroll progress.
 * Prototype: the first half of onScroll(). */
export function updateHeader({ hdr, prog, st, y, dy, vh, setClass }) {
    const compact = y > 40;
    const up = y > 500 && dy > 2 && !st.menu ? true : dy < -2 || y <= 500 ? false : st.up;
    if (compact !== st.compact) {
        st.compact = compact;
        setClass(hdr, 'compact', compact);
    }
    if (up !== st.up) {
        st.up = up;
        setClass(hdr, 'up', up);
    }
    const max = Math.max(1, document.documentElement.scrollHeight - vh);
    if (prog) prog.style.transform = 'scaleX(' + Math.min(1, y / max).toFixed(4) + ')';
}
