/* Live IST clock in the SOC section. Prototype: tickClock().
 * IST is UTC+5:30, so shift by (330 + the local offset) minutes. */
export function startClock(clock) {
    if (!clock) return;
    const p2 = (n) => String(n).padStart(2, '0');
    const tick = () => {
        const d = new Date(Date.now() + (330 + new Date().getTimezoneOffset()) * 60000);
        clock.textContent = p2(d.getHours()) + ':' + p2(d.getMinutes()) + ':' + p2(d.getSeconds());
    };
    tick();
    setInterval(tick, 1000);
}
