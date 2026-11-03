/* Splits design/v7/index.html's <style> block into resources/css partials.
 *
 * The point is fidelity: every declaration is copied byte-for-byte from the
 * prototype rather than retyped, and the line order inside each partial is the
 * prototype's own order. The partials are then imported by app.css in the same
 * order the rules appeared in the prototype (docs/inventory.md §9).
 *
 * Re-runnable. Phase 1 only — once the CSS is in place this script is kept as
 * provenance, not as part of the build.
 *
 *   node scripts/split-prototype-css.mjs
 */
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const SRC = join(ROOT, 'design', 'v7', 'index.html');
const OUT = join(ROOT, 'resources', 'css');

const lines = readFileSync(SRC, 'utf8').split(/\r?\n/);
const L = (n) => lines[n - 1]; // 1-indexed, as reported by grep
const range = (a, b) => lines.slice(a - 1, b);

/*
 * Approved deviations from the prototype. Both are documented in
 * docs/prototype-fixes.md with their reasoning and their cost.
 *
 * F1  `.feed div` was a descendant selector that also matched the .feed-in
 *     wrapper, turning it into a grid so the SOC feed rows overlapped.
 *
 * F2  --faint at .38 composites to #4f5c67 on the navy background, which is
 *     2.69:1 — below the 3:1 WCAG 1.4.3 requires for large text. .44 gives
 *     3.19:1. It affects one thing on screen: the de-emphasised total in the
 *     "01 | 04" counters. Applied because BUILD.md Phase 4 sets Accessibility
 *     = 100 as a target; revert this line to restore the prototype exactly.
 *
 * F3  The footer headings are <h4> directly after an <h2>, which skips a
 *     level. The port uses <h3> and this moves the selector with it. Visually
 *     identical — the declarations are untouched.
 *
 * F4  The word-fade reveal rests at opacity .12, which is 1.37:1 while the
 *     statement is below the fold. Raised to .38 (3.31:1) so the text is
 *     legible before it animates. Invisible to the visual diff, which is
 *     captured with motion off and therefore never shows the resting state.
 */
const FIXES = [
    { line: 14, from: '--faint:rgba(199,208,216,.38)', to: '--faint:rgba(199,208,216,.44)' },
    { line: 234, from: '.feed div{display:grid;', to: '.feed-in>div{display:grid;' },
    { line: 300, from: '.foot h4{', to: '.foot h3{' },
    { line: 301, from: '.foot h4 .dot{', to: '.foot h3 .dot{' },
    { line: 340, from: '.rvon .rw .w{opacity:.12;', to: '.rvon .rw .w{opacity:.38;' },
    { line: 396, from: '.feed div{grid-template-columns:72px minmax(0,1fr)}', to: '.feed-in>div{grid-template-columns:72px minmax(0,1fr)}' },
    { line: 397, from: '.feed div b{display:none}', to: '.feed-in>div b{display:none}' },
];
for (const f of FIXES) {
    const cur = L(f.line);
    if (! cur.includes(f.from)) {
        throw new Error(`deviation no longer applies at line ${f.line}\n  expected: ${f.from}\n  found:    ${cur}`);
    }
    lines[f.line - 1] = cur.replace(f.from, f.to);
}

const HEADER = (what, src) =>
    `/* ${what}\n * Ported verbatim from design/v7/index.html (${src}).\n` +
    ` * Do not retype values — regenerate with scripts/split-prototype-css.mjs.\n */\n`;

/** name -> [description, [[from,to], ...]] in prototype line order */
const FILES = {
    'tokens.css': ['Design tokens. The only colours allowed anywhere.', [[14, 14]]],
    'base.css': ['Reset, document defaults and focus rings.', [[12, 13], [15, 19]]],
    'type.css': ['Type scale.', [[21, 26]]],
    'layout.css': ['Container, section rhythm and section headers.', [[20, 20], [29, 31]]],
    'components.css': ['Shared UI: title dot, chip, crosshair, arrow circle, text link, button.', [[27, 28], [32, 36], [38, 65]]],
    'cursor.css': ['Custom cursor follower (fine pointers only).', [[66, 80]]],
    'sections/header.css': ['Header, dropdown, mobile menu and scroll progress.', [[81, 121]]],
    'sections/hero.css': ['Hero.', [[122, 141]]],
    'sections/trust.css': ['Trust strip and client-logo marquee.', [[142, 152]]],
    'art.css': ['Artwork frames shared by cards, cases and posts.', [[153, 163]]],
    'sections/services.css': ['Services, including the pinned horizontal track.', [[164, 190]]],
    'sections/statement.css': ['Statement and brush stroke.', [[191, 197]]],
    'sections/process.css': ['How we work.', [[198, 213]]],
    'sections/soc.css': ['Managed SOC — the one inverted block.', [[214, 237]]],
    'sections/industries.css': ['Industries list and hover preview.', [[238, 252]]],
    'sections/frameworks.css': ['Frameworks marquees and notes.', [[253, 261]]],
    'sections/cases.css': ['Case files.', [[262, 268]]],
    'sections/insights.css': ['Field notes.', [[269, 278]]],
    'sections/strip.css': ['Closing strip: CTA block, form, artwork panel, contact.', [[279, 296]]],
    'sections/footer.css': ['Footer.', [[297, 309]]],
    'motion.css': ['Keyframes, the load sequence, scroll reveals and reduced-motion.', [[310, 347]]],
    'responsive.css': ['Breakpoints 1180 / 900 / 760. Imported after every section.', [[348, 418]]],
    'v7-overrides.css': [
        'V7 palette, logo lockup and cyber-artwork overrides.\n *\n' +
        ' * This block genuinely overrides earlier rules and MUST stay last:\n' +
        ' * .brand becomes an image lockup, .dd-m a:hover flips #1a1a1a to #dfe5ea,\n' +
        ' * and the arrow strokes are remapped. See docs/inventory.md §9.',
        [[419, 491]],
    ],
};

let total = 0;
for (const [name, [desc, spans]] of Object.entries(FILES)) {
    const src = spans.map(([a, b]) => (a === b ? `line ${a}` : `lines ${a}-${b}`)).join(', ');
    const body = spans.map(([a, b]) => range(a, b).join('\n')).join('\n');
    const path = join(OUT, name);
    mkdirSync(dirname(path), { recursive: true });
    writeFileSync(path, HEADER(desc, src) + body + '\n');
    const n = body.split('\n').length;
    total += n;
    console.log(`${name.padEnd(26)} ${String(n).padStart(3)} lines  (${src})`);
}

// every line of the <style> block must land in exactly one partial
const claimed = new Set();
for (const [, spans] of Object.values(FILES)) {
    for (const [a, b] of spans) for (let i = a; i <= b; i++) {
        if (claimed.has(i)) throw new Error(`line ${i} claimed twice`);
        claimed.add(i);
    }
}
const missing = [];
// blank separator lines between blocks carry nothing and are allowed to drop
for (let i = 12; i <= 491; i++) if (!claimed.has(i) && L(i).trim() !== '') missing.push(i);
console.log(`\n${total} of ${491 - 12 + 1} style lines written`);
if (missing.length) throw new Error(`unclaimed style lines: ${missing.join(', ')}`);
console.log('every line of the prototype <style> block is accounted for.');
