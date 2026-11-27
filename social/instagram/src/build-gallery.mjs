/*
 * Writes output/index.html: a review page with every carousel, reel and
 * caption. Media is referenced by relative path, so the page works when
 * opened from the output folder or published with the files beside it.
 */
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { existsSync } from 'node:fs';
import { posts } from './content.mjs';
import { reels } from './reels.mjs';

const HERE = dirname(fileURLToPath(import.meta.url));
const OUT = join(HERE, '..', 'output');
const hasVoice = (id) => existsSync(join(HERE, '..', 'audio', 'vo', `${String(id).padStart(2, '0')}.mp3`));
const silent = posts.filter((p) => !hasVoice(p.id)).map((p) => p.id);
const pad = (n) => String(n).padStart(2, '0');
const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

const sections = posts.map((p) => {
    const name = `${pad(p.id)}-${p.slug}`;
    const pages = p.pages.map((pg, i) =>
        `<img src="posts/${name}/${name}-${i + 1}.jpg" width="1080" height="1350" loading="lazy" alt="Post ${p.id}, page ${i + 1}: ${esc(pg.h)}">`).join('');
    const caption = `${p.caption}\n\n${p.hashtags}`;
    return `
<article class="post" id="post-${p.id}">
  <header>
    <span class="num">${pad(p.id)}</span>
    <div><h2>${esc(p.pages[0].h)}</h2><p class="kind">${p.service ? esc(p.service) : 'Company'}</p></div>
  </header>
  <div class="media">
    <figure class="carousel"><figcaption>Carousel, 5 pages, 1080 &times; 1350</figcaption><div class="strip" tabindex="0">${pages}</div></figure>
    <figure class="reel"><figcaption>Reel, 1080 &times; 1920, ${hasVoice(p.id) ? 'voiceover + music' : 'music only (voice pending)'}</figcaption><video src="reels/${name}.mp4" controls playsinline preload="none" poster="reels/${name}-cover.jpg"></video></figure>
  </div>
  <details class="vo"><summary>Reel voiceover script</summary><ol>${reels[p.id].vo.map((l) => `<li>${esc(l)}</li>`).join('')}</ol></details>
  <div class="cap"><div class="cap-h"><h3>Caption</h3><button type="button" class="copy" data-copy="${esc(caption)}">Copy caption</button></div><p>${esc(p.caption)}</p><p class="tags">${esc(p.hashtags)}</p></div>
</article>`;
}).join('\n');

const nav = posts.map((p) => `<a href="#post-${p.id}">${pad(p.id)}</a>`).join('');

