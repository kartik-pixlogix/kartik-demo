/* Mobile circular-reveal menu. Prototype: setMenu() plus the menuBtn, link and
 * Escape listeners.
 *
 * Beyond the prototype, BUILD.md Phase 1.4 requires the focus to be trapped
 * while the menu is open, so this module adds: focus moves to the first link on
 * open, Tab and Shift+Tab cycle within the menu, and focus returns to the
 * toggle on close. The links are already removed from the tab order while
 * closed by `.mmenu{visibility:hidden}`, so no `inert` handling is needed. */

const FOCUSABLE = 'a[href],button:not([disabled]),input,select,textarea,[tabindex]:not([tabindex="-1"])';

export function initMobileMenu({ root, hdr, st, setClass, $, $$ }) {
    const menuBtn = $('[data-menu-toggle]');
    const menu = $('.mmenu');
    let lastFocused = null;

    function setMenu(open) {
        st.menu = open;
        setClass(root, 'menu-open', open);
        if (menuBtn) {
            menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuBtn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        }
        if (open) {
            st.up = false;
            setClass(hdr, 'up', false);
            lastFocused = document.activeElement;
            const first = menu && menu.querySelector(FOCUSABLE);
            if (first) first.focus();
        } else if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
            lastFocused = null;
        }
    }

    if (menuBtn) menuBtn.addEventListener('click', () => setMenu(!st.menu));
    $$('.mmenu a').forEach((a) => a.addEventListener('click', () => setMenu(false)));

    document.addEventListener('keydown', (e) => {
        if (!st.menu) return;
        if (e.key === 'Escape') {
            setMenu(false);
            return;
        }
        if (e.key !== 'Tab' || !menu) return;
        // The toggle sits above the overlay and stays reachable, so it belongs in the
        // loop — but it is earlier in the document than the menu, so the list has to be
        // sorted into document order or the wrap-around fires on the wrong element.
        const items = Array.prototype.slice.call(menu.querySelectorAll(FOCUSABLE));
        if (menuBtn) items.push(menuBtn);
        if (!items.length) return;
        items.sort((a, b) =>
            a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1
        );
        const first = items[0];
        const last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });

    return { setMenu };
}
