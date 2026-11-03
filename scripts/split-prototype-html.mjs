/* Splits design/v7/index.html's <body> into Blade section components.
 *
 * Same principle as the CSS splitter: the markup is copied byte-for-byte from
 * the prototype rather than retyped, so the only differences from the original
 * are the ones listed in REWRITES below. Shared components (arrow circle,
 * shield emblem, …) are factored out afterwards, with the visual diff against
 * docs/reference/ acting as the regression test for that refactor.
 *
 *   node scripts/split-prototype-html.mjs
 */
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const SRC = join(ROOT, 'design', 'v7', 'index.html');
const OUT = join(ROOT, 'resources', 'views', 'components');

const lines = readFileSync(SRC, 'utf8').split(/\r?\n/);
const range = (a, b) => lines.slice(a - 1, b).join('\n');

/** Differences from the prototype that every section is subject to. */
const REWRITES = [
    // brand PNGs move from design/v7/assets/ to public/images/brand/
    [/src="assets\/ub-monogram\.png"/g, 'src="/images/brand/ub-monogram.png"'],
    [/src="assets\/ub-wordmark\.png"/g, 'src="/images/brand/ub-wordmark.png"'],
];

/** file -> [description, from, to] */
const PARTS = {
    'svg-defs.blade.php': ['Shared SVG defs. Emitted once, at the top of the body.', 497, 507],
    'ui/cursor.blade.php': ['Custom cursor follower.', 509, 509],
    'ui/industry-preview.blade.php': ['Floating industry artwork previews (6).', 511, 520],
    'sections/header.blade.php': ['Header: brand, nav, services dropdown, action, progress bar.', 522, 545],
    'sections/mobile-menu.blade.php': ['Mobile circular-reveal menu.', 547, 555],
    'sections/hero.blade.php': ['Hero: chip, headline, lead, CTAs, artwork, stats.', 557, 586],
    'sections/trust.blade.php': ['Trust strip: credentials and the client-logo marquee.', 588, 599],
    'sections/services.blade.php': ['Services: 5 cards in the pinned horizontal track.', 601, 648],
    'sections/statement.blade.php': ['Statement with the brush stroke.', 650, 656],
    'sections/process.blade.php': ['How we work: sticky counter and 4 steps.', 658, 705],
    'sections/soc.blade.php': ['Managed SOC: radar, IST clock, incident feed.', 707, 739],
    'sections/industries.blade.php': ['Industries: 6 hover rows.', 741, 760],
    'sections/frameworks.blade.php': ['Frameworks: 2 marquees and 4 notes.', 762, 776],
    'sections/cases.blade.php': ['Case files: 2 cards and the second stats row.', 778, 800],
    'sections/insights.blade.php': ['Field notes: 3 posts and the All resources tile.', 802, 812],
    'sections/strip.blade.php': ['Closing strip: CTA block, form, artwork, contact.', 814, 833],
    'sections/footer.blade.php': ['Footer: 4 link columns, shield lockup, bottom bar.', 835, 846],
};

for (const [name, [desc, a, b]] of Object.entries(PARTS)) {
    let body = range(a, b);
    for (const [re, to] of REWRITES) body = body.replace(re, to);
    const header = `{{--\n    ${desc}\n    Ported verbatim from design/v7/index.html lines ${a}-${b}.\n` +
        `    Regenerate with scripts/split-prototype-html.mjs — do not retype markup.\n--}}\n`;
    const path = join(OUT, name);
    mkdirSync(dirname(path), { recursive: true });
    writeFileSync(path, header + body + '\n');
    console.log(`${name.padEnd(36)} lines ${a}-${b}`);
}

// the body must be fully accounted for: 495 opens .ds6, 848 closes it, 849 is <script>
const claimed = new Set();
for (const [, [, a, b]] of Object.entries(PARTS)) for (let i = a; i <= b; i++) claimed.add(i);
const missing = [];
for (let i = 496; i <= 847; i++) if (!claimed.has(i) && lines[i - 1].trim() !== '') missing.push(i);
if (missing.length) throw new Error(`unclaimed body lines: ${missing.join(', ')}`);
console.log('\nevery non-blank line of the prototype <body> is accounted for.');
