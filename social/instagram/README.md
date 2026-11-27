# UB Shield Instagram kit

15 carousel posts (5 pages each) and 15 reels, built from the client's
"UB Shield: Instagram Carousel Content" document.

```
output/posts/<nn-slug>/<nn-slug>-<1..5>.jpg   1080 x 1350 (4:5), upload in order
output/reels/<nn-slug>.mp4                    1080 x 1920 (9:16), 30 fps, 30-38 s, voiceover + music
output/reels/<nn-slug>-cover.jpg              a still to use as the reel's cover
output/index.html                             review page: every post, reel and caption
output/captions.md                            caption + hashtags for each post
output/reel-scripts.md                        what the voice says in each reel
```

Posts 1-3 are company posts; 4-15 are two per service.

## Carousels

The text is the document's, verbatim. Added beyond it: the service name as a tag on service
covers, "Swipe", and the page counter.

## Reels

A reel is not the carousel in motion. Each one is its own edit in five beats:

1. **Hook** - two or three short lines that land one at a time, the last in cyan.
2. **Logo sting** - the mark pops in and settles into the header.
3. **Three scenes** - a numbered label, a large title, then either list items arriving as
   cards or the illustration as a tilted, floating card. Scenes change with a blur swipe.
4. **Close** - the logo, the call to action and `ubshield.in/contact`.

A female voiceover (Indian English) runs through all five beats over a music bed that ducks
under her, with a soft whoosh on each cut. Captions follow the voice, so the reel also works
with sound off. The spoken script (`src/reels.mjs`) restates the document: no new claims,
numbers, offers or promises.

Nothing important sits in the top 200 px or the bottom 380 px of a reel, where Instagram
draws its own interface.

**Voice files.** `audio/vo/<nn>.mp3` is the raw take for each reel. A reel with no voice
file is rendered with music and captions only, at an estimated pace. To add or replace a
voice: record the five lines from `output/reel-scripts.md` with a clear pause between
beats, save it as `audio/vo/<nn>.mp3`, then run `prep-audio.mjs` and re-render that reel.

## Design

The website's brand system: the six brand colours as a navy to blue to cyan gradient,
Sofia Sans Extra Condensed for headings, Instrument Sans for text, square corners, and the
website's own line illustrations (plus six drawn in the same style). No statistics,
certifications, client names or vendor logos.

## Changing something

Carousel copy is in `src/content.mjs`; reel hooks and voice scripts are in `src/reels.mjs`.
After an edit:

```sh
node social/instagram/src/build-data.mjs       # content + illustrations -> src/data.js
node social/instagram/src/render-posts.mjs     # all posts (or pass post numbers: 4 5)
node social/instagram/src/prep-audio.mjs       # voice timing + audio mix -> src/timing.js, audio/mix/
node social/instagram/src/render-reels.mjs     # all reels (needs ffmpeg; ~6 min each)
node social/instagram/src/build-captions.mjs
node social/instagram/src/build-gallery.mjs
```

`src/stage.html` is the carousel template (`?mode=post&post=4&page=2`); `src/reel.html` is
the reel template (`?post=4`). Open either in a browser to preview. Changing a reel's
spoken words means a new voice take; changing only its hook lines does not.