writeFileSync(join(OUT, 'index.html'), `<title>UB Shield Instagram Kit</title>
<meta name="claude:viewport-fit-sides" content="cover">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Sofia+Sans+Extra+Condensed:wght@800;900&display=swap">
<style>
/* Layout: a contact sheet. Each post is one band: its five pages in a strip, its reel beside them, its caption below. */
:root{--bg:#061522;--surface:#0C2944;--fg:#F4F6F8;--muted:#C7D0D8;--line:#1d3d5c;--accent:#27B8ED;--accent-fg:#061522;
  --disp:'Sofia Sans Extra Condensed','Arial Narrow',sans-serif;--text:'Instrument Sans','Helvetica Neue',Arial,sans-serif;color-scheme:dark}
@media (prefers-color-scheme: light){:root:not([data-theme="dark"]){--bg:#F4F6F8;--surface:#FFFFFF;--fg:#061522;--muted:#44566a;--line:#C7D0D8;--accent:#1479D4;--accent-fg:#FFFFFF;color-scheme:light}}
:root[data-theme="light"]{--bg:#F4F6F8;--surface:#FFFFFF;--fg:#061522;--muted:#44566a;--line:#C7D0D8;--accent:#1479D4;--accent-fg:#FFFFFF;color-scheme:light}
*{box-sizing:border-box}
body{background:var(--bg);color:var(--fg);font:400 16px/1.55 var(--text);padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
.wrap{max-width:1180px;margin:0 auto;padding-block:44px 80px;display:flex;flex-direction:column;gap:44px}
h1,h2{font-family:var(--disp);font-weight:900;text-transform:uppercase;line-height:.94;margin:0;text-wrap:balance}
h1{font-size:clamp(44px,8vw,84px)}
h2{font-size:clamp(26px,3.6vw,38px)}
h3{font:600 13px/1 var(--text);letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin:0}
p{margin:0}
.top{display:flex;flex-direction:column;gap:16px}
.lead{color:var(--muted);font-size:18px;max-width:64ch}
.facts{display:flex;flex-wrap:wrap;gap:8px;margin:0;padding:0;list-style:none}
.facts li{border:1px solid var(--line);background:var(--surface);padding:7px 12px;font-weight:500;font-size:14px}
.nav{display:flex;flex-wrap:wrap;gap:6px}
.nav a{min-width:44px;text-align:center;color:var(--fg);text-decoration:none;border:1px solid var(--line);background:var(--surface);padding:8px 0;font:900 20px/1 var(--disp)}
.nav a:hover,.nav a:focus-visible{background:var(--accent);color:var(--accent-fg);border-color:var(--accent);outline:none}
.post{display:flex;flex-direction:column;gap:18px;border-top:2px solid var(--line);padding-top:28px;scroll-margin-top:12px}
.post header{display:flex;gap:18px;align-items:flex-start}
.num{font:900 54px/.9 var(--disp);color:var(--accent);flex:none}
.kind{color:var(--muted);font-weight:500;margin-top:6px}
.media{display:grid;grid-template-columns:minmax(0,1fr) 230px;gap:20px;align-items:start}
figure{margin:0;min-width:0;display:flex;flex-direction:column;gap:8px}
figcaption{font:600 12px/1 var(--text);letter-spacing:.06em;text-transform:uppercase;color:var(--muted)}
.strip{display:flex;gap:10px;overflow-x:auto;scroll-snap-type:x proximity;padding-bottom:10px}
.strip:focus-visible{outline:2px solid var(--accent);outline-offset:4px}
.strip img{flex:none;width:min(326px,74vw);height:auto;scroll-snap-align:start;display:block}
video{width:100%;aspect-ratio:9/16;background:#000;display:block;object-fit:cover}
.vo{border:1px solid var(--line);padding:12px 20px}.vo summary{cursor:pointer;font:600 13px/1.4 var(--text);letter-spacing:.06em;text-transform:uppercase;color:var(--muted)}.vo ol{margin:12px 0 4px;padding-left:22px;display:flex;flex-direction:column;gap:6px;max-width:78ch}
.cap{background:var(--surface);border:1px solid var(--line);padding:18px 20px;display:flex;flex-direction:column;gap:10px}
.cap-h{display:flex;justify-content:space-between;align-items:center;gap:12px}
.cap p{max-width:78ch}
.tags{color:var(--accent);font-weight:500}
.copy{font:600 14px/1 var(--text);background:var(--accent);color:var(--accent-fg);border:0;padding:10px 14px;cursor:pointer}
.copy:focus-visible{outline:2px solid var(--fg);outline-offset:2px}
footer{color:var(--muted);font-size:14px;border-top:1px solid var(--line);padding-top:18px}
@media (max-width:760px){.media{grid-template-columns:minmax(0,1fr)}.reel{max-width:260px}.post header{gap:12px}.num{font-size:42px}}
</style>
<div class="wrap">
<div class="top">
  <h1>Instagram kit</h1>
  <p class="lead">Fifteen carousel posts and fifteen reels for UB Shield, built from the carousel content document. Carousel text is the document&rsquo;s, word for word. Each reel is its own edit: a hook, three scenes and a call to action, with a voiceover, music and captions that follow the voice.</p>
  <ul class="facts"><li>15 carousels &times; 5 pages</li><li>15 reels, 30 to 38 seconds each</li><li>Posts 1&ndash;3: company</li><li>Posts 4&ndash;15: two per service</li><li>Female voiceover + music${silent.length ? `: reels ${silent.join(', ')} are music only for now` : ''}</li></ul>
  <nav class="nav" aria-label="Jump to a post">${nav}</nav>
</div>
${sections}
<footer>Scroll each strip sideways to see all five pages. Press play on a reel to load it, with sound on.</footer>
</div>
<script>
document.querySelectorAll('.copy').forEach(function (b) {
  b.addEventListener('click', function () {
    var text = b.dataset.copy, done = function () { b.textContent = 'Copied'; setTimeout(function () { b.textContent = 'Copy caption'; }, 1600); };
    (navigator.clipboard ? navigator.clipboard.writeText(text) : Promise.reject()).then(done).catch(function () {
      var r = document.createRange(); r.selectNodeContents(b.closest('.cap')); var s = getSelection(); s.removeAllRanges(); s.addRange(r); b.textContent = 'Selected: press copy';
    });
  });
});
</script>
`);
console.log('index.html written');
