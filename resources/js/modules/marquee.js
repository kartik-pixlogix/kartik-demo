/* Framework marquees: speed and direction follow scroll velocity.
 * Prototype: the mq loop in frame(). The velocity (vel) and direction (dir)
 * are maintained by the scroll pass in app.js. */
export function stepMarquees(mq, mqx, sp, dir) {
    mq.forEach((m, i) => {
        const half = m.scrollWidth / 2 || 1;
        mqx[i] += sp * dir * +m.getAttribute('data-mq');
        if (mqx[i] <= -half) mqx[i] += half;
        if (mqx[i] > 0) mqx[i] -= half;
        m.style.transform = 'translate3d(' + mqx[i].toFixed(1) + 'px,0,0)';
    });
}
