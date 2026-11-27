/*
 * Renders the carousel pages to output/posts/<nn-slug>/<nn-slug>-<page>.jpg
 * at 1080x1350 (JPEG, quality 96: what Instagram stores anyway, a third the size of PNG).
 *
 *   node social/instagram/src/render-posts.mjs          # all 15 posts
 *   node social/instagram/src/render-posts.mjs 4 5      # only posts 4 and 5
 */
import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { posts } from './content.mjs';

const HERE = dirname(fileURLToPath(import.meta.url));
const OUT = join(HERE, '..', 'output', 'posts');
const STAGE = pathToFileURL(join(HERE, 'stage.html')).href;
const only = process.argv.slice(2).map(Number);

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1080, height: 1350 }, deviceScaleFactor: 1 });
page.on('pageerror', (e) => { console.error('PAGE ERROR', e); process.exitCode = 1; });

for (const post of posts) {
    if (only.length && !only.includes(post.id)) continue;
    const name = `${String(post.id).padStart(2, '0')}-${post.slug}`;
    mkdirSync(join(OUT, name), { recursive: true });
    for (let n = 1; n <= 5; n++) {
        await page.goto(`${STAGE}?mode=post&post=${post.id}&page=${n}`);
        await page.waitForSelector('html[data-ready]');
        await page.screenshot({ path: join(OUT, name, `${name}-${n}.jpg`), type: 'jpeg', quality: 96, clip: { x: 0, y: 0, width: 1080, height: 1350 } });
    }
    console.log(name);
}
await browser.close();
