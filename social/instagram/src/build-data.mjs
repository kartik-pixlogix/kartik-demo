/*
 * Writes src/data.js (window.IG) for stage.html: the post content plus the
 * illustrations. Seven illustrations are the website's own artwork
 * (resources/views/components/art), so the posts and the site share one
 * drawing style; six are drawn here in the same style for the visual ideas
 * the site has no artwork for.
 *
 *   node social/instagram/src/build-data.mjs
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { posts } from './content.mjs';

const HERE = dirname(fileURLToPath(import.meta.url));
const ROOT = join(HERE, '..', '..', '..');
const ART = join(ROOT, 'resources', 'views', 'components', 'art');

/** A website illustration, without its Blade comment and its own backdrop. */
const site = (name) =>
    readFileSync(join(ART, `${name}.blade.php`), 'utf8')
        .replace(/\{\{--[\s\S]*?--\}\}/g, '')
        .replace(/<rect width="500" height="400" fill="(#061522|#0C2944)"><\/rect>/, '')
        .replace(/<rect width="500" height="400" fill="url\(#dots\)"><\/rect>/, '')
        .replace(/ data-px="[^"]*"/, '')
        .trim();

const svg = (inner) => `<svg viewBox="0 0 500 400" aria-hidden="true">${inner}</svg>`;
const L = 'fill="none" stroke="#C7D0D8" stroke-width="3"';
const C = 'fill="none" stroke="#27B8ED" stroke-width="3"';
const tick = '<path d="m24 46 12 12 22-24" fill="none" stroke="#F4F6F8" stroke-width="6"></path>';

