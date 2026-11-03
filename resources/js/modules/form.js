/* Posture-review form.
 *
 * Progressive enhancement over a form that already works: the markup is a
 * normal POST to the Laravel route, so with scripting off the browser submits,
 * the server validates, and the page comes back with inline errors or the
 * success message. This module only intercepts that to avoid the round trip.
 *
 * On success it swaps the button label, which is what the prototype did.
 * Error presentation mirrors the server-rendered markup exactly, so the two
 * paths look identical.
 */

const SUCCESS_FALLBACK = 'Request received. We reply within one business day';

export function initReviewForm($) {
    const form = $('[data-review-form]');
    if (!form) return;

    const label = $('[data-submit-label]', form);
    const button = form.querySelector('button[type="submit"]');
    const originalLabel = label ? label.textContent : '';

    form.addEventListener('submit', async (e) => {
        // Let the browser handle it if fetch is unavailable for any reason.
        if (typeof window.fetch !== 'function') return;

        e.preventDefault();
        clearErrors(form);

        if (button) button.disabled = true;
        if (label) label.textContent = 'Sending…';

        let response;
        try {
            response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
        } catch {
            // Offline or blocked. Fall back to a real submit rather than
            // stranding the visitor with a dead button.
            if (button) button.disabled = false;
            if (label) label.textContent = originalLabel;
            form.submit();
            return;
        }

        if (response.ok) {
            const data = await response.json().catch(() => ({}));
            if (label) label.textContent = data.message || SUCCESS_FALLBACK;
            form.querySelectorAll('input, select').forEach((f) => {
                if (f.type !== 'hidden') f.value = '';
            });
            return;
        }

        if (button) button.disabled = false;
        if (label) label.textContent = originalLabel;

        if (response.status === 422) {
            const data = await response.json().catch(() => ({}));
            showErrors(form, data.errors || {});
            return;
        }

        if (response.status === 419) {
            // Expired CSRF token — a full reload gets a fresh one.
            showFormNote(form, 'Your session expired. Please reload the page and try again.');
            return;
        }

        showFormNote(form, 'Something went wrong at our end. Please try again, or email us directly.');
    });
}

function clearErrors(form) {
    form.querySelectorAll('.err, .form-note').forEach((n) => n.remove());
    form.querySelectorAll('[aria-invalid]').forEach((f) => {
        f.removeAttribute('aria-invalid');
        f.removeAttribute('aria-describedby');
    });
}

function showErrors(form, errors) {
    if (errors.form) {
        showFormNote(form, [].concat(errors.form)[0]);
    }

    let first = null;
    Object.keys(errors).forEach((field) => {
        if (field === 'form') return;
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) return;

        const message = [].concat(errors[field])[0];
        const id = `${input.id}-err`;
        input.setAttribute('aria-invalid', 'true');
        input.setAttribute('aria-describedby', id);

        const span = document.createElement('span');
        span.className = 'err';
        span.id = id;
        span.textContent = message;
        input.closest('.f').appendChild(span);

        if (!first) first = input;
    });

    if (first) first.focus();
}

function showFormNote(form, message) {
    const p = document.createElement('p');
    p.className = 'form-note is-error';
    p.setAttribute('role', 'alert');
    p.textContent = message;
    form.prepend(p);
}
