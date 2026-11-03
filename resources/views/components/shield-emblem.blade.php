{{--
    The UB Shield emblem: silver left facet, blue right facet, U and B cut-outs.
    Three uses in the prototype — hero (scanning), closing strip (scanning, as a
    <g> inside the circuit panel) and footer (no scan). Relies on the gSl / gBl
    gradients and the embc clip path from <x-svg-defs />.

      <x-shield-emblem class="emb" scan />
      <x-shield-emblem class="femb" />
      <x-shield-emblem group transform="translate(130 215)" scan />
--}}
@props(['scan' => false, 'group' => false, 'transform' => null])
@if ($group)<g transform="{{ $transform }}">@else<svg {{ $attributes->merge(['viewBox' => '0 0 240 280', 'aria-hidden' => 'true']) }}>@endif<path d="M112 10 24 40v106c0 50 38 88 88 120Z" fill="url(#gSl)"></path><path d="M128 10l88 30v106c0 50-38 88-88 120Z" fill="url(#gBl)"></path><path d="M62 72v76c0 28 18 48 38 56" fill="none" stroke="#061522" stroke-width="15"></path><path d="M128 82l52 18-52 24Zm0 58 56 20-56 28Z" fill="#061522"></path><path d="M112 10 24 40v34l88-32Z" fill="#ffffff" opacity=".35"></path>@if ($scan)<g clip-path="url(#embc)"><g class="scanE"><rect x="0" y="122" width="240" height="18" fill="#27B8ED" opacity=".22"></rect><rect x="0" y="136" width="240" height="3" fill="#F4F6F8"></rect></g></g>@endif@if ($group)</g>@else</svg>@endif