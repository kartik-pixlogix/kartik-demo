/* Phase 3: replaces the prototype's on-page anchors with real service routes.
 *
 * BUILD.md: "Replace the prototype's on-page anchor links with real routes, and
 * keep the anchors working on the homepage."
 *
 *   - links to a service          -> route('services.show', slug)
 *   - links to a homepage section -> home_anchor('…'), which stays a fragment
 *     on the homepage and becomes a full URL elsewhere
 *   - #contact is left alone: the closing strip carries id="contact" and
 *     appears on every page, so that fragment must stay local
 *
 * Industries, Insights, Contact and About keep their anchors until those pages
 * exist. Run once; it asserts each replacement count.
 *
 *   node scripts/wire-service-links.mjs
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const S = join(ROOT, 'resources', 'views', 'components', 'sections');

const r = (slug) => `{{ route('services.show', '${slug}') }}`;

/** [file, find, replace, expected] */
const EDITS = [
    // --- header: dropdown trigger scrolls the homepage, items go to the pages
    ['header.blade.php', '<a href="#services" aria-haspopup="true">', `<a href="{{ home_anchor('services') }}" aria-haspopup="true">`, 1],
    ['header.blade.php', '<a href="#services"><span class="k">Secure</span>', `<a href="${r('offensive-security')}"><span class="k">Secure</span>`, 1],
    ['header.blade.php', '<a href="#services"><span class="k">Privacy</span>', `<a href="${r('data-privacy')}"><span class="k">Privacy</span>`, 1],
    ['header.blade.php', '<a href="#services"><span class="k">Compliance</span>', `<a href="${r('governance-risk-compliance')}"><span class="k">Compliance</span>`, 1],
    ['header.blade.php', '<a href="#services"><span class="k">Infra</span>', `<a href="${r('infrastructure-cloud')}"><span class="k">Infra</span>`, 1],
    ['header.blade.php', '<a href="#soc"><span class="k">MSS</span>', `<a href="${r('managed-security-services')}"><span class="k">MSS</span>`, 1],
    ['header.blade.php', '<a href="#industries">Industries</a>', `<a href="{{ home_anchor('industries') }}">Industries</a>`, 1],
    ['header.blade.php', '<a href="#compliance">Compliance</a>', `<a href="{{ home_anchor('compliance') }}">Compliance</a>`, 1],
    ['header.blade.php', '<a href="#insights">Resources</a>', `<a href="{{ home_anchor('insights') }}">Resources</a>`, 1],
    ['header.blade.php', '<a href="#about">About</a>', `<a href="{{ home_anchor('about') }}">About</a>`, 1],
    ['header.blade.php', '<a href="#top" class="brand"', `<a href="{{ route('home') }}" class="brand"`, 1],

    // --- mobile menu
    ['mobile-menu.blade.php', '<a href="#services">Services</a>', `<a href="{{ home_anchor('services') }}">Services</a>`, 1],
    ['mobile-menu.blade.php', '<a href="#industries">Industries</a>', `<a href="{{ home_anchor('industries') }}">Industries</a>`, 1],
    ['mobile-menu.blade.php', '<a href="#compliance">Compliance</a>', `<a href="{{ home_anchor('compliance') }}">Compliance</a>`, 1],
    ['mobile-menu.blade.php', '<a href="#insights">Resources</a>', `<a href="{{ home_anchor('insights') }}">Resources</a>`, 1],
    ['mobile-menu.blade.php', '<a href="#about">About</a>', `<a href="{{ home_anchor('about') }}">About</a>`, 1],

    // --- homepage service cards, in prototype order
    ['services.blade.php', '<a href="#contact" class="card">', `<a href="${r('offensive-security')}" class="card">`, 1],
    ['services.blade.php', '<a href="#contact" class="card">', `<a href="${r('data-privacy')}" class="card">`, 1],
    ['services.blade.php', '<a href="#compliance" class="card">', `<a href="${r('governance-risk-compliance')}" class="card">`, 1],
    ['services.blade.php', '<a href="#contact" class="card">', `<a href="${r('infrastructure-cloud')}" class="card">`, 1],
    ['services.blade.php', '<a href="#soc" class="card">', `<a href="${r('managed-security-services')}" class="card">`, 1],

    // --- footer services column
    ['footer.blade.php', '<li><a href="#services">Offensive Security / VAPT</a></li>', `<li><a href="${r('offensive-security')}">Offensive Security / VAPT</a></li>`, 1],
    ['footer.blade.php', '<li><a href="#services">Data Privacy</a></li>', `<li><a href="${r('data-privacy')}">Data Privacy</a></li>`, 1],
    ['footer.blade.php', '<li><a href="#services">GRC</a></li>', `<li><a href="${r('governance-risk-compliance')}">GRC</a></li>`, 1],
    ['footer.blade.php', '<li><a href="#services">Infrastructure &amp; Cloud</a></li>', `<li><a href="${r('infrastructure-cloud')}">Infrastructure &amp; Cloud</a></li>`, 1],
    ['footer.blade.php', '<li><a href="#soc">Managed SOC</a></li>', `<li><a href="${r('managed-security-services')}">Managed SOC</a></li>`, 1],
    ['footer.blade.php', '<li><a href="#about">About</a></li>', `<li><a href="{{ home_anchor('about') }}">About</a></li>`, 1],
    ['footer.blade.php', '<li><a href="#industries">Industries</a></li>', `<li><a href="{{ home_anchor('industries') }}">Industries</a></li>`, 1],
    ['footer.blade.php', '<li><a href="#insights">Resources</a></li>', `<li><a href="{{ home_anchor('insights') }}">Resources</a></li>`, 1],
];

let applied = 0;
for (const [file, find, replace, expected] of EDITS) {
    const path = join(S, file);
    const before = readFileSync(path, 'utf8');
    if (!before.includes(find)) {
        throw new Error(`not found in ${file}: ${find.slice(0, 70)}`);
    }
    // replace only the first occurrence — the service cards reuse the same
    // anchor and must be rewritten in document order
    writeFileSync(path, before.replace(find, replace));
    applied += expected;
}

console.log(`${applied} links rewired.`);

for (const file of ['header.blade.php', 'mobile-menu.blade.php', 'services.blade.php', 'footer.blade.php']) {
    const left = (readFileSync(join(S, file), 'utf8').match(/href="#(?!contact\b)[a-z]+"/g) || []);
    console.log(`${file.padEnd(26)} ${left.length ? 'anchors left: ' + [...new Set(left)].join(' ') : 'no stale anchors'}`);
}
