/*
 * Renders one reel per post to output/reels/<nn-slug>.mp4: 1080x1920, 30 fps,
 * H.264 + AAC, with the voiceover, music and transition sounds from
 * audio/mix/<nn>.m4a (run prep-audio.mjs first), plus a cover still.
 *
 * reel.html animates with CSS. Each frame pauses every animation at an exact
 * time and takes a screenshot, so the result is identical on every run, never
 * drops a frame and stays in step with the audio. Needs ffmpeg on PATH.
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
const HERE = dirname(fileURLToPath(import.meta.url));
const OUT = join(HERE, '..', 'output', 'reels');
const MIX = join(HERE, '..', 'audio', 'mix');
const STAGE = pathToFileURL(join(HERE, 'reel.html')).href;
const only = process.argv.slice(2).map(Number);
mkdirSync(OUT, { recursive: true });

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1080, height: 1920 }, deviceScaleFactor: 1 });
page.on('pageerror', (e) => { console.error('PAGE ERROR', e); process.exitCode = 1; });
const seek = (t) => page.evaluate((ms) => { for (const a of document.getAnimations()) { a.pause(); a.currentTime = ms; } }, t * 1000);

for (const post of posts) {
    if (only.length && !only.includes(post.id)) continue;
    const nn = String(post.id).padStart(2, '0');
    const name = `${nn}-${post.slug}`;
    await page.goto(`${STAGE}?post=${post.id}`);
    await page.waitForSelector('html[data-ready]');
    const { dur, hookDone } = await page.evaluate(() => ({ dur: window.REEL_SECONDS, hookDone: window.HOOK_DONE }));
    const frames = Math.round(dur * FPS);

    const ff = spawn('ffmpeg', [
        '-y', '-loglevel', 'error',
        '-f', 'image2pipe', '-framerate', String(FPS), '-c:v', 'mjpeg', '-i', '-',
        '-i', join(MIX, `${nn}.m4a`),
        '-map', '0:v', '-map', '1:a',
        '-c:v', 'libx264', '-preset', 'slow', '-crf', '23', '-pix_fmt', 'yuv420p',
        '-profile:v', 'high', '-level', '4.2', '-r', String(FPS),
        '-c:a', 'copy', '-t', String(frames / FPS), '-movflags', '+faststart',
        join(OUT, `${name}.mp4`),
    ], { stdio: ['pipe', 'inherit', 'inherit'] });
    const done = new Promise((res, rej) => ff.on('close', (c) => (c === 0 ? res() : rej(new Error('ffmpeg ' + c)))));

    for (let f = 0; f < frames; f++) {
        await seek(f / FPS);
        const buf = await page.screenshot({ type: 'jpeg', quality: 93 });
        if (!ff.stdin.write(buf)) await new Promise((r) => ff.stdin.once('drain', r));
    }
    ff.stdin.end();
    await done;
    // Cover image for Instagram: the finished hook, every line lit, with the logo and no caption.
    await seek(hookDone);
    await page.addStyleTag({ content: '.caps,.pulse,.fl{display:none}.hl{color:var(--paper)!important}.hl.last{color:var(--cyan)!important}.hd{opacity:1!important}.sting img{opacity:1!important;transform:translate(-436px,-532px) scale(.229)!important}' });
    await page.screenshot({ type: 'jpeg', quality: 92, path: join(OUT, `${name}-cover.jpg`) });
    console.log(`${name}.mp4  ${dur.toFixed(1)}s  ${frames} frames`);
}
await browser.close();
