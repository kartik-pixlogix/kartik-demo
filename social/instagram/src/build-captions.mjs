/* Writes output/captions.md: the caption and hashtags to paste under each post or reel. */
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { posts } from './content.mjs';

const OUT = join(dirname(fileURLToPath(import.meta.url)), '..', 'output');
mkdirSync(OUT, { recursive: true });
const pad = (n) => String(n).padStart(2, '0');
const body = posts.map((p) => `## Post ${p.id}${p.service ? `: ${p.service}` : ': Company'}

Files: \`posts/${pad(p.id)}-${p.slug}/\` (5 images, in order) and \`reels/${pad(p.id)}-${p.slug}.mp4\`

${p.caption}

${p.hashtags}
`).join('\n');
writeFileSync(join(OUT, 'captions.md'), `# UB Shield Instagram captions\n\nCaption and hashtags for each post. The same caption works for the matching reel.\n\n${body}`);
console.log('captions.md written');
