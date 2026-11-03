/* Shared paths and helpers for the visual regression harness.
 *
 * `design/v7/` is read-only, so the two prototype variants the harness needs are
 * materialised under storage/app/prototype/ (gitignored) on each run:
 *
 *   resting/  motion = false, as docs/reference/ was captured
 *   motion/   motion left on, for the pinned-services comparison
 *
 * Both carry the approved deviations in docs/prototype-fixes.md.
 */
import { readFileSync, writeFileSync, mkdirSync, copyFileSync, readdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

export const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..', '..');
export const DESIGN = join(ROOT, 'design', 'v7');
export const REF = join(ROOT, 'docs', 'reference');
export const DIFF = join(ROOT, 'docs', 'diff');
export const BASE_URL = process.env.BASE_URL || 'http://127.0.0.1:8000/';

/**
 * The build, rendered carrying the design's own content.
 *
 * The references are captures of `design/v7/index.html`, which is full of
 * placeholders — `[email address]`, `[XX]%`, dashed "Client logo" boxes. Once
 * the client fills any of that in, the live page legitimately stops matching,
 * and a diff that is permanently non-zero catches nothing.
 *
 * `?design-preview=1` pins settings to the seeder's values and the client
 * marquee to its placeholders, so the diff keeps measuring only CSS and
 * markup. Local and testing only — see App\Support\DesignPreview.
 */
export const designUrl = (path = '') => {
    const u = new URL(path, BASE_URL);
    u.searchParams.set('design-preview', '1');
    return u.href;
};
export const WIDTHS = [1440, 1180, 900, 760, 390];

/** Frozen so the live IST clock renders identically in every capture. */
export const FIXED_TIME = new Date('2026-01-01T12:34:56Z');

/**
 * The approved deviations in docs/prototype-fixes.md, applied to the prototype
 * before it is captured — otherwise the references would encode the old
 * behaviour and the build would "fail" the diff for doing the agreed thing.
 * Must stay in step with the FIXES list in scripts/split-prototype-css.mjs.
 *
 *   F1  scope the feed row rule to .feed-in's children
 *   F2  --faint .38 -> .44, for 3:1 contrast on the de-emphasised counter total
 *   F3  footer headings h4 -> h3, so the outline does not skip a level. BOTH
 *       the selector and the markup move, or the reference would lose the
 *       footer heading styling and stop matching the build.
 *   F4  word-fade reveal rests at .38 instead of .12. No effect on the capture,
 *       which is taken with motion off, but kept here so the two lists agree.
 */
const FIXES = [
    ['--faint:rgba(199,208,216,.38)', '--faint:rgba(199,208,216,.44)'],
    ['.feed div{display:grid;', '.feed-in>div{display:grid;'],
    ['.feed div{grid-template-columns:72px minmax(0,1fr)}', '.feed-in>div{grid-template-columns:72px minmax(0,1fr)}'],
    ['.feed div b{display:none}', '.feed-in>div b{display:none}'],
    ['.foot h4{', '.foot h3{'],
    ['.foot h4 .dot{', '.foot h3 .dot{'],
    ['.rvon .rw .w{opacity:.12;', '.rvon .rw .w{opacity:.38;'],
];

/*
 * The prototype's shield, both in the header and as the emblem, is a drawing
 * of the client's logo rather than the logo. The client supplied the real
 * artwork and asked for it to be used (docs/prototype-fixes.md A7), so the
 * reference has to carry the same swap or every capture would differ in the
 * four places the mark appears and nowhere else — which is exactly what the
 * diff is for detecting.
 *
 * The placement numbers are the ones in resources/views/components/
 * shield-emblem.blade.php and must stay in step with it.
 */
const MARK = 'assets/ub-shield.png';
const MARK_IMAGE =
    `<image href="${MARK}" x="8.7" y="25.3" width="222.6" height="229.4"></image>`;

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

const NEW_SCAN =
    '<g mask="url(#embm)"><g class="scanE">' +
    '<rect x="-10" y="122" width="260" height="18" fill="#27B8ED" opacity=".22"></rect>' +
    '<rect x="-10" y="136" width="260" height="3" fill="#F4F6F8"></rect>' +
    '</g></g>';

const lit = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

/** F3 moves markup too; A7 swaps the hand-drawn mark for the client's. */
const MARKUP_FIXES = [
    [/<h4>/g, '<h3>'],
    [/<\/h4>/g, '</h3>'],

    // A7 — the header mark. Scan before paths, or the paths rule eats it first.
    [/src="assets\/ub-monogram\.png"/g, `src="${MARK}"`],
    [new RegExp(lit(EMBLEM_SCAN), 'g'), NEW_SCAN],
    [new RegExp(lit(EMBLEM_PATHS), 'g'), MARK_IMAGE],

    // The beam is masked by the artwork's alpha now, so the mask has to exist
    // and mask-type has to be set. The build does both from
    // <x-brand-defs /> and resources/css/inner-pages.css.
    [
        /<clipPath id="embc">/,
        '<mask id="embm" maskUnits="userSpaceOnUse" x="0" y="0" width="240" height="280">'
            + MARK_IMAGE + '</mask><clipPath id="embc">',
    ],
    [/<\/style>/, '#embm{mask-type:alpha}</style>'],
];

/** Writes the prototype variant and returns a file:// URL for it. */
export function prototypeUrl(variant) {
    const out = join(ROOT, 'storage', 'app', 'prototype', variant);
    mkdirSync(join(out, 'assets'), { recursive: true });
    for (const f of readdirSync(join(DESIGN, 'assets'))) {
        copyFileSync(join(DESIGN, 'assets', f), join(out, 'assets', f));
    }
    // A7: the client's mark is not among the prototype's own assets, and
    // design/v7/ is read-only, so it is brought in from the build's copy.
    copyFileSync(
        join(ROOT, 'public', 'images', 'brand', 'ub-shield.png'),
        join(out, 'assets', 'ub-shield.png'),
    );
    let html = readFileSync(join(DESIGN, 'index.html'), 'utf8');
    for (const [from, to] of FIXES) {
        if (!html.includes(from)) throw new Error(`approved deviation no longer applies: ${from}`);
        html = html.replace(from, to);
    }
    for (const [pattern, to] of MARKUP_FIXES) {
        html = html.replace(pattern, to);
    }
    if (variant === 'resting') {
        const flag = 'var motion = !reduced;';
        if (!html.includes(flag)) throw new Error('motion flag not found in the prototype');
        html = html.replace(flag, 'var motion = false;');
    }
    writeFileSync(join(out, 'index.html'), html);
    return pathToFileURL(join(out, 'index.html')).href;
}

/**
 * Reproduces the reference capture conditions on the build: app.js must see
 * reduced motion (so it skips mo/rvon/pinon/has-cur) while the CSS
 * prefers-reduced-motion block, which the references never saw, stays off.
 */
export const forceMotionOff = () => {
    const real = window.matchMedia.bind(window);
    window.matchMedia = (q) =>
        q === '(prefers-reduced-motion: reduce)'
            ? {
                  matches: true,
                  media: q,
                  addEventListener() {},
                  removeEventListener() {},
                  addListener() {},
                  removeListener() {},
              }
            : real(q);
};