const art = {
    network: site('service-infra'),
    vapt: site('service-vapt'),
    radar: site('service-netsec'),
    health: site('industry-2'),
    firewall: site('service-firewall'),
    checklist: site('note-controls'),
    handover: site('service-deploy'),

    // The mark inside its radar rings (post 1 and every closing page).
    emblem: svg(`
<g fill="none" stroke="#1479D4" stroke-width="2"><circle cx="250" cy="200" r="150" opacity=".8"></circle><circle cx="250" cy="200" r="186" stroke-dasharray="4 10" opacity=".6"></circle></g>
<g class="spin"><circle cx="250" cy="200" r="150" fill="none"></circle><path d="M250 50a150 150 0 0 1 130 75" fill="none" stroke="#27B8ED" stroke-width="4"></path></g>
<circle class="pl2" cx="400" cy="200" r="6" fill="#27B8ED"></circle><circle class="pl2" style="animation-delay: 1s" cx="118" cy="268" r="6" fill="#27B8ED"></circle>
<image href="__SHIELD__" x="150" y="97" width="200" height="206"></image>`),

    // Four-step roadmap (post 3).
    steps: svg(`
<path d="M40 352h420" stroke="#C7D0D8" stroke-width="3" opacity=".5"></path>
<g stroke="#C7D0D8" stroke-width="2" stroke-dasharray="3 8" opacity=".4"><path d="M84 316v36M194 246v36M304 176v106M414 106v176"></path></g>
<path class="flow" d="M84 288 194 218 304 148 414 78" ${C}></path>
${[[84, 288], [194, 218], [304, 148], [414, 78]].map(([x, y], i) => `
<rect x="${x - 30}" y="${y - 30}" width="60" height="60" fill="${i === 3 ? '#1479D4' : '#0C2944'}" stroke="${i === 3 ? '#27B8ED' : '#C7D0D8'}" stroke-width="3"></rect>
<text x="${x}" y="${y + 10}" text-anchor="middle" fill="#F4F6F8" font-size="28" font-family="Instrument Sans, sans-serif" font-weight="700">${i + 1}</text>`).join('')}`),

    // One office, four zones (post 5).
    zones: svg(`
<rect x="60" y="56" width="188" height="142" fill="#1479D4" opacity=".30"></rect><rect x="252" y="56" width="188" height="142" fill="#27B8ED" opacity=".16"></rect>
<rect x="60" y="202" width="188" height="142" fill="#0C2944"></rect><rect x="252" y="202" width="188" height="142" fill="#1479D4" opacity=".55"></rect>
<rect x="60" y="56" width="380" height="288" ${L}></rect>
<path class="flow" d="M250 56v288M60 200h380" ${C}></path>
<g ${L}><rect x="116" y="98" width="76" height="48"></rect><path d="M136 164h36M154 146v18"></path></g>
<g ${C}><path d="M304 118a58 58 0 0 1 84 0M318 134a38 38 0 0 1 56 0M332 150a18 18 0 0 1 28 0"></path></g><circle class="pl2" cx="346" cy="166" r="6" fill="#27B8ED"></circle>
<g ${L}><rect x="110" y="232" width="88" height="24"></rect><rect x="110" y="262" width="88" height="24"></rect><rect x="110" y="292" width="88" height="24"></rect></g><g fill="#27B8ED"><circle cx="184" cy="244" r="3.5"></circle><circle cx="184" cy="274" r="3.5"></circle><circle cx="184" cy="304" r="3.5"></circle></g>
<g fill="none" stroke="#F4F6F8" stroke-width="3"><rect x="306" y="246" width="56" height="34"></rect><path d="M362 254l30-12v42l-30-12zM334 280v26M314 306h40"></path></g>
<rect x="232" y="182" width="36" height="36" fill="#061522" stroke="#27B8ED" stroke-width="3"></rect><path d="m241 200 6 6 12-13" fill="none" stroke="#27B8ED" stroke-width="3"></path>`),

    // Looks weak / can it be opened (post 7).
    lock: svg(`
<path class="flow" d="M150 240h30M320 240h44" ${C}></path>
<g ${L}><rect x="54" y="176" width="96" height="128"></rect><path d="M92 208h40M92 240h40M92 272h28"></path><rect x="68" y="200" width="14" height="14"></rect><rect x="68" y="232" width="14" height="14"></rect><rect x="68" y="264" width="14" height="14"></rect></g>
<rect x="180" y="180" width="140" height="124" fill="#0C2944" stroke="#C7D0D8" stroke-width="3"></rect><path d="M206 180v-42a44 44 0 0 1 88 0v42" ${L}></path>
<circle cx="250" cy="228" r="13" fill="#27B8ED"></circle><path d="M250 238v30" stroke="#27B8ED" stroke-width="7"></path>
<g class="pl2"><circle cx="408" cy="240" r="38" ${C}></circle><circle cx="408" cy="240" r="14" fill="#1479D4"></circle><path d="M408 188v26M408 266v26M356 240h26M434 240h26" ${C}></path></g>`),

    // Three warning cards (post 11).
    warn: svg(`
${[[66, 132], [194, 104], [322, 132]].map(([x, y], i) => `
<rect x="${x}" y="${y}" width="112" height="164" fill="#0C2944" stroke="${i === 1 ? '#27B8ED' : '#C7D0D8'}" stroke-width="3"></rect>
<path class="${i === 1 ? 'pl2' : ''}" d="M${x + 56} ${y + 26}l30 52h-60z" ${C}></path><path d="M${x + 56} ${y + 46}v16" stroke="#F4F6F8" stroke-width="4"></path><circle cx="${x + 56}" cy="${y + 69}" r="2.6" fill="#F4F6F8"></circle>
<path d="M${x + 22} ${y + 108}h68M${x + 22} ${y + 130}h46" stroke="#C7D0D8" stroke-width="3" opacity=".7"></path>`).join('')}`),

    // Settings switched to a secure state (post 12).
    toggles: svg(`
${[0, 1, 2].map((i) => {
        const y = 112 + i * 68;
        const on = i < 2;
        return `<rect x="70" y="${y}" width="104" height="44" rx="22" fill="${on ? '#1479D4' : 'none'}" stroke="${on ? '#27B8ED' : '#C7D0D8'}" stroke-width="3"></rect><circle cx="${on ? 152 : 92}" cy="${y + 22}" r="14" fill="${on ? '#F4F6F8' : '#C7D0D8'}"></circle><path d="M198 ${y + 22}h${[92, 64, 80][i]}" stroke="#C7D0D8" stroke-width="3" opacity=".7"></path>`;
    }).join('')}
<circle class="spin" cx="382" cy="204" r="92" fill="none" stroke="#27B8ED" stroke-width="3" stroke-dasharray="6 9"></circle>
<g transform="translate(326 126) scale(1.4)"><use href="#sh" fill="#1479D4"></use>${tick}</g>`),

    // Layers of protection around what matters (post 14).
    layers: svg(`
<g transform="translate(110 30.4) scale(3.5)" fill="none" stroke="#1479D4" opacity=".7"><use href="#sh" stroke-width=".7"></use></g>
<g transform="translate(150 78.9) scale(2.5)" fill="none" stroke="#27B8ED"><use class="flow" href="#sh" stroke-width="1.1"></use></g>
<g transform="translate(190 127.4) scale(1.5)"><use href="#sh" fill="#1479D4"></use>${tick}</g>
<circle class="pl2" cx="96" cy="130" r="6" fill="#27B8ED"></circle><circle class="pl2" style="animation-delay: .8s" cx="404" cy="130" r="6" fill="#27B8ED"></circle><circle class="pl2" style="animation-delay: 1.6s" cx="250" cy="372" r="6" fill="#27B8ED"></circle>`),
};

writeFileSync(
    join(HERE, 'data.js'),
    '/* Generated by build-data.mjs. Do not edit. */\nwindow.IG = ' + JSON.stringify({ posts, art }) + ';\n',
);
console.log(`data.js written: ${posts.length} posts, ${Object.keys(art).length} illustrations`);
