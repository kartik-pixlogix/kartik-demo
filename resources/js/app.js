/* UB Shield — script entry.
 *
 * Phase 1 ports the prototype's single IIFE (design/v7/index.html, lines
 * 851-1046) into the modules under ./modules, keeping two things it is easy to
 * get wrong (docs/inventory.md §11):
 *
 *   1. the scroll listener only sets `dirty = true`; all scroll work happens in
 *      the rAF loop;
 *   2. layout() re-runs on resize, on document.fonts.ready AND on load, because
 *      the pinned-services distance depends on text metrics.
 *
 * The root element carries `ds6` plus the JS-toggled state classes
 * `mo`, `has-cur`, `rvon`, `pinon` and `menu-open`.
 */
