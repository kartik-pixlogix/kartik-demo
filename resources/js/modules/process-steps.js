/* Sticky process: the step nearest the viewport centre becomes active.
 * Prototype: the steps block in onScroll(). */
export function updateSteps({ steps, st, vh, stepEl, setClass }) {
    let best = 0;
    let bd = 1e9;
    steps.forEach((s, i) => {
        const r = s.getBoundingClientRect();
        const d = Math.abs(r.top + Math.min(r.height, 300) / 2 - vh * 0.5);
        if (d < bd) {
            bd = d;
            best = i;
        }
    });
    if (best !== st.step) {
        st.step = best;
        steps.forEach((s, i) => setClass(s, 'on', i === best));
        if (stepEl) stepEl.textContent = '0' + (best + 1);
    }
}
