/* Writes output/captions.md: the caption and hashtags to paste under each post or reel. */
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { posts } from './content.mjs';
import { reels } from './reels.mjs';

const OUT = join(dirname(fileURLToPath(import.meta.url)), '..', 'output');
mkdirSync(OUT, { recursive: true });
const pad = (n) => String(n).padStart(2, '0');
const body = posts.map((p) => `## Post ${p.id}${p.service ? `: ${p.service}` : ': Company'}

Files: \`posts/${pad(p.id)}-${p.slug}/\` (5 images, in order) and \`reels/${pad(p.id)}-${p.slug}.mp4\`

${p.caption}

${p.hashtags}
`).join('\n');
writeFileSync(join(OUT, 'captions.md'), `# UB Shield Instagram captions\n\nCaption and hashtags for each post. The same caption works for the matching reel.\n\n${body}`);
const scripts = posts.map((p) => `## Reel ${p.id}: ${p.pages[0].h}

On-screen hook: ${reels[p.id].hook.join(' / ')}

${reels[p.id].vo.map((l, i) => `${i + 1}. ${l}`).join('\n')}
`).join('\n');
writeFileSync(join(OUT, 'reel-scripts.md'), `# UB Shield reel voiceover scripts\n\nWhat the voice says in each reel, beat by beat (hook, three scenes, close). The same words run as captions on screen.\n\n${scripts}`);
console.log('captions.md and reel-scripts.md written');
