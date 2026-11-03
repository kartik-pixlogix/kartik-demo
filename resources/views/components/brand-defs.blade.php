{{--
    SVG defs for the client's shield mark.

    Kept separate from <x-svg-defs /> on purpose: that file is generated from
    the prototype by scripts/split-prototype-html.mjs and anything added to it
    is lost on the next run. This one is hand-written and permanent.

    #embm masks the emblem's scan beam to the shield's silhouette. The
    prototype clipped the beam to the two facet paths it drew by hand
    (clipPath #embc, still in <x-svg-defs /> and now unused); the real mark is
    raster artwork, so the beam is masked by the artwork's own alpha channel
    instead. mask-type:alpha is set in CSS — see resources/css/inner-pages.css.

    The placement below must stay identical to the <image> in
    <x-shield-emblem /> — see the PLACEMENT note there for where the four
    numbers come from. If they drift apart the beam slides off the mark.
--}}
<svg width="0" height="0" class="brand-defs" aria-hidden="true" focusable="false">
<defs>
<mask id="embm" maskUnits="userSpaceOnUse" x="0" y="0" width="240" height="280">
<image href="{{ asset('images/brand/ub-shield.webp') }}" x="8.7" y="25.3" width="222.6" height="229.4"></image>
</mask>
</defs>
</svg>
