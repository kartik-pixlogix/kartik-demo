# UB Shield Instagram kit

15 carousel posts (5 pages each) and 15 matching reels, built from the client's
"UB Shield: Instagram Carousel Content" document. The text is the document's, verbatim.

```
output/posts/<nn-slug>/<nn-slug>-<1..5>.jpg   1080 x 1350 (4:5), upload in order
output/reels/<nn-slug>.mp4                    1080 x 1920 (9:16), 30 fps, about 22 s, silent
output/reels/<nn-slug>-cover.jpg              a still to use as the reel's cover
output/index.html                             review page: every post, reel and caption
output/captions.md                            caption + hashtags for each post
```

Posts 1-3 are company posts; 4-15 are two per service. Reels have no music: add audio in
Instagram when posting. Nothing important sits in the top 200 px or bottom 420 px of a reel,
where Instagram draws its own interface.

## Design

The website's brand system: the six brand colours as a navy to blue to cyan gradient,
Sofia Sans Extra Condensed for headings, Instrument Sans for text, square corners, and the
website's own line illustrations (plus six drawn in the same style). The logo, page counter,
progress marks and swipe arrow sit in the same place on every page. The last page of each
post is the website's cyan contact block.

Added beyond the document's text: the service name as a tag on service covers, "Swipe", and
the page counter. No statistics, certifications, client names or vendor logos.

## Changing something

All copy is in `src/content.mjs`. After an edit:

```sh
node social/instagram/src/build-data.mjs       # content + illustrations -> src/data.js
node social/instagram/src/render-posts.mjs     # all posts (or pass post numbers: 4 5)
node social/instagram/src/render-reels.mjs     # all reels (needs ffmpeg; ~3 min each)
node social/instagram/src/build-captions.mjs
node social/instagram/src/build-gallery.mjs
```

`src/stage.html` is the one template for both formats. Open
`src/stage.html?mode=post&post=4&page=2` or `?mode=reel&post=4` in a browser to preview.
