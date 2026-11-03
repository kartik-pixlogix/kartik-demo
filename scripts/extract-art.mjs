/* Lifts each inline illustration out of the Blade sections into its own
 * component under resources/views/components/art/.
 *
 * BUILD.md §3: "Extract the shared SVG defs and every illustration into Blade
 * SVG components, keeping all IDs, gradients and animation classes intact."
 * The SVG source is moved byte-for-byte; only its location changes. The visual
 * diff against docs/reference/ is the regression test.
 *
 *   node scripts/extract-art.mjs
 */
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const COMP = join(ROOT, 'resources', 'views', 'components');
const ART = join(COMP, 'art');
mkdirSync(ART, { recursive: true });

/** Illustrations to lift, in the DOM order they appear in each file. */
const PLAN = {
    'sections/hero.blade.php': [
        ['hero-grid', 'Hero layer 1 (depth 14): dot grid, radar rings and corner traces.'],
        ['hero-nodes', 'Hero layer 2 (depth 30): WAF / SIEM / EDR / IAM nodes and their links.'],
        ['hero-badges', 'Hero layer 4 (depth 52): MFA and TLS badges plus two small shields.'],
    ],
    'sections/services.blade.php': [
        ['service-vapt', 'Service 01 — Offensive Security / VAPT: browser window, findings, crosshair.'],
        ['service-privacy', 'Service 02 — Data Privacy: fingerprint scan and redaction bars.'],
        ['service-grc', 'Service 03 — GRC: clipboard, ticks and the AUDIT READY dial.'],
        ['service-infra', 'Service 04 — Infrastructure & Cloud: cloud and three racks.'],
        ['service-mss', 'Service 05 — Managed Security Services: monitor, EKG trace, radar.'],
    ],
    'sections/statement.blade.php': [['brush-stroke', 'The blue brush stroke behind "fixed."']],
    'sections/cases.blade.php': [
        ['case-rbi-csf', 'Case file 01 — RBI cyber framework.'],
        ['case-soc2', 'Case file 02 — SOC 2 readiness.'],
    ],
    'sections/insights.blade.php': [
        ['note-consent', 'Field note 01 — consent toggles.'],
        ['note-controls', 'Field note 02 — control checklist grid.'],
        ['note-cloud', 'Field note 03 — bucket public to private.'],
    ],
    'sections/strip.blade.php': [['strip-circuit', 'Closing strip panel: circuit traces and the shield emblem.']],
    'ui/industry-preview.blade.php': [
        ['industry-1', 'Industry preview 01 — Banking & finance.'],
        ['industry-2', 'Industry preview 02 — Healthcare.'],
        ['industry-3', 'Industry preview 03 — SaaS & IT.'],
        ['industry-4', 'Industry preview 04 — Manufacturing.'],
        ['industry-5', 'Industry preview 05 — Education.'],
        ['industry-6', 'Industry preview 06 — E-commerce.'],
    ],
};

const BIG = /viewBox="0 0 (500 400|640 760|500 700|400 80)"/;

/** Returns [start, end) of the <svg> element beginning at `from`, by depth. */
function svgSpan(s, from) {
    let i = from, depth = 0;
    while (i < s.length) {
        const open = s.indexOf('<svg', i);
        const close = s.indexOf('</svg>', i);
        if (close === -1) throw new Error('unterminated <svg>');
        if (open !== -1 && open < close) { depth++; i = open + 4; }
        else { depth--; i = close + 6; if (depth === 0) return [from, i]; }
    }
    throw new Error('unterminated <svg>');
}

let made = 0;
for (const [rel, names] of Object.entries(PLAN)) {
    const path = join(COMP, rel);
    let s = readFileSync(path, 'utf8');
    for (const [name, desc] of names) {
        // find the next big illustration that has not been replaced yet
        let idx = -1, search = 0;
        for (;;) {
            const at = s.indexOf('<svg', search);
            if (at === -1) break;
            const [a, b] = svgSpan(s, at);
            if (BIG.test(s.slice(a, Math.min(b, a + 300)))) { idx = a; break; }
            search = b;
        }
        if (idx === -1) throw new Error(`no illustration left for ${name} in ${rel}`);
        const [a, b] = svgSpan(s, idx);
        const svg = s.slice(a, b);
        writeFileSync(join(ART, `${name}.blade.php`), `{{-- ${desc} --}}\n${svg}`);
        s = s.slice(0, a) + `<x-art.${name} />` + s.slice(b);
        made++;
        console.log(`art/${name}.blade.php`.padEnd(36) + `${svg.length} bytes  <- ${rel}`);
    }
    // nothing big should be left behind
    let search = 0;
    for (;;) {
        const at = s.indexOf('<svg', search);
        if (at === -1) break;
        const [a, b] = svgSpan(s, at);
        if (BIG.test(s.slice(a, Math.min(b, a + 300)))) throw new Error(`unlifted illustration in ${rel} at ${a}`);
        search = b;
    }
    writeFileSync(path, s);
}
console.log(`\n${made} illustrations lifted into components/art/.`);
