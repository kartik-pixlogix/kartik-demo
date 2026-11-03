/* Prepares the client's shield mark for use, and builds the favicon set.
 *
 * Run after any change to public/images/brand/ub-shield.png.
 *
 *   npm i --no-save sharp          # not a project dependency
 *   node scripts/build-brand-assets.mjs
 *
 * ------------------------------------------------------------------ 1. The mark
 *
 * The supplied artwork has a soft drop shadow baked into it, falling down and
 * to the right. Trimming the file to its alpha bounding box therefore trims to
 * the *shadow*, not to the logo: the solid mark ends up 30px left and 25px up
 * of its own file's centre.
 *
 * Nothing downstream can know that. Centre the file and the shield lands
 * up-left of wherever it was meant to go — measured on the hero at 1440px, the
 * emblem's box sat exactly on the artwork hub (dx -0.1, dy -0.4) while the
 * shield a viewer actually sees sat 12.2px left and 10.3px above it.
 *
 * So the file is padded with transparent pixels until the SOLID mark is
 * centred in it. The artwork is untouched — only empty space is added — and
 * every consumer (emblem, header lockup, favicons, admin logo, OG card) then
 * centres correctly with no per-use fudge factor.
 *
 * Idempotent: once the mark is centred the padding is zero and the step is
 * skipped, so re-running is safe.
 *
 * -------------------------------------------------------------- 2. The icons
 *
 * The client also supplied a circular badge — the obvious favicon, and the
 * wrong one: below about 96px its glow ring and lettering collapse into noise.
 * The mark alone on Deep Navy still reads at 32px.
 */
import sharp from 'sharp';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const BRAND = join(ROOT, 'public', 'images', 'brand');
const MARK = join(BRAND, 'ub-shield.png');

/** Deep Navy — the site's own background, so the icon needs no visible plate. */
const NAVY = { r: 6, g: 21, b: 34, alpha: 1 };

/** Alpha at or above this is the mark itself; below it is the drop shadow. */
const SOLID = 250;

/** Bounding box of the pixels at or above `minAlpha`. */
async function inkBox(file, minAlpha) {
    const { data, info } = await sharp(file).raw().ensureAlpha().toBuffer({ resolveWithObject: true });
    const { width, height } = info;
    let x0 = width, x1 = -1, y0 = height, y1 = -1;

    for (let y = 0; y < height; y++) {
        for (let x = 0; x < width; x++) {
            if (data[(y * width + x) * 4 + 3] < minAlpha) continue;
            if (x < x0) x0 = x;
            if (x > x1) x1 = x;
            if (y < y0) y0 = y;
            if (y > y1) y1 = y;
        }
    }
    return { x0, x1, y0, y1, width, height };
}

const box = await inkBox(MARK, SOLID);
const padLeft = Math.max(0, (box.width - 1 - box.x1) - box.x0);
const padRight = Math.max(0, box.x0 - (box.width - 1 - box.x1));
const padTop = Math.max(0, (box.height - 1 - box.y1) - box.y0);
const padBottom = Math.max(0, box.y0 - (box.height - 1 - box.y1));

if (padLeft || padRight || padTop || padBottom) {
    const centred = await sharp(MARK)
        .extend({ left: padLeft, right: padRight, top: padTop, bottom: padBottom, background: { r: 0, g: 0, b: 0, alpha: 0 } })
        .png({ compressionLevel: 9 })
        .toBuffer();

    await sharp(centred).toFile(MARK);
    await sharp(centred).webp({ lossless: true, effort: 6 }).toFile(join(BRAND, 'ub-shield.webp'));

    const after = await sharp(centred).metadata();
    console.log(`  ub-shield          padded L${padLeft} R${padRight} T${padTop} B${padBottom}`
        + `  ->  ${after.width}x${after.height}  (ratio ${(after.width / after.height).toFixed(4)})`);
} else {
    console.log('  ub-shield          already centred, left alone');
}

/* ---------------------------------------------------------------- icons --- */

/** [name, px, inset as a fraction of the size] */
const ICONS = [
    ['icon-32', 32, 0.04],
    ['icon-192', 192, 0.12],
    ['icon-512', 512, 0.12],
    // iOS masks to a squircle and clips the corners, so this one sits further in.
    ['apple-touch-icon', 180, 0.15],
];

const art = await sharp(MARK).toBuffer();

for (const [name, size, pad] of ICONS) {
    const inner = Math.round(size * (1 - pad * 2));
    const scaled = await sharp(art).resize(inner, inner, { fit: 'inside' }).toBuffer();
    const { width, height } = await sharp(scaled).metadata();

    await sharp({ create: { width: size, height: size, channels: 4, background: NAVY } })
        .composite([{
            input: scaled,
            left: Math.round((size - width) / 2),
            top: Math.round((size - height) / 2),
        }])
        .png({ compressionLevel: 9 })
        .toFile(join(BRAND, `${name}.png`));

    console.log(`  ${name.padEnd(18)} ${size}x${size}  mark ${width}x${height}`);
}

const final = await sharp(MARK).metadata();
console.log(`\nemblem placement for a 240x280 viewBox, matching the prototype's 192x266 area:`);
const ratio = final.width / final.height;
const h = Math.sqrt((192 * 266) / ratio);
const w = h * ratio;
console.log(`  x="${((240 - w) / 2).toFixed(1)}" y="${((280 - h) / 2).toFixed(1)}"`
    + ` width="${w.toFixed(1)}" height="${h.toFixed(1)}"`);
console.log(`  header <img> intrinsic: width="${final.width}" height="${final.height}"`);
