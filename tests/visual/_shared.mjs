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
export const WIDTHS = [1440, 1180, 900, 760, 390];

/** Frozen so the live IST clock renders identically in every capture. */
export const FIXED_TIME = new Date('2026-01-01T12:34:56Z');

/** docs/prototype-fixes.md F1 — scope the feed row rule to .feed-in's children. */
const FIXES = [
    ['.feed div{display:grid;', '.feed-in>div{display:grid;'],
    ['.feed div{grid-template-columns:72px minmax(0,1fr)}', '.feed-in>div{grid-template-columns:72px minmax(0,1fr)}'],
    ['.feed div b{display:none}', '.feed-in>div b{display:none}'],
];

/** Writes the prototype variant and returns a file:// URL for it. */
export function prototypeUrl(variant) {
    const out = join(ROOT, 'storage', 'app', 'prototype', variant);
    mkdirSync(join(out, 'assets'), { recursive: true });
    for (const f of readdirSync(join(DESIGN, 'assets'))) {
        copyFileSync(join(DESIGN, 'assets', f), join(out, 'assets', f));
    }
    let html = readFileSync(join(DESIGN, 'index.html'), 'utf8');
    for (const [from, to] of FIXES) {
        if (!html.includes(from)) throw new Error(`prototype fix F1 no longer applies: ${from}`);
        html = html.replace(from, to);
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
