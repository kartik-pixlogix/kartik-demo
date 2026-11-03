/* Live IST clock in the SOC section. Prototype: tickClock().
 *
 * The prototype computed IST by hand as (330 + the local offset) minutes. That
 * is correct today but it hard-codes the offset, so it would be wrong if the
 * zone ever changed and it is wrong on any machine whose clock reports an
 * unusual offset. Intl resolves Asia/Kolkata from the browser's own tz
 * database instead. The rendered string is identical: zero-padded HH:MM:SS.
 */
const FORMATTER = new Intl.DateTimeFormat('en-GB', {
    timeZone: 'Asia/Kolkata',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hourCycle: 'h23',
});

export function startClock(clock) {
    if (!clock) return;

    const tick = () => {
        clock.textContent = FORMATTER.format(new Date());
    };

    tick();
    setInterval(tick, 1000);
}
