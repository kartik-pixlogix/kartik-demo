{{--
    The UB Shield emblem — the client's own shield mark.

    The prototype drew this as five <path>s: a silver facet, a blue facet, a
    stroked U, two B triangles and a highlight (see design/v7/index.html). That
    drawing approximates the logo rather than reproducing it, so the client
    asked for the real mark; it is raster artwork, placed here with <image>.
    Logged in docs/prototype-fixes.md A7.

    Everything around the artwork is unchanged: the same 240x280 viewBox, so
    .emb / .femb sizing, the scanE keyframe range and the closing strip's
    translate(130 215) all still hold. Only the clip on the scan beam had to
    change — the hand-drawn facets are gone, so the beam is masked by the
    artwork's own alpha instead (#embm, in <x-brand-defs />).

    Three uses, matching the prototype:

      <x-shield-emblem class="emb" scan />                            hero
      <x-shield-emblem group transform="translate(130 215)" scan />   strip
      <x-shield-emblem class="femb" />                                footer

    PLACEMENT

    The prototype's shield filled 192 x 266 of the 240 x 280 viewBox. The real
    mark is 752 x 775 — a much squarer shape — so neither matching its width
    nor its height works: at the prototype's height it spills out of the box,
    which clipped the footer emblem. It is fitted to the same *area* instead
    (222.6 x 229.4, centred), which keeps the emblem's visual weight, stays
    inside the viewBox at every size, and still clears the r=190 circle it
    sits inside on the closing strip.

    The file is padded so the solid mark is centred in it — the supplied
    artwork's drop shadow is not symmetric, so without that the shield lands
    up-left of wherever it is placed. scripts/build-brand-assets.mjs does the
    padding and prints these numbers; it is the only place they come from.

    They are duplicated in <x-brand-defs /> and the two must agree, or the
    scan beam drifts off the mark.
--}}
@props(['scan' => false, 'group' => false, 'transform' => null])
@if ($group)<g transform="{{ $transform }}">@else<svg {{ $attributes->merge(['viewBox' => '0 0 240 280', 'aria-hidden' => 'true']) }}>@endif<image href="{{ asset('images/brand/ub-shield.webp') }}" x="8.7" y="25.3" width="222.6" height="229.4"></image>@if ($scan)<g mask="url(#embm)"><g class="scanE"><rect x="-10" y="122" width="260" height="18" fill="#27B8ED" opacity=".22"></rect><rect x="-10" y="136" width="260" height="3" fill="#F4F6F8"></rect></g></g>@endif@if ($group)</g>@else</svg>@endif
