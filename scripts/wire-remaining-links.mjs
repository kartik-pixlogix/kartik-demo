/* Phase 3, second pass: now that the remaining inner pages exist, point the
 * last of the prototype's anchors at them.
 *
 * Still deliberately left alone:
 *   #contact — the closing strip carries id="contact" and is on every page,
 *              so that fragment must stay local
 *   #services / #industries / #compliance / #insights in the NAV — these go
 *              through home_anchor(), already applied in the first pass
 *
 *   node scripts/wire-remaining-links.mjs
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const S = join(ROOT, 'resources', 'views', 'components', 'sections');

/** [file, find, replace] — each must match exactly once */
const EDITS = [
    // --- header / mobile menu: Resources now has a page of its own
    ['header.blade.php', `<a href="{{ home_anchor('insights') }}">Resources</a>`, `<a href="{{ route('insights.index') }}">Resources</a>`],
    ['header.blade.php', `<a href="{{ home_anchor('about') }}">About</a>`, `<a href="{{ route('about') }}">About</a>`],
    ['header.blade.php', '<a href="#contact" class="act mag">', `<a href="{{ route('contact') }}" class="act mag">`],
    ['mobile-menu.blade.php', `<a href="{{ home_anchor('insights') }}">Resources</a>`, `<a href="{{ route('insights.index') }}">Resources</a>`],
    ['mobile-menu.blade.php', `<a href="{{ home_anchor('about') }}">About</a>`, `<a href="{{ route('about') }}">About</a>`],
    ['mobile-menu.blade.php', '<a href="#contact">Talk to us</a>', `<a href="{{ route('contact') }}">Talk to us</a>`],

    // --- homepage industries list
    ['industries.blade.php', '<a href="#contact" class="ind-row" data-v="1">', `<a href="{{ route('industries.show', 'banking-finance') }}" class="ind-row" data-v="1">`],
    ['industries.blade.php', '<a href="#contact" class="ind-row" data-v="2">', `<a href="{{ route('industries.show', 'healthcare') }}" class="ind-row" data-v="2">`],
    ['industries.blade.php', '<a href="#contact" class="ind-row" data-v="3">', `<a href="{{ route('industries.show', 'saas-it') }}" class="ind-row" data-v="3">`],
    ['industries.blade.php', '<a href="#contact" class="ind-row" data-v="4">', `<a href="{{ route('industries.show', 'manufacturing') }}" class="ind-row" data-v="4">`],
    ['industries.blade.php', '<a href="#contact" class="ind-row" data-v="5">', `<a href="{{ route('industries.show', 'education') }}" class="ind-row" data-v="5">`],
    ['industries.blade.php', '<a href="#contact" class="ind-row" data-v="6">', `<a href="{{ route('industries.show', 'e-commerce') }}" class="ind-row" data-v="6">`],

    // --- homepage field notes
    ['insights.blade.php', '<a href="#insights" class="post">', `<a href="{{ route('insights.show', $insight->slug) }}" class="post">`],
    ['insights.blade.php', '<a href="#insights" class="all">', `<a href="{{ route('insights.index') }}" class="all">`],

    // --- footer
    ['footer.blade.php', `<li><a href="{{ home_anchor('about') }}">About</a></li>`, `<li><a href="{{ route('about') }}">About</a></li>`],
    ['footer.blade.php', `<li><a href="{{ home_anchor('insights') }}">Resources</a></li>`, `<li><a href="{{ route('insights.index') }}">Resources</a></li>`],
    // Careers has no page of its own; About is the closest honest destination.
    ['footer.blade.php', '<li><a href="#about">Careers</a></li>', `<li><a href="{{ route('about') }}">Careers</a></li>`],
    ['footer.blade.php', '<a href="#top">Privacy policy</a>', `<a href="{{ route('privacy') }}">Privacy policy</a>`],
    ['footer.blade.php', '<a href="#top">Terms</a>', `<a href="{{ route('terms') }}">Terms</a>`],
    ['footer.blade.php', '<a href="#top">Responsible disclosure</a>', `<a href="{{ route('responsible-disclosure') }}">Responsible disclosure</a>`],
    ['footer.blade.php', '<li><a href="#contact">Ahmedabad, India</a></li>', `<li><a href="{{ route('contact') }}">Ahmedabad, India</a></li>`],

    // --- homepage case cards: no case-study detail route exists (flagged for
    //     the client), so they point at the field notes index as before
    ['cases.blade.php', '<a href="#insights" class="case">', `<a href="{{ route('insights.index') }}" class="case">`],
];

for (const [file, find, replace] of EDITS) {
    const path = join(S, file);
    const before = readFileSync(path, 'utf8');
    const hits = before.split(find).length - 1;
    if (hits !== 1) {
        throw new Error(`${file}: expected 1 occurrence of "${find.slice(0, 60)}", found ${hits}`);
    }
    writeFileSync(path, before.replace(find, replace));
}

console.log(`${EDITS.length} links rewired.`);

// Report what is deliberately still an anchor
for (const file of ['header.blade.php', 'mobile-menu.blade.php', 'hero.blade.php', 'services.blade.php',
    'soc.blade.php', 'industries.blade.php', 'cases.blade.php', 'insights.blade.php', 'footer.blade.php']) {
    const left = [...new Set(readFileSync(join(S, file), 'utf8').match(/href="#[a-z-]+"/g) || [])];
    console.log(`${file.padEnd(26)} ${left.length ? left.join(' ') : '—'}`);
}
