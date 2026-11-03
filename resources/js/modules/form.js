/* Posture-review form.
 *
 * Phase 1 keeps the prototype's behaviour exactly: intercept the submit and
 * swap the button label. Phase 2 replaces this with a real POST to the Laravel
 * endpoint (validation, honeypot, minimum fill time, rate limiting) while
 * keeping the same inline success/error presentation, and the form must also
 * work without JS through a normal POST and redirect. */
export function initReviewForm($) {
    const form = $('[data-review-form]');
    if (!form) return;
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const lbl = $('[data-submit-label]', form);
        if (lbl) lbl.textContent = 'Request received. We reply within one business day';
    });
}
