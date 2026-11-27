/*
 * Renders one reel per post to output/reels/<nn-slug>.mp4: 1080x1920, 30 fps,
 * H.264 + a silent AAC track (add music in Instagram).
 *
 * The stage animates with CSS. Each frame pauses every animation at an exact
 * time and takes a screenshot, so the result is identical on every run and
 * never drops a frame. Needs ffmpeg on PATH.
 *
 *   node social/instagram/src/render-reels.mjs          # all 15
 *   node social/instagram/src/render-reels.mjs 4 5      # only posts 4 and 5
 */
import { chromium } from 'playwright';
import { spawn, execFileSync } from 'node:child_process';
import { mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { posts } from './content.mjs';

const FPS = 30;
const HOLD = 0.6; // seconds held on the last frame
const HERE = dirname(fileURLToPath(import.meta.url));
const OUT = join(HERE, '..', 'output', 'reels');
const STAGE = pathToFileURL(join(HERE, 'stage.html')).href;
const only = process.argv.slice(2).map(Number);
mkdirSync(OUT, { recursive: true });

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1080, height: 1920 }, deviceScaleFactor: 1 });
page.on('pageerror', (e) => { console.error('PAGE ERROR', e); process.exitCode = 1; });

for (const post of posts) {
    if (only.length && !only.includes(post.id)) continue;
    const name = `${String(post.id).padStart(2, '0')}-${post.slug}`;
    await page.goto(`${STAGE}?mode=reel&post=${post.id}`);
    await page.waitForSelector('html[data-ready]');
    const seconds = (await page.evaluate(() => window.REEL_SECONDS)) + HOLD;
    const frames = Math.round(seconds * FPS);

    const ff = spawn('ffmpeg', [
        '-y', '-loglevel', 'error',
        '-f', 'image2pipe', '-framerate', String(FPS), '-c:v', 'mjpeg', '-i', '-',
        '-f', 'lavfi', '-i', 'anullsrc=channel_layout=stereo:sample_rate=44100',
        '-c:v', 'libx264', '-preset', 'medium', '-crf', '22', '-pix_fmt', 'yuv420p',
        '-profile:v', 'high', '-level', '4.2', '-r', String(FPS),
        '-c:a', 'aac', '-b:a', '128k', '-shortest', '-movflags', '+faststart',
        join(OUT, `${name}.mp4`),
    ], { stdio: ['pipe', 'inherit', 'inherit'] });
    const done = new Promise((res, rej) => ff.on('close', (c) => (c === 0 ? res() : rej(new Error('ffmpeg ' + c)))));

    for (let f = 0; f < frames; f++) {
        await page.evaluate((ms) => {
            for (const a of document.getAnimations()) { a.pause(); a.currentTime = ms; }
        }, (f / FPS) * 1000);
        const buf = await page.screenshot({ type: 'jpeg', quality: 94 });
        if (!ff.stdin.write(buf)) await new Promise((r) => ff.stdin.once('drain', r));
    }
    ff.stdin.end();
    await done;
    // A still from the cover scene, to use as the reel's cover image in Instagram.
    execFileSync('ffmpeg', ['-y', '-loglevel', 'error', '-ss', '3.4', '-i', join(OUT, `${name}.mp4`), '-frames:v', '1', '-q:v', '3', join(OUT, `${name}-cover.jpg`)]);
    console.log(`${name}.mp4  ${seconds.toFixed(1)}s  ${frames} frames`);
}
await browser.close();
