/* Scroll reveals. Prototype: the IntersectionObserver built inside layout().
 * Adds .in, fires any nested [data-count], then unobserves. */
import { countUp } from './count-up.js';

export function createRevealObserver($$) {
    const io = new IntersectionObserver(
        (ents) => {
            ents.forEach((en) => {
                if (!en.isIntersecting) return;
                en.target.classList.add('in');
                $$('[data-count]', en.target).forEach(countUp);
                io.unobserve(en.target);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -6% 0px' }
    );
    $$('.rv,.rm,.rs,.rl,.rw,.brush,.art.rc,.stats').forEach((el) => io.observe(el));
    return io;
}
