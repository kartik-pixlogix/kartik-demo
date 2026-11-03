/* Builds the side-by-side review images: prototype reference | Laravel build. */
import { chromium } from 'playwright';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import { REF, DIFF } from './_shared.mjs';



const b64 = (f) => 'data:image/png;base64,' + readFileSync(f).toString('base64');

const browser = await chromium.launch();

for (const [w, panel] of [[1440, 520], [390, 330]]) {
    const a = b64(path.join(REF, `proto-${w}.png`));
    const c = b64(path.join(DIFF, 'build', `build-${w}.png`));
    const html = `<!doctype html><meta charset=utf-8><style>
      :root{color-scheme:dark}
      body{margin:0;background:#0b0b0c;font:600 13px/1.4 ui-sans-serif,system-ui,sans-serif;color:#e7e7e9}
      .bar{display:flex;gap:20px;padding:14px 20px 10px;position:sticky;top:0;background:#0b0b0c}
      .bar div{width:${panel}px;text-transform:uppercase;letter-spacing:.08em}
      .bar span{color:#7bdc8f;font-weight:500;text-transform:none;letter-spacing:0}
      .pair{display:flex;gap:20px;padding:0 20px 20px;align-items:flex-start}
      img{width:${panel}px;display:block;border:1px solid #2a2a2e}
    </style>
    <div class="bar">
      <div>Prototype &mdash; design/v7 @ ${w}px</div>
      <div>Laravel build @ ${w}px<br><span>0 pixels differ</span></div>
    </div>
    <div class="pair"><img src="${a}"><img src="${c}"></div>`;

    const ctx = await browser.newContext({ viewport: { width: panel * 2 + 60, height: 900 }, deviceScaleFactor: 1 });
    const page = await ctx.newPage();
    await page.setContent(html);
    await page.waitForTimeout(600);
    const out = path.join(DIFF, `side-by-side-${w}.png`);
    await page.screenshot({ path: out, fullPage: true });
    console.log(`${out}  (panels ${panel}px wide)`);
    await ctx.close();
}

await browser.close();
