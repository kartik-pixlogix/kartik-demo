/* Factors the repeated markup in the generated Blade sections into components.
 *
 * Runs after scripts/split-prototype-html.mjs. Every replacement below is an
 * exact string swap, and the visual diff against docs/reference/ is the
 * regression test — the page must still render 0 differing pixels afterwards.
 *
 *   node scripts/extract-components.mjs
 */
import { readFileSync, writeFileSync, readdirSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const COMP = join(ROOT, 'resources', 'views', 'components');

const ARROW_SVGS =
    '<svg class="a1" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"></path></svg>' +
    '<svg class="a2" viewBox="0 0 24 24"><path d="M7 17 17 7M9 7h8v8"></path></svg>';

const EMBLEM_PATHS =
    '<path d="M112 10 24 40v106c0 50 38 88 88 120Z" fill="url(#gSl)"></path>' +
    '<path d="M128 10l88 30v106c0 50-38 88-88 120Z" fill="url(#gBl)"></path>' +
    '<path d="M62 72v76c0 28 18 48 38 56" fill="none" stroke="#061522" stroke-width="15"></path>' +
    '<path d="M128 82l52 18-52 24Zm0 58 56 20-56 28Z" fill="#061522"></path>' +
    '<path d="M112 10 24 40v34l88-32Z" fill="#ffffff" opacity=".35"></path>';

const EMBLEM_SCAN =
    '<g clip-path="url(#embc)"><g class="scanE">' +
    '<rect x="0" y="122" width="240" height="18" fill="#27B8ED" opacity=".22"></rect>' +
    '<rect x="0" y="136" width="240" height="3" fill="#F4F6F8"></rect>' +
    '</g></g>';

/** [find, replace, expected occurrences across all files] */
const SWAPS = [
    [`<span class="ac on-acid">${ARROW_SVGS}</span>`, '<x-ui.arrow-circle on-acid />', 1],
    [`<span class="ac">${ARROW_SVGS}</span>`, '<x-ui.arrow-circle />', 20],
    [
        `<svg class="emb" viewBox="0 0 240 280" aria-hidden="true">${EMBLEM_PATHS}${EMBLEM_SCAN}</svg>`,
        '<x-shield-emblem class="emb" scan />',
        1,
    ],
    [
        `<svg class="femb" viewBox="0 0 240 280" aria-hidden="true">${EMBLEM_PATHS}</svg>`,
        '<x-shield-emblem class="femb" />',
        1,
    ],
    [
        `<g transform="translate(130 215)">${EMBLEM_PATHS}${EMBLEM_SCAN}</g>`,
        '<x-shield-emblem group transform="translate(130 215)" scan />',
        1,
    ],
];

const files = [];
for (const dir of ['sections', 'ui']) {
    for (const f of readdirSync(join(COMP, dir))) {
        if (f.endsWith('.blade.php')) files.push(join(COMP, dir, f));
    }
}

const counts = SWAPS.map(() => 0);
for (const path of files) {
    let s = readFileSync(path, 'utf8');
    const before = s;
    SWAPS.forEach(([find, repl], i) => {
        let n = 0;
        while (s.includes(find)) {
            s = s.replace(find, repl);
            n++;
            if (n > 100) throw new Error('runaway replacement');
        }
        counts[i] += n;
    });
    if (s !== before) writeFileSync(path, s);
}

let bad = false;
SWAPS.forEach(([, repl, want], i) => {
    const ok = counts[i] === want;
    if (!ok) bad = true;
    console.log(`${ok ? 'ok  ' : 'FAIL'} ${String(counts[i]).padStart(3)}/${want}  ${repl}`);
});
if (bad) throw new Error('replacement counts do not match the prototype — check the source markup');
console.log('\nall component swaps applied as expected.');
