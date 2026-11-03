/* Pauses infinite animation outside the viewport (BUILD.md Phase 4).
 *
 * The prototype runs a lot of continuous CSS animation — marquees, the radar
 * sweep, the SOC feed, and `.flow` / `.pl2` / `.spin` / `.scan*` inside every
 * illustration. On the homepage that is well over a hundred animated elements,
 * all of them compositing whether or not anybody can see them. On a mid-range
 * phone that is the single biggest cost on the page.
 *
 * Each animated block gets `.offscreen` while it is out of view, which
 * `motion.css` turns into `animation-play-state: paused`. Pausing rather than
 * disabling means an animation resumes mid-cycle instead of restarting, so
 * scrolling past and back does not visibly reset anything.
 *
 * This changes nothing a visitor can see: by definition the paused elements
 * are off-screen. The visual diff stays at 0 because the harness scrolls each
 * section into view before capturing.
 */

const WATCH = '.hero-art, .art, .radar, .feed, .lmq, .mq, .strip .img, .flogo';

export function initOffscreenPausing($$) {
    if (!('IntersectionObserver' in window)) return null;

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                entry.target.classList.toggle('offscreen', !entry.isIntersecting);
            });
        },
        // Generous margin: start again before it scrolls into view, so nothing
        // is ever caught mid-resume.
        { rootMargin: '200px 0px' }
    );

    $$(WATCH).forEach((el) => io.observe(el));

    return io;
}
